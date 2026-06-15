<?php

namespace App\Services;

use App\Models\CompB;
use App\Models\Registration;
use App\Models\Payhouse;
use App\Models\Contact;
use App\Models\Banks;  // ← Add this
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Log;

class IFTReportService
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

            $headers = [
                'Bene Ref', 'Bene Name', 'Address', 'SwiftCode', 'Bank Name', 'Branch Name',
                'Branch Address', 'Account Number', 'Currency', 'Amount', 'Pay method', 'Ref Bank'
            ];

            foreach ($headers as $col => $header) {
                $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . "1")->setValue($header);
            }

            $defaultBank = CompB::first();
            $bankCode = $defaultBank ? ltrim($defaultBank->Bankcode, '0') : '';

            $employees = Payhouse::with([
                'employee.registration' => function($query) use ($bankCode) {
                    $query->where('BankCode', $bankCode);
                },
                'employee.contact'
            ])
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->where('pname', 'NET PAY')
            ->whereHas('employee.registration', function($query) use ($bankCode) {
                $query->where('BankCode', $bankCode)
                      ->whereIn('payrolty', $this->allowedPayrollTypes);
            })
            ->get();

            $row = 2;

            $banksMap = Banks::all()->keyBy('BranchCode');

            foreach ($employees as $payhouse) {
                $employee = $payhouse->employee;

                if (!$employee) continue;

                $registration = $employee->registration->firstWhere('BankCode', $bankCode);

                if (!$registration) continue;

                // ← Lookup bank record by matching BranchCode
                $bankRecord = $banksMap->get($registration->BranchCode);

                $rowData = [
                    $this->formatNumericField($employee->emp_id),
                    $employee->full_name ?? '',
                    $employee->contact->PhysicalAddress ?? '',
                    $bankRecord->dtbcode ?? '',         // dtbcode from Banks
                    $registration->Bank ?? '',
                    $bankRecord->Branch ?? '',          // Branch from Banks
                    $bankRecord->Branch ?? '',          // Branch Address from Banks (same field — adjust if you have a separate one)
                    $this->formatNumericField($registration->AccountNo ?? ''),
                    'KES',
                    number_format($payhouse->tamount ?? 0, 2, '.', ''),
                    'Internal Funds Transfer',
                    $this->formatNumericField($defaultBank->accno ?? '')
                ];

                foreach ($rowData as $col => $value) {
                    $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . $row);
                    $cell->setValue($value);

                    if (in_array($col, [0, 3, 6, 7, 11])) {
                        $cell->getStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    } elseif ($col === 9) {
                        $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                }

                $row++;
            }

            return $spreadsheet;

        } catch (\Exception $e) {
            Log::error("IFT Report generation error: " . $e->getMessage());
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
        return "IFT{$this->month}{$this->year}.csv";
    }
}