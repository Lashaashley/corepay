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
use Illuminate\Support\Facades\Auth;
use Exception;

class DeductionImportService
{
    private $activePeriod;
    private $month;
    private $year;
    private $period;
    private $dateposted;
    private $missingEmployees = [];

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
       

        $this->initializePeriod();

          $userId = session('user_id') ?? Auth::id();

    // ✅ ADD: Log import started
    logAuditTrail(
        $userId,
        'OTHER',
        'employee_deductions_import',
        "{$this->month}_{$this->year}",
        null,
        null,
        [
            'action' => 'deduction_import_started',
            'import_mode' => $importMode,
            'file_path' => basename($filePath),
            'month' => $this->month,
            'year' => $this->year,
            'period' => $this->period
        ]
    );


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


        DB::beginTransaction();

        try {
            // Clear existing deductions if fresh import
            if ($importMode === 'fresh') {
                $deletedCount = EmployeeDeduction::where('month', $this->month)
                    ->where('year', $this->year)
                    ->delete();

                    logAuditTrail(
        $userId,
        'DELETE',
        'employeedeductions',
        "{$this->month}_{$this->year}",
        null,
        null,
        [
            'action' => 'bulk_delete_for_fresh_import',
            'deleted_count' => $deletedCount,
            'month' => $this->month,
            'year' => $this->year
        ]
    );


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

            // Clear cache after successful import
            $this->clearCache();

           logAuditTrail(
    $userId,
    'OTHER',
    'employee_deductions_import',
    "{$this->month}_{$this->year}",
    null,
    null,
    [
        'action' => 'deduction_import_completed',
        'import_mode' => $importMode,
        'month' => $this->month,
        'year' => $this->year,
        'total_rows' => $total,
        'success_count' => $successCount,
        'error_count' => $errorCount,
        'missing_employees_count' => count($this->missingEmployees), // ✅ ADD
        'file_name' => basename($filePath)
    ]
);

            // Log a sample of errors for review
            if (!empty($errors)) {
            }

           yield [
    'status' => 'success',
    'message' => "Import completed! {$successCount} records processed successfully, 0 errors.",
    'success' => $successCount,
    'errors' => '0',
    'errorDetails' => '0',
    'missingEmployees' => $this->missingEmployees // ✅ ADD THIS LINE
];

        } catch (Exception $e) {
            DB::rollBack();
            logAuditTrail(
        $userId,
        'ERROR',
        'employee_deductions_import',
        "{$this->month}_{$this->year}",
        null,
        null,
        [
            'action' => 'deduction_import_failed',
            'import_mode' => $importMode,
            'month' => $this->month,
            'year' => $this->year,
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'file_name' => basename($filePath)
        ]
    );
            throw $e;
        }
    }

    /**
     * Process a single row from Excel
     */
   private function processRow(array $row, $rowNumber)
{
    $workNo = $this->getCellValue($row, 0);
    $code = $this->getCellValue($row, 1);
    $amountValue = $this->getCellValue($row, 2);
    $balanceValue = $this->getCellValue($row, 3);
    
    $amount = $amountValue ? floatval(str_replace(',', '', $amountValue)) : 0;
    $balance = $balanceValue ? floatval(str_replace(',', '', $balanceValue)) : 0;

    if (!$workNo || !$code) {
        throw new Exception("Missing Work Number or Code");
    }

    // Get payment type data
    $ptype = Ptype::where('code', $code)->first();
    if (!$ptype) {
        throw new Exception("Payment type '{$code}' not found");
    }

    // Get employee data
    $employee = Agents::where('emp_id', $workNo)->first();
    if (!$employee) {
        // ✅ ADD: Track missing employee instead of throwing exception
        $this->missingEmployees[] = [
            'row' => $rowNumber,
            'work_no' => $workNo,
            'code' => $code,
            'amount' => $amount,
            'balance' => $balance
        ];
        
        throw new Exception("Employee '{$workNo}' not found");
    }

    // Rest of your code stays the same...
    if ($ptype->category === 'balance') {
        $this->handleBalanceSchedule($workNo, $code, $amount, $balance, $ptype);
    }
    elseif ($ptype->category === 'loan') {
        $this->handleLoanSchedule($workNo, $code, $amount, $balance, $ptype);
    }

    $this->upsertEmployeeDeduction($workNo, $code, $amount, $balance, $ptype, $employee);
}
/**
 * Generate Excel file for missing employees
 */
/**
 * Get missing employees collected during import
 */
public function getMissingEmployees()
{
    return $this->missingEmployees;
}
public function generateMissingEmployeesReport($missingEmployees)
{
    if (empty($missingEmployees)) {
        return null;
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set headers
    $sheet->setCellValue('A1', 'Row Number');
    $sheet->setCellValue('B1', 'Work Number');
    $sheet->setCellValue('C1', 'Payment Code');
    $sheet->setCellValue('D1', 'Amount');
    $sheet->setCellValue('E1', 'Balance');
    
    // Style header row
    $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    $sheet->getStyle('A1:E1')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFE0E0E0');
    
    // Add data
    $rowIndex = 2;
    foreach ($missingEmployees as $employee) {
        $sheet->setCellValue('A' . $rowIndex, $employee['row']);
        $sheet->setCellValue('B' . $rowIndex, $employee['work_no']);
        $sheet->setCellValue('C' . $rowIndex, $employee['code']);
        $sheet->setCellValue('D' . $rowIndex, $employee['amount']);
        $sheet->setCellValue('E' . $rowIndex, $employee['balance']);
        $rowIndex++;
    }
    
    // Auto-size columns
    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Generate filename
    $filename = 'missing_employees_' . date('Y-m-d_His') . '.xlsx';
    $filepath = storage_path('app/temp/' . $filename);
    
    // Ensure temp directory exists
    if (!file_exists(storage_path('app/temp'))) {
        mkdir(storage_path('app/temp'), 0755, true);
    }
    
    // Save file
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($filepath);
    
    return [
        'filename' => $filename,
        'filepath' => $filepath
    ];
}

    /**
     * Handle balance schedule insert/update
     */
    private function handleBalanceSchedule($empid, $code, $amount, $balance, $ptype)
    {
       

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

          
        } catch (\Exception $e) {
         
            throw $e;
        }
    }

    /**
     * Handle loan schedule insert/update
     */
    private function handleLoanSchedule($empid, $code, $amount, $balance, $ptype)
    {
      

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

         
        } catch (\Exception $e) {
          
            throw $e;
        }
    }

    /**
     * Insert or update employee deduction
     */
    private function upsertEmployeeDeduction($workNo, $code, $amount, $balance, $ptype, $employee)
    {
      
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

        
        } catch (\Exception $e) {
          
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