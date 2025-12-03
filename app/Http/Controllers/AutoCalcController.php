<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\PayrollService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class AutoCalcController extends Controller
{
    protected $payroll;

    public function __construct(PayrollService $payroll)
    {
        $this->payroll = $payroll;
    }

    /**
     * Process payroll totals with real-time progress updates via SSE
     */
    public function processTotals(Request $request)
{
     set_time_limit(300); // or 0 for unlimited (use with caution)
    ini_set('max_execution_time', 300);
    // Validate request parameters
    $validated = $request->validate([
        'month' => 'required|string|max:20',
        'year' => 'required|string|max:4',
    ]);

    $month = $validated['month'];
    $year = $validated['year'];
     $userId = session('user_id') ?? Auth::id();
    
    // Get allowed payroll IDs from session
    $allowedPayrollIds = session('allowedPayroll', []);

    // ✅ LOG: Process started
    logAuditTrail(
        $userId,
        'OTHER',
        'payroll_processing',
        "{$month}_{$year}",
        null,
        null,
        [
            'action' => 'process_totals_started',
            'month' => $month,
            'year' => $year,
            'allowed_payrolls' => $allowedPayrollIds,
            'ip_address' => $request->ip()
        ]
    );

    return response()->stream(function () use ($month, $year, $allowedPayrollIds, $userId) {
        
        // Disable output buffering for real-time streaming
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Helper function to send SSE events
        $send = function ($message, $percent) {
            echo "event: progress\n";
            echo "data: " . json_encode([
                'message' => $message,
                'percent' => $percent
            ]) . "\n\n";

            if (ob_get_level()) {
                ob_flush();
            }
            flush();
            usleep(50000); // 50ms delay for UI updates
        };

        // Send initial progress
        $send('Initializing process...', 5);

        try {
            // Check payroll access
            if (empty($allowedPayrollIds)) {
                throw new \Exception('No payroll access granted');
            }

            // Clear previous data
            $this->payroll->clearVariables($month, $year);

            // Step 1: Calculate gross pays
            $send('Calculating gross pays...', 10);
            $grossPays = $this->payroll->calcGrossPay($month, $year, $allowedPayrollIds);

            if (isset($grossPays['status']) && $grossPays['status'] === 'error') {
                // ✅ LOG: Gross pay calculation failed
                logAuditTrail(
                    $userId,
                    'ERROR',
                    'payroll_processing',
                    "{$month}_{$year}",
                    null,
                    null,
                    [
                        'action' => 'process_totals_failed',
                        'step' => 'gross_pay_calculation',
                        'month' => $month,
                        'year' => $year,
                        'error' => $grossPays
                    ]
                );

                echo "event: error\n";
                echo "data: " . json_encode($grossPays) . "\n\n";
                flush();
                return;
            }

            // Step 2: Process benefits
            $send('Processing benefits...', 25);
            $totalGrossPays = $this->payroll->processBenefits($month, $year, $grossPays);

            // Step 3: Calculate medical covers
            $send('Calculating medical covers...', 35);
            $this->payroll->calculateMedCover($month, $year, $allowedPayrollIds);

            // Step 4: Calculate housing levies
            $send('Calculating housing levies...', 45);
            $this->payroll->calcAffodHousingLevy($month, $year, $allowedPayrollIds);

            // Step 5: Calculate taxable amounts
            $send('Calculating taxable amounts...', 55);
            $taxables = $this->payroll->calcGrelief($month, $year, $totalGrossPays);

            // Step 6: Calculate tax charges
            $send('Calculating tax charges...', 65);
            $taxCharged = $this->payroll->calculateTax($month, $year, $taxables);

            // Step 7: Calculate taxable reliefs and PAYE
            $send('Calculating taxable reliefs...', 75);
            $payeResults = $this->payroll->calcTaxableRelief($month, $year, $allowedPayrollIds);

            // Step 8: Calculate union dues
            $send('Calculating union dues...', 85);
            $this->payroll->processAllUnionDues($month, $year, $allowedPayrollIds);

            // Step 9: Calculate net pay
            $send('Calculating net pay...', 90);
            $this->payroll->calcNetPay($month, $year, $allowedPayrollIds);

            // ✅ Force database flush
            DB::connection()->getPdo()->exec('COMMIT');
            sleep(1); // Give MySQL time to fully write

            // ✅ Verify immediately
            $verifyCount = DB::table('payhouse')
                ->where('month', $month)
                ->where('year', $year)
                ->where('pcategory', 'Deduction')
                ->count();

            Log::info('Immediate verification in stream', [
                'deductions_found' => $verifyCount
            ]);

            // Finalization
            $send('Finalizing data...', 95);

            // Log successful completion
            Log::info('Payroll processing completed', [
                'month' => $month,
                'year' => $year,
                'payroll_ids' => $allowedPayrollIds
            ]);

            $resultData = [
                'status' => 'success',
                'message' => "Totals processed successfully for $month $year",
                'taxCharged' => count($taxCharged ?? []),
                'payeProcessed' => count($payeResults ?? []),
                'payrollTypes' => count($allowedPayrollIds),
                'deductionsVerified' => $verifyCount
            ];

            // ✅ LOG: Process completed successfully
            logAuditTrail(
                $userId,
                'OTHER',
                'payroll_processing',
                "{$month}_{$year}",
                null,
                null,
                [
                    'action' => 'process_totals_completed',
                    'month' => $month,
                    'year' => $year,
                    'allowed_payrolls' => $allowedPayrollIds,
                    'results' => $resultData
                ]
            );

            // Send completion event
            echo "event: complete\n";
            echo "data: " . json_encode($resultData) . "\n\n";

            flush();

        } catch (\Exception $e) {
            // Log error
            Log::error('Payroll processing error', [
                'month' => $month,
                'year' => $year,
                'payroll_ids' => $allowedPayrollIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorData = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];

            // ✅ LOG: Process failed with exception
            logAuditTrail(
                $userId,
                'ERROR',
                'payroll_processing',
                "{$month}_{$year}",
                null,
                null,
                [
                    'action' => 'process_totals_exception',
                    'month' => $month,
                    'year' => $year,
                    'allowed_payrolls' => $allowedPayrollIds,
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine()
                ]
            );

            // Send error event
            echo "event: error\n";
            echo "data: " . json_encode($errorData) . "\n\n";

            flush();
        }

    }, 200, $this->sseHeaders());
}

    /**
     * Get SSE headers
     */
    private function sseHeaders()
    {
        return [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ];
    }




    
}