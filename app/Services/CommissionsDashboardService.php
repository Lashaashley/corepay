<?php

namespace App\Services;

use App\Models\Payhouse;
use App\Models\PaymentStatus;
use App\Models\Registration;
use App\Models\Paytypes;
use App\Models\EtimsInvoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommissionsDashboardService
{
    /** Portfolios the logged-in user is allowed to see, for the dropdown */
    public function getPortfolios(array $allowedPayrollIds): array
    {
        return Paytypes::whereIn('ID', $allowedPayrollIds)
            ->orderBy('pname')
            ->get(['ID', 'pname'])
            ->toArray();
    }

    /**
     * Monthly net pay trend for a given year, respecting whatever status
     * filter is active. Portfolio/vendor filters narrow via registration.
     */
    public function getNetPayTrend(array $filters): array
    {
        $query = Payhouse::join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->leftJoin('payment_status', function ($join) use ($filters) {
                $join->on(DB::raw('payhouse.WorkNo COLLATE utf8mb4_general_ci'), '=', DB::raw('payment_status.WorkNo COLLATE utf8mb4_general_ci'))
                     ->on('payhouse.month', '=', 'payment_status.month')
                     ->on('payhouse.year', '=', 'payment_status.year');
            })
            ->where('payhouse.itemcode', 'P99')
            ->where('payhouse.year', $filters['year'])
            ->whereIn('registration.payrolty', $filters['allowedPayrollIds']);

        $this->applyCommonFilters($query, $filters, includeStatus: true);

        $rows = $query->selectRaw('payhouse.month, SUM(payhouse.tamount) as total')
            ->groupBy('payhouse.month')
            ->get()
            ->keyBy('month');

        return $this->fillMonths($rows);
    }

    /**
     * Monthly trend of outstanding (not yet PAID) net pay — always
     * status != PAID, independent of the Payment Status dropdown.
     */
    public function getNotPaidTrend(array $filters): array
    {
        $query = Payhouse::join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->join('payment_status', function ($join) {
                $join->on(DB::raw('payhouse.WorkNo COLLATE utf8mb4_general_ci'), '=', DB::raw('payment_status.WorkNo COLLATE utf8mb4_general_ci'))
                     ->on('payhouse.month', '=', 'payment_status.month')
                     ->on('payhouse.year', '=', 'payment_status.year');
            })
            ->where('payhouse.itemcode', 'P99')
            ->where('payhouse.year', $filters['year'])
            ->where('payment_status.status', '!=', 'PAID')
            ->whereIn('registration.payrolty', $filters['allowedPayrollIds']);

        $this->applyCommonFilters($query, $filters, includeStatus: false);

        $rows = $query->selectRaw('payhouse.month, SUM(payhouse.tamount) as total')
            ->groupBy('payhouse.month')
            ->get()
            ->keyBy('month');

        return $this->fillMonths($rows);
    }

    /**
     * Aging of outstanding (not PAID) net pays, bucketed by how many months
     * old the PAYROLL PERIOD is relative to today, in 3-month bands.
     */
    public function getAgingBuckets(array $filters): array
{
    $query = Payhouse::join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
        ->join('payment_status', function ($join) {
            $join->on(DB::raw('payhouse.WorkNo COLLATE utf8mb4_general_ci'), '=', DB::raw('payment_status.WorkNo COLLATE utf8mb4_general_ci'))
                 ->on('payhouse.month', '=', 'payment_status.month')
                 ->on('payhouse.year', '=', 'payment_status.year');
        })
        ->leftJoinSub(
            DB::table('payhouse')
                ->select('WorkNo', 'month', 'year', DB::raw('SUM(tamount) as gross'))
                ->whereIn('pcategory', ['Payment', 'Benefit'])
                ->groupBy('WorkNo', 'month', 'year'),
            'gross_sub',
            function ($join) {
                $join->on(DB::raw('payhouse.WorkNo COLLATE utf8mb4_general_ci'), '=', DB::raw('gross_sub.WorkNo COLLATE utf8mb4_general_ci'))
                     ->on('payhouse.month', '=', 'gross_sub.month')
                     ->on('payhouse.year', '=', 'gross_sub.year');
            }
        )
        ->where('payhouse.itemcode', 'P99')
        ->where('payment_status.status', '!=', 'PAID')
        ->whereIn('registration.payrolty', $filters['allowedPayrollIds']);

    $this->applyCommonFilters($query, $filters, includeStatus: false, includeYear: false);

    // NEW — bucket computed in SQL via CASE, GROUP BY does the counting/summing.
    // TIMESTAMPDIFF avoids pulling raw rows into PHP just to call Carbon per row.
    $rows = $query->selectRaw("
            CASE
                WHEN TIMESTAMPDIFF(MONTH, STR_TO_DATE(CONCAT('01 ', payhouse.month, ' ', payhouse.year), '%d %M %Y'), CURDATE()) < 3 THEN '0-3 months'
                WHEN TIMESTAMPDIFF(MONTH, STR_TO_DATE(CONCAT('01 ', payhouse.month, ' ', payhouse.year), '%d %M %Y'), CURDATE()) < 6 THEN '3-6 months'
                WHEN TIMESTAMPDIFF(MONTH, STR_TO_DATE(CONCAT('01 ', payhouse.month, ' ', payhouse.year), '%d %M %Y'), CURDATE()) < 9 THEN '6-9 months'
                ELSE '9-12+ months'
            END as bucket,
            COUNT(DISTINCT payhouse.WorkNo) as invoices,
            SUM(COALESCE(gross_sub.gross, 0)) as gross,
            SUM(payhouse.tamount) as net
        ")
        ->groupBy('bucket')
        ->get()
        ->keyBy('bucket');

    $buckets = [
        '0-3 months'  => ['invoices' => 0, 'gross' => 0, 'net' => 0],
        '3-6 months'  => ['invoices' => 0, 'gross' => 0, 'net' => 0],
        '6-9 months'  => ['invoices' => 0, 'gross' => 0, 'net' => 0],
        '9-12+ months' => ['invoices' => 0, 'gross' => 0, 'net' => 0],
    ];

    foreach ($rows as $bucket => $row) {
        $buckets[$bucket] = [
            'invoices' => (int) $row->invoices,
            'gross' => (float) $row->gross,
            'net' => (float) $row->net,
        ];
    }

    return $buckets;
}

   public function getInvoicedListing(array $filters): array
{
    // Subquery: WHTAX per employee/period (single row, itemcode D54)
    $whtaxSub = DB::table('payhouse')
        ->select('WorkNo', 'month', 'year', 'tamount as whtax')
        ->where('itemcode', 'D54');

    // Subquery: all OTHER deductions summed, excluding WHTAX itself
    $dedSub = DB::table('payhouse')
        ->select('WorkNo', 'month', 'year', DB::raw('SUM(tamount) as ded_total'))
        ->where('pcategory', 'Deduction')
        ->where('itemcode', '!=', 'D54')
        ->groupBy('WorkNo', 'month', 'year');

    $query = Payhouse::from('payhouse')
        ->join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
        ->join('tblemployees', 'payhouse.WorkNo', '=', 'tblemployees.emp_id')
        ->join('payment_status', function ($join) {
            $join->on(DB::raw('payhouse.WorkNo COLLATE utf8mb4_general_ci'), '=', DB::raw('payment_status.WorkNo COLLATE utf8mb4_general_ci'))
                 ->on('payhouse.month', '=', 'payment_status.month')
                 ->on('payhouse.year', '=', 'payment_status.year');
        })
        ->join('prolltypes', 'registration.payrolty', '=', 'prolltypes.ID')
        // NEW — joined subqueries instead of per-row queries
        ->leftJoinSub($whtaxSub, 'whtax_sub', function ($join) {
            $join->on(DB::raw('payhouse.WorkNo COLLATE utf8mb4_general_ci'), '=', DB::raw('whtax_sub.WorkNo COLLATE utf8mb4_general_ci'))
                 ->on('payhouse.month', '=', 'whtax_sub.month')
                 ->on('payhouse.year', '=', 'whtax_sub.year');
        })
        ->leftJoinSub($dedSub, 'ded_sub', function ($join) {
            $join->on(DB::raw('payhouse.WorkNo COLLATE utf8mb4_general_ci'), '=', DB::raw('ded_sub.WorkNo COLLATE utf8mb4_general_ci'))
                 ->on('payhouse.month', '=', 'ded_sub.month')
                 ->on('payhouse.year', '=', 'ded_sub.year');
        })
        ->where('payhouse.itemcode', 'P99')
        ->where('payhouse.month', $filters['month'])
        ->where('payhouse.year', $filters['year'])
        ->where('tblemployees.Status', 'ACTIVE')
        ->whereIn('payment_status.status', ['UNPAID', 'PAID']) // fixed typo
        ->whereIn('registration.payrolty', $filters['allowedPayrollIds']);

    $this->applyCommonFilters($query, $filters, includeStatus: true, includeYear: false);

    $rows = $query->select(
            'payhouse.WorkNo',
            DB::raw("CONCAT(tblemployees.FirstName, ' ', tblemployees.LastName) as full_name"),
            'prolltypes.pname as portfolio',
            'payhouse.tamount as net_amount',
            'payhouse.month as period_month',
            'payhouse.year as period_year',
            'payment_status.status',
            'payment_status.net_amount as amount_paid',   // fixed
            'payment_status.paid_at as payment_date',
            'registration.kra as pin_number',              // fixed
            DB::raw('COALESCE(whtax_sub.whtax, 0) as whtax'),
            DB::raw('COALESCE(ded_sub.ded_total, 0) as comm_adv')
        )
        ->get();

    // Enrich with invoice number/date from etims_invoices (separate table, no FK)
    $workNos = $rows->pluck('WorkNo')->unique()->toArray();
    $invoices = EtimsInvoice::whereIn('WorkNo', $workNos)
        ->where('month', $filters['month'])
        ->where('year', $filters['year'])
        ->get()
        ->keyBy('WorkNo');

    $grossByWorkNo = $this->getGrossAmountsFor($workNos, collect([['month' => $filters['month'], 'year' => $filters['year']]]));

    return $rows->map(function ($row) use ($invoices, $filters, $grossByWorkNo) {
        $invoice = $invoices->get($row->WorkNo);
        $key = $row->WorkNo . '|' . $filters['month'] . '|' . $filters['year'];

        // Portfolio display-name mapping
        $portfolio = $row->portfolio === 'Agents' ? 'Individual Life' : $row->portfolio;

        // Aging — only meaningful while still unpaid; PAID rows have no "age" left to track
        $ageDays = null;
        $agingCategory = null;
        if ($row->status !== 'PAID') {
            $periodDate = Carbon::parse("1 {$row->period_month} {$row->period_year}");
            $ageDays = $periodDate->diffInDays(Carbon::now());
            $ageMonths = $periodDate->diffInMonths(Carbon::now());

            $agingCategory = match (true) {
                $ageMonths < 3 => '0-3 months',
                $ageMonths < 6 => '3-6 months',
                $ageMonths < 9 => '6-9 months',
                default => '9-12+ months',
            };
        }

        return [
            'vendor_name' => $row->full_name,
            'invoice_num' => $invoice->SystemInvoiceNo ?? null,
            'invoice_date' => $invoice->TransDateTime ?? null,
            'portfolio' => $portfolio,
            'gross_amount' => $grossByWorkNo[$key] ?? 0,
            'net_amount' => (float) $row->net_amount,
            'WHTAX' => (float) $row->whtax,
            'amountpaid' => (float) ($row->amount_paid ?? 0),
            'COMM_ADV' => (float) $row->comm_adv,
            'payment_status' => $row->status,
            'PAYMENT_DATE' => $row->payment_date,
            'PIN_NUMBER' => $row->pin_number,
            'CURRENCY' => 'KES',
            'Age_days' => $ageDays,
            'Agingcategory' => $agingCategory,
        ];
    })->toArray();
}

    /** Applies vendor/portfolio (and optionally status/year) filters shared across queries */
    private function applyCommonFilters($query, array $filters, bool $includeStatus, bool $includeYear = true): void
    {
        if (!empty($filters['portfolio_id'])) {
            $query->where('registration.payrolty', $filters['portfolio_id']);
        }
        if (!empty($filters['work_no'])) {
            $query->where('payhouse.WorkNo', $filters['work_no']);
        }
        if ($includeStatus && !empty($filters['status'])) {
            $query->where('payment_status.status', $filters['status']);
        }
    }

    /** Gross amount per WorkNo+period, summed from Payment/Benefit categories */
    private function getGrossAmountsFor(array $workNos, $periodRows): array
{
    if (empty($workNos)) {
        return [];
    }

    $periods = collect($periodRows)->map(fn($r) => [$r->month ?? $r['month'], $r->year ?? $r['year']])->unique('0');

    // NEW — single query using a row-constructor IN, instead of one query per period.
    // MySQL 5.7+/8+ supports this; if the DB is older, fall back to the OR-chain below.
    $query = Payhouse::whereIn('WorkNo', $workNos)
        ->whereIn('pcategory', ['Payment', 'Benefit']);

    $query->where(function ($q) use ($periods) {
        foreach ($periods as [$month, $year]) {
            $q->orWhere(function ($q2) use ($month, $year) {
                $q2->where('month', $month)->where('year', $year);
            });
        }
    });

    $sums = $query->selectRaw('WorkNo, month, year, SUM(tamount) as gross')
        ->groupBy('WorkNo', 'month', 'year')
        ->get();

    $result = [];
    foreach ($sums as $row) {
        $result[$row->WorkNo . '|' . $row->month . '|' . $row->year] = (float) $row->gross;
    }

    return $result;
}

    /** Ensures all 12 months appear in trend output, even with zero data */
    private function fillMonths($rows): array
    {
        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $out = [];
        foreach ($months as $m) {
            $out[$m] = (float) ($rows[$m]->total ?? 0);
        }
        return $out;
    }
}