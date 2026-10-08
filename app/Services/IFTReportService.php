<?php

namespace App\Services;

use App\Models\CompB;
use App\Models\Payhouse;
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
    {
        try {
            $userId = Auth::id();
            Log::info('IFT: starting generate()');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = [
                'Bene Ref', 'Bene Name', 'Address', 'SwiftCode', 'Bank Name', 'Branch Name',
                'Branch Address', 'Account Number', 'Currency', 'Amount', 'Pay method', 'Ref Bank'
            ];

            foreach ($headers as $col => $header) {
                $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . '1')->setValue($header);
            }

            $defaultBank = CompB::first();
            $bankCode = $defaultBank ? ltrim($defaultBank->Bankcode, '0') : '';

            $employees = Payhouse::with([
                    'employee.registration' => function ($query) use ($bankCode) {
                        $query->where('BankCode', $bankCode);
                    },
                    'employee.contact'
                ])
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->where('pname', 'NET PAY')
                ->where('tamount', '>', 0)
                ->whereHas('employee.registration', function ($query) use ($bankCode) {
                    $query->where('BankCode', $bankCode)
                          ->whereIn('payrolty', $this->allowedPayrollTypes);
                })
                ->get();

            Log::info('IFT: employees query complete', ['count' => $employees->count()]);

            $banksMap = Banks::all()->keyBy('BranchCode');

            $row = 2;
            $exported = 0;
            $skippedNoEmployee = 0;
            $skippedNoRegistration = 0;

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

                $exported++;
                $row++;
            }

            Log::info('IFT file generated successfully', [
                'month' => $this->month,
                'year' => $this->year,
                'total_employees' => $employees->count(),
                'total_exported' => $exported,
                'skipped_no_employee' => $skippedNoEmployee,
                'skipped_no_registration' => $skippedNoRegistration,
            ]);

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

        } catch (\Throwable $e) {
            Log::error('IFT: error generating file', [
                'month' => $this->month,
                'year' => $this->year,
                'type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getFileName()
    {
        return "IFT{$this->month}{$this->year}.xlsx";
    }
}