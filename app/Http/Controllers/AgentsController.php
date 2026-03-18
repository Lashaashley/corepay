<?php

namespace App\Http\Controllers;

use App\Models\Agents;
use App\Models\Depts;
use App\Models\PendingRegistrationUpdate;
use App\Models\StaffType;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AgentsController extends Controller
{
    private $emailConfig;
    private $companydetails;

    public function __construct()
    {
        $this->loadEmailConfig();
        $this->loadCompanyDetails();
    }
    /**
     * Display the agents management page
     */
    public function index()
    {
        return view('students.amanage');
    }
    public function aindex()
    {
        return view('students.areports');
    }
    public function impindex()
    {
        return view('students.aimport');
    }
    public function newganet()
    {
        return view('students.nagent');
    }

    /**
     * Get agents data for DataTable (AJAX)
     * This uses server-side processing for better performance
     */
    public function getData(Request $request)
{
    try {
        // ✅ Log incoming request
       

        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';

        

        // Column mapping for ordering
        $columns = [
            0 => 'FirstName',
            1 => 'emp_id',
            2 => 'stafftype',
            3 => 'Department',
            4 => 'desigid',
            5 => 'Status'
        ];

        // ✅ Base query with relationships
        $query = Agents::select(
                'tblemployees.*',
                'tbldepartments.DepartmentName',
                'stafftypes.Desig'
            )
            ->leftJoin('tbldepartments', 'tblemployees.Department', '=', 'tbldepartments.ID')
            ->leftJoin('stafftypes', 'tblemployees.desigid', '=', 'stafftypes.ID')
            ->where('tblemployees.emp_id', '!=', '1');

        
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('tblemployees.FirstName', 'like', "%{$searchValue}%")
                  ->orWhere('tblemployees.LastName', 'like', "%{$searchValue}%")
                  ->orWhere('tblemployees.emp_id', 'like', "%{$searchValue}%")
                  ->orWhere('tblemployees.stafftype', 'like', "%{$searchValue}%")
                  ->orWhere('tbldepartments.DepartmentName', 'like', "%{$searchValue}%")
                  ->orWhere('stafftypes.Desig', 'like', "%{$searchValue}%")
                  ->orWhere('tblemployees.Status', 'like', "%{$searchValue}%");
            });

            Log::info('AgentsController getData: Search applied', [
                'searchValue' => $searchValue
            ]);
        }

        // Get total records before pagination
        $totalRecords = Agents::where('emp_id', '!=', '1')->count();
        $filteredRecords = $query->count();

        Log::info('AgentsController getData: Record counts', [
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords
        ]);

        // Apply ordering
        $orderColumnName = $columns[$orderColumn] ?? 'emp_id';
        $query->orderBy($orderColumnName, $orderDir);

        
        // Apply pagination
        $agents = $query->skip($start)->take($length)->get();

       

        // Format data for DataTable
        $data = [];
        foreach ($agents as $agent) {
            $agentData = [
                'full_name' => $agent->FirstName . ' ' . $agent->LastName,
                'profile_photo' => $agent->profile_photo_url,
                'emp_id' => $agent->emp_id,
                'stafftype' => $agent->stafftype ?? 'N/A',
                'department' => $agent->DepartmentName ?? 'N/A',
                'designation' => $agent->Desig ?? 'N/A',
                'status' => $agent->Status,
                'actions' => $agent->emp_id
            ];
            
            $data[] = $agentData;
        }

     

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        

        return response()->json($response);

    } catch (\Exception $e) {
        Log::error('AgentsController getData error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'draw' => $request->get('draw', 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Error loading data: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Show create form
     */
    public function create()
    {
        $departments = Depts::orderBy('DepartmentName')->get();
        $staffTypes = StaffType::orderBy('Desig')->get();
        
        return view('agents.create', compact('departments', 'staffTypes'));
    }

    /**
     * Get agent details
     */
    public function show($id)
    {
        try {
            $agent = Agents::with(['department', 'designation'])
                ->where('emp_id', $id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $agent
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }
    }

    /**
     * Terminate an agent
     */
    public function terminate(Request $request, $id)
    {
        try {
            $agent = Agents::findOrFail($id);
            $agent->Status = 'INACTIVE';
            $agent->save();

            return response()->json([
                'success' => true,
                'message' => 'Agent terminated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Agent termination error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to terminate agent'
            ], 500);
        }
    }

    public function registerAgent(Request $request)
{
    $userId = Auth::id();

    $validator = Validator::make($request->all(), [
        'firstname'   => 'required|string|max:255',
        'lastname'    => 'required|string|max:255',
        'agentno'     => 'required|string|max:255|unique:tblemployees,emp_id',
        'email'       => 'nullable|email|max:255',
        'phonenumber' => 'nullable|string|max:255',
        'brid'        => 'required|string|max:255',
        'dept'        => 'required|string|max:255',
        'dob'         => 'nullable|date',
        'gender'      => 'nullable|in:male,female',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $agent = Agents::create([
            'emp_id'      => $request->agentno,
            'FirstName'   => $request->firstname,
            'LastName'    => $request->lastname,
            'EmailId'     => $request->email,
            'Phonenumber' => $request->phonenumber,
            'Dob'         => $request->dob,
            'Gender'      => $request->gender,
            'brid'        => $request->brid,
            'Department'  => $request->dept,
            'Status'      => 'ACTIVE'
        ]);

        logAuditTrail(
            $userId,
            'INSERT',
            'agentsdata',
            $request->agentno,
            null,
            null,
            ['message' => 'Created new Agent']
        );

        return response()->json([
            'status' => 'success',
            'empid'  => $request->agentno
        ]);

    } catch (\Exception $e) {
        Log::error('Agent registration failed', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to create agent'
        ], 500);
    }
}

public function registrationdetails(Request $request)
{
    $userId = Auth::id();

    $validator = Validator::make($request->all(), [
        'aggentno'        => 'required|string|max:255|unique:registration,empid',
        'idno'           => 'nullable|string|max:255|unique:registration,idno',
        'krapin'         => 'required|string|max:255|unique:registration,kra',
        'paymentMethod' => 'required|in:Etransfer,Cheque',
        'proltype'       => 'required|string|max:255',
        'bank'           => 'required|string|max:255',
        'branch'         => 'nullable|string|max:255',
        'bcode'          => 'nullable|string|max:255',
        'bankcode'       => 'required|string|max:255',
        'swiftcode'      => 'nullable|string|max:255',
        'account'        => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        Registration::create([
            'empid'       => $request->aggentno,
            'nhif'        => $request->nhif_shif ? 'YES' : 'NO',
            'nssf'        => $request->nssf ? 'YES' : 'NO',
            'contractor'  => $request->contractor ? 'YES' : 'NO',
            'unionized'   => $request->unionized ? 'YES' : 'NO',
            'unionno'     => $request->unionno,
            'idno'        => $request->idno,
            'nhifno'      => $request->nhifno,
            'nssfno'      => $request->nssfno,
            'nssfopt'     => $request->nssfopt ? 'YES' : 'NO',
            'kra'      => $request->krapin,
            'paymode'     => $request->paymentMethod,
            'payrolty'    => $request->proltype,
            'Bank'        => $request->bank,
            'Branch'      => $request->branch,
            'BranchCode'  => $request->bcode,
            'BankCode'    => $request->bankcode,
            'swiftcode'   => $request->swiftcode,
            'AccountNo'     => $request->account,
        ]);

        logAuditTrail(
            $userId ?? 'system',
            session_id(),
            'INSERT',
            'registration',
            $request->agentno,
            null,
            null,
            'Inserted registration details'
        );

        return response()->json([
            'status' => 'success',
            'empid'  => $request->agentno
        ]);

    } catch (\Exception $e) {
        Log::error('Registration insert failed', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to save registration'
        ], 500);
    }
}
public function editagent($id)
{
    try {
        // Eager load the registration relationship
        $agent = Agents::with('registration')->findOrFail($id);
        
        // Get the first registration record (or null if none exists)
        $moredetails = $agent->registration->first();
        
        // Build response array with proper null handling
        $agentData = [
            'emp_id' => $agent->emp_id,
            'FirstName' => $agent->FirstName,
            'LastName' => $agent->LastName,
            'EmailId' => $agent->EmailId,
            'Phonenumber' => $agent->Phonenumber,
            'Dob' => $agent->Dob,
            'Gender' => $agent->Gender,
            'brid' => $agent->brid,
            'Department' => $agent->Department,
            'Status' => $agent->Status,
        ];
        
        // Add registration details if they exist
        if ($moredetails) {
            $agentData = array_merge($agentData, [
                'nhif' => $moredetails->nhif ?? 'NO',
                'nssf' => $moredetails->nssf ?? 'NO',
                'contractor' => $moredetails->contractor ?? 'NO',
                'unionized' => $moredetails->unionized ?? 'NO',
                'nssfopt' => $moredetails->nssfopt ?? 'NO',
                'unionno' => $moredetails->unionno,
                'idno' => $moredetails->idno,
                'nhifno' => $moredetails->nhifno,
                'nssfno' => $moredetails->nssfno,
                'kra' => $moredetails->kra,
                'paymode' => $moredetails->paymode,
                'payrolty' => $moredetails->payrolty,
                'Bank' => $moredetails->Bank,
                'BankCode' => $moredetails->BankCode,
                'Branch' => $moredetails->Branch,
                'BranchCode' => $moredetails->BranchCode,
                'swiftcode' => $moredetails->swiftcode,
                'AccountNo' => $moredetails->AccountNo
            ]);
        } else {
            // Set default values if no registration details exist
            $agentData = array_merge($agentData, [
                'nhif' => 'NO',
                'nssf' => 'NO',
                'contractor' => 'NO',
                'unionized' => 'NO',
                'nssfopt' => 'NO',
                'unionno' => null,
                'idno' => null,
                'nhifno' => null,
                'nssfno' => null,
                'kra' => null,
                'paymode' => null,
                'payrolty' => null,
                'Bank' => null,
                'BankCode' => null,
                'Branch' => null,
                'BranchCode' => null,
                'swiftcode' => null,
                'AccountNo' => null
            ]);
        }
        
        return response()->json([
            'status' => 'success',
            'agent' => $agentData
        ]);
        
    }  catch (\Exception $e) {
        Log::error('Failed to load agent for editing', [
            'user_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to load user data. Please try again.'
        ], 500);
    }
}

public function update(Request $request, $id)
{
    try {
        Log::info('Update request received for agent: ' . $id);
        Log::info('Request data:', $request->all());
        
        // Find agent by emp_id (not id)
        $agent = Agents::where('emp_id', $id)->firstOrFail();
        
        // Validate with unique check using emp_id as the primary key
        $validated = $request->validate([
            'firstname'   => 'required|string|max:255',
            'lastname'    => 'required|string|max:255',
            'agentno'     => [
                'required',
                'string',
                'max:255',
                Rule::unique('tblemployees', 'emp_id')->ignore($id, 'emp_id')
            ],
            'email'       => 'nullable|email|max:255',
            'phonenumber' => 'nullable|string|max:20',
            'brid'        => 'required|integer',
            'dept'        => 'required|integer',
            'dob'         => 'nullable|date',
            'gender'      => 'nullable|in:Male,Female,male,female,Other',
        ]);
        
        Log::info('Validation passed');
        Log::info('Validated data:', $validated);
        
        // Update agent with correct database column names
        $agent->update([
            'FirstName'   => $validated['firstname'],
            'LastName'    => $validated['lastname'],
            'emp_id'      => $validated['agentno'],
            'EmailId'     => $validated['email'],
            'Phonenumber' => $validated['phonenumber'],
            'brid'        => $validated['brid'],
            'Department'  => $validated['dept'],
            'Dob'         => $validated['dob'],
            'Gender'      => ucfirst(strtolower($validated['gender'])), // Normalize to Male/Female
        ]);
        
        Log::info('Agent updated successfully:', $agent->toArray());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Agent updated successfully',
            'data' => $agent
        ]);
        
    }  catch (\Exception $e) {
        Log::error('Update failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update agent: ' . $e->getMessage()
        ], 500);
    }
}
public function regupdate(Request $request, $id)
    {
        try {
            Log::info('Registration update request received for agent: ' . $id);
            Log::info('Request data:', $request->all());
            
            // Find registration by empid
            $regagent = Registration::where('empid', $id)->firstOrFail();
            
            // Validate with unique check
            $validated = $request->validate([
                'aggentno'       => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('registration', 'empid')->ignore($id, 'empid')
                ],
                'idno'           => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('registration', 'idno')->ignore($regagent->id ?? null)->whereNotNull('idno')
                ],
                'krapin'         => 'required|string|max:255',
                'nhifno'         => 'nullable|string|max:255',
                'nssfno'         => 'nullable|string|max:255',
                'unionno'        => 'nullable|string|max:255',
                'paymentMethod'  => 'required|in:Etransfer,Cheque',
                'proltype'       => 'required|integer',
                'bank'           => 'required|string|max:255',
                'branch'         => 'nullable|string|max:255',
                'bcode'          => 'nullable|string|max:255',
                'bankcode'       => 'required|string|max:255',
                'swiftcode'      => 'nullable|string|max:255',
                'account'        => 'required|string|max:255',
                'submission_notes' => 'nullable|string|max:500'
            ]);
            
            Log::info('Validation passed');
            
            // Get current user
            $userId = Auth::id();
            
            // Prepare original data (current state)
            $originalData = [
                'empid'      => $regagent->empid,
                'nhif'       => $regagent->nhif,
                'nssf'       => $regagent->nssf,
                'contractor' => $regagent->contractor,
                'unionized'  => $regagent->unionized,
                'nssfopt'    => $regagent->nssfopt,
                'idno'       => $regagent->idno,
                'kra'        => $regagent->kra,
                'nhifno'     => $regagent->nhifno,
                'nssfno'     => $regagent->nssfno,
                'unionno'    => $regagent->unionno,
                'paymode'    => $regagent->paymode,
                'payrolty'   => $regagent->payrolty,
                'Bank'       => $regagent->Bank,
                'BankCode'   => $regagent->BankCode,
                'Branch'     => $regagent->Branch,
                'BranchCode' => $regagent->BranchCode,
                'swiftcode'  => $regagent->swiftcode,
                'AccountNo'  => $regagent->AccountNo,
            ];
            
            // Prepare pending data (new values)
            $pendingData = [
                'empid'      => $validated['aggentno'],
                'nhif'       => $request->has('nhif_shif') ? 'YES' : 'NO',
                'nssf'       => $request->has('nssf') ? 'YES' : 'NO',
                'contractor' => $request->has('contractor') ? 'YES' : 'NO',
                'unionized'  => $request->has('unionized') ? 'YES' : 'NO',
                'nssfopt'    => $request->has('nssfopt') ? 'YES' : 'NO',
                'idno'       => $validated['idno'],
                'kra'        => $validated['krapin'],
                'nhifno'     => $validated['nhifno'],
                'nssfno'     => $validated['nssfno'],
                'unionno'    => $validated['unionno'],
                'paymode'    => $validated['paymentMethod'],
                'payrolty'   => $validated['proltype'],
                'Bank'       => $validated['bank'],
                'BankCode'   => $validated['bankcode'],
                'Branch'     => $validated['branch'],
                'BranchCode' => $validated['bcode'],
                'swiftcode'  => $validated['swiftcode'],
                'AccountNo'  => $validated['account'],
            ];
            
            // Check if there are any pending updates for this employee
            $existingPending = PendingRegistrationUpdate::where('empid', $id)
                ->where('status', 'PENDING')
                ->first();
            
            if ($existingPending) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'There is already a pending update for this employee. Please wait for approval or contact administrator.',
                    'pending_update_id' => $existingPending->id
                ], 409);
            }
            
            // Create pending update record
            $pendingUpdate = PendingRegistrationUpdate::create([
                'empid' => $id,
                'submitted_by' => $userId,
                'original_data' => $originalData,
                'pending_data' => $pendingData,
                'status' => 'PENDING',
                'submission_notes' => $validated['submission_notes'] ?? null,
                'submitted_at' => now()
            ]);
            
            Log::info('Pending registration update created', [
                'pending_update_id' => $pendingUpdate->id,
                'empid' => $id,
                'submitted_by' => $userId
            ]);
            
            // Log audit trail
            logAuditTrail(
                $userId,
                'UPDATE',
                'registration_kyc',
                $id,
                null,
                null,
                [
                    'action' => 'kyc_update_submitted',
                    'empid' => $id,
                    'pending_update_id' => $pendingUpdate->id,
                    'changes' => $this->getChangedFields($originalData, $pendingData),
                    'status' => 'PENDING_APPROVAL'
                ]
            );
            
            // Send notification to approver
            $this->notifyApprover($pendingUpdate);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Registration update submitted for approval. You will be notified once it is reviewed.',
                'data' => [
                    'pending_update_id' => $pendingUpdate->id,
                    'status' => 'PENDING',
                    'submitted_at' => $pendingUpdate->submitted_at
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update submission failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit registration update: ' . $e->getMessage()
            ], 500);
        }
    }

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
     * Notify approver about pending update
     */
     private function notifyApprover(PendingRegistrationUpdate $pendingUpdate)
    {
        try {
            // Get approver (user with approvelvl = 'YES')
            $approver = User::where('approvelvl', 'YES')
                ->whereNotNull('email')
                ->get();
            
            if (!$approver) {
                Log::warning('No approver found for KYC update notification');
                return;
            }
            
            // Get employee details
            $employee = Agents::where('emp_id', $pendingUpdate->empid)->first();
            $employeeName = $employee ? trim(($employee->FirstName ?? '') . ' ' . ($employee->LastName ?? '')) : $pendingUpdate->empid;
            
            // Get changed fields
            $changes = $this->getChangedFields(
                $pendingUpdate->original_data,
                $pendingUpdate->pending_data
            );
            
            // Send email
        foreach ($approver as $approvalUser) {
            $this->sendKycApprovalNotificationEmail(
                $approvalUser->email,
                $approvalUser->name,
                $pendingUpdate->empid,
                $employeeName,
                $changes,
                $pendingUpdate->id
            );
        }
            
        } catch (\Exception $e) {
            Log::error('Failed to send KYC approval notification: ' . $e->getMessage());
            // Don't throw - we don't want to fail the submission if email fails
        }
    }
    private function sendKycApprovalNotificationEmail(
        string $email,
        string $name,
        string $empid,
        string $employeeName,
        array $changes,
        int $pendingUpdateId
    ): void
    {
        $mail = new PHPMailer(true);

        try {
            Log::info("Sending KYC approval notification to: {$email}");
            
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
            $fromName = $this->emailConfig['from_name'] ?? 'CorePay';
            
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($email, $name);
            $mail->addReplyTo($fromEmail, $fromName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "KYC Update Pending Approval - {$empid}";
            $mail->Body = $this->getKycApprovalEmailBody($name, $empid, $employeeName, $changes, $pendingUpdateId);
            $mail->AltBody = $this->getKycApprovalEmailBodyPlainText($name, $empid, $employeeName, $changes);
            
            // Send email
            if (!$mail->send()) {
                throw new \Exception("Send failed: {$mail->ErrorInfo}");
            }

            // Log success with subject
        DB::table('email_logs')->insert([
            'recipient' => $email,
            'subject'   => $mail->Subject,
            'template'  => 'KYC_approval_notification',
            'status'    => 'success',
            'sent_at'   => now(),
        ]);
            
            Log::info("KYC approval notification sent successfully to {$email}");
            
        } catch (Exception $e) {
            Log::error("KYC approval notification failed for {$email}:", [
                'error' => $e->getMessage(),
                'mail_error' => $mail->ErrorInfo ?? 'N/A'
            ]);

             DB::table('email_logs')->insert([
            'recipient'     => $email,
            'subject'       =>  $mail->Subject,
            'template'      => 'KYC_approval_notification',
            'status'        => 'error',
            'error_message' => $e->getMessage(),
            'sent_at'       => now(),
        ]);
            
            // Don't throw - just log
        }
    }

    private function getKycApprovalEmailBody(
        string $name,
        string $empid,
        string $employeeName,
        array $changes,
        int $pendingUpdateId
    ): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $reviewUrl = url('/rapprove');
        
        // Build changes table
        $changesRows = '';
        $fieldLabels = [
            'empid' => 'Employee ID',
            'idno' => 'ID Number',
            'kra' => 'KRA PIN',
            'nhifno' => 'NHIF Number',
            'nssfno' => 'NSSF Number',
            'unionno' => 'Union Number',
            'nhif' => 'NHIF Status',
            'nssf' => 'NSSF Status',
            'contractor' => 'Contractor',
            'unionized' => 'Unionized',
            'nssfopt' => 'NSSF Option',
            'paymode' => 'Payment Method',
            'payrolty' => 'Payroll Type',
            'Bank' => 'Bank',
            'BankCode' => 'Bank Code',
            'Branch' => 'Branch',
            'BranchCode' => 'Branch Code',
            'swiftcode' => 'Swift Code',
            'AccountNo' => 'Account Number',
        ];
        
        foreach ($changes as $field => $change) {
            $label = $fieldLabels[$field] ?? ucfirst($field);
            $oldValue = $change['old'] ?? 'N/A';
            $newValue = $change['new'] ?? 'N/A';
            
            $changesRows .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;'>{$label}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; color: #e74c3c;'>{$oldValue}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; color: #27ae60;'>{$newValue}</td>
                </tr>
            ";
        }
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 700px; margin: 0 auto; padding: 20px; }
                .header { background-color: #3498db; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .changes-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: white; }
                .changes-table th { background-color: #34495e; color: white; padding: 12px; text-align: left; }
                .changes-table td { padding: 10px; border-bottom: 1px solid #ddd; }
                .action-button { 
                    display: inline-block; 
                    background-color: #3498db; 
                    color: white; 
                    padding: 15px 30px; 
                    text-decoration: none; 
                    border-radius: 5px; 
                    margin: 20px 0;
                    font-weight: bold;
                }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
                .important { background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; }
                .employee-info {
                    background: white;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 15px 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>{$companyName}</h2>
                    <p>KYC Update Approval Required</p>
                </div>
                
                <div class='content'>
                    <p>Hi {$name},</p>
                    
                    <div class='important'>
                        <strong>⚠️ Action Required:</strong> A KYC update is pending your approval.
                    </div>
                    
                    <div class='employee-info'>
                        <strong>Employee ID:</strong> {$empid}<br>
                        <strong>Employee Name:</strong> {$employeeName}<br>
                        <strong>Number of Changes:</strong> " . count($changes) . "
                    </div>
                    
                    <h3>Changes Summary:</h3>
                    
                    <table class='changes-table'>
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Current Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$changesRows}
                        </tbody>
                    </table>
                    
                    <p style='text-align: center;'>
                        <a href='{$reviewUrl}' class='action-button'>Review & Approve/Reject</a>
                    </p>
                    
                    <p>Please review the changes carefully before approving or rejecting this update.</p>
                    
                    
                    <strong>CorePay</strong><br>
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

     private function getKycApprovalEmailBodyPlainText(
        string $name,
        string $empid,
        string $employeeName,
        array $changes
    ): string
    {
        $companyName = $this->companydetails['name'] ?? 'Company';
        $reviewUrl = url('/rapprove');
        
        $fieldLabels = [
            'empid' => 'Employee ID',
            'idno' => 'ID Number',
            'kra' => 'KRA PIN',
            'nhifno' => 'NHIF Number',
            'nssfno' => 'NSSF Number',
            'unionno' => 'Union Number',
            'nhif' => 'NHIF Status',
            'nssf' => 'NSSF Status',
            'contractor' => 'Contractor',
            'unionized' => 'Unionized',
            'nssfopt' => 'NSSF Option',
            'paymode' => 'Payment Method',
            'payrolty' => 'Payroll Type',
            'Bank' => 'Bank',
            'BankCode' => 'Bank Code',
            'Branch' => 'Branch',
            'BranchCode' => 'Branch Code',
            'swiftcode' => 'Swift Code',
            'AccountNo' => 'Account Number',
        ];
        
        $changesText = '';
        foreach ($changes as $field => $change) {
            $label = $fieldLabels[$field] ?? ucfirst($field);
            $oldValue = $change['old'] ?? 'N/A';
            $newValue = $change['new'] ?? 'N/A';
            
            $changesText .= "{$label}:\n";
            $changesText .= "  Current: {$oldValue}\n";
            $changesText .= "  New: {$newValue}\n\n";
        }
        
        return "
Hi {$name},

ACTION REQUIRED: KYC Update Pending Approval

EMPLOYEE DETAILS:
-----------------
Agent ID: {$empid}
Agent Name: {$employeeName}
Number of Changes: " . count($changes) . "

CHANGES SUMMARY:
-----------------
{$changesText}

Please review and approve or reject this update by visiting:
{$reviewUrl}


CorePay
{$companyName}

---
This is an automated notification from the payroll system.
© " . date('Y') . " {$companyName}. All rights reserved.
        ";
    }

  
}


