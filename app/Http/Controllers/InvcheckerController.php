<?php

namespace App\Http\Controllers;

use App\Models\Pperiod;
use App\Models\Registration;
use App\Models\EtimsInvoice;
use App\Models\PaymentStatus;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvcheckerController extends Controller
{
    public function index()
    {
        $period = Pperiod::where('sstatus', 'Active')->first();

        return view('students.ichecker', [
            'month' => $period->mmonth ?? '',
            'year'  => $period->yyear ?? ''
        ]);
    }

    public function import(Request $request)
{
    try {
        if (!$request->hasFile('file')) {
            $this->streamLine([
                'status' => 'error',
                'message' => 'No file received — check the file input name matches "file".',
            ]);
            exit;
        }

        $validated = $request->validate([
            'file' => 'required|file|extensions:xlsx,xls',
        ]);

        $period = Pperiod::where('sstatus', 'Active')->first();
        if (!$period) {
            $this->streamLine([
                'status' => 'error',
                'message' => 'No active pay period found.',
            ]);
            exit;
        }

        $month = $period->mmonth;
        $year = $period->yyear;

        $storedPath = $request->file('file')->store('etims-imports');
        $fullPath = \Illuminate\Support\Facades\Storage::path($storedPath);

        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json');
        header('X-Accel-Buffering: no');

        $this->processImport($fullPath, $month, $year);

    } catch (\Throwable $e) {
        Log::error('Etims import fatal error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        $this->streamLine([
            'status' => 'error',
            'message' => 'Import failed: ' . $e->getMessage(),
        ]);
    }

    exit;
}

  protected function processImport(string $filePath, string $importFilename)
{
    $userId = Auth::id();
    try {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $reader = $ext === 'xls' ? new Xls() : new Xlsx();
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);
        $header = array_shift($rows);

        $colMap = [];
        foreach ($header as $col => $label) {
            $colMap[trim((string) $label)] = $col;
        }

        $required = ['PIN', 'ETims Invoice No', 'TransDateTime'];
        foreach ($required as $col) {
            if (!isset($colMap[$col])) {
                $this->streamLine(['status' => 'error', 'message' => "Missing expected column: {$col}"]);
                exit;
            }
        }

        $total = count($rows);
        $this->streamLine(['status' => 'progress', 'progress' => 5, 'message' => 'Validating rows…', 'success' => 0, 'errors' => 0]);

        // ---- Pass 1: parse + collect all PINs, no DB calls yet ----
        $parsed = [];
        $exceptions = [];
        $pins = [];

        foreach ($rows as $row) {
            $pin = trim((string) ($row[$colMap['PIN']] ?? ''));
            $etimsInv = trim((string) ($row[$colMap['ETims Invoice No']] ?? ''));
            $rawDate = $row[$colMap['TransDateTime']] ?? null;
            $date = $this->parseTransDate($rawDate);

            if ($pin === '' || $etimsInv === '') {
                $exceptions[] = [$pin, $etimsInv, $rawDate, 'Missing PIN or Invoice No'];
                continue;
            }
            if (!$date) {
                $exceptions[] = [$pin, $etimsInv, $rawDate, 'Unrecognized TransDateTime'];
                continue;
            }

            $pins[] = $pin;
            $parsed[] = compact('pin', 'etimsInv', 'date');
        }

        // ---- One bulk lookup instead of N ----
        $registrations = Registration::whereIn('kra', array_unique($pins))->get()->keyBy('kra');

        $this->streamLine(['status' => 'progress', 'progress' => 20, 'message' => 'Matching employees…', 'success' => 0, 'errors' => count($exceptions)]);

        // ---- Pass 2: resolve WorkNo, drop unmatched PINs into exceptions ----
        $matched = [];
        foreach ($parsed as $item) {
            $registration = $registrations->get($item['pin']);
            if (!$registration) {
                $exceptions[] = [$item['pin'], $item['etimsInv'], $item['date']->toDateTimeString(), 'PIN not found in registration records'];
                continue;
            }
            $matched[] = [
                'workNo' => $registration->empid,
                'pin' => $item['pin'],
                'etimsInv' => $item['etimsInv'],
                'date' => $item['date'],
                'month' => $item['date']->format('F'),
                'year' => $item['date']->format('Y'),
                'periodTag' => $item['date']->format('M') . $item['date']->format('Y'),
            ];
        }

        // ---- Reserve one block of sequence numbers, not one per row ----
        $startNumber = $this->reserveInvoiceNumbers(count($matched));

        $etimsRows = [];
        $now = now();
        foreach ($matched as $i => $m) {
            $seqNumber = $startNumber + $i;
            $etimsRows[] = [
                'WorkNo' => $m['workNo'],
                'month' => $m['month'],
                'year' => $m['year'],
                'PIN' => $m['pin'],
                'Etimsinv' => $m['etimsInv'],
                'TransDateTime' => $m['date']->toDateTimeString(),
                'SystemInvoiceNo' => "{$m['periodTag']}/{$m['workNo']}/" . str_pad($seqNumber, 4, '0', STR_PAD_LEFT),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->streamLine(['status' => 'progress', 'progress' => 50, 'message' => 'Saving invoices…', 'success' => count($etimsRows), 'errors' => count($exceptions)]);

        // ---- Bulk upsert EtimsInvoice, chunked to keep queries reasonable ----
        foreach (array_chunk($etimsRows, 200) as $chunk) {
            EtimsInvoice::upsert(
                $chunk,
                ['WorkNo', 'month', 'year'],
                ['PIN', 'Etimsinv', 'TransDateTime', 'SystemInvoiceNo', 'updated_at']
            );
        }

        $this->streamLine(['status' => 'progress', 'progress' => 75, 'message' => 'Updating payment status…', 'success' => count($etimsRows), 'errors' => count($exceptions)]);

        // ---- Conditional upsert on PaymentStatus: insert if missing, flip only if UNPAID ----
        foreach (array_chunk($matched, 200) as $chunk) {
            $this->upsertPaymentStatusChunk($chunk, $now);
        }

        $reportUrl = null;
        $reportFilename = null;
        if (!empty($exceptions)) {
            [$reportFilename, $reportUrl] = $this->buildExceptionReport($exceptions);
        }

        Log::info('Etims import complete', [
            'file' => $importFilename,
            'imported' => count($etimsRows),
            'errors' => count($exceptions),
        ]);

        logAuditTrail(
                $userId,
                'OTHER',
                'Etims_Checker',
                'ALL',
                $importFilename,
                null,
                [
                    'action' => 'Etimschecker',
                    'checked' => count($etimsRows),
                    'errors' => count($exceptions)
                ]
            );

        $this->streamLine([
            'status' => 'success',
            'message' => "Import complete: " . count($etimsRows) . " matched, " . count($exceptions) . " exceptions.",
            'success' => count($etimsRows),
            'errors' => count($exceptions),
            'has_duplicate_report' => !empty($exceptions),
            'duplicate_report_url' => $reportUrl,
            'duplicate_report_filename' => $reportFilename,
        ]);

    } catch (\Throwable $e) {
        Log::error('Etims import failed: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        $this->streamLine(['status' => 'error', 'message' => 'Import failed: ' . $e->getMessage()]);
    }
}

protected function upsertPaymentStatusChunk(array $rows, $now)
{
    // Raw SQL because the update is conditional (only flip if currently UNPAID),
    // which Eloquent's upsert() can't express — it always overwrites.
    $placeholders = [];
    $bindings = [];

    foreach ($rows as $m) {
        $placeholders[] = '(?, ?, ?, NULL, ?, ?, ?)';
        array_push($bindings, $m['workNo'], $m['month'], $m['year'], 'TO BE PAID', $now, $now);
    }

    $sql = "INSERT INTO payment_status (WorkNo, month, year, net_amount, status, invoiced_at, created_at, updated_at)
            VALUES " . implode(',', array_map(fn($p) => str_replace('?, ?, ?, NULL, ?, ?, ?', '?, ?, ?, NULL, ?, ?, NOW(), ?', $p), $placeholders)) . "
            ON DUPLICATE KEY UPDATE
                status = IF(status = 'UNPAID', 'TO BE PAID', status),
                invoiced_at = IF(status = 'UNPAID', VALUES(invoiced_at), invoiced_at)";

    // Simpler and less error-prone: rebuild cleanly rather than string-patching placeholders above
    $values = [];
    $bindings = [];
    foreach ($rows as $m) {
        $values[] = '(?, ?, ?, NULL, ?, ?, ?, ?)';
        array_push($bindings, $m['workNo'], $m['month'], $m['year'], 'TO BE PAID', $now, $now, $now);
    }

    $sql = "INSERT INTO payment_status (WorkNo, month, year, net_amount, status, invoiced_at, created_at, updated_at)
            VALUES " . implode(',', $values) . "
            ON DUPLICATE KEY UPDATE
                status = IF(status = 'UNPAID', 'TO BE PAID', status),
                invoiced_at = IF(status = 'UNPAID', VALUES(invoiced_at), invoiced_at),
                updated_at = VALUES(updated_at)";

    DB::statement($sql, $bindings);
}

protected function buildExceptionReport(array $exceptions): array
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(['PIN', 'ETims Invoice No', 'TransDateTime', 'Reason'], null, 'A1');
    $sheet->fromArray($exceptions, null, 'A2');

    $filename = 'etims_exceptions_' . now()->format('Ymd_His') . '.xlsx';
    $dir = storage_path('app/exports');
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    (new XlsxWriter($spreadsheet))->save($dir . '/' . $filename);

    return [$filename, route('ichecker.exceptions.download', $filename)];
}

    /**
     * Writes one JSON line and flushes immediately, matching the frontend's
     * line-by-line xhr.onprogress parsing.
     */
    protected function streamLine(array $data)
    {
        echo json_encode($data) . "\n";
        if (ob_get_level() > 0) ob_flush();
        flush();
    }

    /**
     * Builds an .xlsx of rows that failed PIN matching, so the accountant can
     * see exactly who was skipped and why (missing PIN, or PIN not in Registration).
     */
    

    // InvcheckerController
public function downloadException(string $filename)
{
    $path = storage_path('app/exports/' . basename($filename)); // basename() blocks path traversal
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->download($path)->deleteFileAfterSend(false);
}
protected function parseTransDate($value): ?Carbon
{
    if ($value === null || $value === '') {
        return null;
    }
    if (is_numeric($value)) {
        return Carbon::instance(
            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
        );
    }

    $value = trim((string) $value);

    // Worksheet format is dd/mm/yyyy HH:mm:ss — explicit format, no guessing
    try {
        return Carbon::createFromFormat('d/m/Y H:i:s', $value);
    } catch (\Throwable $e) {
        // fall back for rows that might come in as date-only, no time
        try {
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Throwable $e2) {
            return null;
        }
    }
}
protected function reserveInvoiceNumbers(int $count): int
{
    if ($count === 0) return 1;

    return DB::transaction(function () use ($count) {
        $seq = DB::table('invoice_sequences')->where('name', 'etims_invoice')->lockForUpdate()->first();

        if (!$seq) {
            DB::table('invoice_sequences')->insert([
                'name' => 'etims_invoice',
                'next_number' => 1 + $count,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            return 1;
        }

        $start = $seq->next_number;
        DB::table('invoice_sequences')->where('name', 'etims_invoice')
            ->update(['next_number' => $start + $count, 'updated_at' => now()]);

        return $start;
    });
}
}