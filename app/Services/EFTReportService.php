<?php

namespace App\Services;

use App\Models\CompB;
use App\Models\Payhouse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EFTReportService
{
    protected $month;
    protected $year;
    protected $allowedPayrollTypes;

    private const TEXT_COLUMNS = [0, 3, 6, 7, 12];

    public function __construct($period, $allowedPayrollTypes)
    {
        $this->month = substr($period, 0, -4);
        $this->year = substr($period, -4);
        $this->allowedPayrollTypes = $allowedPayrollTypes;

       
    }

   public function generate()
{
    try {
        $userId = Auth::id();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [
        'Bene Ref', 'Bene Name', 'Bene Address', 'SwiftCode', 'Branch', 'Bank',
        'Branch Code', 'Account Number', 'Amount', 'Pay method', 'Remarks',
        'Currency', 'Debit Account', 'Pay Purpose', 'Email', 'Document Name',
        'Corporate Code', 'Execution Date'
    ];

    foreach ($headers as $col => $header) {
        $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . "1")->setValue($header);
    }

    $defaultBank = CompB::first();
    $bankCode = $defaultBank ? $defaultBank->Bankcode : '';

    // ── NEW: pending payments across ALL periods, not just the current one ──
    $pendingPayments = \App\Models\PaymentStatus::where('status', 'TO BE PAID')
        ->get(['WorkNo', 'month', 'year']);

    $byPeriod = $pendingPayments->groupBy(fn($p) => $p->month . '|' . $p->year);

    $employees = collect();

    foreach ($byPeriod as $periodKey => $group) {
        [$periodMonth, $periodYear] = explode('|', $periodKey);
        $workNosForPeriod = $group->pluck('WorkNo');

        $periodEmployees = Payhouse::with([
            'employee.registration' => function ($query) use ($bankCode) {
                $query->where('BankCode', '!=', $bankCode);
            },
            'employee.contact'
        ])
        ->where('month', $periodMonth)
        ->where('year', $periodYear)
        ->where('pname', 'NET PAY')
        ->where('tamount', '<', 1000000)
        ->where('tamount', '>', 0)
        ->whereIn('WorkNo', $workNosForPeriod)
        ->whereHas('employee.registration', function ($query) use ($bankCode) {
            $query->where('BankCode', '!=', $bankCode)
                  ->whereIn('payrolty', $this->allowedPayrollTypes);
        })
        ->get();

        $employees = $employees->merge($periodEmployees);
    }

    $row = 2;
    $skippedNoEmployee = 0;
    $skippedNoRegistration = 0;
    $paidRows = [];   // keyed by "WorkNo|month|year" — each row keeps its OWN period, not $this->month/$this->year

    foreach ($employees as $payhouse) {
        $employee = $payhouse->employee;
        if (!$employee) {
            $skippedNoEmployee++;
            continue;
        }

        $registration = $employee->registration->firstWhere('BankCode', '!=', $bankCode);
        if (!$registration) {
            $skippedNoRegistration++;
            continue;
        }

        $rowData = [
            $employee->emp_id,
            $employee->full_name ?? '',
            $employee->contact->PhysicalAddress ?? '',
            $registration->swiftcode ?? '',
            $registration->Branch ?? '',
            $registration->Bank ?? '',
            $registration->BranchCode ?? '',
            $registration->AccountNo ?? '',
            number_format($payhouse->tamount ?? 0, 0, '.', ''),
            'External Funds Transfer',
            'Life Agents Comm Mar',
            'KES',
            $defaultBank->accno ?? '',
            'Life Agents Comm Mar',
            $employee->EmailId ?? '',
            '',
            '',
            ''
        ];

        foreach ($rowData as $col => $value) {
            $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . $row);

            if (in_array($col, self::TEXT_COLUMNS, true)) {
                $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
                $cell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            } elseif ($col === 8) {
                $cell->setValue($value);
                $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
            } else {
                $cell->setValue($value);
            }
        }

        // NEW: key carries the row's OWN period — critical for backlog rows
        $paidRows[$payhouse->WorkNo . '|' . $payhouse->month . '|' . $payhouse->year] = [
            'WorkNo' => $payhouse->WorkNo,
            'month' => $payhouse->month,
            'year' => $payhouse->year,
            'net_amount' => (float) $payhouse->tamount,
        ];

        $row++;
    }

    // ── NEW: close the loop, per period group, per-employee net_amount ──
    if (!empty($paidRows)) {
        $now = now();
        $groupedByOwnPeriod = collect($paidRows)->groupBy(fn($r) => $r['month'] . '|' . $r['year']);

        foreach ($groupedByOwnPeriod as $periodKey => $rows) {
            [$originalMonth, $originalYear] = explode('|', $periodKey);

            $case = "CASE WorkNo ";
            $bindings = [];
            foreach ($rows as $r) {
                $case .= "WHEN ? THEN ? ";
                array_push($bindings, $r['WorkNo'], $r['net_amount']);
            }
            $case .= "END";

            $workNos = $rows->pluck('WorkNo')->toArray();
            $placeholders = implode(',', array_fill(0, count($workNos), '?'));

            \Illuminate\Support\Facades\DB::update(
                "UPDATE payment_status
                 SET net_amount = {$case},
                     status = 'PAID',
                     report_type = 'EFT',
                     paid_at = ?,
                     paid_atmonth = ?,
                     paid_atyear = ?
                 WHERE month = ? AND year = ? AND status = 'TO BE PAID' AND WorkNo IN ({$placeholders})",
                array_merge(
                    $bindings,
                    [$now, $this->month, $this->year],  // paid_atmonth/year = the CURRENT run, i.e. when it actually got disbursed
                    [$originalMonth, $originalYear],     // WHERE clause targets the row's ORIGINAL period
                    $workNos
                )
            );
        }
    }

    logAuditTrail(
                $userId,
                'OTHER',
                'EFT_RPT',
                "{$periodKey}",
                null,
                null,
                [
                    'action' => 'EFT Interface Generated'
                ]
            );

    return $spreadsheet;
}
     catch (\Throwable $e) {
        Log::error("EFT Report generation error: " . $e->getMessage(), [
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

    public function getFileName()
    {
        return "EFT{$this->month}{$this->year}.xlsx";
    }
}