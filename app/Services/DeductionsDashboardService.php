<?php

namespace App\Services;

use App\Models\Payhouse;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class DeductionsDashboardService
{
    /**
     * Total deducted per item type for a given period — feeds the bar chart.
     * Always excludes D54 (WHTAX) since that's tax, not a loan/saving deduction.
     */
    public function getDeductionsByType(array $filters): array
    {
        $query = Payhouse::join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->join('ptypes', 'payhouse.itemcode', '=', 'ptypes.code')
            ->where('payhouse.pcategory', 'Deduction')
            ->where('payhouse.itemcode', '!=', 'D54')
            ->where('payhouse.month', $filters['month'])
            ->where('payhouse.year', $filters['year'])
            ->whereIn('registration.payrolty', $filters['allowedPayrollIds']);

        $this->applyCommonFilters($query, $filters);

        return $query->select(
                'payhouse.itemcode',
                'ptypes.cname as item_description',
                DB::raw('SUM(payhouse.tamount) as total_deducted'),
                DB::raw('COUNT(DISTINCT payhouse.WorkNo) as employee_count')
            )
            ->groupBy('payhouse.itemcode', 'ptypes.cname')
            ->orderByDesc('total_deducted')
            ->get()
            ->toArray();
    }

    /**
     * Total outstanding balance per item type, as of the selected period —
     * a snapshot (balance is stored per-row, per-period), not a lifetime sum.
     */
    public function getDeductionBalances(array $filters): array
    {
        $query = Payhouse::join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->join('ptypes', 'payhouse.itemcode', '=', 'ptypes.code')
            ->where('payhouse.pcategory', 'Deduction')
            ->where('payhouse.itemcode', '!=', 'D54')
            ->where('payhouse.month', $filters['month'])
            ->where('payhouse.year', $filters['year'])
            ->whereNotNull('payhouse.balance')
            ->whereIn('registration.payrolty', $filters['allowedPayrollIds']);

        $this->applyCommonFilters($query, $filters);

        return $query->select(
                'payhouse.itemcode',
                'ptypes.cname as item_description',
                DB::raw('SUM(payhouse.balance) as total_balance'),
                DB::raw('COUNT(DISTINCT payhouse.WorkNo) as employee_count')
            )
            ->groupBy('payhouse.itemcode', 'ptypes.cname')
            ->orderByDesc('total_balance')
            ->get()
            ->toArray();
    }

    /**
     * Employee-level listing — one row per employee per deduction item,
     * mirroring the shape of getInvoicedListing().
     */
    public function getDeductionListing(array $filters): array
    {
        $query = Payhouse::join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->join('tblemployees', 'payhouse.WorkNo', '=', 'tblemployees.emp_id')
            ->join('ptypes', 'payhouse.itemcode', '=', 'ptypes.code')
            ->join('prolltypes', 'registration.payrolty', '=', 'prolltypes.ID')
            ->where('payhouse.pcategory', 'Deduction')
            ->where('payhouse.itemcode', '!=', 'D54')
            ->where('payhouse.month', $filters['month'])
            ->where('payhouse.year', $filters['year'])
            ->where('tblemployees.Status', 'ACTIVE')
            ->whereIn('registration.payrolty', $filters['allowedPayrollIds']);

        $this->applyCommonFilters($query, $filters);

        $rows = $query->select(
                'payhouse.WorkNo',
                DB::raw("CONCAT(tblemployees.FirstName, ' ', tblemployees.LastName) as full_name"),
                'prolltypes.pname as portfolio',
                'payhouse.itemcode',
                'ptypes.cname as item_description',
                'payhouse.tamount as amount_deducted',
                'payhouse.balance',
                'registration.kra as pin_number'
            )
            ->orderBy('payhouse.WorkNo')
            ->get();

        return $rows->map(function ($row) {
            $portfolio = $row->portfolio === 'Agents' ? 'Individual Life' : $row->portfolio;

            return [
                'vendor_name' => $row->full_name,
                'pin_number' => $row->pin_number,
                'portfolio' => $portfolio,
                'item_code' => $row->itemcode,
                'item_description' => $row->item_description,
                'amount_deducted' => (float) $row->amount_deducted,
                'balance' => $row->balance !== null ? (float) $row->balance : null,
                'currency' => 'KES',
            ];
        })->toArray();
    }

    /** Applies vendor/portfolio filters shared across all three methods above */
    private function applyCommonFilters($query, array $filters): void
    {
        if (!empty($filters['portfolio_id'])) {
            $query->where('registration.payrolty', $filters['portfolio_id']);
        }
        if (!empty($filters['work_no'])) {
            $query->where('payhouse.WorkNo', $filters['work_no']);
        }
    }
}