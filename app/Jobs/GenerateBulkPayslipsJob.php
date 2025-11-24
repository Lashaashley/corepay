<?php

namespace App\Jobs;

use App\Services\BulkPayslipService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateBulkPayslipsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout
    public $tries = 1;

    protected $month;
    protected $year;
    protected $savePath;
    protected $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $month, string $year, string $savePath, string $jobId)
    {
        $this->month = $month;
        $this->year = $year;
        $this->savePath = $savePath;
        $this->jobId = $jobId;
    }

    /**
     * Execute the job.
     */
    public function handle(BulkPayslipService $bulkPayslipService)
    {
        try {
            $bulkPayslipService->setSavePath($this->savePath);
            
            $bulkPayslipService->generateBulkPayslips(
                $this->month, 
                $this->year, 
                $this->jobId
            );

        } catch (\Exception $e) {
            Log::error('Bulk payslip job failed', [
                'job_id' => $this->jobId,
                'error' => $e->getMessage()
            ]);

            // Update cache with error
            cache()->put("bulk_payslip_progress_{$this->jobId}", [
                'progress' => 0,
                'message' => 'Generation failed: ' . $e->getMessage(),
                'total' => 0,
                'success' => 0,
                'failed' => 0,
                'error' => true
            ], now()->addHours(2));

            throw $e;
        }
    }
}