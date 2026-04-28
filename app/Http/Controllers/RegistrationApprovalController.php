<?php

namespace App\Http\Controllers;

use App\Models\PendingRegistrationUpdate;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\JubiPayEmailService;

class RegistrationApprovalController extends Controller
{
    private $emailConfig;
    private $companydetails;

    public function __construct(protected JubiPayEmailService $jubiPay)
    {
        $this->loadEmailConfig();
        $this->loadCompanyDetails();
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

     public function index()
    {
        $pendingUpdates = PendingRegistrationUpdate::with(['employee', 'submitter', 'registration'])
            ->where('status', 'PENDING')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        return view('students.rapprove', compact('pendingUpdates'));
    }
    
    /**
     * View single pending update details
     */
    public function show($id)
{
    $pendingUpdate = PendingRegistrationUpdate::with(['employee', 'submitter', 'registration'])
        ->findOrFail($id);

    $changes = $this->getChangedFields(
        $pendingUpdate->original_data,
        $pendingUpdate->pending_data
    );

    return response()->json([
        'status' => 'success',
        'pendingUpdate' => $pendingUpdate,
        'changes' => $changes
    ]);
}


    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $approverId = Auth::id();
            
            $pendingUpdate = PendingRegistrationUpdate::where('id', $id)
                ->where('status', 'PENDING')
                ->firstOrFail();
            
            // Find the registration record
            $registration = Registration::where('empid', $pendingUpdate->empid)->firstOrFail();
            
            // Apply the pending changes
            $registration->update($pendingUpdate->pending_data);
            
            // Update the pending record
            $pendingUpdate->update([
                'status' => 'APPROVED',
                'approved_by' => $approverId,
                'reviewed_at' => now()
            ]);
            
            // Log audit trail
            logAuditTrail(
                $approverId,
                'APPROVE',
                'registration_kyc',
                $pendingUpdate->empid,
                null,
                null,
                [
                    'action' => 'kyc_update_approved',
                    'pending_update_id' => $id,
                    'empid' => $pendingUpdate->empid,
                    'changes_applied' => $this->getChangedFields(
                        $pendingUpdate->original_data,
                        $pendingUpdate->pending_data
                    )
                ]
            );
            
            DB::commit();
            
            Log::info('KYC update approved', [
                'pending_update_id' => $id,
                'empid' => $pendingUpdate->empid,
                'approved_by' => $approverId
            ]);
            
            // Notify the submitter
            $this->notifySubmitter($pendingUpdate, 'APPROVED');

            // 2. Notify the other approvers that this has already been handled
        $this->sendApprovalFollowUpToOtherApprovers(
            $approverId,
            $approverUser->name ?? 'A colleague',
            $pendingUpdate->empid
        );
            
            return response()->json([
                'success' => true,
                'message' => 'Registration update approved and applied successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Approval failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve update: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendApprovalFollowUpToOtherApprovers(
    int $approverId,
    string $approverName,
    string $empid
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
        ->where('template', 'KYC_approval_notification')
        ->where('status', 'success')
        ->where('subject', 'LIKE', "%{$empid}%")
        ->latest('sent_at')
        ->first();

    // Fall back gracefully if no log found
    $originalSubject = $originalLog?->subject
        ?? "KYC Update Pending Approval - {$empid}";

    // Re: prefix for inbox threading
    $followUpSubject = "Re: {$originalSubject}";

    foreach ($otherApprovers as $approver) {
        $this->sendApprovalFollowUpEmail(
            $approver->email,
            $approver->name,
            $approverName,
            $empid,
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
    string $empid,
    string $subject
): void {
    Log::info("sendApprovalFollowUpEmail: Initiating", [
        'recipient'    => $email,
        'empid'        => $empid,
        'approverName' => $approverName,
    ]);

    try {
        $this->jubiPay->send(
            email   : $email,
            name    : $name,
            subject : $subject,
            message : $this->getFollowUpEmailBody($name, $approverName, $empid),
            template: 'KYC_followup_notification',
            context : ['empid' => $empid, 'approver' => $approverName]
        );

        DB::table('email_logs')->insert([
            'recipient' => $email,
            'subject'   => $subject,
            'template'  => 'KYC_approval_notification',
            'status'    => 'success',
            'sent_at'   => now(),
        ]);

        Log::info("sendApprovalFollowUpEmail: Sent successfully", [
            'recipient' => $email,
            'empid'     => $empid,
        ]);

    } catch (\Exception $e) {
        Log::error("sendApprovalFollowUpEmail: Failed", [
            'recipient' => $email,
            'empid'     => $empid,
            'error'     => $e->getMessage(),
        ]);

        DB::table('email_logs')->insert([
            'recipient'     => $email,
            'subject'       => $subject,
            'template'      => 'KYC_approval_notification',
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
    string $empid,
): string {
    return "
        <p>Hi {$name},</p>
        <p>This is to let you know that the KYC Actions required for <strong>{$empid}</strong> 
           has already been reviewed 
        by <strong>{$approverName}</strong>.</p>
        <p>No further action is required from your side.</p>
        <p>Thank you,<br>Core Pay</p>
    ";
}

private function getFollowUpEmailBodyPlainText(
    string $name,
    string $approverName,
    string $empid
): string {
    return "Hi {$name},\n\n"
        . "This is to let you know that the KYC Actions required for {$empid}"
        . "has already been reviewed  {$approverName}.\n\n"
        . "No further action is required from your side.\n\n"
        . "Thank you,\nCore Pay";
}


    
    
    /**
     * Reject KYC update
     */
    public function reject(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000'
            ]);
            
            $approverId = Auth::id();
            
            $pendingUpdate = PendingRegistrationUpdate::where('id', $id)
                ->where('status', 'PENDING')
                ->firstOrFail();
            
            // Update the pending record
            $pendingUpdate->update([
                'status' => 'REJECTED',
                'approved_by' => $approverId,
                'reviewed_at' => now(),
                'rejection_reason' => $validated['rejection_reason']
            ]);
            
            // Log audit trail
            logAuditTrail(
                $approverId,
                'REJECT',
                'registration_kyc',
                $pendingUpdate->empid,
                null,
                null,
                [
                    'action' => 'kyc_update_rejected',
                    'pending_update_id' => $id,
                    'empid' => $pendingUpdate->empid,
                    'rejection_reason' => $validated['rejection_reason']
                ]
            );
            
            DB::commit();
            
            Log::info('KYC update rejected', [
                'pending_update_id' => $id,
                'empid' => $pendingUpdate->empid,
                'rejected_by' => $approverId,
                'reason' => $validated['rejection_reason']
            ]);
            
            // Notify the submitter
            $this->notifySubmitter($pendingUpdate, 'REJECTED', $validated['rejection_reason']);

             // 2. Notify the other approvers that this has already been handled
        $this->sendApprovalFollowUpToOtherApprovers(
            $approverId,
            $approverUser->name ?? 'A colleague',
            $pendingUpdate->empid
        );
            
            return response()->json([
                'success' => true,
                'message' => 'Registration update rejected successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Rejection failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject update: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get changed fields
     */
    private function getChangedFields(array $original, array $pending): array
    {
        $changes = [];
        
        foreach ($pending as $key => $newValue) {
            $oldValue = $original[$key] ?? null;
            
            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }
        
        return $changes;
    }
    
    /**
     * Notify submitter about decision
     */
    private function notifySubmitter(PendingRegistrationUpdate $pendingUpdate, string $decision, string $reason = null)
    {
        try {
            $submitter = User::find($pendingUpdate->submitted_by);
            
            if (!$submitter || !$submitter->email) {
                Log::warning('Cannot notify submitter - no email found', [
                    'submitter_id' => $pendingUpdate->submitted_by
                ]);
                return;
            }
            
            // Get employee details
            $employee = \App\Models\Agents::where('emp_id', $pendingUpdate->empid)->first();
            $employeeName = $employee ? trim(($employee->FirstName ?? '') . ' ' . ($employee->LastName ?? '')) : $pendingUpdate->empid;
            
            // Send decision email
            $this->sendKycDecisionEmail(
                $submitter->email,
                $submitter->name,
                $pendingUpdate->empid,
                $employeeName,
                $decision,
                $reason
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to send KYC decision notification: ' . $e->getMessage());
            // Don't throw - just log
        }
    }
    
    /**
     * Send KYC decision notification email
     */

    private function sendKycDecisionEmail(
    string $email,
    string $name,
    string $empid,
    string $employeeName,
    string $decision,
    ?string $reason = null
): void {
    $subject = "KYC fields Update {$decision} - {$empid}";

    Log::info("sendKycDecisionEmail: Initiating", [
        'recipient' => $email,
        'empid'     => $empid,
        'decision'  => $decision,
    ]);

    try {
        $this->jubiPay->send(
            email   : $email,
            name    : $name,
            subject : $subject,
            message : $this->getKycDecisionEmailBody($name, $empid, $employeeName, $decision, $reason),
            template: 'KYC_decision_notification',
            context : ['empid' => $empid, 'decision' => $decision]
        );

        Log::info("sendKycDecisionEmail: Sent successfully", [
            'recipient' => $email,
            'empid'     => $empid,
            'decision'  => $decision,
        ]);

    } catch (\Exception $e) {
        Log::error("sendKycDecisionEmail: Failed", [
            'recipient' => $email,
            'empid'     => $empid,
            'decision'  => $decision,
            'error'     => $e->getMessage(),
        ]);

        // Don't throw — preserved from original
    }
}
    
    /**
     * Get HTML email body for KYC decision
     */
    private function getKycDecisionEmailBody(
        string $name,
        string $empid,
        string $employeeName,
        string $decision,
        ?string $reason
    ): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $isApproved = $decision === 'APPROVED';
        $statusColor = $isApproved ? '#4CAF50' : '#e74c3c';
        $statusIcon = $isApproved ? '✓' : '✗';
        
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
                }
                .employee-info {
                    background: white;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 15px 0;
                }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>{$companyName}</h2>
                    <p>KYC fields Update {$decision}</p>
                </div>
                
                <div class='content'>
                    <p>Hi {$name},</p>
                    
                    <div class='status-box'>
                        <h2 style='color: {$statusColor}; margin: 0;'>
                            {$statusIcon} KYC Update {$decision}
                        </h2>
                    </div>
                    
                    <div class='employee-info'>
                        <strong>Agent ID:</strong> {$empid}<br>
                        <strong>Agent Name:</strong> {$employeeName}<br>
                        <strong>Status:</strong> <span style='color: {$statusColor}; font-weight: bold;'>{$decision}</span>
                    </div>
                    
                    {$reasonSection}
                    
                    <p>" . ($isApproved 
                        ? "Your KYC fields update has been approved and the changes have been applied to the system." 
                        : "Your KYC fields update has been rejected. Please review the rejection reason above and resubmit with the necessary corrections if needed.") . "</p>
                    
                   
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
     * Get plain text email body for KYC decision
     */
    private function getKycDecisionEmailBodyPlainText(
        string $name,
        string $empid,
        string $employeeName,
        string $decision,
        ?string $reason
    ): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $isApproved = $decision === 'APPROVED';
        
        $reasonText = '';
        if (!$isApproved && $reason) {
            $reasonText = "\nREJECTION REASON:\n{$reason}\n";
        }
        
        $message = $isApproved 
            ? "Your KYC update has been approved and the changes have been applied to the system."
            : "Your KYC update has been rejected. Please review the rejection reason above and resubmit with the necessary corrections if needed.";
        
        return "
Hi {$name},

KYC UPDATE {$decision}

EMPLOYEE DETAILS:
-----------------
Agent ID: {$empid}
Agent Name: {$employeeName}
Status: {$decision}
{$reasonText}

{$message}

{$companyName}

---
This is an automated notification from the payroll system.
© " . date('Y') . " {$companyName}. All rights reserved.
        ";
    }
}