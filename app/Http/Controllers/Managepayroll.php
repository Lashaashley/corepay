<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EmployeeDeduction;
use App\Models\Agents;
use App\Models\Registration;
use App\Models\Pperiod;
use App\Models\Hlevy;
use App\Models\Shif;
use App\Models\Nhif;
use App\Models\Nssf;
use App\Models\Pension;
use App\Models\Ptype;
use App\Helpers\helpers;
use App\Services\PayrollSubmissionService; 
use Illuminate\Support\Facades\Auth;
class Managepayroll extends Controller
{

    protected $payrollService;

    public function __construct(PayrollSubmissionService $payrollService)
    {
        $this->payrollService = $payrollService;
    }


    public function index()
    {
         $period = Pperiod::where('sstatus', 'Active')->first();

    return view('students.mngprol', [
            'month' => $period->mmonth ?? '',
            'year'  => $period->yyear ?? '',
            'nhif'      => Nhif::first()->hstatus ?? '',
            'nssf'      => Nssf::first()->hstatus ?? '',
            'shif'      => Shif::first()->hstatus ?? '',
            'pension'   => Pension::first()->hstatus ?? '',
            'hlevy'     => Hlevy::first()->hstatus ?? ''
        ]);
    }
public function getDeductions(Request $request)
{
    try {
         $userId = session('user_id') ?? Auth::id(); // Get authenticated user ID
        
        // Get pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $searchQuery = $request->get('search', '');

        // Get allowed payroll types from session
        $allowedPayroll = session('allowedPayroll', []);
        
        if (empty($allowedPayroll)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No payroll access configured'
            ], 403);
        }

        // Get active period
        $activePeriod = Pperiod::getActivePeriod();
        
        if (!$activePeriod) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active payroll period found'
            ], 404);
        }

        $month = $activePeriod->mmonth;
        $year = $activePeriod->yyear;

        // Build query
        $query = EmployeeDeduction::select(
                'employeedeductions.*',
                'tbldepartments.DepartmentName'
            )
            ->join('tbldepartments', 'employeedeductions.dept', '=', 'tbldepartments.id')
            ->join('registration', 'employeedeductions.WorkNo', '=', 'registration.empid')
            ->whereIn('registration.payrolty', $allowedPayroll)
            ->where('employeedeductions.Amount', '>', 0);

        // Apply search or period filter
        if (!empty($searchQuery)) {
            $query->search($searchQuery);
        } else {
            $query->where('employeedeductions.month', $month)
                  ->where('employeedeductions.year', $year);
        }

        // Order by ID descending
        $query->orderBy('employeedeductions.ID', 'DESC');

        // Get paginated results
        $deductions = $query->paginate($perPage);

        // Log audit trail for VIEW action
        logAuditTrail(
            $userId,
            'VIEW',
            'employeedeductions',
            null, // No specific record ID for list view
            null,
            null,
            [
                'action' => 'view_deductions_list',
                'filters' => [
                    'month' => $month,
                    'year' => $year,
                    'search' => $searchQuery,
                    'page' => $page,
                    'per_page' => $perPage
                ],
                'result_count' => $deductions->total()
            ]
        );

        // Format data for view
        $data = $deductions->map(function($deduction) {
            return [
                'id' => $deduction->ID,
                'full_name' => trim($deduction->Surname . ' ' . $deduction->othername),
                'work_no' => $deduction->WorkNo,
                'department' => $deduction->DepartmentName,
                'code' => $deduction->PCode,
                'name' => $deduction->pcate,
                'category' => $deduction->loanshares,
                'amount' => number_format($deduction->Amount, 2)
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'pagination' => [
                'current_page' => $deductions->currentPage(),
                'last_page' => $deductions->lastPage(),
                'per_page' => $deductions->perPage(),
                'total' => $deductions->total(),
                'from' => $deductions->firstItem(),
                'to' => $deductions->lastItem()
            ],
            'period' => [
                'month' => $month,
                'year' => $year
            ]
        ]);

    } catch (\Exception $e) {
        // Log error to audit trail
        logAuditTrail(
           $userId,
            'ERROR',
            'employeedeductions',
            null,
            null,
            null,
            [
                'error_message' => $e->getMessage(),
                'action' => 'view_deductions_list'
            ]
        );

        Log::error('Deductions fetch error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Error fetching deductions: ' . $e->getMessage()
        ], 500);
    }
}

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'model'  => 'required|string',
            'status' => 'required|string',
        ]);

        // Map model alias → real model class
        $map = [
            'nhif'    => Nhif::class,
            'nssf'    => Nssf::class,
            'shif'    => Shif::class,
            'pension' => Pension::class,
            'hlevy'   => Hlevy::class,
        ];

        if (!isset($map[$request->model])) {
            return response()->json(['error' => 'Invalid model'], 400);
        }

        $model = $map[$request->model];

        $record = $model::first();
        $record->hstatus = $request->status;
        $record->save();

        return response()->json(['message' => 'Updated successfully']);
    }

    public function getAllpitems()
{
    // Fetch all branches
    $pitems = Ptype::all();

    return response()->json([
        'data' => $pitems,
    ]);
}

