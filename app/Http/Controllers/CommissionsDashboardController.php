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

    // NEW — cache key derived from the actual filter combination + allowed portfolios,
    // so different users with different access scopes never share a cached result.
    $cacheKey = 'commissions_dashboard:' . md5(json_encode($filters));

    try {
        $payload = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function () use ($service, $filters) {
            return [
                'net_pay_trend' => $service->getNetPayTrend($filters),
                'not_paid_trend' => $service->getNotPaidTrend($filters),
                'aging' => $service->getAgingBuckets($filters),
                'listing' => $service->getInvoicedListing($filters),
            ];
        });

        return response()->json(array_merge(['status' => 'success'], $payload));
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