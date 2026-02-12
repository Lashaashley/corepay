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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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

    public function sendEarningsImportationEmail(): void
{
    try {
        // Get aggregated earnings data
        $earningsSummary = $this->getEarningsSummary();
        
        if (empty($earningsSummary)) {
            Log::warning("No earnings data found for email notification");
            return;
        }
        
        // Get approval user
        $approvalUser = User::where('approvelvl', 'YES')
            ->whereNotNull('email')
            ->first();
        
        if (!$approvalUser) {
            Log::error("No approval user found with approvelvl = 'YES'");
            throw new \Exception("No approval user configured for notifications");
        }
        
        // Send the email
        $this->sendImportNotificationEmail(
            $approvalUser->email,
            $approvalUser->name,
            $earningsSummary
        );
        
        // Insert record to paytracker
        $this->insertPaytracker();
        
        Log::info("Earnings importation notification sent successfully to {$approvalUser->email}");
        
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

/**
 * Send importation notification email
 */
private function sendImportNotificationEmail(string $email, string $name, array $earningsSummary): void
{
    $mail = new PHPMailer(true);

    try {
        Log::info("Sending earnings importation notification to: {$email}");
        
        // Server settings
        $mail->SMTPDebug = 0; // Disable debug output for this email
        $mail->isSMTP();
        $mail->Host = $this->emailConfig['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->emailConfig['username'];
        $mail->Password = $this->emailConfig['password'];
        
        // Set encryption
        $encryption = strtolower($this->emailConfig['encryption'] ?? '');
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = false;
        }
        
        $mail->Port = intval($this->emailConfig['port']);
        $mail->Timeout = 30;
        
        // Recipients
        $fromEmail = $this->emailConfig['from_email'] ?? $this->emailConfig['username'];
        $fromName = $this->emailConfig['from_name'] ?? 'Core Pay';
        
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email, $name);
        $mail->addReplyTo($fromEmail, $fromName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Agents Earnings Import - {$this->month} {$this->year} - Pending Approval";
        $mail->Body = $this->getImportEmailBody($name, $earningsSummary);
        $mail->AltBody = $this->getImportEmailBodyPlainText($name, $earningsSummary);
        
        // Send email
        if (!$mail->send()) {
            throw new \Exception("Send failed: {$mail->ErrorInfo}");
        }
        
        Log::info("Import notification email sent successfully to {$email}");
        
    } catch (Exception $e) {
        Log::error("Import notification email failed for {$email}:", [
            'error' => $e->getMessage(),
            'mail_error' => $mail->ErrorInfo ?? 'N/A'
        ]);
        
        throw new \Exception("Failed to send notification email to {$email}: " . $e->getMessage());
    }
}

/**
 * Get HTML email body for import notification
 */
private function getImportEmailBody(string $name, array $earningsSummary): string
{
    $companyName = $this->companydetails['name'] ?? 'Company';
    $loginUrl = url('/login'); // Adjust this to your actual login route
    
    // Build summary table rows
    $summaryRows = '';
    foreach ($earningsSummary['items'] as $item) {
        $formattedAmount = number_format($item['amount'], 2);
        $summaryRows .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$item['category']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>KES {$formattedAmount}</td>
            </tr>
        ";
    }
    
    $grandTotal = number_format($earningsSummary['grand_total'], 2);
    
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
                display: inline-block; 
                background-color: #4CAF50; 
                color: white; 
                padding: 15px 30px; 
                text-decoration: none; 
                border-radius: 5px; 
                margin: 20px 0;
                font-weight: bold;
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
                <p>Hi {$name},</p>
                
                <p>The importation of Agents earnings for <strong>{$this->month} {$this->year}</strong> has been completed by the operator.</p>
                
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
                    <a href='{$loginUrl}' class='action-button'>Login for Verification & Approval</a>
                </p>
                
                <p>Please review and approve the imported earnings at your earliest convenience.</p>
                
                <p>Best regards,<br>
                <strong>Payroll System</strong><br>
                {$companyName}</p>
            </div>
            
            <div class='footer'>
                <p>This is an automated notification from the payroll system.</p>
                <p>&copy; " . date('Y') . " {$companyName}. All rights reserved.</p>
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

Best regards,
Payroll System
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
        
        Paytracker::create([
            'month' => $this->month,
            'year' => $this->year,
            'sstatus' => 'PENDING',
            'paytype' => $paytypeValue,
            'creator' => $userId
        ]);
        
        Log::info("Paytracker record created", [
            'month' => $this->month,
            'year' => $this->year,
            'status' => 'PENDING',
            'paytype' => $paytypeValue
        ]);
        
    } catch (\Exception $e) {
        Log::error("Failed to insert paytracker record: " . $e->getMessage());
        throw $e;
    }
}
}