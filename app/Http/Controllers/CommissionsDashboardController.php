<?php

namespace App\Http\Controllers;

use App\Services\CommissionsDashboardService;
use App\Models\Pperiod;
use App\Models\Agents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionsDashboardController extends Controller
{
    public function index(CommissionsDashboardService $service)
    {
        $allowedPayrollIds = session('allowedPayroll', []);
        $period = Pperiod::where('sstatus', 'Active')->first();

        return view('students.invdash', [
            'portfolios' => $service->getPortfolios($allowedPayrollIds),
            'defaultMonth' => $period->mmonth ?? '',
            'defaultYear' => $period->yyear ?? date('Y'),
        ]);
    }

    public function data(Request $request, CommissionsDashboardService $service)
    {
        $allowedPayrollIds = session('allowedPayroll', []);
        if (empty($allowedPayrollIds)) {
            return response()->json(['status' => 'error', 'message' => 'No payroll access granted'], 403);
        }

        $filters = [
            'allowedPayrollIds' => $allowedPayrollIds,
            'portfolio_id' => $request->input('portfolio_id'),
            'work_no' => $request->input('work_no'),
            'status' => $request->input('status'),
            'month' => $request->input('month'),
            'year' => $request->input('year', date('Y')),
        ];

        try {
            return response()->json([
                'status' => 'success',
                'net_pay_trend' => $service->getNetPayTrend($filters),
                'not_paid_trend' => $service->getNotPaidTrend($filters),
                'aging' => $service->getAgingBuckets($filters),
                'listing' => $service->getInvoicedListing($filters),
            ]);
        } catch (\Throwable $e) {
            Log::error('Commissions dashboard data error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to load dashboard data'], 500);
        }
    }

    public function getAgents()
{
    $allowedPayroll = session('allowedPayroll', []);
     $staff = Agents::select(
                'tblemployees.emp_id as WorkNo',
                DB::raw("CONCAT(tblemployees.FirstName, ' ', tblemployees.LastName) as fullname")
            )
            ->join('registration', 'tblemployees.emp_id', '=', 'registration.empid')
            ->whereIn('registration.payrolty', $allowedPayroll)
            ->where('tblemployees.Status', 'ACTIVE')
            ->orderBy('tblemployees.FirstName')
            ->get();
           

    return response()->json([
        'data' => $staff,
    ]);
    
}
}