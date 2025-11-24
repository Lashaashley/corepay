<?php

namespace App\Services;

use App\Models\CompB;
use App\Models\Registration;
use App\Models\Payhouse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Log;

class EFTReportService
{
    protected $month;
    protected $year;
    protected $allowedPayrollTypes;

    public function __construct($period, $allowedPayrollTypes)
    {
        $this->month = substr($period, 0, -4);
        $this->year = substr($period, -4);
        $this->allowedPayrollTypes = $allowedPayrollTypes;
    }

    public function generate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Define and set headers
            $headers = [
                'Bene Ref', 'Bene Name', 'Bene Address', 'SwiftCode', 'Branch', 'Bank',
                'Branch Code', 'Account Number', 'Amount', 'Pay method', 'Remarks',
                'Currency', 'Debit Account', 'Pay Purpose', 'Email', 'Document Name',
                'Corporate Code', 'Execution Date'
            ];

            foreach ($headers as $col => $header) {
                $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . "1")->setValue($header);
            }

            // Get default bank
            $defaultBank = CompB::first();
            $bankCode = $defaultBank ? $defaultBank->BankCode : '';

            // Get employees with their net pay for the specified period
            // Key differences from IFT:
            // 1. BankCode != default bank code (external transfers)
            // 2. netpay < 1000000
            $employees = Payhouse::with([
                'employee.registration' => function($query) use ($bankCode) {
                    $query->where('BankCode', '!=', $bankCode);
                },
                'employee.contact'
            ])
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->where('pname', 'NET PAY')
            ->where('tamount', '<', 1000000) // Amount less than 1,000,000
            ->whereHas('employee.registration', function($query) use ($bankCode) {
                $query->where('BankCode', '!=', $bankCode)
                      ->whereIn('payrolty', $this->allowedPayrollTypes);
            })
            ->get();

            $row = 2;

            foreach ($employees as $payhouse) {
                $employee = $payhouse->employee;
                
                if (!$employee) {
                    continue;
                }

                // Get registration where BankCode is NOT the default bank (external banks)
                $registration = $employee->registration->firstWhere('BankCode', '!=', $bankCode);
                
                if (!$registration) {
                    continue;
                }

                // Populate row data
                $rowData = [
                    $this->formatNumericField($employee->emp_id),
                    $employee->full_name ?? '',
                    $employee->contact->PhysicalAddress ?? '',
                    $this->formatNumericField($registration->swiftcode ?? ''),
                    $registration->Branch ?? '',
                    $registration->Bank ?? '',
                    $this->formatNumericField($registration->BranchCode ?? ''),
                    $this->formatNumericField($registration->AccountNo ?? ''),
                    number_format($payhouse->tamount ?? 0, 2, '.', ''),
                    'External Funds Transfer', // Different from IFT
                    'Life Agents Comm Mar',
                    'KES',
                    $this->formatNumericField($defaultBank->accno ?? ''),
                    'Life Agents Comm Mar',
                    $employee->EmailId ?? '',
                    '',
                    '',
                    ''
                ];

                foreach ($rowData as $col => $value) {
                    $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . $row);
                    $cell->setValue($value);

                    // Set number format for specific columns
                    if (in_array($col, [0, 3, 6, 7, 12])) {
                        $cell->getStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    } else if ($col === 8) {
                        $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                }

                $row++;
            }

            return $spreadsheet;

        } catch (\Exception $e) {
            Log::error("EFT Report generation error: " . $e->getMessage());
            throw $e;
        }
    }

    private function formatNumericField($value)
    {
        if (empty($value) && $value !== '0') {
            return '';
        }
        return "'" . (string)$value;
    }

    public function getFileName()
    {
        return "EFT{$this->month}{$this->year}.csv";
    }
}