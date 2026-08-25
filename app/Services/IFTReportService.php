<?php

namespace App\Services;

use App\Models\CompB;
use App\Models\Registration;
use App\Models\Payhouse;
use App\Models\Contact;
use App\Models\Banks;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class IFTReportService
{
    protected $month;
    protected $year;
    protected $allowedPayrollTypes;

    // Columns that must always be stored as text (0-indexed)
    private const TEXT_COLUMNS = [0, 3, 6, 7, 11]; // Bene Ref, SwiftCode, Branch Address, Account Number, Ref Bank

    public function __construct($period, $allowedPayrollTypes)
    {
        $this->month = substr($period, 0, -4);
        $this->year = substr($period, -4);
        $this->allowedPayrollTypes = $allowedPayrollTypes;

        Log::info('IFT: constructed', [
            'period' => $period,
            'month' => $this->month,
            'year' => $this->year,
            'allowedPayrollTypes' => $this->allowedPayrollTypes
        ]);
    }

    public function generate()
{try {
    $userId = Auth::id();
    Log::info('IFT: starting generate()');

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [
        'Bene Ref', 'Bene Name', 'Address', 'SwiftCode', 'Bank Name', 'Branch Name',
        'Branch Address', 'Account Number', 'Currency', 'Amount', 'Pay method', 'Ref Bank'
    ];

    foreach ($headers as $col => $header) {
        $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . "1")->setValue($header);
    }

    $defaultBank = CompB::first();
    $bankCode = $defaultBank ? ltrim($defaultBank->Bankcode, '0') : '';

    // ── NEW: Get pending payments across ALL periods, not just current ──
    $pendingPayments = \App\Models\PaymentStatus::where('status', 'TO BE PAID')
        ->where('net_amount', '>', 0)  // All positive amounts for IFT
        ->get(['WorkNo', 'month', 'year', 'net_amount']);

    $byPeriod = $pendingPayments->groupBy(fn($p) => $p->month . '|' . $p->year);

    Log::info('IFT: pending payments by period', [
        'total_pending' => $pendingPayments->count(),
        'periods' => $byPeriod->keys()->toArray()
    ]);

    $employees = collect();

    foreach ($byPeriod as $periodKey => $group) {
        [$periodMonth, $periodYear] = explode('|', $periodKey);
        $workNosForPeriod = $group->pluck('WorkNo');

        Log::info('IFT: processing period', [
            'period' => $periodKey,
            'work_nos_count' => $workNosForPeriod->count()
        ]);

        $periodEmployees = Payhouse::with([
            'employee.registration' => function ($query) use ($bankCode) {
                $query->where('BankCode', $bankCode);
            },
            'employee.contact'
        ])
        ->where('month', $periodMonth)
        ->where('year', $periodYear)
        ->where('pname', 'NET PAY')
        ->where('tamount', '>', 0)
        ->whereIn('WorkNo', $workNosForPeriod)
        ->whereHas('employee.registration', function ($query) use ($bankCode) {
            $query->where('BankCode', $bankCode)
                  ->whereIn('payrolty', $this->allowedPayrollTypes);
        })
        ->get();

        $employees = $employees->merge($periodEmployees);
    }

    Log::info('IFT: employees query complete', ['count' => $employees->count()]);

    $banksMap = Banks::all()->keyBy('BranchCode');

    $row = 2;
    $skippedNoEmployee = 0;
    $skippedNoRegistration = 0;
    $paidRows = [];   // keyed by "WorkNo|month|year" — each row keeps its OWN period

    foreach ($employees as $payhouse) {
        $employee = $payhouse->employee;
        if (!$employee) {
            $skippedNoEmployee++;
            continue;
        }

        $registration = $employee->registration->firstWhere('BankCode', $bankCode);
        if (!$registration) {
            $skippedNoRegistration++;
            continue;
        } 

        $bankRecord = $banksMap->get($registration->BranchCode);

        $rowData = [
            $employee->emp_id,
            $employee->full_name ?? '',
            $employee->contact->PhysicalAddress ?? '',
            $bankRecord->dtbcode ?? '',
            $registration->Bank ?? '',
            $bankRecord->Branch ?? '',
            $bankRecord->Branch ?? '',
            $registration->AccountNo ?? '',
            'KES',
            number_format($payhouse->tamount ?? 0, 0, '.', ''),
            'Internal Funds Transfer',
            $defaultBank->accno ?? ''
        ];

        foreach ($rowData as $col => $value) {
            $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . $row);

            if (in_array($col, self::TEXT_COLUMNS, true)) {
                $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
                $cell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            } elseif ($col === 9) {
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
        $currentMonth = $this->month;
        $currentYear = $this->year;
        
        $groupedByOwnPeriod = collect($paidRows)->groupBy(fn($r) => $r['month'] . '|' . $r['year']);

        Log::info('IFT: updating payment_status', [
            'total_paid_rows' => count($paidRows),
            'periods' => $groupedByOwnPeriod->keys()->toArray()
        ]);

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

            $updatedCount = \Illuminate\Support\Facades\DB::update(
                "UPDATE payment_status
                 SET net_amount = {$case},
                     status = 'PAID',
                     report_type = 'IFT',
                     paid_at = ?,
                     paid_atmonth = ?,
                     paid_atyear = ?
                 WHERE month = ? AND year = ? 
                   AND status = 'TO BE PAID' 
                   AND WorkNo IN ({$placeholders})",
                array_merge(
                    $bindings,
                    [$now, $currentMonth, $currentYear],  // paid_atmonth/year = the CURRENT run
                    [$originalMonth, $originalYear],      // WHERE clause targets the row's ORIGINAL period
                    $workNos
                )
            );

            
        }
    }

    logAuditTrail(
                $userId,
                'OTHER',
                'IFT_RPT',
                "{$this->month}{$this->year}",
                null,
                null,
                [
                    'action' => 'IFT Interface Generated'
                ]
            );

    return $spreadsheet;

} catch (\Exception $e) {
    Log::error('IFT: error generating file', [
        'month' => $this->month,
        'year' => $this->year,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    throw $e;
}
     catch (\Throwable $e) {
        Log::error("IFT Report generation error: " . $e->getMessage(), [
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
        return "IFT{$this->month}{$this->year}.xlsx"; // ✅ .xlsx, not .csv
    }
}