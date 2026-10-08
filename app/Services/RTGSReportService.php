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

class RTGSReportService
{
    protected $month;
    protected $year;
    protected $allowedPayrollTypes;

    private const RTGS_THRESHOLD = 1000000;
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
                $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . '1')->setValue($header);
            }

            $defaultBank = CompB::first();
            $bankCode = $defaultBank ? $defaultBank->Bankcode : '';

            $employees = Payhouse::with([
                    'employee.registration' => function ($query) use ($bankCode) {
                        $query->where('BankCode', '!=', $bankCode);
                    },
                    'employee.contact'
                ])
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->where('pname', 'NET PAY')
                ->where('tamount', '>=', self::RTGS_THRESHOLD)
                ->whereHas('employee.registration', function ($query) use ($bankCode) {
                    $query->where('BankCode', '!=', $bankCode)
                          ->whereIn('payrolty', $this->allowedPayrollTypes);
                })
                ->get();

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

                $exported++;
                $row++;
            }

            Log::info('RTGS EFT file generated successfully', [
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
                'RTGS_RPT',
                "{$this->month} . {$this->year}",
                null,
                null,
                [
                    'action' => 'RTGS Interface Generated'
                ]
            );

            return $spreadsheet;

        } catch (\Throwable $e) {
            Log::error('Error generating RTGS EFT file', [
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
        return "RTGS{$this->month}{$this->year}.xlsx";
    }
}