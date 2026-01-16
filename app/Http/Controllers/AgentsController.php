<?php

namespace App\Http\Controllers;

use App\Models\Agents;
use App\Models\Depts;
use App\Models\StaffType;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class AgentsController extends Controller
{
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
            'CREATE',
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
        'swiftcode'      => 'required|string|max:255',
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
            'CREATE',
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
        
        // Validate with unique check - ignore current record
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
            'swiftcode'      => 'required|string|max:255',
            'account'        => 'required|string|max:255',
        ]);
        
        Log::info('Validation passed');
        
        // Update registration details
        $regagent->update([
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
        ]);
        
        Log::info('Registration details updated successfully');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Agent registration details updated successfully',
            'data' => $regagent
        ]);
        
    }  catch (\Exception $e) {
        Log::error('Update failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update registration: ' . $e->getMessage()
        ], 500);
    }
}
}