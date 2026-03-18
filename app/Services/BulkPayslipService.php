<?php

namespace App\Services;

use App\Models\Payhouse;
use App\Models\Agents;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use setasign\Fpdi\Fpdi;

class BulkPayslipService
{
    protected $payslipService;
    protected $savePath;
    protected $emailConfig;
    protected $companydetails;

    public function __construct(PayslipService $payslipService)
    {
        $this->payslipService = $payslipService;
    
        $this->loadEmailConfig();
        $this->loadCstructure();
    }

    /**
     * Load email configuration from database
     */
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
     * Set the save path for PDFs
     */
    public function setSavePath(string $path): void
    {
        $this->savePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0755, true);
        }
    }

    /**
     * Get all distinct WorkNos for a period
     */
    public function getEmployeesForPeriod(string $month, string $year): array
    {
        return Payhouse::where('month', $month)
            ->where('year', $year)
            ->distinct()
            ->pluck('WorkNo')
            ->toArray();
    }

    /**
     * Generate bulk payslips with optional email sending
     */
    public function generateBulkPayslips(string $month, string $year, string $jobId = null, bool $sendEmail = false): array
    {
        set_time_limit(0);

        $workNos = $this->getEmployeesForPeriod($month, $year);
        $total = count($workNos);

        if ($jobId) {
            cache()->put("bulk_payslip_progress_{$jobId}", [
                'progress' => 0,
                'processed' => 0,
                'total' => $total,
                'success' => 0,
                'failed' => 0,
                'emailed' => 0,
                'email_failed' => 0,
                'current_employee' => null,
                'message' => "Starting bulk payslip generation..."
            ], now()->addHours(1));
        }

        $results = [
            'total' => $total,
            'success' => 0,
            'failed' => 0,
            'emailed' => 0,
            'email_failed' => 0,
            'files' => [],
            'errors' => [],
            'email_errors' => []
        ];

        Log::info("Starting bulk payslip generation for {$month} {$year}. Total: {$total}, Send Email: " . ($sendEmail ? 'Yes' : 'No'));

        $processed = 0;

        foreach ($workNos as $workNo) {
            try {
                // Generate payslip
                $payslipData = $this->generateSinglePayslip($workNo, $month, $year, $sendEmail);
                
                $results['success']++;
                $results['files'][] = [
                    'workno' => $workNo,
                    'filename' => basename($payslipData['filepath']),
                    'path' => $payslipData['filepath'],
                    'email' => $payslipData['email'] ?? null,
                    'emailed' => $payslipData['emailed'] ?? false
                ];

                // Send email if requested
                if ($sendEmail && $payslipData['can_email']) {
                    try {
                        $this->sendPayslipEmail(
                            $payslipData['email'],
                            $payslipData['employee_name'],
                            $payslipData['protected_filepath'],
                            $payslipData['filename'],
                            $month,
                            $year
                        );
                        
                        $results['emailed']++;
                        $results['files'][count($results['files']) - 1]['emailed'] = true;
                        
                    } catch (\Exception $e) {
                        $results['email_failed']++;
                        $results['email_errors'][] = [
                            'workno' => $workNo,
                            'email' => $payslipData['email'],
                            'error' => $e->getMessage()
                        ];
                        Log::error("Email failed for {$workNo} ({$payslipData['email']}): {$e->getMessage()}");
                    }
                }

            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'workno' => $workNo,
                    'error' => $e->getMessage()
                ];
                Log::error("Payslip generation failed for {$workNo}: {$e->getMessage()}");
            }

            $processed++;

            // Update progress
            if ($jobId) {
                $percent = round(($processed / $total) * 100);

                cache()->put("bulk_payslip_progress_{$jobId}", [
                    'progress' => $percent,
                    'processed' => $processed,
                    'total' => $total,
                    'success' => $results['success'],
                    'failed' => $results['failed'],
                    'emailed' => $results['emailed'],
                    'email_failed' => $results['email_failed'],
                    'current_employee' => $workNo,
                    'message' => "Processing {$processed} / {$total}" . ($sendEmail ? " (Emailing)" : "")
                ], now()->addHours(1));
            }
        }

        Log::info("Generation complete. Success: {$results['success']}, Failed: {$results['failed']}, Emailed: {$results['emailed']}, Email Failed: {$results['email_failed']}");

        return $results;
    }

    /**
     * Generate single payslip with password protection
     */
    private function generateSinglePayslip(string $workNo, string $month, string $year, bool $needsProtection = false): array
    {
        // Get employee details with email and KRA PIN
        $employee = Agents::where('emp_id', $workNo)
            ->leftJoin('registration', 'tblemployees.emp_id', '=', 'registration.empid')
            ->select(
                DB::raw("(tblemployees.FirstName) AS fullname"),
                'tblemployees.EmailId',
                'registration.kra'
            )
            ->first();

        if (!$employee) {
            throw new \Exception("Employee {$workNo} not found");
        }

        $employeeName = $this->sanitizeFilename($employee->fullname);
        $email = $employee->EmailId;
        $kraPin = $employee->kra;

        // Generate PDF data
        $pdfData = $this->payslipService->generatePayslip($workNo, $month, $year);

        // Create filename
        $filename = "{$workNo}.pdf";
        $filePath = $this->savePath . $filename;

        // Save unprotected PDF
        file_put_contents($filePath, $pdfData);

        if (!empty($kraPin)) {
    $protectedFilePath = $this->savePath . "Protected_{$filename}";
    $this->protectPdfWithPassword($filePath, $protectedFilePath, $kraPin);
    $result['protected_filepath'] = $protectedFilePath;
    
    // Replace the unprotected file with protected version
    // so ZIP picks up the protected one
    copy($protectedFilePath, $filePath);
    
    // Clean up the separate protected copy
    unlink($protectedFilePath);
}

        $result = [
            'filepath' => $filePath,
            'filename' => $filename,
            'employee_name' => $employee->fullname,
            'email' => $email,
            'can_email' => !empty($email) && !empty($kraPin),
            'emailed' => false
        ];

        // Create password-protected version if needed for email
        if ($needsProtection && !empty($kraPin)) {
            $protectedFilePath = $this->savePath . "Protected_{$filename}";
            $this->protectPdfWithPassword($filePath, $protectedFilePath, $kraPin);
            $result['protected_filepath'] = $protectedFilePath;
        }

        return $result;
    }

    /**
     * Protect PDF with password using FPDI
     */
    private function protectPdfWithPassword(string $sourcePath, string $destPath, string $password): void
{
    try {
        // Create FPDI instance with TCPDF
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        
        // Set document information
        $pdf->SetCreator('Payroll System');
        $pdf->SetAuthor('HR Department');
        $pdf->SetTitle('Payslip');
        
        // Set password protection
        $pdf->SetProtection(
            ['print', 'copy'], // Permissions
            $password,         // User password
            null,              // Owner password
            0,                 // Encryption mode
            null
        );
        
        // Import the existing PDF
        $pageCount = $pdf->setSourceFile($sourcePath);
        
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);
        }
        
        // Output to file
        $pdf->Output($destPath, 'F');
        
    } catch (\Exception $e) {
        Log::error("PDF protection failed: {$e->getMessage()}");
        
        // Fallback: Copy unprotected file
        copy($sourcePath, $destPath);
    }
}
    /**
     * Send payslip via email
     */
    private function sendPayslipEmail(string $email, string $employeeName, string $pdfPath, string $filename, string $month, string $year): void
{
    $mail = new PHPMailer(true);

    try {
        Log::info("Attempting to send email to: {$email}", $this->emailConfig);
        
        // Server settings
        $mail->SMTPDebug = 2; // Enable debug to see detailed error
        $mail->Debugoutput = function($str, $level) {
            Log::info("PHPMailer [Level {$level}]: {$str}");
        };
        
        $mail->isSMTP();
        $mail->Host = $this->emailConfig['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->emailConfig['username'];
        $mail->Password = $this->emailConfig['password'];
        
        // Fix: Set encryption constant based on config value
        $encryption = strtolower($this->emailConfig['encryption'] ?? '');
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = false;
        }
        
        $mail->Port = intval($this->emailConfig['port']);
        
        // Connection timeout
        $mail->Timeout = 30;
        $mail->SMTPKeepAlive = true;
        
        // Recipients
        $fromEmail = $this->emailConfig['from_email'] ?? $this->emailConfig['username'];
        $fromName = $this->emailConfig['from_name'] ?? 'Agents Payroll';
        
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email, $employeeName);
        
        // Optional: Add reply-to
        $mail->addReplyTo($fromEmail, $fromName);

        // Attachments
        if (file_exists($pdfPath)) {
            $mail->addAttachment($pdfPath, $filename);
        } else {
            throw new \Exception("PDF file not found: {$pdfPath}");
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Payslip for {$month} {$year}";
        $mail->Body = $this->getEmailBody($employeeName, $month, $year);
        $mail->AltBody = $this->getEmailBodyPlainText($employeeName, $month, $year);
        
        // Send email
        if (!$mail->send()) {
            throw new \Exception("Send failed: {$mail->ErrorInfo}");
        }
        
        Log::info("Payslip email sent successfully to {$email} ({$employeeName})");
        
    } catch (Exception $e) {
        Log::error("Email send failed for {$email}:", [
            'error' => $e->getMessage(),
            'mail_error' => $mail->ErrorInfo ?? 'N/A',
            'config' => [
                'host' => $this->emailConfig['host'],
                'username' => $this->emailConfig['username'],
                'port' => $this->emailConfig['port'],
                'encryption' => $this->emailConfig['encryption']
            ]
        ]);
        
        throw new \Exception("Failed to send email to {$email}: " . $e->getMessage());
    }
}

    /**
     * Get HTML email body
     */
    private function getEmailBody(string $employeeName, string $month, string $year): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
                .important { background-color: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>{$companyName}</h2>
                    <p>Payslip Notification</p>
                </div>
                
                <div class='content'>
                    <p>Hi {$employeeName},</p>
                    
                    <p>Your payslip for <strong>{$month} {$year}</strong> is now available.</p>
                    
                    <p>Please find your payslip attached to this email as a PDF document.</p>
                    
                    <div class='important'>
                        <strong>⚠️ Important:</strong><br>
                        <strong>Password:</strong> Your KRA PIN number
                    </div>
                    
                    <p>If you have any questions regarding your payslip, please contact the HR department.</p>
                    
                    <p>Best regards,<br>
                    <strong>Agents Payroll</strong><br>
                    {$companyName}</p>
                </div>
                
                <div class='footer'>
                    <p>This is an automated email. Please do not reply to this message.</p>
                    <p>&copy; " . date('Y') . " {$companyName}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get plain text email body
     */
    private function getEmailBodyPlainText(string $employeeName, string $month, string $year): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        
        return "
Dear {$employeeName},

Your payslip for {$month} {$year} is now available.

Please find your payslip attached to this email as a PDF document.

IMPORTANT: This PDF is password-protected for your security.
Password: Your KRA PIN number

If you have any questions regarding your payslip, please contact the HR department.

Best regards,
HR Department
{$companyName}

---
This is an automated email. Please do not reply to this message.
© " . date('Y') . " {$companyName}. All rights reserved.
        ";
    }

    /**
     * Sanitize filename
     */
    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $filename);
        $filename = str_replace(' ', '_', $filename);
        return substr($filename, 0, 100);
    }

    /**
     * Get available periods
     */
    public function getAvailablePeriods(): array
    {
        return Payhouse::select('month', 'year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderByRaw("FIELD(month, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')")
            ->get()
            ->map(function($item) {
                return [
                    'value' => $item->month . $item->year,
                    'label' => $item->month . ' ' . $item->year
                ];
            })
            ->toArray();
    }
}