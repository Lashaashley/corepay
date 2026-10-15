<?php

namespace App\Jobs;

use App\Services\BulkPayslipService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GenerateBulkPayslipsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 7200;
    public $tries = 1;   // never auto-retry: a retry would re-email everyone

    public function __construct(
        public string $month,
        public string $year,
        public string $jobId,
        public bool $sendEmail,
        public ?int $userId
    ) {}

    public function handle(BulkPayslipService $service): void
    {
        $service->setSavePath(storage_path("app/temp_payslips/{$this->jobId}"));

        $results = $service->generateBulkPayslips(
            $this->month, $this->year, $this->jobId, $this->sendEmail, $this->userId
        );

        // Results first, THEN mark 100%, so the frontend never sees "done" before results exist
        cache()->put("bulk_payslip_results_{$this->jobId}", $results, now()->addHours(2));

        $progress = cache()->get("bulk_payslip_progress_{$this->jobId}", []);
        cache()->put("bulk_payslip_progress_{$this->jobId}",
            array_merge($progress, ['progress' => 100, 'message' => 'Complete']),
            now()->addHours(1));
    }

    public function failed(\Throwable $e): void
    {
        Log::error("Bulk payslip job {$this->jobId} failed: " . $e->getMessage());
        $progress = cache()->get("bulk_payslip_progress_{$this->jobId}", []);
        cache()->put("bulk_payslip_progress_{$this->jobId}",
            array_merge($progress, ['message' => 'Failed: ' . $e->getMessage(), 'failed_job' => true]),
            now()->addHours(1));
    }
}