<?php

namespace App\Services;

use App\Models\Agents;
use App\Models\EmployeeDeduction;
use App\Models\BalanceSched;
use App\Models\LoanShedule;
use App\Models\Ptype;
use App\Models\Pperiod;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class DeductionImportService
{
    private $activePeriod;
    private $month;
    private $year;
    private $period;
    private $dateposted;

    public function __construct()
    {
        $this->dateposted = now()->format('Y-m-d');
    }

    /**
     * Get active period and set properties
     */
    private function initializePeriod()
    {
        $this->activePeriod = Pperiod::where('sstatus', 'ACTIVE')->first();
        
        if (!$this->activePeriod) {
            throw new Exception("No active period found. Please activate a payroll period first.");
        }

        $this->month = $this->activePeriod->mmonth;
        $this->year = $this->activePeriod->yyear;
        $this->period = $this->month . $this->year;
    }

    /**
     * Import deductions from Excel file
     */
    public function import($filePath, $importMode = 'update')
    {
        /*Log::info("=== IMPORT STARTED ===", [
            'import_mode' => $importMode,
            'file_path' => $filePath
        ]);*/

        $this->initializePeriod();

       /* Log::info("Active Period Initialized", [
            'month' => $this->month,
            'year' => $this->year,
            'period' => $this->period
        ]);*/

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) <= 1) {
            throw new Exception("Excel file appears empty or missing data rows.");
        }

        $header = array_shift($rows);
        $total = count($rows);
        $current = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

       // Log::info("Starting to process {$total} rows");

        DB::beginTransaction();

        try {
            // Clear existing deductions if fresh import
            if ($importMode === 'fresh') {
                $deletedCount = EmployeeDeduction::where('month', $this->month)
                    ->where('year', $this->year)
                    ->delete();

              //  Log::info("Fresh import - Deleted {$deletedCount} existing deductions");

                yield [
                    'status' => 'progress',
                    'progress' => 0,
                    'message' => "Cleared {$deletedCount} existing deductions for {$this->month} {$this->year}. Starting import...",
                    'success' => 0,
                    'errors' => 0
                ];
            }

            foreach ($rows as $rowIndex => $row) {
                $current++;

                try {
                    $this->processRow($row, $rowIndex + 2);
                    $successCount++;
                } catch (Exception $e) {
                    $errorCount++;
                    $errorMessage = $e->getMessage();
                    
                    $errors[] = [
                        'row' => $rowIndex + 2,
                        'message' => $errorMessage
                    ];

                    // Log each error
                  /*  Log::error("Row " . ($rowIndex + 2) . " Error", [
                        'row_number' => $rowIndex + 2,
                        'error' => $errorMessage,
                        'row_data' => $row
                    ]);*/
                }

                // Send progress updates every 50 rows or at completion
                if ($current % 50 === 0 || $current === $total) {
                    yield [
                        'status' => 'progress',
                        'progress' => round(($current / $total) * 100),
                        'message' => "Processed {$current} of {$total} rows",
                        'success' => $successCount,
                        'errors' => $errorCount
                    ];
                }
            }

            DB::commit();

           /* Log::info("=== IMPORT COMPLETED ===", [
                'total_rows' => $total,
                'success' => $successCount,
                'errors' => $errorCount
            ]);*/

            // Clear cache after successful import
            $this->clearCache();

            // Log a sample of errors for review
            if (!empty($errors)) {
              /*  Log::warning("Import completed with errors", [
                    'total_errors' => count($errors),
                    'first_10_errors' => array_slice($errors, 0, 10)
                ]);*/
            }

            yield [
                'status' => 'success',
                'message' => "Import completed! {$successCount} records processed successfully, {$errorCount} errors.",
                'success' => $successCount,
                'errors' => $errorCount,
                'errorDetails' => $errors
            ];

        } catch (Exception $e) {
            DB::rollBack();
          /*  Log::error("=== IMPORT FAILED ===", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);*/
            throw $e;
        }
    }

    /**
     * Process a single row from Excel
     */
    private function processRow(array $row, $rowNumber)
    {
       // Log::debug("Processing row {$rowNumber}", ['row_data' => $row]);

        $workNo = $this->getCellValue($row, 0);
        $code = $this->getCellValue($row, 1);
        $amountValue = $this->getCellValue($row, 2);
        $balanceValue = $this->getCellValue($row, 3);
        
        $amount = $amountValue ? floatval(str_replace(',', '', $amountValue)) : 0;
        $balance = $balanceValue ? floatval(str_replace(',', '', $balanceValue)) : 0;

        if (!$workNo || !$code) {
            throw new Exception("Missing Work Number or Code");
        }

      /*  Log::debug("Row {$rowNumber} parsed data", [
            'workNo' => $workNo,
            'code' => $code,
            'amount' => $amount,
            'balance' => $balance
        ]); */

        // Get payment type data
        $ptype = Ptype::where('code', $code)->first();
        if (!$ptype) {
            throw new Exception("Payment type '{$code}' not found");
        }

       /* Log::debug("Row {$rowNumber} - Payment type found", [
            'code' => $code,
            'category' => $ptype->category,
            'cname' => $ptype->cname
        ]);*/

        // Get employee data
        $employee = Agents::where('emp_id', $workNo)->first();
        if (!$employee) {
            throw new Exception("Employee '{$workNo}' not found");
        }

      /*  Log::debug("Row {$rowNumber} - Employee found", [
            'workNo' => $workNo,
            'name' => ($employee->FirstName ?? '') . ' ' . ($employee->LastName ?? '')
        ]); */

        // Handle balance schedules
        if ($ptype->category === 'balance') {
         /*   Log::info("Row {$rowNumber} - Handling BALANCE schedule", [
                'empid' => $workNo,
                'code' => $code,
                'amount' => $amount,
                'balance' => $balance
            ]);*/
            $this->handleBalanceSchedule($workNo, $code, $amount, $balance, $ptype);
        }
        // Handle loan schedules
        elseif ($ptype->category === 'loan') {
           /* Log::info("Row {$rowNumber} - Handling LOAN schedule", [
                'empid' => $workNo,
                'code' => $code,
                'amount' => $amount,
                'balance' => $balance
            ]);*/
            $this->handleLoanSchedule($workNo, $code, $amount, $balance, $ptype);
        } else {
           // Log::debug("Row {$rowNumber} - No schedule handling (category: {$ptype->category})");
        }

        // Insert or update employee deductions
        $this->upsertEmployeeDeduction($workNo, $code, $amount, $balance, $ptype, $employee);

       // Log::debug("Row {$rowNumber} - Successfully processed");
    }

    /**
     * Handle balance schedule insert/update
     */
    private function handleBalanceSchedule($empid, $code, $amount, $balance, $ptype)
    {
       /* Log::info("=== handleBalanceSchedule TRIGGERED ===", [
            'empid' => $empid,
            'balancecode' => $code,
            'period' => $this->period,
            'amount' => $amount,
            'balance' => $balance,
            'increREDU' => $ptype->increREDU
        ]);*/

        try {
            $balanceSched = BalanceSched::updateOrCreate(
                [
                    'empid' => $empid,
                    'balancecode' => $code,
                    'Pperiod' => $this->period
                ],
                [
                    'rrecovery' => $amount,
                    'balance' => $balance,
                    'increREDU' => $ptype->increREDU,
                    'paidcheck' => 'NO',
                    'stat' => '1'
                ]
            );

          /*  Log::info("Balance schedule saved successfully", [
                'empid' => $empid,
                'code' => $code,
                'was_recently_created' => $balanceSched->wasRecentlyCreated
            ]);*/
        } catch (\Exception $e) {
          /*  Log::error("Error in handleBalanceSchedule", [
                'empid' => $empid,
                'code' => $code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);*/
            throw $e;
        }
    }

    /**
     * Handle loan schedule insert/update
     */
    private function handleLoanSchedule($empid, $code, $amount, $balance, $ptype)
    {
      /*  Log::info("=== handleLoanSchedule TRIGGERED ===", [
            'empid' => $empid,
            'loantype' => $code,
            'period' => $this->period,
            'amount' => $amount,
            'balance' => $balance,
            'interest' => $ptype->rate,
            'recintres' => $ptype->recintres
        ]);*/

        try {
            $loanSched = LoanShedule::updateOrCreate(
                [
                    'empid' => $empid,
                    'loantype' => $code,
                    'Period' => $this->period
                ],
                [
                    'interest' => $ptype->rate,
                    'mpay' => $amount,
                    'balance' => $balance,
                    'paidcheck' => 'NO',
                    'statlon' => '1',
                    'recintres' => $ptype->recintres
                ]
            );

         /*   Log::info("Loan schedule saved successfully", [
                'empid' => $empid,
                'code' => $code,
                'was_recently_created' => $loanSched->wasRecentlyCreated
            ]);*/
        } catch (\Exception $e) {
          /*  Log::error("Error in handleLoanSchedule", [
                'empid' => $empid,
                'code' => $code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);*/
            throw $e;
        }
    }

    /**
     * Insert or update employee deduction
     */
    private function upsertEmployeeDeduction($workNo, $code, $amount, $balance, $ptype, $employee)
    {
       /* Log::debug("Upserting employee deduction", [
            'workNo' => $workNo,
            'code' => $code,
            'month' => $this->month,
            'year' => $this->year
        ]);*/

        try {
            $deduction = EmployeeDeduction::updateOrCreate(
                [
                    'WorkNo' => $workNo,
                    'PCode' => $code,
                    'month' => $this->month,
                    'year' => $this->year
                ],
                [
                    'Surname' => $employee->FirstName ?? null,
                    'othername' => $employee->LastName ?? null,
                    'dept' => $employee->Department ?? null,
                    'pcate' => $ptype->cname,
                    'Amount' => $amount,
                    'balance' => $balance,
                    'loanshares' => $ptype->category,
                    'procctype' => $ptype->procctype,
                    'varorfixed' => $ptype->varorfixed,
                    'taxaornon' => $ptype->taxaornon,
                    'increREDU' => $ptype->increREDU,
                    'rate' => $ptype->rate,
                    'prossty' => $ptype->prossty,
                    'dateposted' => $this->dateposted,
                    'statdeduc' => '1',
                    'relief' => $ptype->relief,
                    'recintres' => $ptype->recintres,
                    'parent' => $ptype->parent
                ]
            );

         /*   Log::debug("Employee deduction saved", [
                'workNo' => $workNo,
                'code' => $code,
                'was_recently_created' => $deduction->wasRecentlyCreated
            ]);*/
        } catch (\Exception $e) {
          /*  Log::error("Error upserting employee deduction", [
                'workNo' => $workNo,
                'code' => $code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);*/
            throw new Exception("Failed to save employee deduction: " . $e->getMessage());
        }
    }

    /**
     * Get cell value helper
     */
    private function getCellValue($row, $index)
    {
        return isset($row[$index]) && trim($row[$index]) !== '' ? trim($row[$index]) : null;
    }

    /**
     * Clear cache files
     */
    private function clearCache()
    {
        $cacheKeys = ['periods', 'pname', 'statutory', 'staff'];
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}