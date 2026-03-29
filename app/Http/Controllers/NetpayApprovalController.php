<?php

namespace App\Http\Controllers;

use App\Models\Paytracker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NetpayApprovalController extends Controller
{
    private $emailConfig;
    private $companydetails;

    public function __construct()
    {
        $this->loadEmailConfig();
        $this->loadCompanyDetails();
    }

    // ✅ In your controller, validate $month and $year before use
private function validatePeriodInputs(string $month, string $year): void
{
    $validMonths = [
        'January','February','March','April','May','June',
        'July','August','September','October','November','December'
    ];

    if (!in_array($month, $validMonths, true)) {
        throw new \InvalidArgumentException('Invalid month value.');
    }

    if (!preg_match('/^\d{4}$/', $year) || (int)$year < 2000 || (int)$year > 2100) {
        throw new \InvalidArgumentException('Invalid year value.');
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


    /**
     * Notify approver that netpay is ready for approval
     */
    public function notifyApprover(Request $request)
    {
        try {
            $request->validate([
                'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                'year' => 'required|digits:4|integer|min:2000|max:2100',
                ]);

            $month = ucfirst(strtolower($request->month)); // normalize
            $year  = (int) $request->year;
            $userId = Auth::id();

            // Validate inputs
            if (!$month || !$year) {
                return response()->json([
                    'success' => false,
                    'message' => 'Month and year are required'
                ], 400);
            }

            // Get allowed payroll IDs from session
            $allowedPayrollIds = session('allowedPayroll', []);
            
            if (empty($allowedPayrollIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payroll types found in session'
                ], 400);
            }

            // Find the paytracker record
            $paytracker = Paytracker::where('month', $month)
                ->where('year', $year)
                ->where('sstatus', 'APPROVED')
                ->first();

            if (!$paytracker) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payroll must be approved before submitting netpay for approval'
                ], 404);
            }

            // Check if already submitted
            if ($paytracker->netpay_status === 'PENDING_APPROVAL') {
                return response()->json([
                    'success' => false,
                    'message' => 'Netpay approval is already pending'
                ], 409);
            }

            // Calculate total netpay and employee count
            $netpayData = $this->calculateNetpayTotals($month, $year, $allowedPayrollIds);

            if ($netpayData['total_netpay'] <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No netpay data found. Please run Auto Calculate first.'
                ], 404);
            }

            // Update paytracker
            $paytracker->update([
                'total_netpay' => $netpayData['total_netpay'],
                'employee_count' => $netpayData['employee_count'],
                'netpay_status' => 'PENDING_APPROVAL',
                'netpay_submitted_at' => now()
            ]);

            Log::info("Netpay submitted for approval", [
                'month' => $month,
                'year' => $year,
                'total_netpay' => $netpayData['total_netpay'],
                'employee_count' => $netpayData['employee_count'],
                'submitted_by' => $userId
            ]);

            // Log audit trail
            logAuditTrail(
                $userId,
                'SUBMIT',
                'netpay_approval',
                "{$month}_{$year}",
                null,
                null,
                [
                    'action' => 'netpay_submitted_for_approval',
                    'month' => $month,
                    'year' => $year,
                    'total_netpay' => $netpayData['total_netpay'],
                    'employee_count' => $netpayData['employee_count'],
                    'allowed_payrolls' => $allowedPayrollIds
                ]
            );

            // Get approver and send notification
            $approver = User::where('approvelvl', 'YES')
                ->whereNotNull('email')
                ->get();

            if (!$approver) {
                Log::warning("No approver found for netpay notification");
            } else {
            $this->validatePeriodInputs($month, $year);
            foreach ($approver as $approvalUser) {
                $email = filter_var($approvalUser->email, FILTER_VALIDATE_EMAIL);
                $name  = trim($approvalUser->name);
                if (!$email) {
                    throw new \Exception('Invalid email detected');
                    }
                $this->sendNetpayApprovalEmail(
    $email,
    $this->cleanString($name),
    $this->cleanString($month),
    (string) $year,
    $netpayData['total_netpay'],
    $netpayData['employee_count'],
    $allowedPayrollIds
);
            }
            }

            return response()->json([
                'success' => true,
                'message' => "Netpay submitted for approval. Total: KES " . number_format($netpayData['total_netpay'], 2),
                'data' => $netpayData
            ]);

        } catch (\Exception $e) {
            Log::error("Netpay notification failed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to notify approver: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate netpay totals
     */
    private function calculateNetpayTotals(string $month, string $year, array $allowedPayrollIds): array
    {
        // Get employees based on allowed payroll types
        $employees = DB::table('payhouse as p')
            ->select('p.WorkNo')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->whereIn('r.payrolty', $allowedPayrollIds)
            ->distinct()
            ->pluck('WorkNo')
            ->toArray();

        // Calculate total netpay
        $totalNetpay = DB::table('payhouse')
            ->where('month', $month)
            ->where('year', $year)
            ->where('pname', 'NET PAY')
            ->where('pcategory', 'NET')
            ->whereIn('WorkNo', $employees)
            ->sum('tamount');

        return [
            'total_netpay' => $totalNetpay,
            'employee_count' => count($employees)
        ];
    }

    /**
     * Send netpay approval notification email
     */
    private function sendNetpayApprovalEmail(
        string $email,
        string $name,
        string $month,
        string $year,
        float $totalNetpay,
        int $employeeCount,
        array $allowedPayrollIds
    ): void
    {
        $mail = new PHPMailer(true);

        try {
            Log::info("Sending netpay approval notification to: {$email}");
            
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
            $mail->Subject = "Netpay Approval Required - {$month} {$year}";
            $mail->Body = $this->getNetpayApprovalEmailBody($name, $month, $year, $totalNetpay, $employeeCount, $allowedPayrollIds); //line 328
            $mail->AltBody = $this->getNetpayApprovalEmailBodyPlainText($name, $month, $year, $totalNetpay, $employeeCount);
            
            // Send email
            if (!$mail->send()) {
                throw new \Exception("Send failed: {$mail->ErrorInfo}");
            }

            DB::table('email_logs')->insert([
            'recipient' => $email,
            'subject'   => $mail->Subject,
            'template'  => 'netpay_approval_notification',
            'status'    => 'success',
            'sent_at'   => now(),
        ]);
            
            Log::info("Netpay approval notification sent successfully to {$email}");
            
        } catch (Exception $e) {
            Log::error("Netpay approval notification failed for {$email}:", [
                'error' => $e->getMessage(),
                'mail_error' => $mail->ErrorInfo ?? 'N/A'
            ]);

            DB::table('email_logs')->insert([
            'recipient'     => $email,
            'subject'       => $mail->Subject,
            'template'      => 'netpay_approval_notification',
            'status'        => 'error',
            'error_message' => $e->getMessage(),
            'sent_at'       => now(),
        ]);
        }
    }

    /**
     * Get HTML email body for netpay approval
     */
private function getNetpayApprovalEmailBody(
    string $name,
    string $month,
    string $year,
    float $totalNetpay,
    int $employeeCount,
    array $allowedPayrollIds
): string {
    // ✅ Escape every string variable before HTML interpolation
    $safeName        = htmlspecialchars($name,           ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeMonth       = htmlspecialchars($month,          ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeYear        = htmlspecialchars($year,           ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeCompany     = htmlspecialchars(
                           $this->companydetails['name'] ?? 'Company',
                           ENT_QUOTES | ENT_HTML5, 'UTF-8'
                       );

    // ✅ approvalUrl is generated server-side — still escape for safety
    $safeApprovalUrl = htmlspecialchars(url('/papprove'), ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // ✅ formattedNetpay and employeeCount are numeric — cast to ensure safety
    $safeNetpay        = htmlspecialchars(number_format($totalNetpay, 2), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeEmployeeCount = (int) $employeeCount;

    // ✅ payrollTypes come from DB but escape each value individually
    $payrollTypes = DB::table('ptypes')
        ->whereIn('id', $allowedPayrollIds)
        ->pluck('cname')
        ->toArray();

    $safePayrollTypesStr = implode(', ', array_map(
        fn($type) => htmlspecialchars($type, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        $payrollTypes
    ));

    $safeYear2 = (int) date('Y'); // ✅ copyright year — integer, safe

    // ✅ All variables in the template are now escaped
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 700px; margin: 0 auto; padding: 20px; }
            .header { background-color: #e67e22; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .summary-box {
                background: white; padding: 20px; border-radius: 5px;
                margin: 20px 0; border: 2px solid #e67e22;
            }
            .amount-highlight {
                font-size: 32px; font-weight: bold;
                color: #e67e22; text-align: center; margin: 20px 0;
            }
            .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; background: white; }
            .info-table td { padding: 12px; border: 1px solid #ddd; }
            .info-table td:first-child {
                font-weight: bold; background-color: #f0f0f0; width: 40%;
            }
            .action-button {
                display: inline-block; background-color: #e67e22; color: white;
                padding: 15px 30px; text-decoration: none; border-radius: 5px;
                margin: 20px 0; font-weight: bold;
            }
            .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            .important {
                background-color: #fff3cd; padding: 15px;
                border-left: 4px solid #ffc107; margin: 15px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>{$safeCompany}</h2>
                <p>Netpay Approval Notification</p>
            </div>

            <div class='content'>
                <p>Hi {$safeName},</p>

                <div class='important'>
                    <strong>⚠️ Action Required:</strong> The netpay calculation for
                    {$safeMonth} {$safeYear} has been completed and is ready for your approval.
                </div>

                <div class='summary-box'>
                    <h3 style='text-align: center; color: #e67e22; margin-top: 0;'>Netpay Summary</h3>

                    <div class='amount-highlight'>KES {$safeNetpay}</div>

                    <table class='info-table'>
                        <tr><td>Period</td><td>{$safeMonth} {$safeYear}</td></tr>
                        <tr><td>Total Netpay</td><td><strong>KES {$safeNetpay}</strong></td></tr>
                        <tr><td>Number of Employees</td><td><strong>{$safeEmployeeCount}</strong></td></tr>
                        <tr><td>Payroll Types</td><td>{$safePayrollTypesStr}</td></tr>
                        <tr>
                            <td>Status</td>
                            <td><span style='color: #e67e22; font-weight: bold;'>PENDING APPROVAL</span></td>
                        </tr>
                    </table>
                </div>

                <p style='text-align: center;'>
                    <a href='{$safeApprovalUrl}' class='action-button'>Review &amp; Approve Netpay</a>
                </p>

                <p>Please review the netpay calculations and approve to proceed with payroll processing.</p>

                <p>Best regards,<br>
                <strong>Payroll System</strong><br>
                {$safeCompany}</p>
            </div>

            <div class='footer'>
                <p>This is an automated notification from the payroll system.</p>
                <p>&copy; {$safeYear2} {$safeCompany}. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

    /**
     * Get plain text email body for netpay approval
     */
    private function getNetpayApprovalEmailBodyPlainText(
        string $name,
        string $month,
        string $year,
        float $totalNetpay,
        int $employeeCount
    ): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $approvalUrl = url('/netpay/approvals');
        
        $formattedNetpay = number_format($totalNetpay, 2);
        
        return "
Hi {$name},

ACTION REQUIRED: Netpay Approval

The netpay calculation for {$month} {$year} has been completed and is ready for your approval.

NETPAY SUMMARY:
===================
Total Netpay: KES {$formattedNetpay}
Number of Employees: {$employeeCount}
Period: {$month} {$year}
Status: PENDING APPROVAL

Please review the netpay calculations and approve to proceed with payroll processing.

Review and approve at:
{$approvalUrl}

Best regards,
Payroll System
{$companyName}

---
This is an automated notification from the payroll system.
© " . date('Y') . " {$companyName}. All rights reserved.
        ";
    }

    /**
     * Approve netpay
     */
    
    public function approve(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                'year' => 'required|digits:4|integer|min:2000|max:2100',
                ]);

            $month = ucfirst(strtolower($request->month)); // normalize
            $year  = (int) $request->year;
            $approverId = Auth::id();

            if (!$month || !$year) {
                return response()->json([
                    'success' => false,
                    'message' => 'Month and year are required'
                ], 400);
            }

            $paytracker = Paytracker::where('month', $month)
                ->where('year', $year)
                ->where('netpay_status', 'PENDING_APPROVAL')
                ->first();

            if (!$paytracker) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending netpay approval found for the selected period'
                ], 404);
            }

            // Update netpay status
            $paytracker->update([
                'netpay_status' => 'APPROVED',
                'netpay_approver' => $approverId,
                'netpay_approved_at' => now()
            ]);

            // Log audit trail
            logAuditTrail(
                $approverId,
                'APPROVE',
                'netpay_approval',
                "{$month}_{$year}",
                null,
                null,
                [
                    'action' => 'netpay_approved',
                    'month' => $month,
                    'year' => $year,
                    'total_netpay' => $paytracker->total_netpay,
                    'employee_count' => $paytracker->employee_count
                ]
            );

            DB::commit();

            Log::info('Netpay approved', [
                'month' => $month,
                'year' => $year,
                'approved_by' => $approverId,
                'total_netpay' => $paytracker->total_netpay
            ]);

            // Notify the creator
            $this->notifyCreator($paytracker, 'APPROVED');

            $this->sendApprovalFollowUpToOtherApprovers(
            $approverId,
            $approverUser->name ?? 'A colleague',
            $month,
            $year,
            $paytracker->paytype
        );

            return response()->json([
                'success' => true,
                'message' => "Netpay for {$month} {$year} has been approved successfully"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Netpay approval failed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve netpay: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                 'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                 'year' => 'required|digits:4|integer|min:2000|max:2100',
                'rejection_reason' => 'required|string|max:1000'
            ]);

            $approverId = Auth::id();
            $month = ucfirst(strtolower($request->month)); // normalize
            $year  = (int) $request->year;

            $paytracker = Paytracker::where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->where('netpay_status', 'PENDING_APPROVAL')
                ->first();

            if (!$paytracker) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending netpay approval found'
                ], 404);
            }

            // Update netpay status
            $paytracker->update([
                'netpay_status' => 'REJECTED',
                'netpay_approver' => $approverId,
                'netpay_approved_at' => now(),
                'netpay_rejection_reason' => $validated['rejection_reason']
            ]);

            // Log audit trail
            logAuditTrail(
                $approverId,
                'REJECT',
                'netpay_approval',
                "{$validated['month']}_{$validated['year']}",
                null,
                null,
                [
                    'action' => 'netpay_rejected',
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'rejection_reason' => $validated['rejection_reason']
                ]
            );

            DB::commit();

            Log::info('Netpay rejected', [
                'month' => $validated['month'],
                'year' => $validated['year'],
                'rejected_by' => $approverId,
                'reason' => $validated['rejection_reason']
            ]);

            // Notify the creator
            $this->notifyCreator($paytracker, 'REJECTED', $validated['rejection_reason']);

            $this->sendApprovalFollowUpToOtherApprovers(
            $approverId,
            $approverUser->name ?? 'A colleague',
            $month,
            $year,
            $paytracker->paytype
        );


            return response()->json([
                'success' => true,
                'message' => 'Netpay rejected successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Netpay rejection failed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject netpay: ' . $e->getMessage()
            ], 500);
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
        ->where('template', 'netpay_approval_notification')
        ->where('status', 'success')
        ->where('subject', 'LIKE', "%{$month} {$year}%")
        ->latest('sent_at')
        ->first();

    // Fall back gracefully if no log found
    $originalSubject = $originalLog?->subject
        ?? "Netpay Approval Required - {$month} {$year}";

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
           Netpay Report (<strong>{$paytype}</strong>) has already been reviewed 
            by <strong>{$approverName}</strong>.</p>
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
        . "This is to let you know that the {$month} {$year}  Netpay Report "
        . "({$paytype}) has already been reviewed by {$approverName}.\n\n"
        . "No further action is required from your side.\n\n"
        . "Thank you,\nCore Pay";
}

    /**
     * Reject netpay
     */
    

    /**
     * Notify creator about approval/rejection
     */
    private function notifyCreator(Paytracker $paytracker, string $decision, ?string $reason = null)
    {
        try {
            $creator = User::find($paytracker->creator);
            
            if (!$creator || !$creator->email) {
                Log::warning('Cannot notify creator - no email found', [
                    'creator_id' => $paytracker->creator
                ]);
                return;
            }
            
            $this->sendNetpayDecisionEmail(
                $creator->email,
                $creator->name,
                $paytracker->month,
                $paytracker->year,
                $paytracker->total_netpay,
                $decision,
                $reason
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to send netpay decision notification: ' . $e->getMessage());
        }
    }

    /**
     * Send netpay decision email
     */
    private function sendNetpayDecisionEmail(
        string $email,
        string $name,
        string $month,
        string $year,
        float $totalNetpay,
        string $decision,
        ?string $reason = null
    ): void
    {
        $mail = new PHPMailer(true);

        try {
            Log::info("Sending netpay decision notification to: {$email}");
            
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
            $mail->Subject = "Netpay {$decision} - {$month} {$year}";
            $mail->Body = $this->getNetpayDecisionEmailBody($name, $month, $year, $totalNetpay, $decision, $reason);
            $mail->AltBody = $this->getNetpayDecisionEmailBodyPlainText($name, $month, $year, $totalNetpay, $decision, $reason);
            
            // Send email
            if (!$mail->send()) {
                throw new \Exception("Send failed: {$mail->ErrorInfo}");
            }
            
            Log::info("Netpay decision notification sent successfully to {$email}");
            
        } catch (Exception $e) {
            Log::error("Netpay decision notification failed for {$email}:", [
                'error' => $e->getMessage(),
                'mail_error' => $mail->ErrorInfo ?? 'N/A'
            ]);
        }
    }

    /**
     * Get HTML email body for netpay decision
     */
    private function getNetpayDecisionEmailBody(
        string $name,
        string $month,
        string $year,
        float $totalNetpay,
        string $decision,
        ?string $reason
    ): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $isApproved = $decision === 'APPROVED';
        $statusColor = $isApproved ? '#4CAF50' : '#e74c3c';
        $statusIcon = $isApproved ? '✓' : '✗';
        
        $formattedNetpay = number_format($totalNetpay, 2);
        
        $reasonSection = '';
        if (!$isApproved && $reason) {
            $reasonSection = "
                <div style='background-color: #fee; padding: 15px; border-left: 4px solid #e74c3c; margin: 15px 0;'>
                    <strong>Rejection Reason:</strong><br>
                    {$reason}
                </div>
            ";
        }
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: {$statusColor}; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .status-box { 
                    text-align: center;
                    padding: 20px;
                    margin: 20px 0;
                    background: white;
                    border-radius: 5px;
                    border: 2px solid {$statusColor};
                }
                .amount-highlight {
                    font-size: 28px;
                    font-weight: bold;
                    color: {$statusColor};
                    margin: 10px 0;
                }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>{$companyName}</h2>
                    <p>Netpay {$decision}</p>
                </div>
                
                <div class='content'>
                    <p>Hi {$name},</p>
                    
                    <div class='status-box'>
                        <h2 style='color: {$statusColor}; margin: 0;'>
                            {$statusIcon} Netpay {$decision}
                        </h2>
                        <p style='margin: 10px 0;'><strong>{$month} {$year}</strong></p>
                        <div class='amount-highlight'>KES {$formattedNetpay}</div>
                    </div>
                    
                    {$reasonSection}
                    
                    <p>" . ($isApproved 
                        ? "The netpay for {$month} {$year} has been approved. You can now proceed with the final payroll processing steps." 
                        : "The netpay for {$month} {$year} has been rejected. Please review the rejection reason above, make necessary corrections, and resubmit.") . "</p>
                    
                    <p>Best regards,<br>
                    <strong>Payroll Management Team</strong><br>
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

    private function cleanString(string $value): string
{
    return trim(strip_tags($value));
}

    /**
     * Get plain text email body for netpay decision
     */
    private function getNetpayDecisionEmailBodyPlainText(
        string $name,
        string $month,
        string $year,
        float $totalNetpay,
        string $decision,
        ?string $reason
    ): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $isApproved = $decision === 'APPROVED';
        $formattedNetpay = number_format($totalNetpay, 2);
        
        $reasonText = '';
        if (!$isApproved && $reason) {
            $reasonText = "\nREJECTION REASON:\n{$reason}\n";
        }
        
        $message = $isApproved 
            ? "The netpay for {$month} {$year} has been approved. You can now proceed with the final payroll processing steps."
            : "The netpay for {$month} {$year} has been rejected. Please review the rejection reason above, make necessary corrections, and resubmit.";
        
        return "
Hi {$name},

NETPAY {$decision}

Period: {$month} {$year}
Total Netpay: KES {$formattedNetpay}
Status: {$decision}
{$reasonText}

{$message}

Best regards,
Payroll Management Team
{$companyName}

---
This is an automated notification from the payroll system.
© " . date('Y') . " {$companyName}. All rights reserved.
        ";
    }
}

