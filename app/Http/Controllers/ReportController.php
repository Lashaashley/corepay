<?php

namespace App\Http\Controllers;

use App\Services\StaffReportService;
use App\Services\PayrollVarianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\PayrollItemsService;
use App\Services\PayrollSummaryService;
use App\Services\OverallSummaryService;
use App\Services\BankAdviceService;
use App\Services\VarianceReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $staffReportService;
    protected $summaryService;
    protected $payrollItemsService;
    protected $payrollSummaryService;
    protected $bankAdviceService;
    protected $varianceReportService;
    protected $payrollVarianceService;

    public function __construct(
        StaffReportService $staffReportService,
        OverallSummaryService $summaryService,
        PayrollItemsService $payrollItemsService,
        PayrollSummaryService $payrollSummaryService,
        BankAdviceService $bankAdviceService,
        VarianceReportService $varianceReportService,
        PayrollVarianceService $payrollVarianceService
    ) {
        $this->staffReportService = $staffReportService;
        $this->summaryService = $summaryService;
        $this->payrollItemsService = $payrollItemsService;
        $this->payrollSummaryService = $payrollSummaryService;
        $this->bankAdviceService = $bankAdviceService;
        $this->varianceReportService = $varianceReportService;
        $this->payrollVarianceService = $payrollVarianceService;
    }
    

    public function fullStaffReport(Request $request)
    {
        try {
            $userId = session('user_id') ?? Auth::id();
            Log::info('Full Staff Report requested', [
                'user_id' => $userId,
                'ip' => $request->ip()
            ]);

            $pdfData = $this->staffReportService->generateFullStaffReport();
            
            return response()->json([
                'success' => true,
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Full Staff Report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }


    public function overallSummary(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|string'
        ]);

        try {
            $period = $request->input('period');
            
            // Extract month and year from period (format: "August2024")
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            $pdfData = $this->summaryService->generateOverallSummary($month, $year);

            return response()->json([
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Overall summary generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }

    public function payrollItems(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|string',
            'pname' => 'required|string',
            'staff3' => 'nullable|string',
            'staff4' => 'nullable|string'
        ]);

        try {
            $period = $request->input('period');
            $pname = $request->input('pname');
            $staff3 = $request->input('staff3');
            $staff4 = $request->input('staff4');
            
            // Extract month and year from period
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            $pdfData = $this->payrollItemsService->generatePayrollItemsReport($month, $year, $pname, $staff3, $staff4);

            return response()->json([
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Payroll items report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
    public function payrollSummary(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|string',
            'staff3' => 'nullable|string',
            'staff4' => 'nullable|string'
        ]);

        try {
            $period = $request->input('period');
            $staff3 = $request->input('staff3');
            $staff4 = $request->input('staff4');
            
            // Extract month and year from period
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            $pdfData = $this->payrollSummaryService->generatePayrollSummary($month, $year, $staff3, $staff4);

            return response()->json([
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Payroll summary report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }

    public function bankAdvice(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|string',
            'recintres' => 'required|string'
        ]);

        try {
            $period = $request->input('period');
            $recintres = $request->input('recintres');
            
            // Extract month and year from period
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            $pdfData = $this->bankAdviceService->generateBankAdvice($month, $year, $recintres);

            return response()->json([
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Bank advice report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
    public function variance(Request $request): JsonResponse
    {
        $request->validate([
            'stperiod' => 'required|string',
            'ndperiod' => 'required|string',
            'pname' => 'required|string',
            'staff3' => 'nullable|string',
            'staff4' => 'nullable|string'
        ]);

        try {
            $stperiod = $request->input('stperiod');
            $ndperiod = $request->input('ndperiod');
            $pname = $request->input('pname');
            $staff3 = $request->input('staff3');
            $staff4 = $request->input('staff4');

            // Validate periods are different
            if ($stperiod === $ndperiod) {
                return response()->json([
                    'error' => 'The 1st and 2nd period cannot be the same'
                ], 422);
            }
            
            // Extract months and years from periods
            $stmonth = substr($stperiod, 0, -4);
            $styear = substr($stperiod, -4);
            $ndmonth = substr($ndperiod, 0, -4);
            $ndyear = substr($ndperiod, -4);

            $pdfData = $this->varianceReportService->generateVarianceReport(
                $stmonth, $styear, $ndmonth, $ndyear, $pname, $staff3, $staff4
            );

            return response()->json([
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Variance report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
    public function payrollVariance(Request $request): JsonResponse
    {
        $request->validate([
            'stperiod' => 'required|string',
            'ndperiod' => 'required|string'
        ]);

        try {
            $stperiod = $request->input('stperiod');
            $ndperiod = $request->input('ndperiod');

            // Validate periods are different
            if ($stperiod === $ndperiod) {
                return response()->json([
                    'error' => 'The 1st and 2nd period cannot be the same'
                ], 422);
            }
            
            // Extract months and years from periods
            $stmonth = substr($stperiod, 0, -4);
            $styear = substr($stperiod, -4);
            $ndmonth = substr($ndperiod, 0, -4);
            $ndyear = substr($ndperiod, -4);

            $pdfData = $this->payrollVarianceService->generatePayrollVarianceReport(
                $stmonth, $styear, $ndmonth, $ndyear
            );

            return response()->json([
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Payroll variance report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
}
