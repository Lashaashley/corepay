<?php

namespace App\Http\Controllers;

use App\Services\DeductionsDashboardService;
use App\Services\CommissionsDashboardService; // reuse getPortfolios()
use App\Models\Pperiod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeductionsDashboardController extends Controller
{
    public function index(CommissionsDashboardService $commissionsService)
    {
        $allowedPayrollIds = session('allowedPayroll', []);
        $period = Pperiod::where('sstatus', 'Active')->first();

        return view('dashboard.deductions', [
            'portfolios' => $commissionsService->getPortfolios($allowedPayrollIds),
            'defaultMonth' => $period->mmonth ?? '',
            'defaultYear' => $period->yyear ?? date('Y'),
        ]);
    }

    public function data(Request $request, DeductionsDashboardService $service)
    { 
        $allowedPayrollIds = session('allowedPayroll', []);
        if (empty($allowedPayrollIds)) {
            return response()->json(['status' => 'error', 'message' => 'No payroll access granted'], 403);
        }

        $filters = [
            'allowedPayrollIds' => $allowedPayrollIds,
            'portfolio_id' => $request->input('portfolio_id'),
            'work_no' => $request->input('work_no'),
            'month' => $request->input('month'),
            'year' => $request->input('year', date('Y')),
        ];

        if (empty($filters['month']) || empty($filters['year'])) {
            return response()->json(['status' => 'error', 'message' => 'Month and year are required'], 422);
        }

        try {
            return response()->json([
                'status' => 'success',
                'by_type' => $service->getDeductionsByType($filters),
                'balances' => $service->getDeductionBalances($filters),
                'listing' => $service->getDeductionListing($filters),
            ]);
        } catch (\Throwable $e) {
            Log::error('Deductions dashboard data error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to load dashboard data'], 500);
        }
    }
}