<?php

namespace App\Services;

use App\Models\Payhouse;
use App\Models\Agents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BulkPayslipService
{
    protected $payslipService;
    protected $savePath;

    public function __construct(PayslipService $payslipService)
    {
        $this->payslipService = $payslipService;
    }

    /**
     * Set the save path for PDFs
     */
    public function setSavePath(string $path): void
    {
        $this->savePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        // Create directory if it doesn't exist
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0755, true);
        }
    }

    /**
     * Get all distinct WorkNos for a period
     */
    public function getEmployeesForPeriod(string $month, string $year): array
    {
        return Payhouse::where('month', $month)
            ->where('year', $year)
            ->distinct()
            ->pluck('WorkNo')
            ->toArray();
    }

    /**
     * Generate bulk payslips
     */
    public function generateBulkPayslips(string $month, string $year, string $jobId = null): array
{
    // Prevent timeout
    set_time_limit(0);

    $workNos = $this->getEmployeesForPeriod($month, $year);
    $total = count($workNos);

    // Initial progress so JS sees something immediately
    if ($jobId) {
        cache()->put("bulk_payslip_progress_{$jobId}", [
            'progress' => 0,
            'processed' => 0,
            'total' => $total,
            'success' => 0,
            'failed' => 0,
            'current_employee' => null,
            'message' => "Starting bulk payslip generation..."
        ], now()->addHours(1));
    }

    $results = [
        'total' => $total,
        'success' => 0,
        'failed' => 0,
        'files' => [],
        'errors' => []
    ];

    Log::info("Starting bulk payslip generation for {$month} {$year}. Total: {$total}");

    $processed = 0;

    foreach ($workNos as $workNo) {

        try {
            $filePath = $this->generateSinglePayslip($workNo, $month, $year);
            $results['success']++;
            $results['files'][] = [
                'workno' => $workNo,
                'filename' => basename($filePath),
                'path' => $filePath
            ];

        } catch (\Exception $e) {
            $results['failed']++;
            $results['errors'][] = [
                'workno' => $workNo,
                'error' => $e->getMessage()
            ];
            Log::error("Payslip failed for {$workNo}: {$e->getMessage()}");
        }

        $processed++;

        // Live progress update
        if ($jobId) {
            $percent = round(($processed / $total) * 100);

            cache()->put("bulk_payslip_progress_{$jobId}", [
                'progress' => $percent,
                'processed' => $processed,
                'total' => $total,
                'success' => $results['success'],
                'failed' => $results['failed'],
                'current_employee' => $workNo,
                'message' => "Processing {$processed} / {$total}"
            ], now()->addHours(1));
        }
    }

    Log::info("Generation done. Success: {$results['success']}, Failed: {$results['failed']}");

    return $results;
}


    /**
     * Generate single payslip and save to file
     */
    private function generateSinglePayslip(string $workNo, string $month, string $year): string
    {
        // Generate PDF data using existing service
        $pdfData = $this->payslipService->generatePayslip($workNo, $month, $year);

        // Get employee name for filename
        $employee = Agents::where('emp_id', $workNo)
            ->select(DB::raw("CONCAT(FirstName, ' ', LastName) AS fullname"))
            ->first();

        $employeeName = $employee ? $this->sanitizeFilename($employee->fullname) : $workNo;

        // Create filename
        $filename = "Payslip_{$employeeName}_{$month}_{$year}_{$workNo}.pdf";
        $filePath = $this->savePath . $filename;

        // Save PDF to file
        file_put_contents($filePath, $pdfData);

        return $filePath;
    }

    /**
     * Sanitize filename for safe saving
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove invalid characters
        $filename = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $filename);
        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $filename);
        // Limit length
        return substr($filename, 0, 100);
    }

    /**
     * Update progress for monitoring
     */
    private function updateProgress(string $jobId, int $processed, int $total, array $results): void
    {
        $progress = round(($processed / $total) * 100);
        
        cache()->put("bulk_payslip_progress_{$jobId}", [
            'progress' => $progress,
            'processed' => $processed,
            'total' => $total,
            'success' => $results['success'],
            'failed' => $results['failed'],
            'current_employee' => $results['files'][$processed - 1]['workno'] ?? null,
            'message' => "Processed {$processed} of {$total} employees"
        ], now()->addHours(1));
    }

    /**
     * Get available periods for dropdown
     */
    public function getAvailablePeriods(): array
    {
        return Payhouse::select('month', 'year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderByRaw("FIELD(month, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')")
            ->get()
            ->map(function($item) {
                return [
                    'value' => $item->month . $item->year,
                    'label' => $item->month . ' ' . $item->year
                ];
            })
            ->toArray();
    }
}