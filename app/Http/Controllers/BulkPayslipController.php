<?php

namespace App\Http\Controllers;

use App\Services\BulkPayslipService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * Generate bulk payslips
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|string',
            'save_path' => 'required|string'
        ]);

        try {
            $period = $request->input('period');
            $savePath = $request->input('save_path');
            
            // Extract month and year from period (format: "August2024")
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            // Validate save path
            if (!$this->isValidSavePath($savePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid save path or directory is not writable'
                ], 400);
            }

            // Set save path
            $this->bulkPayslipService->setSavePath($savePath);

            // Generate job ID for progress tracking
            $jobId = Str::uuid()->toString();

            // Start generation process (you can queue this for large datasets)
            $results = $this->bulkPayslipService->generateBulkPayslips($month, $year, $jobId);

            return response()->json([
                'status' => 'success',
                'job_id' => $jobId,
                'message' => 'Bulk payslip generation started',
                'data' => [
                    'total_employees' => $results['total'],
                    'save_path' => $savePath
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
     * Validate save path
     */
    private function isValidSavePath(string $path): bool
    {
        // Check if path is not empty
        if (empty($path)) {
            return false;
        }

        // Check if directory exists or can be created
        if (!is_dir($path)) {
            if (!@mkdir($path, 0755, true)) {
                return false;
            }
        }

        // Check if directory is writable
        return is_writable($path);
    }
}