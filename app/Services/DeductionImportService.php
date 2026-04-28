<?php

namespace App\Services;

use App\Models\Agents;
use App\Models\EmployeeDeduction;
use App\Models\BalanceSched;
use App\Models\LoanShedule;
use App\Models\Ptype;
use App\Models\Pperiod;
use App\Models\User;
use App\Models\Paytracker;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Http;


class DeductionImportService
{
    private $activePeriod;
    private $month;
    private $year;
    private $period;
    private $dateposted;
    protected $emailConfig;
    protected $companydetails;
    private $missingEmployees = [];
private $duplicates = [];
private $seenWorkCode = [];

private array $allWorkCodeCombinations = []; // Track all combinations with their rows
private array $duplicateKeys = [];           // Track which combinations are duplicates


    public function __construct()
    {
        $this->dateposted = now()->format('Y-m-d');
         $this->loadEmailConfig();
        $this->loadCstructure();
    }

    private function loadEmailConfig(): void
    {
        $config = DB::table('email_config')->first();
        
        if (!$config) {
            throw new \Exception('Email configuration not found in database');
        }
        
        $this->emailConfig = (array) $config;
    }

    private function loadCstructure(): void
    {
        $config2 = DB::table('cstructure')->first();
        
        if (!$config2) {
            throw new \Exception('Email configuration not found in database');
        }
        
        $this->companydetails = (array) $config2;
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

    // Log import started
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

    // PRE-PROCESS: Identify ALL duplicates in the file
    $this->preProcessDuplicates($rows);

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
            $rowNumber = $rowIndex + 2;

            try {
                // Process row - duplicates will be handled internally
                $result = $this->processRow($row, $rowNumber);
                
                if ($result === 'duplicate') {
                    $errorCount++;
                    // Duplicate already recorded in $this->duplicates
                } elseif ($result === 'missing_employee') {
                    $errorCount++;
                    // Missing employee already recorded in $this->missingEmployees
                } elseif ($result === 'success') {
                    $successCount++;
                }
                
            } catch (Exception $e) {
                $errorCount++;
                $errors[] = [
                    'row' => $rowNumber,
                    'message' => $e->getMessage()
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

        // Generate exception report if needed
        $reportInfo = null;
        if (!empty($this->missingEmployees) || !empty($this->duplicates)) {
            $reportInfo = $this->generateMissingEmployeesReport($this->missingEmployees, $this->duplicates);
            if ($reportInfo) {
                session(['exception_report_path' => $reportInfo['filepath']]);
                session(['exception_report_filename' => $reportInfo['filename']]);
            }
        }

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
                'duplicates_count' => count($this->duplicates),
                'missing_employees_count' => count($this->missingEmployees),
                'file_name' => basename($filePath)
            ]
        );

        yield [
            'status' => 'success',
            'message' => "Import completed! {$successCount} records processed successfully, {$errorCount} errors.",
            'success' => $successCount,
            'errors' => $errorCount,
            'errorDetails' => $errors,
            'missingEmployees' => $this->missingEmployees,
            'duplicates' => $this->duplicates,
            'has_exception_report' => !empty($reportInfo)
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
private function preProcessDuplicates(array $rows): void
{
    // Reset arrays
    $this->allWorkCodeCombinations = [];
    $this->duplicateKeys = [];
    $this->duplicates = [];
    $this->seenWorkCode = []; // Reset for backward compatibility
    
    // First pass: Collect ALL combinations with their row numbers and data
    foreach ($rows as $index => $row) {
        $workNo = $this->getCellValue($row, 0);
        $code = $this->getCellValue($row, 1);
        
        if (!$workNo || !$code) {
            continue; // Skip invalid rows for duplicate detection
        }
        
        $key = $workNo . '|' . $code;
        $rowNumber = $index + 2;
        
        $amountValue = $this->getCellValue($row, 2);
        $balanceValue = $this->getCellValue($row, 3);
        
        $amount = $amountValue ? floatval(str_replace(',', '', $amountValue)) : 0;
        $balance = $balanceValue ? floatval(str_replace(',', '', $balanceValue)) : 0;
        
        if (!isset($this->allWorkCodeCombinations[$key])) {
            $this->allWorkCodeCombinations[$key] = [];
        }
        
        $this->allWorkCodeCombinations[$key][] = [
            'row' => $rowNumber,
            'work_no' => $workNo,
            'code' => $code,
            'amount' => $amount,
            'balance' => $balance,
            'index' => $index
        ];
    }
    
    // Second pass: Identify which combinations are duplicates
    foreach ($this->allWorkCodeCombinations as $key => $occurrences) {
        if (count($occurrences) > 1) {
            // This combination appears multiple times - mark ALL as duplicates
            $this->duplicateKeys[] = $key;
            
            // Add ALL occurrences to duplicates array
            foreach ($occurrences as $occ) {
                $this->duplicates[] = [
                    'row' => $occ['row'],
                    'work_no' => $occ['work_no'],
                    'code' => $occ['code'],
                    'amount' => $occ['amount'],
                    'balance' => $occ['balance']
                ];
            }
        }
    }
}
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

    $key = $workNo . '|' . $code;

    // Check if this combination is a duplicate (from pre-processing)
    if (in_array($key, $this->duplicateKeys)) {
        // This is a duplicate row - already recorded in $this->duplicates during pre-processing
        // No need to throw exception, just return 'duplicate'
        return 'duplicate';
    }

    // Get payment type data
    $ptype = Ptype::where('code', $code)->first();
    if (!$ptype) {
        throw new Exception("Payment type '{$code}' not found");
    }

    // Get employee data
    $employee = Agents::where('emp_id', $workNo)->first();
    if (!$employee) {
        // Track missing employee
        $this->missingEmployees[] = [
            'row' => $rowNumber,
            'work_no' => $workNo,
            'code' => $code,
            'amount' => $amount,
            'balance' => $balance
        ];
        
        return 'missing_employee'; // Don't throw exception, just return
    }

    // Process the valid row
    if ($ptype->category === 'balance') {
        $this->handleBalanceSchedule($workNo, $code, $amount, $balance, $ptype);
    }
    elseif ($ptype->category === 'loan') {
        $this->handleLoanSchedule($workNo, $code, $amount, $balance, $ptype);
    }

    $this->upsertEmployeeDeduction($workNo, $code, $amount, $balance, $ptype, $employee);
    
    return 'success';
}
    /**
     * Process a single row from Excel
     */
   
public function getDuplicates()
{
    return $this->duplicates;
}

/**
 * Get missing employees collected during import
 */
public function getMissingEmployees()
{
    return $this->missingEmployees;
}

/**
 * Generate Excel file for missing employees and duplicates
 */
public function generateMissingEmployeesReport($missingEmployees, $duplicates)
{
    if (empty($missingEmployees) && empty($duplicates)) {
        return null;
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

    /*
    ==========================
    SHEET 1 : Missing Employees
    ==========================
    */

    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Missing Agents');

    $headers = ['Row Number', 'Work Number', 'Item Code', 'Amount', 'Balance'];

    $col = 'A';
    foreach ($headers as $header) {
        $sheet1->setCellValue($col . '1', $header);
        $sheet1->getStyle($col . '1')->getFont()->setBold(true);
        $col++;
    }

    $rowIndex = 2;

    foreach ($missingEmployees as $employee) {
        $sheet1->setCellValue('A' . $rowIndex, $employee['row']);
        $sheet1->setCellValue('B' . $rowIndex, $employee['work_no']);
        $sheet1->setCellValue('C' . $rowIndex, $employee['code']);
        $sheet1->setCellValue('D' . $rowIndex, $employee['amount']);
        $sheet1->setCellValue('E' . $rowIndex, $employee['balance']);

        $rowIndex++;
    }

    foreach (range('A', 'E') as $col) {
        $sheet1->getColumnDimension($col)->setAutoSize(true);
    }

    /*
    ==========================
    SHEET 2 : Duplicate Records
    ==========================
    */

    if (!empty($duplicates)) {
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Duplicate Exceptions');

        $headers = ['Row Number', 'Work Number', 'Item Code', 'Amount', 'Balance'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet2->setCellValue($col . '1', $header);
            $sheet2->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        $rowIndex = 2;

        // Remove duplicates by row number to avoid showing same row twice
        $uniqueDuplicates = [];
        $seenRows = [];
        foreach ($duplicates as $dup) {
            if (!in_array($dup['row'], $seenRows)) {
                $uniqueDuplicates[] = $dup;
                $seenRows[] = $dup['row'];
            }
        }

        foreach ($uniqueDuplicates as $dup) {
            $sheet2->setCellValue('A' . $rowIndex, $dup['row']);
            $sheet2->setCellValue('B' . $rowIndex, $dup['work_no']);
            $sheet2->setCellValue('C' . $rowIndex, $dup['code']);
            $sheet2->setCellValue('D' . $rowIndex, $dup['amount']);
            $sheet2->setCellValue('E' . $rowIndex, $dup['balance']);

            $rowIndex++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /*
    ==========================
    SAVE FILE
    ==========================
    */

    $filename = 'import_exceptions_' . date('Y-m-d_His') . '.xlsx';
    $filepath = storage_path('app/temp/' . $filename);

    if (!file_exists(storage_path('app/temp'))) {
        mkdir(storage_path('app/temp'), 0755, true);
    }

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

  public function sendEarningsImportationEmail(): void
{
    try {
        // Get aggregated earnings data
        $earningsSummary = $this->getEarningsSummary();

        if (empty($earningsSummary)) {
            Log::warning("No earnings data found for email notification");
            return;
        }

        // Get ALL approval users
        $approvalUsers = User::where('approvelvl', 'YES')
            ->whereNotNull('email')
            ->get();

        if ($approvalUsers->isEmpty()) {
            Log::error("No approval users found with approvelvl = 'YES'");
            throw new \Exception("No approval users configured for notifications");
        }

        // Send to each approver
        foreach ($approvalUsers as $approvalUser) {
            $this->sendImportNotificationEmail(
                $approvalUser->email,
                $approvalUser->name,
                $earningsSummary
            );
        }

        // Insert/update paytracker after all emails sent
        $this->insertPaytracker();

        Log::info("Earnings importation notification sent to {$approvalUsers->count()} approver(s)");

    } catch (\Exception $e) {
        Log::error("Failed to send earnings importation email: " . $e->getMessage());
        throw $e;
    }
}  
private function getEarningsSummary(): array
{
    $summary = EmployeeDeduction::select('pcate', DB::raw('SUM(Amount) as total_amount'))
        ->where('prossty', 'Payment')
        ->where('month', $this->month)
        ->where('year', $this->year)
        ->groupBy('pcate')
        ->orderBy('pcate')
        ->get();
    
    $data = [];
    $grandTotal = 0;
    
    foreach ($summary as $item) {
        $data[] = [
            'category' => $item->pcate,
            'amount' => $item->total_amount
        ];
        $grandTotal += $item->total_amount;
    }
    
    return [
        'items' => $data,
        'grand_total' => $grandTotal
    ];
}

private function getJubiPayAccessToken(): string
{
    $baseUrl    = config('services.jubipay.base_url');
    $username   = config('services.jubipay.username');
    $signinPath = '/api/auth/signin';

    Log::info("getJubiPayAccessToken: Attempting authentication", [
        'url'      => "{$baseUrl}{$signinPath}",
        'username' => $username,
    ]);

    $response = Http::timeout(30)
        ->post("{$baseUrl}{$signinPath}", [
            'username' => $username,
            'password' => config('services.jubipay.password'),
        ]);

    Log::info("getJubiPayAccessToken: Signin response received", [
        'status' => $response->status(),
        'body'   => $response->body(),
    ]);

    if ($response->failed()) {
        Log::error("getJubiPayAccessToken: Authentication failed", [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
        throw new \Exception(
            "JubiPay authentication failed: HTTP {$response->status()} — {$response->body()}"
        );
    }

    $data = $response->json();

    if (empty($data['accessToken'])) {
        Log::error("getJubiPayAccessToken: accessToken missing from response", [
            'response_keys' => array_keys($data ?? []),
        ]);
        throw new \Exception('JubiPay authentication response missing accessToken.');
    }

    Log::info("getJubiPayAccessToken: Token acquired successfully", [
        'token_type' => $data['tokenType']  ?? 'unknown',
        'expires_in' => $data['expires_in'] ?? 'unknown',
        'issued_at'  => $data['issued_at']  ?? 'unknown',
    ]);

    return $data['accessToken'];
}

private function sendImportNotificationEmail(string $email, string $name, array $earningsSummary): void
{
    $subject = "Agents Earnings Import - {$this->month} {$this->year} - Pending Approval";

    Log::info("sendImportNotificationEmail: Initiating import notification email", [
        'recipient' => $email,
        'subject'   => $subject,
    ]);

    try {
        // Step 1: Authenticate with JubiPay
        $accessToken = $this->getJubiPayAccessToken();

        // Step 2: Dispatch email
        $this->dispatchJubiPayImportEmail($accessToken, $email, $name, $subject, $earningsSummary);

        // Step 3: Log success to DB (preserved from original)
        DB::table('email_logs')->insert([
            'recipient' => $email,
            'subject'   => $subject,
            'template'  => 'earnings_import_notification',
            'status'    => 'success',
            'sent_at'   => now(),
        ]);

        Log::info("sendImportNotificationEmail: Import notification email sent successfully", [
            'recipient' => $email,
        ]);

    } catch (\Exception $e) {
        Log::error("sendImportNotificationEmail: Failed for [{$email}]", [
            'error' => $e->getMessage(),
        ]);

        // Log failure to DB (preserved from original)
        DB::table('email_logs')->insert([
            'recipient'     => $email,
            'subject'       => $subject,
            'template'      => 'earnings_import_notification',
            'status'        => 'error',
            'error_message' => $e->getMessage(),
            'sent_at'       => now(),
        ]);

        throw new \Exception("Failed to send notification email to {$email}: " . $e->getMessage());
    }
}


private function dispatchJubiPayImportEmail(string $accessToken, string $email, string $name, string $subject, array $earningsSummary): void
{
    $baseUrl       = config('services.jubipay.base_url');
    $emailEndpoint = config('services.jubipay.email_endpoint');

    $fromEmail = 'no-reply@jubileeinsurance.com';
    $fromName  = 'Corepay';

    Log::info("dispatchJubiPayImportEmail: Preparing payload", [
        'to'       => $email,
        'toName'   => $name,
        'subject'  => $subject,
        'endpoint' => "{$baseUrl}{$emailEndpoint}",
    ]);

    $response = Http::timeout(30)
        ->withToken($accessToken)
        ->asMultipart()
        ->post("{$baseUrl}{$emailEndpoint}", [
            ['name' => 'to',                'contents' => $email],
            ['name' => 'from',              'contents' => $fromEmail],
            ['name' => 'message',           'contents' => $this->getImportEmailBody($name, $earningsSummary)],
            ['name' => 'subject',           'contents' => $subject],
            ['name' => 'toName',            'contents' => $name],
            ['name' => 'fromName',          'contents' => $fromName],
            ['name' => 'sourceApplication', 'contents' => config('services.jubipay.source_application')],
        ]);

    Log::info("dispatchJubiPayImportEmail: Response received", [
        'status' => $response->status(),
        'body'   => $response->body(),
    ]);

    if ($response->failed()) {
        Log::error("dispatchJubiPayImportEmail: Email dispatch failed", [
            'status'    => $response->status(),
            'body'      => $response->body(),
            'recipient' => $email,
        ]);
        throw new \Exception(
            "JubiPay import email dispatch failed: HTTP {$response->status()} — {$response->body()}"
        );
    }

    Log::info("dispatchJubiPayImportEmail: Email successfully dispatched via JubiPay", [
        'recipient' => $email,
        'status'    => $response->status(),
    ]);
}

/**
 * Get HTML email body for import notification
 */
private function getImportEmailBody(string $name, array $earningsSummary): string
{
    // ✅ Escape all string variables before interpolation
    $companyName = htmlspecialchars(
        $this->companydetails['name'] ?? 'Company',
        ENT_QUOTES | ENT_HTML5, 'UTF-8'
    );
    $safeName    = htmlspecialchars($name,           ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeMonth   = htmlspecialchars($this->month,    ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeYear    = htmlspecialchars($this->year,     ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeLoginUrl = htmlspecialchars(url('/login'),  ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeYear2   = (int) date('Y'); // ✅ integer — no escaping needed

    // ✅ Escape each summary row item individually
    $summaryRows = '';
    foreach ($earningsSummary['items'] as $item) {
        $safeCategory      = htmlspecialchars($item['category'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $formattedAmount   = htmlspecialchars(
                                number_format($item['amount'], 2),
                                ENT_QUOTES | ENT_HTML5, 'UTF-8'
                             );
        $summaryRows .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$safeCategory}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>KES {$formattedAmount}</td>
            </tr>
        ";
    }

    // ✅ grandTotal is numeric — escape for consistency
    $grandTotal = htmlspecialchars(
        number_format($earningsSummary['grand_total'], 2),
        ENT_QUOTES | ENT_HTML5, 'UTF-8'
    );

    // ✅ All interpolated values are now escaped
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 700px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2196F3; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .summary-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: white; }
            .summary-table th { background-color: #4CAF50; color: white; padding: 12px; text-align: left; }
            .summary-table td { padding: 10px; border-bottom: 1px solid #ddd; }
            .total-row { background-color: #f0f0f0; font-weight: bold; font-size: 1.1em; }
            .action-button {
                display: inline-block; background-color: #4CAF50; color: white;
                padding: 15px 30px; text-decoration: none; border-radius: 5px;
                margin: 20px 0; font-weight: bold;
            }
            .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            .important { background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>{$companyName}</h2>
                <p>Earnings Importation Notification</p>
            </div>

            <div class='content'>
                <p>Hi {$safeName},</p>

                <p>The importation of Agents earnings for <strong>{$safeMonth} {$safeYear}</strong>
                has been completed by the operator.</p>

                <div class='important'>
                    <strong>⚠️ Action Required:</strong> This importation is pending your verification and approval.
                </div>

                <h3>Earnings Summary:</h3>

                <table class='summary-table'>
                    <thead>
                        <tr>
                            <th>Payment Category</th>
                            <th style='text-align: right;'>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$summaryRows}
                        <tr class='total-row'>
                            <td>TOTAL</td>
                            <td style='text-align: right;'>KES {$grandTotal}</td>
                        </tr>
                    </tbody>
                </table>

                <p style='text-align: center;'>
                    <a href='{$safeLoginUrl}' class='action-button'>Login for Verification &amp; Approval</a>
                </p>

                <p>Please review and approve the imported earnings at your earliest convenience.</p>

                <strong>CorePay</strong><br>
                {$companyName}</p>
            </div>

            <div class='footer'>
                <p>This is an automated notification from the payroll system.</p>
                <p>&copy; {$safeYear2} {$companyName}. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Get plain text email body for import notification
 */
private function getImportEmailBodyPlainText(string $name, array $earningsSummary): string
{
    $companyName = $this->companydetails['name'] ?? 'Company';
    $loginUrl = url('/login');
    
    // Build summary text
    $summaryText = '';
    foreach ($earningsSummary['items'] as $item) {
        $formattedAmount = number_format($item['amount'], 2);
        $summaryText .= "{$item['category']}: KES {$formattedAmount}\n";
    }
    
    $grandTotal = number_format($earningsSummary['grand_total'], 2);
    
    return "
Hi {$name},

The importation of Agents earnings for {$this->month} {$this->year} has been completed by the operator.

EARNINGS SUMMARY:
-----------------
{$summaryText}
-----------------
TOTAL: KES {$grandTotal}

ACTION REQUIRED:
This importation is pending your verification and approval.

Please click the link below to login for verification and approval:
{$loginUrl}


CorePay
{$companyName}

---
This is an automated notification from the payroll system.
© " . date('Y') . " {$companyName}. All rights reserved.
    ";
}

/**
 * Insert record to paytracker table
 */
private function insertPaytracker(): void
{
    try {
        $selectedPayrolls = session('allowedPayroll', []);
        $userId = session('user_id') ?? Auth::id();
        
        if (empty($selectedPayrolls)) {
            Log::warning("No payroll types found in session");
            return;
        }
        
        // Convert array to comma-separated string if it's an array
        $paytypeValue = is_array($selectedPayrolls) ? implode(',', $selectedPayrolls) : $selectedPayrolls;
        
        Paytracker::updateOrCreate(
            [
                'month'   => $this->month,
                'year'    => $this->year,
                'paytype' => $paytypeValue,
            ],
            [
                'sstatus' => 'PENDING',
                'creator' => $userId,
            ]
        );
        
        Log::info("Paytracker record created/updated", [
            'month'   => $this->month,
            'year'    => $this->year,
            'status'  => 'PENDING',
            'paytype' => $paytypeValue,
        ]);
        
    } catch (\Exception $e) {
        Log::error("Failed to insert/update paytracker record: " . $e->getMessage());
        throw $e;
    }
}
}