<?php

namespace App\Http\Controllers;

use App\Models\Pperiod;
use App\Models\Registration;
use App\Models\EtimsInvoice;
use App\Models\PaymentStatus;
use App\Models\Payhouse;
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
    set_time_limit(300);
    ini_set('max_execution_time', 300);
    $userId = Auth::id();

    $validMonths = ['January','February','March','April','May','June','July','August','September','October','November','December'];

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

        $required = ['Agent Code', 'Period', 'PIN', 'KRA Invoice No', 'Payment Voucher Date'];
        foreach ($required as $col) {
            if (!isset($colMap[$col])) {
                $this->streamLine(['status' => 'error', 'message' => "Missing expected column: {$col}"]);
                exit;
            }
        }
        $hasGrossColumn = isset($colMap['Gross Amount']);

        $this->streamLine(['status' => 'progress', 'progress' => 5, 'message' => 'Validating rows…', 'success' => 0, 'errors' => 0]);

        // ---- Pass 1: parse every row, no DB calls yet ----
        $parsed = [];
        $exceptions = [];
        $reasonCounts = [];

        foreach ($rows as $row) {
            $workNo = trim((string) ($row[$colMap['Agent Code']] ?? ''));
            $periodRaw = trim((string) ($row[$colMap['Period']] ?? ''));
            $pin = trim((string) ($row[$colMap['PIN']] ?? ''));
            $invoiceNo = trim((string) ($row[$colMap['KRA Invoice No']] ?? ''));
            $voucherDateRaw = $row[$colMap['Payment Voucher Date']] ?? null;

            $grossRaw = $hasGrossColumn ? ($row[$colMap['Gross Amount']] ?? null) : null;
            $grossClean = is_string($grossRaw) ? preg_replace('/[^0-9.\-]/', '', $grossRaw) : $grossRaw;
            $grossFromFile = is_numeric($grossClean) ? (float) $grossClean : null;

            if ($workNo === '' || $invoiceNo === '') {
                $reasonCounts['missing_agent_code_or_invoice_no'] = ($reasonCounts['missing_agent_code_or_invoice_no'] ?? 0) + 1;
                $exceptions[] = [$workNo, $pin, $invoiceNo, $periodRaw, null, 'Missing Agent Code or KRA Invoice No'];
                continue;
            }

            // NEW — Period is stated explicitly, just split and validate. No date-guessing at all.
            $periodParts = explode(' ', $periodRaw, 2);
            if (count($periodParts) !== 2 || !in_array($periodParts[0], $validMonths, true) || !preg_match('/^\d{4}$/', $periodParts[1])) {
                $reasonCounts['invalid_period_format'] = ($reasonCounts['invalid_period_format'] ?? 0) + 1;
                $exceptions[] = [$workNo, $pin, $invoiceNo, $periodRaw, null, "Invalid Period format — expected 'MonthName YYYY', e.g. 'January 2026'"];
                continue;
            }
            [$month, $year] = $periodParts;

            $voucherDate = $this->parseTransDate($voucherDateRaw); // kept — audit-only now, no fallback chain pressure
            if (!$voucherDate) {
                $reasonCounts['unrecognized_voucher_date'] = ($reasonCounts['unrecognized_voucher_date'] ?? 0) + 1;
                $exceptions[] = [$workNo, $pin, $invoiceNo, $periodRaw, null, 'Unrecognized Payment Voucher Date'];
                continue;
            }

            $parsed[] = [
                'workNo' => $workNo,
                'pin' => $pin,
                'invoiceNo' => $invoiceNo,
                'month' => $month,
                'year' => $year,
                'periodTag' => substr($month, 0, 3) . $year,
                'voucherDate' => $voucherDate,
                'grossFromFile' => $grossFromFile,
            ];
        }

        Log::info('Etims import: Pass 1 complete', [
            'file' => $importFilename,
            'total_rows' => count($rows),
            'passed_pass1' => count($parsed),
            'failed_pass1_by_reason' => $reasonCounts,
        ]);

        $this->streamLine(['status' => 'progress', 'progress' => 25, 'message' => 'Verifying against payroll records…', 'success' => 0, 'errors' => count($exceptions)]);

        // ---- One bulk registration lookup, keyed by WorkNo — for PIN cross-check ----
        $workNos = collect($parsed)->pluck('workNo')->unique()->toArray();
        $registrations = Registration::whereIn('empid', $workNos)->get()->keyBy('empid');

        // ---- One bulk payment_status lookup, keyed by WorkNo|month|year — direct, exact match, no guessing ----
        $statusRows = \App\Models\PaymentStatus::whereIn('WorkNo', $workNos)->get()->keyBy(
            fn($r) => $r->WorkNo . '|' . $r->month . '|' . $r->year
        );

        // ---- Pass 2: validate each row against registration + payment_status ----
        $verified = [];
        $noRegistrationCount = 0;
        $noPeriodRecordCount = 0;
        $alreadyPaidCount = 0;
        $pinMismatchCount = 0;
        $grossMismatchCount = 0;

        foreach ($parsed as $m) {
            $registration = $registrations->get($m['workNo']);
            if (!$registration) {
                $noRegistrationCount++;
                $exceptions[] = [$m['workNo'], $m['pin'], $m['invoiceNo'], "{$m['month']} {$m['year']}", null, 'Agent Code not found in registration records'];
                continue;
            }

            if ($m['pin'] !== '' && trim((string) $registration->kra) !== $m['pin']) {
                $pinMismatchCount++;
                $exceptions[] = [$m['workNo'], $m['pin'], $m['invoiceNo'], "{$m['month']} {$m['year']}", null,
                    "PIN does not match registration record (expected {$registration->kra})"];
                continue;
            }

            $statusKey = $m['workNo'] . '|' . $m['month'] . '|' . $m['year'];
            $statusRow = $statusRows->get($statusKey);

            if (!$statusRow) {
                $noPeriodRecordCount++;
                $exceptions[] = [$m['workNo'], $m['pin'], $m['invoiceNo'], "{$m['month']} {$m['year']}", null,
                    'No payroll record found for this Agent/Period combination'];
                continue;
            }

            if ($statusRow->status === 'PAID') {
                $alreadyPaidCount++;
                $exceptions[] = [$m['workNo'], $m['pin'], $m['invoiceNo'], "{$m['month']} {$m['year']}", $statusRow->gross_amount,
                    'This Agent/Period has already been marked PAID'];
                continue;
            }

            // Gross Amount is now a SANITY CHECK, not the matching mechanism — only runs if the column was supplied
            if ($m['grossFromFile'] !== null && abs((float) $statusRow->gross_amount - $m['grossFromFile']) > 1) {
                $grossMismatchCount++;
                $exceptions[] = [$m['workNo'], $m['pin'], $m['invoiceNo'], "{$m['month']} {$m['year']}", $statusRow->gross_amount,
                    "Gross Amount in file ({$m['grossFromFile']}) does not match payroll record ({$statusRow->gross_amount})"];
                continue;
            }

            $verified[] = $m;
        }

        Log::info('Etims import: Pass 2 verification summary', [
            'file' => $importFilename,
            'total_checked' => count($parsed),
            'verified' => count($verified),
            'no_registration_match' => $noRegistrationCount,
            'pin_mismatch' => $pinMismatchCount,
            'no_period_record' => $noPeriodRecordCount,
            'already_paid' => $alreadyPaidCount,
            'gross_mismatch' => $grossMismatchCount,
        ]);

        $this->streamLine(['status' => 'progress', 'progress' => 50, 'message' => 'Saving invoices…', 'success' => count($verified), 'errors' => count($exceptions)]);

        // ---- Reserve sequence numbers, sized to verified rows only ----
        $startNumber = $this->reserveInvoiceNumbers(count($verified));

        $etimsRows = [];
        $now = now();
        foreach ($verified as $i => $m) {
            $seqNumber = $startNumber + $i;
            $etimsRows[] = [
                'WorkNo' => $m['workNo'],
                'month' => $m['month'],
                'year' => $m['year'],
                'PIN' => $m['pin'],
                'Etimsinv' => $m['invoiceNo'],
                'TransDateTime' => $m['voucherDate']->toDateTimeString(),
                'SystemInvoiceNo' => "{$m['periodTag']}/{$m['workNo']}/" . str_pad($seqNumber, 4, '0', STR_PAD_LEFT),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($etimsRows, 200) as $chunk) {
            EtimsInvoice::upsert(
                $chunk,
                ['WorkNo', 'month', 'year'],
                ['PIN', 'Etimsinv', 'TransDateTime', 'SystemInvoiceNo', 'updated_at']
            );
        }

        $this->streamLine(['status' => 'progress', 'progress' => 75, 'message' => 'Updating payment status…', 'success' => count($etimsRows), 'errors' => count($exceptions)]);

        foreach (array_chunk($verified, 200) as $chunk) {
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
            $userId, 'OTHER', 'Etims_Checker', 'ALL', $importFilename, null,
            ['action' => 'Etimschecker', 'checked' => count($etimsRows), 'errors' => count($exceptions)]
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
    $sheet->fromArray(['Agent Code', 'PIN', 'KRA Invoice No', 'Period', 'Payroll Gross Amount', 'Reason'], null, 'A1');
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

    // mm/dd/yyyy HH:mm:ss +HH:MM — e.g. "08/25/2026 02:45:33 +03:00"
    try {
        $parsed = Carbon::createFromFormat('m/d/Y H:i:sP', $value);
        if ($parsed !== false) {
            return $parsed;
        }
    } catch (\Throwable $e) {
        // fall through
    }

    // dd/mm/yyyy HH:mm:ss — original worksheet format, no offset
    try {
        $parsed = Carbon::createFromFormat('d/m/Y H:i:s', $value);
        if ($parsed !== false) {
            return $parsed;
        }
    } catch (\Throwable $e) {
        // fall through
    }

    // Fallback for date-only rows, no time
    try {
        $parsed = Carbon::createFromFormat('d/m/Y', $value);
        if ($parsed !== false) {
            return $parsed;
        }
    } catch (\Throwable $e) {
        // fall through
    }

    return null;
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

public function downloadTemplate()
{
    $headers = [
        'Agent Code',
        'Date',
        'Period', 
        'PIN',
        'KRA Invoice No',
        'Payment Voucher Date',
        'Gross Amount'
    ];

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Alternative approach: Set headers using fromArray() method
    $sheet->fromArray($headers, null, 'A1');

    // Add sample data
    $sampleData = [
        ['63842', '01/06/2026', 'June 2026', 'A003925120R', 'KRACU0300004667/14', '08/07/2026', '50000.00'],
        ['638423', '01/07/2026', 'July 2026', 'A003925120R', 'KRACU0300004667/18', '08/07/2026', '60000.00']
    ];
    
    // Add sample data starting from row 2
    $sheet->fromArray($sampleData, null, 'A2');

    // Auto-size columns for all columns (A to H since you have 8 columns)
    foreach (range('A', 'G') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Create writer
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Generate filename and save to temp file
    $fileName = 'Invoice_checker' . date('Y-m-d') . '.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), 'excel') . '.xlsx';
    
    $writer->save($tempFile);

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}
}