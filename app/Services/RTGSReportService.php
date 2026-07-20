<?php

namespace App\Services;

use App\Models\CompB;
use App\Models\Payhouse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Log;

class RTGSReportService
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


        $employees = Payhouse::with([
            'employee.registration' => function ($query) use ($bankCode) {
                $query->where('BankCode', '!=', $bankCode);
            },
            'employee.contact'
        ])
        ->where('month', $this->month)
        ->where('year', $this->year)
        ->where('pname', 'NET PAY')
        ->where('tamount', '>=', 1000000)
        ->whereHas('employee.registration', function ($query) use ($bankCode) {
            $query->where('BankCode', '!=', $bankCode)
                  ->whereIn('payrolty', $this->allowedPayrollTypes);
        })
        ->get();

       

        $row = 2;
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
                number_format($payhouse->tamount ?? 0, 2, '.', ''),
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
                    $cell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT); // ✅ per-cell, not per-100k-range
                } elseif ($col === 8) {
                    $cell->setValue($value);
                    $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $cell->setValue($value);
                }
            }

            $row++;
        }



        return $spreadsheet;

    } catch (\Throwable $e) {
        Log::error("RTGS Report generation error: " . $e->getMessage(), [
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
        return "RTGS{$this->month}{$this->year}.xlsx";
    }
}