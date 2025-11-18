<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Agents;
use Illuminate\Support\Facades\Log;
use App\Services\SummaryDataService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    protected $summaryService;

    public function __construct(SummaryDataService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    public function index()
    {
        return view('students.preports');
    }

    /**
     * Get summary data for dropdowns
     */
    public function getSummaryData(Request $request)
    {
        try {
            // Get allowed payroll from session
            $allowedPayroll = session('allowedPayroll', []);
            
            if (empty($allowedPayroll)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 401);
            }

            // Get summary data with caching
            $data = $this->summaryService->getSummaryData($allowedPayroll);

            return response()->json([
                'success' => true,
                'periodOptions' => $data['periodOptions'],
                'pnameOptions' => $data['pnameOptions'],
                'statutoryOptions' => $data['statutoryOptions'],
                'snameOptions' => $data['snameOptions']
            ]);

        } catch (\Exception $e) {
            Log::error('Summary data error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while fetching data'
            ], 500);
        }
    }

    /**
     * Clear summary cache
     */
    public function clearCache(Request $request)
    {
        try {
            $allowedPayroll = session('allowedPayroll', []);
            $this->summaryService->clearCache($allowedPayroll);

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache'
            ], 500);
        }
    }


    public function search(Request $request)
    {
        $allowedPayroll = session('allowedPayroll', []);
        
        if (empty($allowedPayroll)) {
            return response()->json(['results' => []]);
        }

        $search = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 30;

        try {
            // Create cache key based on allowed payroll
            $cacheKey = 'staff_list_' . md5(json_encode($allowedPayroll));
            
            // Get staff data from cache or database
            $allStaff = Cache::remember($cacheKey, 300, function () use ($allowedPayroll) {
                return Agents::select(
                        'tblemployees.emp_id as WorkNo',
                        DB::raw("CONCAT(tblemployees.FirstName, ' ', tblemployees.LastName) as fullname")
                    )
                    ->join('registration', 'tblemployees.emp_id', '=', 'registration.empid')
                    ->whereIn('registration.payrolty', $allowedPayroll)
                    ->where('tblemployees.Status', 'ACTIVE')
                    ->groupBy('tblemployees.emp_id', 'tblemployees.FirstName', 'tblemployees.LastName')
                    ->orderBy('tblemployees.FirstName')
                    ->get()
                    ->toArray();
            });

            // Filter by search term
            $filteredStaff = $this->filterStaff($allStaff, $search);

            // Paginate results
            $paginatedStaff = $this->paginateResults($filteredStaff, $page, $perPage);

            // Format for Select2
            $formattedResults = $this->formatSelect2Results($paginatedStaff);

            return response()->json([
                'results' => $formattedResults,
                'pagination' => [
                    'more' => ($page * $perPage) < count($filteredStaff)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Staff selection error: ' . $e->getMessage());
            
            return response()->json([
                'results' => [],
                'error' => 'An error occurred while fetching staff'
            ], 500);
        }
    }

    /**
     * Filter staff by search term
     */
    private function filterStaff(array $staff, string $search): array
    {
        if (empty($search)) {
            return $staff;
        }

        $searchLower = strtolower($search);
        
        return array_filter($staff, function($staffMember) use ($searchLower) {
            return stripos($staffMember['WorkNo'], $searchLower) !== false ||
                   stripos($staffMember['fullname'], $searchLower) !== false;
        });
    }

    /**
     * Paginate staff results
     */
    private function paginateResults(array $staff, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        return array_slice($staff, $offset, $perPage);
    }

    /**
     * Format results for Select2
     */
    private function formatSelect2Results(array $staff): array
    {
        return array_map(function($item) {
            return [
                'id' => $item['WorkNo'],
                'text' => $item['WorkNo'] . ' - ' . $item['fullname']
            ];
        }, $staff);
    }
}