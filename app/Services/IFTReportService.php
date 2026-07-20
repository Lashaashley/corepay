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
            Log::info('IFT: starting generate()');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            Log::info('IFT: spreadsheet + sheet created');

            $headers = [
                'Bene Ref', 'Bene Name', 'Address', 'SwiftCode', 'Bank Name', 'Branch Name',
                'Branch Address', 'Account Number', 'Currency', 'Amount', 'Pay method', 'Ref Bank'
            ];

            foreach ($headers as $col => $header) {
                $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . "1")->setValue($header);
            }
            Log::info('IFT: headers written');

            $defaultBank = CompB::first();
            $bankCode = $defaultBank ? ltrim($defaultBank->Bankcode, '0') : '';
            Log::info('IFT: default bank fetched', [
                'found' => (bool) $defaultBank,
                'bankCode' => $bankCode
            ]);

            $employees = Payhouse::with([
                'employee.registration' => function ($query) use ($bankCode) {
                    $query->where('BankCode', $bankCode);
                },
                'employee.contact'
            ])
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->where('pname', 'NET PAY')
            ->whereHas('employee.registration', function ($query) use ($bankCode) {
                $query->where('BankCode', $bankCode)
                      ->whereIn('payrolty', $this->allowedPayrollTypes);
            })
            ->get();

            Log::info('IFT: employees query complete', ['count' => $employees->count()]);

            $banksMap = Banks::all()->keyBy('BranchCode');
            Log::info('IFT: banks map loaded', ['count' => $banksMap->count()]);

            $row = 2;
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
                    $bankRecord->Branch ?? '', // Branch Address — same field as Branch Name; confirm if a distinct field exists
                    $registration->AccountNo ?? '',
                    'KES',
                    number_format($payhouse->tamount ?? 0, 2, '.', ''),
                    'Internal Funds Transfer',
                    $defaultBank->accno ?? ''
                ];

                foreach ($rowData as $col => $value) {
                    $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($col + 1) . $row);

                    if (in_array($col, self::TEXT_COLUMNS, true)) {
                        // ✅ explicit string type — no apostrophe
                        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
                        $cell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    } elseif ($col === 9) {
                        $cell->setValue($value);
                        $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
                    } else {
                        $cell->setValue($value);
                    }
                }

                $row++;
            }

            Log::info('IFT: row-building loop complete', [
                'rows_written' => $row - 2,
                'skipped_no_employee' => $skippedNoEmployee,
                'skipped_no_registration' => $skippedNoRegistration
            ]);

            return $spreadsheet;

        } catch (\Throwable $e) {
            // ✅ \Throwable, not \Exception — catches fatal errors too
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