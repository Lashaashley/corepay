<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $sessionId = Auth::id();
        
        // Get current employee data
        $employee = DB::table('tblemployees')
            ->where('emp_id', $sessionId)
            ->first();

        // Get gender statistics
        $genderStats = DB::table('tblemployees')
            ->select('Gender', DB::raw('COUNT(emp_id) as genderCount'))
            ->where('Status', 'ACTIVE')
            ->where('emp_id', '!=', '1')
            ->groupBy('Gender')
            ->get();

        $totalEmployees = DB::table('tblemployees')
            ->where('Status', 'ACTIVE')
            ->where('emp_id', '!=', '1')
            ->count();

        // Get branch statistics
        $branchStats = DB::table('branches as b')
            ->leftJoin('tblemployees as e', 'b.ID', '=', 'e.brid')
            ->select('b.branchname', DB::raw('COUNT(e.emp_id) as staffCount'))
            ->groupBy('b.ID', 'b.branchname')
            ->get();

        $totalStaff = DB::table('tblemployees')->count();

        // Get payment earnings data
        $paymentsData = $this->getPaymentsData();

        // Get turnover data
        $turnoverData = $this->getTurnoverData();

        // Get netpay data
        $netpayData = $this->getNetpayData();

        return view('dashboard', compact(
            'employee',
            'genderStats',
            'totalEmployees',
            'branchStats',
            'totalStaff',
            'paymentsData',
            'turnoverData',
            'netpayData'
        ));
    }

    private function getPaymentsData()
    {
        $results = DB::table('payhouse')
            ->select(
                DB::raw('SUM(tamount) as tamount'),
                'pname',
                'month',
                'year',
                DB::raw("CONCAT(month, '.', year) as period")
            )
            ->where('pcategory', 'Payment')
            ->where('pname', '!=', 'Total Gross Salary')
            ->groupBy('pname', 'month', 'year')
            ->orderByRaw("year, CASE month 
                WHEN 'January' THEN 1 
                WHEN 'February' THEN 2 
                WHEN 'March' THEN 3 
                WHEN 'April' THEN 4 
                WHEN 'May' THEN 5 
                WHEN 'June' THEN 6 
                WHEN 'July' THEN 7 
                WHEN 'August' THEN 8 
                WHEN 'September' THEN 9 
                WHEN 'October' THEN 10 
                WHEN 'November' THEN 11 
                WHEN 'December' THEN 12 
            END")
            ->get();

        $periods = [];
        $seriesData = [];
        $pcodes = [];

        foreach ($results as $row) {
            if (!in_array($row->period, $periods)) {
                $periods[] = $row->period;
            }
            if (!in_array($row->pname, $pcodes)) {
                $pcodes[] = $row->pname;
            }
            $seriesData[$row->pname][$row->period] = (float)$row->tamount;
        }

        $series = [];
        foreach ($pcodes as $pcode) {
            $data = [];
            foreach ($periods as $period) {
                $data[] = $seriesData[$pcode][$period] ?? 0;
            }
            $series[] = [
                'name' => $pcode,
                'data' => $data
            ];
        }

        return [
            'periods' => $periods,
            'series' => $series
        ];
    }

    private function getTurnoverData()
    {
        $results = DB::table('turnover')
            ->select(
                DB::raw("DATE_FORMAT(Date, '%Y-%m') as period"),
                'Action',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('period', 'Action')
            ->orderBy('period', 'ASC')
            ->get();

        $turnoverData = [
            'JOIN' => [],
            'LEFT' => []
        ];

        foreach ($results as $row) {
            $turnoverData[$row->Action][] = [
                'period' => $row->period,
                'count' => (int)$row->count
            ];
        }

        return $turnoverData;
    }

    private function getNetpayData()
    {
        $results = DB::table('payhouse')
            ->select(
                DB::raw('SUM(tamount) as tamount'),
                'pname',
                'month',
                'year',
                DB::raw("CONCAT(month, '.', year) as period")
            )
            ->where('pcategory', 'NET')
            ->groupBy('pname', 'month', 'year')
            ->orderByRaw("year, CASE month 
                WHEN 'January' THEN 1 
                WHEN 'February' THEN 2 
                WHEN 'March' THEN 3 
                WHEN 'April' THEN 4 
                WHEN 'May' THEN 5 
                WHEN 'June' THEN 6 
                WHEN 'July' THEN 7 
                WHEN 'August' THEN 8 
                WHEN 'September' THEN 9 
                WHEN 'October' THEN 10 
                WHEN 'November' THEN 11 
                WHEN 'December' THEN 12 
            END")
            ->get();

        $periods = [];
        $seriesData = [];
        $pcodes = [];

        foreach ($results as $row) {
            if (!in_array($row->period, $periods)) {
                $periods[] = $row->period;
            }
            if (!in_array($row->pname, $pcodes)) {
                $pcodes[] = $row->pname;
            }
            $seriesData[$row->pname][$row->period] = (float)$row->tamount;
        }

        $series = [];
        foreach ($pcodes as $pcode) {
            $data = [];
            foreach ($periods as $period) {
                $data[] = $seriesData[$pcode][$period] ?? 0;
            }
            $series[] = [
                'name' => $pcode,
                'data' => $data
            ];
        }

        return [
            'periods' => $periods,
            'series' => $series
        ];
    }
}