public function searchStaff(Request $request)
{
    $term = $request->term ?? '';

    $allowedPayroll = session('allowedPayroll', []);

    if (empty($allowedPayroll)) {
        return response()->json([
            'results' => []
        ]);
    }

    $employees = DB::table('tblemployees')
        ->join('registration', 'tblemployees.emp_id', '=', 'registration.empid')
        ->whereIn('registration.payrolty', $allowedPayroll)
        ->where('tblemployees.Status', 'ACTIVE')
        ->when($term, function ($query) use ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('tblemployees.emp_id', 'LIKE', "%$term%")
                  ->orWhere(DB::raw("CONCAT(tblemployees.FirstName, ' ', tblemployees.LastName)"), 'LIKE', "%$term%");
            });
        })
        ->select('tblemployees.emp_id', DB::raw("CONCAT(tblemployees.FirstName,' ',tblemployees.LastName) as fullname"))
        ->limit(30)
        ->get();

    $results = $employees->map(function ($e) {
        return [
            'emp_id' => $e->emp_id,
            'label'  => "{$e->emp_id} - {$e->fullname}"
        ];
    });

    return response()->json([
        'results' => $results
    ]);
}

public function searchStaffDetails(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'searchCategory' => 'required|string|max:20',
        'searchValue'    => 'required|string|max:100',
        'category'       => 'nullable|string|max:20',
        'code'           => 'nullable|string|max:20',
        'codes'          => 'array',
        'codes.*'        => 'string|max:20'
    ]);

    $allowedPayroll = session('allowedPayroll', []);

    if (empty($allowedPayroll)) {
        return response()->json([
            'success' => false,
            'message' => 'Access denied: Invalid payroll permissions'
        ]);
    }

    $searchCategory = $validated['searchCategory'];
    $searchValue    = trim($validated['searchValue']);
    $category       = $validated['category'] ?? null;
    $code           = $validated['code'] ?? null;
    $codes          = $validated['codes'] ?? [];

    // Mapping
    $columnMap = [
        'Surname'    => 'LastName',
        'othername'  => 'FirstName',
        'WorkNumber' => 'emp_id',
        'Department' => 'Department',
        'ALL'        => 'ALL'
    ];

    if (!isset($columnMap[$searchCategory])) {
        return response()->json(['success' => false, 'message' => 'Invalid search category']);
    }

    try {
        // --------------------------
        // 1. Employee Basic Details
        // --------------------------
        if ($searchCategory === 'ALL') {
            $employee = DB::table('tblemployees AS e')
                ->join('tbldepartments AS d', 'e.Department', '=', 'd.id')
                ->join('registration AS r', 'e.emp_id', '=', 'r.empid')
                ->where(function ($q) use ($searchValue) {
                    $q->where('e.LastName', $searchValue)
                      ->orWhere('e.FirstName', $searchValue)
                      ->orWhere('e.emp_id', $searchValue)
                      ->orWhere('e.Department', $searchValue);
                })
                ->where('e.Status', 'ACTIVE')
                ->whereIn('r.payrolty', $allowedPayroll)
                ->select('e.emp_id', 'e.LastName', 'e.FirstName', 'e.Department', 'd.DepartmentName')
                ->first();
        } else {
            $employee = DB::table('tblemployees AS e')
                ->join('tbldepartments AS d', 'e.Department', '=', 'd.id')
                ->join('registration AS r', 'e.emp_id', '=', 'r.empid')
                ->where("e.".$columnMap[$searchCategory], $searchValue)
                ->where('e.Status', 'ACTIVE')
                ->whereIn('r.payrolty', $allowedPayroll)
                ->select('e.emp_id', 'e.LastName', 'e.FirstName', 'e.Department', 'd.DepartmentName')
                ->first();
        }

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found']);
        }

        $empId = $employee->emp_id;

        $response = [
            'surname'        => $employee->LastName,
            'othername'      => $employee->FirstName,
            'workNumber'     => $empId,
            'department'     => $employee->Department,
            'departmentname' => $employee->DepartmentName
        ];

        // --------------------------
        // 2. Deduction Codes
        // --------------------------
        $existingCodes = [];
        if (!empty($codes)) {
            $codesResult = DB::table('employeedeductions')
                ->where('WorkNo', $empId)
                ->whereIn('PCode', $codes)
                ->where(function ($q) {
                    $q->whereNull('quantity')->orWhere('quantity', '');
                })
                ->pluck('Amount', 'PCode')
                ->toArray();

            foreach ($codes as $c) {
                $existingCodes[$c] = $codesResult[$c] ?? '';
            }
        }

        $response['existingCodes'] = $existingCodes;

        // --------------------------
        // 3. Financial Data (Loan / Balance)
        // --------------------------
        if ($code && in_array($category, ['balance', 'loan'])) {
            $fin = DB::table('employeedeductions')
                ->where('WorkNo', $empId)
                ->where('PCode', $code)
                ->select('balance', 'Amount', 'statdeduc')
                ->first();

            $response['balance']   = $fin->balance   ?? '';
            $response['statdeduc'] = $fin->statdeduc ?? '';
            $response['Amount']    = $fin->Amount    ?? '';
        }

        // --------------------------
        // 4. Pension Rates
        // --------------------------
        $pension = DB::table('emppensionrates')
            ->where('WorkNo', $empId)
            ->select('epmpenperce', 'emplopenperce')
            ->first();

        $response['epmpenperce']  = $pension->epmpenperce  ?? '';
        $response['emplopenperce'] = $pension->emplopenperce ?? '';

        // --------------------------
        // 5. Pension Group Total
        // --------------------------
        $pensionTotal = DB::table('pensiongroups AS pg')
            ->join('employeedeductions AS ed', 'pg.code', '=', 'ed.PCode')
            ->where('ed.WorkNo', $empId)
            ->sum('ed.Amount');

        $response['totalPensionAmount'] = $pensionTotal ?? 0;

        return response()->json(['success' => true] + $response);

    } catch (\Exception $e) {
        Log::error("Search error: ".$e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error occurred']);
    }
}

