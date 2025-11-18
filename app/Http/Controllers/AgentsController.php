<?php

namespace App\Http\Controllers;

use App\Models\Agents;
use App\Models\Depts;
use App\Models\StaffType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get agents data for DataTable (AJAX)
     * This uses server-side processing for better performance
     */
    public function getData(Request $request)
{
    try {
        // ✅ Log incoming request
        Log::info('AgentsController getData: Request received', [
            'all_params' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';

        Log::info('AgentsController getData: Parsed parameters', [
            'draw' => $draw,
            'start' => $start,
            'length' => $length,
            'searchValue' => $searchValue,
            'orderColumn' => $orderColumn,
            'orderDir' => $orderDir
        ]);

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

        // ✅ Log the SQL query
        Log::info('AgentsController getData: Base query SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // Search functionality
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

        Log::info('AgentsController getData: Ordering applied', [
            'column' => $orderColumnName,
            'direction' => $orderDir
        ]);

        // Apply pagination
        $agents = $query->skip($start)->take($length)->get();

        Log::info('AgentsController getData: Query executed', [
            'agents_count' => $agents->count(),
            'first_agent' => $agents->first() ? $agents->first()->toArray() : null
        ]);

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

        Log::info('AgentsController getData: Data formatted', [
            'data_count' => count($data),
            'first_record' => $data[0] ?? null
        ]);

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        Log::info('AgentsController getData: Response prepared', [
            'response_structure' => [
                'draw' => $response['draw'],
                'recordsTotal' => $response['recordsTotal'],
                'recordsFiltered' => $response['recordsFiltered'],
                'data_count' => count($response['data'])
            ]
        ]);

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
}