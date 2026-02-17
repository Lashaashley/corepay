<?php

namespace App\Http\Controllers;

use App\Models\Payhouse;
use App\Models\Ptype;
use App\Models\Pperiod;
use App\Models\Agents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Get available periods
        $periods = Payhouse::distinctPeriods()->get();
        
        // Get active period
        $activePeriod = Pperiod::where('sstatus', 'Active')->first();
        
        // Get payroll types
        $payrollTypes = session('allowedPayroll', []);
        
        return view('students.analytics', compact('periods', 'activePeriod', 'payrollTypes'));
    }

    /**
     * Get dashboard data for selected period
     */
    public function getDashboardData(Request $request)
    {
        try {
            $month = $request->input('month');
            $year = $request->input('year');
            $payrollTypes = $request->input('payroll_types', session('allowedPayroll', []));
            
            if (!$month || !$year) {
                return response()->json([
                    'success' => false,
                    'message' => 'Month and year are required'
                ], 400);
            }

            // Get employees based on payroll types
            $employees = $this->getEmployeesByPayrollType($month, $year, $payrollTypes);

            // Calculate summary statistics
            $summary = $this->calculateSummary($month, $year, $employees);
            
            // Get payment breakdown
            $paymentBreakdown = $this->getPaymentBreakdown($month, $year, $employees);
            
            // Get deduction breakdown
            $deductionBreakdown = $this->getDeductionBreakdown($month, $year, $employees);
            
            // Get top earners
            $topEarners = $this->getTopEarners($month, $year, $employees, 10);
            
            // Get department breakdown
            $departmentBreakdown = $this->getDepartmentBreakdown($month, $year, $employees);
            
            // Get payment vs deduction trend
            $monthlyTrend = $this->getMonthlyTrend($year, $payrollTypes);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'paymentBreakdown' => $paymentBreakdown,
                    'deductionBreakdown' => $deductionBreakdown,
                    'topEarners' => $topEarners,
                    'departmentBreakdown' => $departmentBreakdown,
                    'monthlyTrend' => $monthlyTrend
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard data fetch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employees by payroll type
     */
    private function getEmployeesByPayrollType($month, $year, $payrollTypes)
    {
        if (empty($payrollTypes)) {
            // If no specific types, get all employees for the period
            return Payhouse::where('month', $month)
                ->where('year', $year)
                ->distinct()
                ->pluck('WorkNo')
                ->toArray();
        }

        return DB::table('payhouse as p')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->whereIn('r.payrolty', $payrollTypes)
            ->distinct()
            ->pluck('p.WorkNo')
            ->toArray();
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($month, $year, $employees)
    {
        $totalGrossPay = Payhouse::where('month', $month)
            ->where('year', $year)
            ->where('pname', 'Total Gross Salary')
            ->whereIn('WorkNo', $employees)
            ->sum('tamount');

        $totalDeductions = Payhouse::where('month', $month)
            ->where('year', $year)
            ->where('pcategory', 'Deduction')
            ->whereIn('WorkNo', $employees)
            ->sum('tamount');

        $totalNetPay = Payhouse::where('month', $month)
            ->where('year', $year)
            ->where('pname', 'NET PAY')
            ->whereIn('WorkNo', $employees)
            ->sum('tamount');

        $totalPayments = Payhouse::join('ptypes', 'payhouse.itemcode', '=', 'ptypes.code')
            ->where('payhouse.month', $month)
            ->where('payhouse.year', $year)
            ->where('ptypes.prossty', 'Payment')
            ->whereIn('payhouse.WorkNo', $employees)
            ->sum('payhouse.tamount');

        $employeeCount = count($employees);

        $avgNetPay = $employeeCount > 0 ? $totalNetPay / $employeeCount : 0;

        return [
            'total_gross_pay' => round($totalGrossPay, 2),
            'total_deductions' => round($totalDeductions, 2),
            'total_net_pay' => round($totalNetPay, 2),
            'total_payments' => round($totalPayments, 2),
            'employee_count' => $employeeCount,
            'average_net_pay' => round($avgNetPay, 2),
            'deduction_rate' => $totalGrossPay > 0 ? round(($totalDeductions / $totalGrossPay) * 100, 2) : 0
        ];
    }

    /**
     * Get payment breakdown by category
     */
    private function getPaymentBreakdown($month, $year, $employees)
    {
        $payments = DB::table('payhouse')
            ->join('ptypes', 'payhouse.itemcode', '=', 'ptypes.code')
            ->select(
                'payhouse.pname',
                DB::raw('SUM(payhouse.tamount) as total'),
                DB::raw('COUNT(DISTINCT payhouse.WorkNo) as employee_count')
            )
            ->where('payhouse.month', $month)
            ->where('payhouse.year', $year)
            ->where('ptypes.prossty', 'Payment')
            ->whereIn('payhouse.WorkNo', $employees)
            ->whereNotIn('payhouse.pname', ['Total Gross Salary', 'NET PAY'])
            ->groupBy('payhouse.pname')
            ->orderByDesc('total')
            ->get();

        return $payments->map(function($item) {
            return [
                'name' => $item->pname,
                'value' => round($item->total, 2),
                'employee_count' => $item->employee_count
            ];
        });
    }

    /**
     * Get deduction breakdown by category
     */
    private function getDeductionBreakdown($month, $year, $employees)
    {
        $deductions = DB::table('payhouse')
            ->join('ptypes', 'payhouse.itemcode', '=', 'ptypes.code')
            ->select(
                'payhouse.pname',
                'ptypes.category',
                DB::raw('SUM(payhouse.tamount) as total'),
                DB::raw('COUNT(DISTINCT payhouse.WorkNo) as employee_count')
            )
            ->where('payhouse.month', $month)
            ->where('payhouse.year', $year)
            ->where('ptypes.prossty', 'Deduction')
            ->whereIn('payhouse.WorkNo', $employees)
            ->groupBy('payhouse.pname', 'ptypes.category')
            ->orderByDesc('total')
            ->get();

        return $deductions->map(function($item) {
            return [
                'name' => $item->pname,
                'category' => $item->category,
                'value' => round($item->total, 2),
                'employee_count' => $item->employee_count
            ];
        });
    }

    /**
     * Get top earners
     */
    private function getTopEarners($month, $year, $employees, $limit = 10)
    {
        $topEarners = DB::table('payhouse as p')
            ->join('tblemployees as a', 'p.WorkNo', '=', 'a.emp_id')
            ->select(
                'p.WorkNo',
                DB::raw("CONCAT(a.FirstName, ' ', a.LastName) as name"),
                'a.Department',
                DB::raw('SUM(CASE WHEN p.pname = "NET PAY" THEN p.tamount ELSE 0 END) as net_pay'),
                DB::raw('SUM(CASE WHEN p.pname = "Total Gross Salary" THEN p.tamount ELSE 0 END) as gross_pay')
            )
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->whereIn('p.WorkNo', $employees)
            ->groupBy('p.WorkNo', 'a.FirstName', 'a.LastName', 'a.Department')
            ->orderByDesc('net_pay')
            ->limit($limit)
            ->get();

        return $topEarners->map(function($item) {
            return [
                'work_no' => $item->WorkNo,
                'name' => $item->name,
                'department' => $item->Department,
                'net_pay' => round($item->net_pay, 2),
                'gross_pay' => round($item->gross_pay, 2)
            ];
        });
    }

    /**
     * Get department breakdown
     */
    private function getDepartmentBreakdown($month, $year, $employees)
    {
        $departments = DB::table('payhouse as p')
            ->join('tblemployees as a', 'p.WorkNo', '=', 'a.emp_id')
            ->select(
                'a.Department',
                DB::raw('COUNT(DISTINCT p.WorkNo) as employee_count'),
                DB::raw('SUM(CASE WHEN p.pname = "NET PAY" THEN p.tamount ELSE 0 END) as total_net_pay'),
                DB::raw('SUM(CASE WHEN p.pname = "Total Gross Salary" THEN p.tamount ELSE 0 END) as total_gross_pay')
            )
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->whereIn('p.WorkNo', $employees)
            ->groupBy('a.Department')
            ->orderByDesc('total_net_pay')
            ->get();

        return $departments->map(function($item) {
            $avgNetPay = $item->employee_count > 0 ? $item->total_net_pay / $item->employee_count : 0;
            
            return [
                'department' => $item->Department ?? 'Unknown',
                'employee_count' => $item->employee_count,
                'total_net_pay' => round($item->total_net_pay, 2),
                'total_gross_pay' => round($item->total_gross_pay, 2),
                'average_net_pay' => round($avgNetPay, 2)
            ];
        });
    }

    /**
     * Get monthly trend for the year
     */
    private function getMonthlyTrend($year, $payrollTypes)
    {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 
                   'July', 'August', 'September', 'October', 'November', 'December'];

        $trends = [];

        foreach ($months as $month) {
            $employees = $this->getEmployeesByPayrollType($month, $year, $payrollTypes);

            if (empty($employees)) {
                $trends[] = [
                    'month' => $month,
                    'payments' => 0,
                    'deductions' => 0,
                    'net_pay' => 0
                ];
                continue;
            }

            $payments = DB::table('payhouse')
                ->join('ptypes', 'payhouse.itemcode', '=', 'ptypes.code')
                ->where('payhouse.month', $month)
                ->where('payhouse.year', $year)
                ->where('ptypes.prossty', 'Payment')
                ->whereIn('payhouse.WorkNo', $employees)
                ->whereNotIn('payhouse.pname', ['Total Gross Salary', 'NET PAY'])
                ->sum('payhouse.tamount');

            $deductions = DB::table('payhouse')
                ->join('ptypes', 'payhouse.itemcode', '=', 'ptypes.code')
                ->where('payhouse.month', $month)
                ->where('payhouse.year', $year)
                ->where('ptypes.prossty', 'Deduction')
                ->whereIn('payhouse.WorkNo', $employees)
                ->sum('payhouse.tamount');

            $netPay = Payhouse::where('month', $month)
                ->where('year', $year)
                ->where('pname', 'NET PAY')
                ->whereIn('WorkNo', $employees)
                ->sum('tamount');

            $trends[] = [
                'month' => $month,
                'payments' => round($payments, 2),
                'deductions' => round($deductions, 2),
                'net_pay' => round($netPay, 2)
            ];
        }

        return $trends;
    }

    /**
     * Compare two periods
     */
    public function comparePeriods(Request $request)
    {
        try {
            $period1 = $request->input('period1'); // {month: '', year: ''}
            $period2 = $request->input('period2');
            $payrollTypes = $request->input('payroll_types', session('allowedPayroll', []));

            $employees1 = $this->getEmployeesByPayrollType($period1['month'], $period1['year'], $payrollTypes);
            $employees2 = $this->getEmployeesByPayrollType($period2['month'], $period2['year'], $payrollTypes);

            $summary1 = $this->calculateSummary($period1['month'], $period1['year'], $employees1);
            $summary2 = $this->calculateSummary($period2['month'], $period2['year'], $employees2);

            $comparison = [
                'period1' => [
                    'label' => $period1['month'] . ' ' . $period1['year'],
                    'data' => $summary1
                ],
                'period2' => [
                    'label' => $period2['month'] . ' ' . $period2['year'],
                    'data' => $summary2
                ],
                'variance' => [
                    'gross_pay' => round($summary2['total_gross_pay'] - $summary1['total_gross_pay'], 2),
                    'deductions' => round($summary2['total_deductions'] - $summary1['total_deductions'], 2),
                    'net_pay' => round($summary2['total_net_pay'] - $summary1['total_net_pay'], 2),
                    'employee_count' => $summary2['employee_count'] - $summary1['employee_count']
                ],
                'percentage_change' => [
                    'gross_pay' => $summary1['total_gross_pay'] > 0 
                        ? round((($summary2['total_gross_pay'] - $summary1['total_gross_pay']) / $summary1['total_gross_pay']) * 100, 2)
                        : 0,
                    'deductions' => $summary1['total_deductions'] > 0
                        ? round((($summary2['total_deductions'] - $summary1['total_deductions']) / $summary1['total_deductions']) * 100, 2)
                        : 0,
                    'net_pay' => $summary1['total_net_pay'] > 0
                        ? round((($summary2['total_net_pay'] - $summary1['total_net_pay']) / $summary1['total_net_pay']) * 100, 2)
                        : 0
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $comparison
            ]);

        } catch (\Exception $e) {
            Log::error('Period comparison failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to compare periods: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get date range analysis
     */
    public function getDateRangeAnalysis(Request $request)
    {
        try {
            $startMonth = $request->input('start_month');
            $startYear = $request->input('start_year');
            $endMonth = $request->input('end_month');
            $endYear = $request->input('end_year');
            $payrollTypes = $request->input('payroll_types', session('allowedPayroll', []));

            // Get all periods between start and end
            $periods = $this->getPeriodsBetween($startMonth, $startYear, $endMonth, $endYear);

            $analysis = [];
            $totals = [
                'total_payments' => 0,
                'total_deductions' => 0,
                'total_net_pay' => 0,
                'total_employees' => 0
            ];

            foreach ($periods as $period) {
                $employees = $this->getEmployeesByPayrollType($period['month'], $period['year'], $payrollTypes);
                $summary = $this->calculateSummary($period['month'], $period['year'], $employees);

                $analysis[] = [
                    'period' => $period['month'] . ' ' . $period['year'],
                    'data' => $summary
                ];

                $totals['total_payments'] += $summary['total_payments'];
                $totals['total_deductions'] += $summary['total_deductions'];
                $totals['total_net_pay'] += $summary['total_net_pay'];
            }

            $totals['average_employees'] = count($analysis) > 0 
                ? round(array_sum(array_column(array_column($analysis, 'data'), 'employee_count')) / count($analysis), 0)
                : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'periods' => $analysis,
                    'totals' => $totals,
                    'period_count' => count($periods)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Date range analysis failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze date range: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get periods between two dates
     */
    private function getPeriodsBetween($startMonth, $startYear, $endMonth, $endYear)
    {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 
                   'July', 'August', 'September', 'October', 'November', 'December'];

        $periods = [];
        $startIndex = array_search($startMonth, $months);
        $endIndex = array_search($endMonth, $months);

        for ($year = $startYear; $year <= $endYear; $year++) {
            $monthStart = ($year == $startYear) ? $startIndex : 0;
            $monthEnd = ($year == $endYear) ? $endIndex : 11;

            for ($i = $monthStart; $i <= $monthEnd; $i++) {
                $periods[] = [
                    'month' => $months[$i],
                    'year' => $year
                ];
            }
        }

        return $periods;
    }
}