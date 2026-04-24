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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NetPayExport;

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
        $allowedPayrollTypes = session('allowedPayroll', []);
        $userId = session('user_id') ?? Auth::id();

        Log::info('Full Staff Report requested', [
            'user_id' => $userId,
            'ip' => $request->ip()
        ]);

        $pdfData = $this->staffReportService->generateFullStaffReport();

        logAuditTrail(
            $userId,
            'OTHER',
            'full_agents_report',
            null,
            null,
            null,
            [
                'action' => 'full_agents_report_opened',
                'allowed_payrolls' => $allowedPayrollTypes
            ]
        );

        return response($pdfData, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Full_Staff_Report.pdf"');

    } catch (\Exception $e) {
        Log::error('Full Staff Report generation failed', [
            'error' => $e->getMessage()
        ]);

        abort(500, 'Failed to generate report');
    }
}

    public function overallSummary(Request $request)
    {
        $request->validate([
            'period' => 'required|string'
        ]);

        try {
            $period = $request->input('period');
            $allowedPayrollTypes = session('allowedPayroll', []);
            $userId = Auth::id();
            
            // Extract month and year from period (format: "August2024")
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            $pdfData = $this->summaryService->generateOverallSummary($month, $year);

            logAuditTrail(
                $userId,
                'OTHER',
                'overrall_payroll_summary',
                $period,
                null,
                null,
                [
                    'action' => 'overrall_payroll_summary_opened',
                    'period' => $period,
                    'allowed_payrolls' => $allowedPayrollTypes
                ]
            );


              return response($pdfData, 200)
    ->header('Content-Type', 'application/pdf') 
    ->header(
        'Content-Disposition',
        'inline; filename="`Company Summary_'.$period.'.pdf"'
    );


            

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
            $allowedPayrollTypes = session('allowedPayroll', []);
            $userId = Auth::id();
            
            // Extract month and year from period
            $month = substr($period, 0, -4);
            $year = substr($period, -4);
 
            $pdfData = $this->payrollItemsService->generatePayrollItemsReport($month, $year, $pname, $staff3, $staff4);

            logAuditTrail(
                $userId,
                'OTHER',
                'payroll_item_list',
                $period,
                null,
                null,
                [
                    'action' => 'payroll_item_list_report_opened',
                    'period' => $period,
                    'allowed_payrolls' => $allowedPayrollTypes,
                    'Item' => $pname
                ]
            );
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
    public function EarningsReport(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|string',
            'pname' => 'required|string',
            'staff3' => 'nullable|string',
            'staff4' => 'nullable|string'
        ]);

        try {
            $month = $request->input('month');
            $year = $request->input('year');
            $pcate = $request->input('pname');
            $staff3 = $request->input('staff3');
            $staff4 = $request->input('staff4');

             $allowedPayrollTypes = session('allowedPayroll', []);
             $userId = Auth::id();
            
 
            $pdfData = $this->payrollItemsService->generateEarningsReport($month, $year, $pcate, $staff3, $staff4);

            logAuditTrail(
                $userId,
                'OTHER',
                'earnings_report_review',
                $month . $year,
                null,
                null,
                [
                    'action' => 'earnings_report_review_opened',
                    'period' => $month . $year,
                    'allowed_payrolls' => $allowedPayrollTypes,
                    'Earning' => $pcate
                ]
            );

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
    public function NetpayReport(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|string',
            'pname' => 'required|string',
            'staff3' => 'nullable|string',
            'staff4' => 'nullable|string'
        ]);

        try {
            $month = $request->input('month');
            $year = $request->input('year');
            $pcate = $request->input('pname');
            $staff3 = $request->input('staff3');
            $staff4 = $request->input('staff4');
             $allowedPayrollTypes = session('allowedPayroll', []);
             $userId = Auth::id();
            
 
            $pdfData = $this->payrollItemsService->generateNetpay($month, $year, $pcate, $staff3, $staff4);

             logAuditTrail(
                $userId,
                'OTHER',
                'netpay_report_review',
                $month . $year,
                null,
                null,
                [
                    'action' => 'netpay_report_review_opened',
                    'period' => $month . $year,
                    'allowed_payrolls' => $allowedPayrollTypes
                ]
            );

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
    public function NetpayReportExcel(Request $request)
{
    $request->validate([
        'month' => 'required|string',
        'year' => 'required|string',
        'pname' => 'required|string',
        'staff3' => 'nullable|string',
        'staff4' => 'nullable|string'
    ]);

    $month = $request->month;
    $year = $request->year;
    $pname = $request->pname;
    $staff3 = $request->staff3;
    $staff4 = $request->staff4;

    $allowedPayrollTypes = session('allowedPayroll', []);
        $userId = Auth::id();

    $fileName = "NETPAY_{$month}_{$year}.xlsx";

    return Excel::download(new NetPayExport($month, $year, $pname, $staff3, $staff4), $fileName);

    logAuditTrail(
                $userId,
                'OTHER',
                'netpay_report_excel',
                $period,
                null,
                null,
                [
                    'action' => 'netpay_report_excel_generated',
                    'period' => $month . $year,
                    'allowed_payrolls' => $allowedPayrollTypes,
                    'file_name' => $fileName
                ]
            );
}

public function EarningsReportExcel(Request $request)
{
    $request->validate([
        'month' => 'required|string',
        'year' => 'required|string',
        'pname' => 'required|string',
        'staff3' => 'nullable|string',
        'staff4' => 'nullable|string'
    ]);

    $month = $request->month;
    $year = $request->year;
    $pname = $request->pname;
    $staff3 = $request->staff3;
    $staff4 = $request->staff4;
    $allowedPayrollTypes = session('allowedPayroll', []);
        $userId = Auth::id();

    $fileName = "{$pname}_{$month}_{$year}.xlsx";

    return Excel::download(new NetPayExport($month, $year, $pname, $staff3, $staff4), $fileName);

    logAuditTrail(
                $userId,
                'OTHER',
                'earnings_report_excel',
                $period,
                null,
                null,
                [
                    'action' => 'earnings_report_excel_generated',
                    'period' => $month . $year,
                    'allowed_payrolls' => $allowedPayrollTypes,
                    'file_name' => $fileName
                ]
            );
}

    public function payrollSummary(Request $request)
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

            $allowedPayrollTypes = session('allowedPayroll', []);
            $userId = Auth::id();

            $pdfData = $this->payrollSummaryService->generatePayrollSummary($month, $year, $staff3, $staff4);

             logAuditTrail(
                $userId,
                'OTHER',
                'payrollsummary_report_generation',
                $period,
                null,
                null,
                [
                    'action' => 'payrollsummary_report_generation',
                    'period' => $period,
                    'allowed_payrolls' => $allowedPayrollTypes
                ]
            );

             return response($pdfData, 200)
             ->header('Content-Type', 'application/pdf') 
              ->header(
                'Content-Disposition',
                'inline; filename="Payroll_Summary_'.$period.'.pdf"'
                );


           

        } catch (\Exception $e) {
            Log::error('Payroll summary report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
    public function generatePayrollSummaryExcel(Request $request)
{
    try {
        $validated = $request->validate([
            'period' => 'required|string',
            'staff3' => 'nullable|string',
            'staff4' => 'nullable|string'
        ]);
         $period = $request->input('period');
            $staff3 = $request->input('staff3');
            $staff4 = $request->input('staff4');
            
            // Extract month and year from period
            $month = substr($period, 0, -4);
            $year = substr($period, -4);
            $allowedPayrollTypes = session('allowedPayroll', []);
            $userId = Auth::id();

        $service = new PayrollSummaryService();
        $excelContent = $service->generatePayrollSummaryExcel(
          $month, $year, $staff3, $staff4
        );

        $filename = 'payroll_summary_' . $month . '_' . $year . '.xlsx';


        logAuditTrail(
                $userId,
                'OTHER',
                'payroll_summary_excel',
                $period,
                null,
                null,
                [
                    'action' => 'payroll_summary_excel_opened',
                    'period' => $period,
                    'allowed_payrolls' => $allowedPayrollTypes,
                    'file_name' => $filename
                ]
            );


        return response($excelContent)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');

            
    } catch (\Exception $e) {
        Log::error('Failed to generate Excel report: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to generate Excel: ' . $e->getMessage()
        ], 500);
    }
}

    public function bankAdvice(Request $request)
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


             return response($pdfData, 200)
    ->header('Content-Type', 'application/pdf') 
    ->header(
        'Content-Disposition',
        'inline; filename="'.$recintres.'_Bank_Advice_Report_'.$period.'.pdf"'
    );

        } catch (\Exception $e) {
            Log::error('Bank advice report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
    public function variance(Request $request)
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

            $allowedPayrollTypes = session('allowedPayroll', []);
            $userId = Auth::id();

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

             logAuditTrail(
                $userId,
                'OTHER',
                'item_variance_report',
                $stperiod . $ndperiod,
                null,
                null,
                [
                    'action' => 'item_variance_report_opened',
                    'period_difference' => $stperiod . $ndperiod,
                    'allowed_payrolls' => $allowedPayrollTypes,
                    'item' => $pname
                ]
            );

            

             return response($pdfData, 200)
    ->header('Content-Type', 'application/pdf') 
    ->header(
        'Content-Disposition',
        'inline; filename="Variance_Report'.$pname.'_'.$stperiod.'_'.$ndperiod.'.pdf"'
    );

           

        } catch (\Exception $e) {
            Log::error('Variance report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
    public function payrollVariance(Request $request)
    {
        $request->validate([
            'stperiod' => 'required|string',
            'ndperiod' => 'required|string'
        ]);

        try {
            $stperiod = $request->input('stperiod');
            $ndperiod = $request->input('ndperiod');
             $allowedPayrollTypes = session('allowedPayroll', []);
             $userId = Auth::id();

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

            logAuditTrail(
                $userId,
                'OTHER',
                'overall_variance_report',
                $stperiod . $ndperiod,
                null,
                null,
                [
                    'action' => 'overall_variance_report_opened',
                    'period_difference' => $stperiod . $ndperiod,
                    'allowed_payrolls' => $allowedPayrollTypes
                ]
            );

             return response($pdfData, 200)
    ->header('Content-Type', 'application/pdf') 
    ->header(
        'Content-Disposition',
        'inline; filename="Payroll_Variance_Report_'.$stperiod.'_'.$ndperiod.'.pdf"'
    );

            

        } catch (\Exception $e) {
            Log::error('Payroll variance report generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
}
