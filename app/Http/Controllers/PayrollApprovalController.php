<?php

namespace App\Http\Controllers;

use App\Models\Paytracker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\DB;

class PayrollApprovalController extends Controller
{
    private $emailConfig;
    private $companydetails;

    public function __construct()
    {
        $this->loadEmailConfig();
        $this->loadCompanyDetails();
    }

    /**
     * Approve payroll and send notification
     */
    public function approvePayroll(Request $request)
{
    try {
        $month = $request->input('month');
        $year = $request->input('year');
        $approverId = Auth::id();

        Log::info("Approval request received", [
            'month'       => $month,
            'year'        => $year,
            'approver_id' => $approverId
        ]);

        if (!$month || !$year) {
            return response()->json([
                'success' => false,
                'message' => 'Month and year are required'
            ], 400);
        }

        $paytracker = Paytracker::where('month', $month)
            ->where('year', $year)
            ->where('sstatus', 'PENDING')
            ->first();

        if (!$paytracker) {
            Log::warning("No paytracker found", ['month' => $month, 'year' => $year]);

            $anyRecord = Paytracker::where('month', $month)->where('year', $year)->get();
            Log::info("All records for this period", [
                'count'   => $anyRecord->count(),
                'records' => $anyRecord->toArray()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No pending payroll found for the selected period'
            ], 404);
        }

        Log::info("Paytracker found", [
            'id'             => $paytracker->id,
            'current_status' => $paytracker->sstatus,
            'creator'        => $paytracker->creator
        ]);

        $updated = DB::table('paytracker')
            ->where('id', $paytracker->id)
            ->update([
                'sstatus'     => 'APPROVED',
                'approver'    => $approverId,
                'approved_at' => now()
            ]);

        Log::info("Update result", [
            'rows_affected' => $updated,
            'paytracker_id' => $paytracker->id
        ]);

        $paytracker->refresh();

        // Get the approver who just acted
        $approverUser = User::find($approverId);

        // 1. Notify the creator
        $creatorUser = User::where('id', $paytracker->creator)->whereNotNull('email')->first();
        if ($creatorUser) {
            $this->sendApprovalNotificationEmail(
                $creatorUser->email,
                $creatorUser->name,
                $month,
                $year,
                $paytracker->paytype
            );
        } else {
            Log::warning("Creator user not found or has no email", [
                'creator_id' => $paytracker->creator
            ]);
        }

        // 2. Notify the other approvers that this has already been handled
        $this->sendApprovalFollowUpToOtherApprovers(
            $approverId,
            $approverUser->name ?? 'A colleague',
            $month,
            $year,
            $paytracker->paytype
        );

        return response()->json([
            'success' => true,
            'message' => "Payroll for {$month} {$year} has been approved successfully"
        ]);

    } catch (\Exception $e) {
        Log::error("Payroll approval failed: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to approve payroll: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Load email configuration
     */
    private function loadEmailConfig(): void
    {
        try {
            $config = \App\Models\Email::first();
            
            if ($config) {
                $this->emailConfig = [
                    'host' => $config->host ?? config('mail.mailers.smtp.host'),
                    'username' => $config->username ?? config('mail.mailers.smtp.username'),
                    'password' => $config->password ?? config('mail.mailers.smtp.password'),
                    'port' => $config->port ?? config('mail.mailers.smtp.port'),
                    'encryption' => $config->encryption ?? config('mail.mailers.smtp.encryption'),
                    'from_email' => $config->from_email ?? config('mail.from.address'),
                    'from_name' => $config->from_name ?? config('mail.from.name'),
                ];
            } else {
                $this->emailConfig = [
                    'host' => config('mail.mailers.smtp.host'),
                    'username' => config('mail.mailers.smtp.username'),
                    'password' => config('mail.mailers.smtp.password'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'from_email' => config('mail.from.address'),
                    'from_name' => config('mail.from.name'),
                ];
            }
        } catch (\Exception $e) {
            Log::error("Failed to load email config: " . $e->getMessage());
            
            $this->emailConfig = [
                'host' => config('mail.mailers.smtp.host'),
                'username' => config('mail.mailers.smtp.username'),
                'password' => config('mail.mailers.smtp.password'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from_email' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ];
        }
    }

    /**
     * Load company details
     */
    private function loadCompanyDetails(): void
    {
        try {
            $company = \App\Models\Structure::first();
            
            if ($company) {
                $this->companydetails = [
                    'name' => $company->name ?? config('app.name'),
                ];
            } else {
                $this->companydetails = [
                    'name' => config('app.name', 'Company'),
                ];
            }
        } catch (\Exception $e) {
            Log::error("Failed to load company details: " . $e->getMessage());
            
            $this->companydetails = [
                'name' => config('app.name', 'Company'),
            ];
        }
    }


    private function sendApprovalFollowUpToOtherApprovers(
    int $approverId,
    string $approverName,
    string $month,
    string $year,
    string $paytype
): void {
    // Fetch all other approvers (excluding the one who just approved)
    $otherApprovers = User::where('approvelvl', 'YES')
        ->whereNotNull('email')
        ->where('id', '!=', $approverId)
        ->get();

    if ($otherApprovers->isEmpty()) {
        Log::info("No other approvers to notify for follow-up");
        return;
    }

    // Retrieve the original subject from email_logs for this import
    $originalLog = DB::table('email_logs')
        ->where('template', 'earnings_import_notification')
        ->where('status', 'success')
        ->where('subject', 'LIKE', "%{$month} {$year}%")
        ->latest('sent_at')
        ->first();

    // Fall back gracefully if no log found
    $originalSubject = $originalLog?->subject
        ?? "Agents Earnings Import - {$month} {$year} - Pending Approval";

    // Re: prefix for inbox threading
    $followUpSubject = "Re: {$originalSubject}";

    foreach ($otherApprovers as $approver) {
        $this->sendApprovalFollowUpEmail(
            $approver->email,
            $approver->name,
            $approverName,
            $month,
            $year,
            $paytype,
            $followUpSubject
        );
    }

    Log::info("Follow-up sent to {$otherApprovers->count()} other approver(s)", [
        'subject' => $followUpSubject
    ]);
}

private function sendApprovalFollowUpEmail(
    string $email,
    string $name,
    string $approverName,
    string $month,
    string $year,
    string $paytype,
    string $subject
): void {
    $mail = new PHPMailer(true);

    try {
        Log::info("Sending approval follow-up to: {$email}");

        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host     = $this->emailConfig['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->emailConfig['username'];
        $mail->Password = $this->emailConfig['password'];

        $encryption = strtolower($this->emailConfig['encryption'] ?? '');
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = false;
        }

        $mail->Port    = intval($this->emailConfig['port']);
        $mail->Timeout = 30;

        $fromEmail = $this->emailConfig['from_email'] ?? $this->emailConfig['username'];
        $fromName  = $this->emailConfig['from_name'] ?? 'Core Pay';

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email, $name);
        $mail->addReplyTo($fromEmail, $fromName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $this->getFollowUpEmailBody($name, $approverName, $month, $year, $paytype);
        $mail->AltBody = $this->getFollowUpEmailBodyPlainText($name, $approverName, $month, $year, $paytype);

        if (!$mail->send()) {
            throw new \Exception("Send failed: {$mail->ErrorInfo}");
        }

        DB::table('email_logs')->insert([
            'recipient' => $email,
            'subject'   => $subject,
            'template'  => 'earnings_approval_followup',
            'status'    => 'success',
            'sent_at'   => now(),
        ]);

        Log::info("Approval follow-up sent successfully to {$email}");

    } catch (Exception $e) {
        Log::error("Approval follow-up email failed for {$email}:", [
            'error'      => $e->getMessage(),
            'mail_error' => $mail->ErrorInfo ?? 'N/A'
        ]);

        DB::table('email_logs')->insert([
            'recipient'     => $email,
            'subject'       => $subject,
            'template'      => 'earnings_approval_followup',
            'status'        => 'error',
            'error_message' => $e->getMessage(),
            'sent_at'       => now(),
        ]);

        // Don't throw — follow-up failure shouldn't roll back the approval
    }
}
private function getFollowUpEmailBody(
    string $name,
    string $approverName,
    string $month,
    string $year,
    string $paytype
): string {
    return "
        <p>Hi {$name},</p>
        <p>This is to let you know that the <strong>{$month} {$year}</strong> 
           earnings import (<strong>{$paytype}</strong>) has already been reviewed 
           and approved by <strong>{$approverName}</strong>.</p>
        <p>No further action is required from your side.</p>
        <p>Thank you,<br>Core Pay</p>
    ";
}

private function getFollowUpEmailBodyPlainText(
    string $name,
    string $approverName,
    string $month,
    string $year,
    string $paytype
): string {
    return "Hi {$name},\n\n"
        . "This is to let you know that the {$month} {$year} earnings import "
        . "({$paytype}) has already been reviewed and approved by {$approverName}.\n\n"
        . "No further action is required from your side.\n\n"
        . "Thank you,\nCore Pay";
}

    /**
     * Send approval notification email to creator
     */
    private function sendApprovalNotificationEmail(
        string $email, 
        string $name, 
        string $month, 
        string $year,
        string $paytype
    ): void
    {
        $mail = new PHPMailer(true);

        try {
            Log::info("Sending approval notification to creator: {$email}");
            
            if (empty($this->emailConfig['host']) || empty($this->emailConfig['username'])) {
                throw new \Exception("Invalid email configuration");
            }
            
            // Server settings
            $mail->SMTPDebug = 0;
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
            $fromName = $this->emailConfig['from_name'] ?? 'Agents Payroll';
            
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($email, $name);
            $mail->addReplyTo($fromEmail, $fromName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Payroll Approved - {$month} {$year}";
            $mail->Body = $this->getApprovalEmailBody($name, $month, $year, $paytype);
            $mail->AltBody = $this->getApprovalEmailBodyPlainText($name, $month, $year, $paytype);
            
            // Send email
            if (!$mail->send()) {
                throw new \Exception("Send failed: {$mail->ErrorInfo}");
            }
            
            Log::info("Approval notification email sent successfully to {$email}");
            
        } catch (Exception $e) {
            Log::error("Approval notification email failed for {$email}:", [
                'error' => $e->getMessage(),
                'mail_error' => $mail->ErrorInfo ?? 'N/A'
            ]);
            
            // Don't throw - we don't want to fail the approval if email fails
        }
    }

    /**
     * Get HTML email body for approval notification
     */
    private function getApprovalEmailBody(string $name, string $month, string $year, string $paytype): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $dashboardUrl = url('/dashboard'); // Adjust to your actual dashboard route
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .success-box { 
                    background-color: #d4edda; 
                    border: 1px solid #c3e6cb; 
                    color: #155724; 
                    padding: 15px; 
                    border-radius: 5px; 
                    margin: 20px 0; 
                    text-align: center;
                }
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
                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                    background: white;
                }
                .info-table td {
                    padding: 10px;
                    border: 1px solid #ddd;
                }
                .info-table td:first-child {
                    font-weight: bold;
                    background-color: #f0f0f0;
                    width: 40%;
                }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>{$companyName}</h2>
                    <p>Payroll Approval Notification</p>
                </div>
                
                <div class='content'>
                    <p>Hi {$name},</p>
                    
                    <div class='success-box'>
                        <h3 style='margin: 0; color: #155724;'>
                            <i style='font-size: 24px;'>✓</i> Payroll Approved!
                        </h3>
                    </div>
                    
                    <p>The payroll for <strong>{$month} {$year}</strong> has been approved and you can now proceed to process the payroll.</p>
                    
                    <table class='info-table'>
                        <tr>
                            <td>Period</td>
                            <td>{$month} {$year}</td>
                        </tr>
                        <tr>
                            <td>Payroll Type</td>
                            <td>{$paytype}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><span style='color: #4CAF50; font-weight: bold;'>APPROVED</span></td>
                        </tr>
                        <tr>
                            <td>Next Step</td>
                            <td>Process Payroll</td>
                        </tr>
                    </table>
                    
                    <p style='text-align: center;'>
                        <a href='{$dashboardUrl}' class='action-button'>Go to Dashboard</a>
                    </p>
                    
                    <p>Please proceed with the payroll processing at your earliest convenience.</p>
                    
                    
                    <strong>Core Pay</strong><br>
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
     * Get plain text email body for approval notification
     */
    private function getApprovalEmailBodyPlainText(string $name, string $month, string $year, string $paytype): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $dashboardUrl = url('/dashboard');
        
        return "
Hi {$name},

✓ PAYROLL APPROVED

The payroll for {$month} {$year} has been approved and you can now proceed to process the payroll.

DETAILS:
-----------------
Period: {$month} {$year}
Payroll Type: {$paytype}
Status: APPROVED
Next Step: Process Payroll

Please proceed with the payroll processing at your earliest convenience.

Access your dashboard here:
{$dashboardUrl}

Best regards,
Core Pay
{$companyName}

---
This is an automated notification from the payroll system.
© " . date('Y') . " {$companyName}. All rights reserved.
        ";
    }
}