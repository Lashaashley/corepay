<?php

namespace App\Http\Controllers;

use App\Services\BulkPayslipService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class BulkPayslipController extends Controller
{
    protected $bulkPayslipService;

    public function __construct(BulkPayslipService $bulkPayslipService)
    {
        $this->bulkPayslipService = $bulkPayslipService;
    }

    /**
     * Show bulk payslip generation form
     */
    public function index()
    {
        $periods = $this->bulkPayslipService->getAvailablePeriods();
        
        return view('payslips.bulk-generate', compact('periods'));
    }

    /**
     * Generate bulk payslips (stored temporarily on server)
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|string',
            'download_method' => 'required|in:zip,individual,nogeneration',
            'send_email' => 'nullable|boolean'
        ]);

        try {
            $period = $request->input('period');
            $downloadMethod = $request->input('download_method');
            $sendEmail = $request->input('send_email', false);
            
            // Extract month and year from period (format: "August2024")
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            // Generate job ID for progress tracking
            $jobId = Str::uuid()->toString();

            // Create temporary directory for this job
            $tempPath = storage_path("app/temp_payslips/{$jobId}");
            
            if (!is_dir($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            // Set save path to temporary directory
            $this->bulkPayslipService->setSavePath($tempPath);

            // Start generation process with optional email sending
            $results = $this->bulkPayslipService->generateBulkPayslips($month, $year, $jobId, $sendEmail);

            // Store results for later retrieval
            cache()->put("bulk_payslip_results_{$jobId}", $results, now()->addHours(2));

            return response()->json([
                'status' => 'success',
                'job_id' => $jobId,
                'message' => 'Bulk payslip generation started',
                'data' => [
                    'total_employees' => $results['total'],
                    'download_method' => $downloadMethod,
                    'send_email' => $sendEmail
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk payslip generation error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to start bulk payslip generation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get generation progress
     */
    public function progress(string $jobId): JsonResponse
    {
        $progress = cache()->get("bulk_payslip_progress_{$jobId}");

        if (!$progress) {
            return response()->json([
                'status' => 'error',
                'message' => 'Progress not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'progress' => $progress
        ]);
    }

    /**
     * Download all payslips as ZIP
     */
    public function downloadZip(string $jobId)
{
    try {
        $results = cache()->get("bulk_payslip_results_{$jobId}");
        
        if (!$results) {
            return response()->json(['status' => 'error', 'message' => 'Job not found or expired'], 404);
        }

        $tempPath = storage_path("app/temp_payslips/{$jobId}");
        
        if (!is_dir($tempPath)) {
            return response()->json(['status' => 'error', 'message' => 'Payslip files not found'], 404);
        }

        $zipPath = storage_path("app/temp_payslips/{$jobId}.zip");
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Failed to create ZIP file');
        }

        $files = scandir($tempPath);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                $zip->addFile($tempPath . '/' . $file, $file);
            }
        }

        $zip->close();

        // Clean up PDF folder now that ZIP is ready
        $this->deleteDirectory($tempPath);
        
        // Clean up cache
        cache()->forget("bulk_payslip_results_{$jobId}");
        cache()->forget("bulk_payslip_progress_{$jobId}");

        // ZIP auto-deletes after send
        return response()->download($zipPath, 'Payslips_' . date('Y-m-d') . '.zip')
            ->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        Log::error('ZIP download error: ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'Failed to create ZIP file: ' . $e->getMessage()], 500);
    }
}

    /**
     * Get list of generated files
     */
    public function listFiles(string $jobId): JsonResponse
    {
        try {
            $results = cache()->get("bulk_payslip_results_{$jobId}");
            
            if (!$results) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Job not found or expired'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'files' => $results['files']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download single payslip
     */
    public function downloadSingle(string $jobId, string $workNo)
    {
        try {
            $tempPath = storage_path("app/temp_payslips/{$jobId}");
            
            // Find the file for this employee
            $files = scandir($tempPath);
            $targetFile = null;
            
            foreach ($files as $file) {
                if (strpos($file, $workNo) !== false && pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                    $targetFile = $file;
                    break;
                }
            }

            if (!$targetFile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found'
                ], 404);
            }

            $filePath = $tempPath . '/' . $targetFile;

            return response()->download($filePath, $targetFile);

        } catch (\Exception $e) {
            Log::error('Single file download error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to download file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup old temporary files (run this via cron)
     */
    public function cleanup(): JsonResponse
    {
        try {
            $tempBasePath = storage_path('app/temp_payslips');
            
            if (!is_dir($tempBasePath)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No temp files to clean'
                ]);
            }

            $dirs = scandir($tempBasePath);
            $cleaned = 0;

            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..') continue;

                $dirPath = $tempBasePath . '/' . $dir;
                
                // Remove directories older than 2 hours
                if (is_dir($dirPath) && (time() - filemtime($dirPath)) > 7200) {
                    $this->deleteDirectory($dirPath);
                    $cleaned++;
                }
                
                // Remove ZIP files older than 2 hours
                if (is_file($dirPath) && (time() - filemtime($dirPath)) > 7200) {
                    unlink($dirPath);
                    $cleaned++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Cleaned {$cleaned} old files/directories"
            ]);

        } catch (\Exception $e) {
            Log::error('Cleanup error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recursively delete directory
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        return rmdir($dir);
    }
}