<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; // Correct PDF import
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditTrailExport;

class AuditController extends Controller
{
    public function index()
    {
        // Get all users for filter dropdown
        $users = User::select('id', 'name')->orderBy('name')->get();
        
        // Get distinct table names from audit trail
        $tables = AuditTrail::select('table_name')
            ->distinct()
            ->orderBy('table_name')
            ->pluck('table_name');
        
        return view('students.vaudit', compact('users', 'tables'));
    }

    /**
     * Get audit trail data for DataTables
     */
    public function getData(Request $request)
    {
        try {
            $draw = $request->get('draw', 1);
            $start = $request->get('start', 0);
            $length = $request->get('length', 50);
            $searchValue = $request->get('search')['value'] ?? '';
            $orderColumn = $request->get('order')[0]['column'] ?? 0;
            $orderDir = $request->get('order')[0]['dir'] ?? 'desc';

            // Column mapping for ordering
            $columns = [
                0 => 'id',
                1 => 'id',
                2 => 'created_at',
                3 => 'user_id',
                4 => 'action',
                5 => 'table_name',
                6 => 'record_id',
                7 => 'ip_address'
            ];

            // Base query with relationships
            $query = AuditTrail::select([
                'audittrail.id',
                'audittrail.user_id',
                'audittrail.action',
                'audittrail.table_name',
                'audittrail.record_id',
                'audittrail.old_values',
                'audittrail.new_values',
                'audittrail.context_data',
                'audittrail.ip_address',
                'audittrail.user_agent',
                'audittrail.created_at',
                'users.name as user_name',
                DB::raw("CONCAT(COALESCE(tblemployees.FirstName, ''), ' ', COALESCE(tblemployees.LastName, '')) as affected_user_name")
            ])
            ->leftJoin('users', 'audittrail.user_id', '=', 'users.id')
            ->leftJoin('tblemployees', 'audittrail.record_id', '=', 'tblemployees.emp_id');

            // Apply filters from the filter form
            $this->applyFilters($query, $request);

            // Search functionality
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('audittrail.user_id', 'like', "%{$searchValue}%")
                      ->orWhere('audittrail.action', 'like', "%{$searchValue}%")
                      ->orWhere('audittrail.table_name', 'like', "%{$searchValue}%")
                      ->orWhere('audittrail.record_id', 'like', "%{$searchValue}%")
                      ->orWhere('audittrail.ip_address', 'like', "%{$searchValue}%")
                      ->orWhere('users.name', 'like', "%{$searchValue}%")
                      ->orWhereRaw("CONCAT(tblemployees.FirstName, ' ', tblemployees.LastName) like ?", ["%{$searchValue}%"]);
                });
            }

            // Get total records before pagination
            $totalRecords = AuditTrail::count();
            $filteredRecords = $query->count();

            // Apply ordering
            $orderColumnName = $columns[$orderColumn] ?? 'audittrail.id';
            $query->orderBy($orderColumnName, $orderDir);

            // Apply pagination
            $audits = $query->skip($start)->take($length)->get();

            // Format data for DataTable
            $data = [];
            foreach ($audits as $audit) {
                $data[] = [
                    'id' => $audit->id,
                    'user_id' => $audit->user_id,
                    'user_name' => $audit->user_name ?? 'Unknown User',
                    'action' => $audit->action,
                    'table_name' => $audit->table_name,
                    'record_id' => $audit->record_id,
                    'affected_user_name' => !empty(trim($audit->affected_user_name)) ? $audit->affected_user_name : null,
                    'old_values' => $audit->old_values,
                    'new_values' => $audit->new_values,
                    'context_data' => $audit->context_data,
                    'ip_address' => $audit->ip_address,
                    'user_agent' => $audit->user_agent,
                    'created_at' => $audit->created_at,
                ];
            }

            $response = [
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('AuditController getData error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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
     * Apply filters to the query
     */
    private function applyFilters($query, Request $request)
    {
        // Report type based filters
        $reportType = $request->get('report_type');

        // User filter
        if ($request->filled('user_id')) {
            $query->where('audittrail.user_id', $request->user_id);
        }

        // Action filter
        if ($request->filled('action')) {
            $query->where('audittrail.action', $request->action);
        }

        // Table filter
        if ($request->filled('table_name')) {
            $query->where('audittrail.table_name', $request->table_name);
        }

        // Record ID filter
        if ($request->filled('record_id')) {
            $query->where('audittrail.record_id', $request->record_id);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('audittrail.created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('audittrail.created_at', '<=', $request->to_date);
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $fileName = 'audit_trail_' . date('Y-m-d_His') . '.xlsx';
            
            return Excel::download(
                new AuditTrailExport($request->all()), 
                $fileName
            );

        } catch (\Exception $e) {
            Log::error('Audit trail Excel export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Export to PDF
     */
    public function viewPdf(Request $request)
{
    try {
        // Build query with filters
        $query = AuditTrail::select([
            'audittrail.id',
            'audittrail.user_id',
            'audittrail.action',
            'audittrail.table_name',
            'audittrail.record_id',
            'audittrail.old_values',
            'audittrail.new_values',
            'audittrail.context_data',
            'audittrail.ip_address',
            'audittrail.created_at',
            'users.name as user_name',
            DB::raw("CONCAT(COALESCE(tblemployees.FirstName, ''), ' ', COALESCE(tblemployees.LastName, '')) as affected_user_name")
        ])
        ->leftJoin('users', 'audittrail.user_id', '=', 'users.id')
        ->leftJoin('tblemployees', 'audittrail.record_id', '=', 'tblemployees.emp_id');
        
        $this->applyFilters($query, $request);
        
        $audits = $query->orderBy('audittrail.created_at', 'desc')->get();
        
        // Get filter summary
        $filterSummary = $this->getFilterSummary($request);
        
        // Generate statistics
        $statistics = [
            'total_records' => $audits->count(),
            'date_range' => $filterSummary['date_range'] ?? 'All Time',
            'actions_breakdown' => $audits->groupBy('action')->map->count(),
            'tables_affected' => $audits->groupBy('table_name')->map->count(),
        ];
        
        $pdf = Pdf::loadView('students.audit_pdf', [
            'audits' => $audits,
            'filterSummary' => $filterSummary,
            'statistics' => $statistics,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => session('user_id')
        ]);
        
        $pdf->setPaper('a4', 'landscape');
        
        // Stream instead of download
        return $pdf->stream('audit_trail_preview.pdf');
        
    } catch (\Exception $e) {
        Log::error('Audit trail PDF preview failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Failed to generate PDF preview: ' . $e->getMessage()
        ], 500);
    }
}
    public function exportPdf(Request $request)
    {
        try {
            // Build query with filters
            $query = AuditTrail::select([
                'audittrail.id',
                'audittrail.user_id',
                'audittrail.action',
                'audittrail.table_name',
                'audittrail.record_id',
                'audittrail.old_values',
                'audittrail.new_values',
                'audittrail.context_data',
                'audittrail.ip_address',
                'audittrail.created_at',
                'users.name as user_name',
                DB::raw("CONCAT(COALESCE(tblemployees.FirstName, ''), ' ', COALESCE(tblemployees.LastName, '')) as affected_user_name")
            ])
            ->leftJoin('users', 'audittrail.user_id', '=', 'users.id')
            ->leftJoin('tblemployees', 'audittrail.record_id', '=', 'tblemployees.emp_id');

            $this->applyFilters($query, $request);
            
            $audits = $query->orderBy('audittrail.created_at', 'desc')->get();

            // Get filter summary
            $filterSummary = $this->getFilterSummary($request);

            // Generate statistics
            $statistics = [
                'total_records' => $audits->count(),
                'date_range' => $filterSummary['date_range'] ?? 'All Time',
                'actions_breakdown' => $audits->groupBy('action')->map->count(),
                'tables_affected' => $audits->groupBy('table_name')->map->count(),
            ];

            // Use the correct Pdf facade
            $pdf = Pdf::loadView('students.audit_pdf', [
                'audits' => $audits,
                'filterSummary' => $filterSummary,
                'statistics' => $statistics,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'generated_by' => session('user_id')
            ]);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'landscape');

            $fileName = 'audit_trail_' . date('Y-m-d_His') . '.pdf';
            
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Audit trail PDF export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export PDF file: ' . $e->getMessage());
        }
    }

    /**
     * Get filter summary for reports
     */
    private function getFilterSummary(Request $request)
    {
        $summary = [];

        if ($request->filled('report_type')) {
            $reportTypes = [
                'user_activity' => 'User Activity Report',
                'action_type' => 'Action Type Report',
                'record_history' => 'Record History Report',
                'table_activity' => 'Table Activity Report',
                'comprehensive' => 'Comprehensive Audit Report'
            ];
            $summary['report_type'] = $reportTypes[$request->report_type] ?? 'Custom Report';
        }

        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
            $summary['user'] = $user ? $user->name : 'Unknown';
        }

        if ($request->filled('action')) {
            $summary['action'] = $request->action;
        }

        if ($request->filled('table_name')) {
            $summary['table'] = $request->table_name;
        }

        if ($request->filled('record_id')) {
            $summary['record_id'] = $request->record_id;
        }

        $fromDate = $request->from_date ?? 'Beginning';
        $toDate = $request->to_date ?? 'Now';
        $summary['date_range'] = "{$fromDate} to {$toDate}";

        return $summary;
    }

    /**
     * Get audit detail
     */
    public function getDetail($id)
    {
        try {
            $audit = AuditTrail::select([
                'audittrail.*',
                'users.name as user_name',
                DB::raw("CONCAT(COALESCE(tblemployees.FirstName, ''), ' ', COALESCE(tblemployees.LastName, '')) as affected_user_name")
            ])
            ->leftJoin('users', 'audittrail.user_id', '=', 'users.id')
            ->leftJoin('tblemployees', 'audittrail.record_id', '=', 'tblemployees.emp_id')
            ->where('audittrail.id', $id)
            ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'audit' => $audit
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get audit detail', [
                'audit_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load audit detail'
            ], 500);
        }
    }
}