public function fetchItems(Request $request)
{
    $request->validate([
        'parameter' => 'required|string|max:50'
    ]);

    $parameter = trim($request->parameter);
    $allowedPayroll = session('allowedPayroll', []);

    if (empty($allowedPayroll)) {
        return response()->json([
            'success' => false,
            'message' => 'No allowed payroll types'
        ]);
    }

    try {
        $items = DB::table('employeedeductions AS ed')
            ->join('registration AS r', 'ed.WorkNo', '=', 'r.empid')
            ->where('ed.pcate', $parameter)
            ->whereIn('r.payrolty', $allowedPayroll)
            ->select(
                'ed.Surname',
                'ed.othername',
                'ed.WorkNo',
                'ed.dept',
                'ed.PCode',
                'ed.Amount'
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);

    } catch (\Exception $e) {
        Log::error("FetchItems Error: ".$e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Server error occurred'
        ]);
    }
}


public function submitPayroll(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'month' => 'required|string',
                'year' => 'required|integer',
                'parameter' => 'required|string',
                'surname' => 'required|string',
                'othername' => 'nullable|string',
                'workNumber' => 'required|string',
                'department' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'balance' => 'nullable|numeric',
                'quantity' => 'nullable|numeric',
                'category' => 'nullable|string',
                'openvalue' => 'nullable|string',
                'otdate' => 'nullable|date',
                'epmpenperce' => 'nullable|numeric',
                'emplopenperce' => 'nullable|numeric'
            ]);

            $userId = session('user_id') ?? Auth::id();

            // Process submission
            $result = $this->payrollService->processSubmission($validated, $userId);

            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

}
