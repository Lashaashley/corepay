<?php

namespace App\Http\Controllers;

use App\Services\DeductionImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT ADDED

class DeductionImportController extends Controller
{
    private $importService;
  
    public function __construct(DeductionImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show the import page
     */
    public function index()
    {
        return view('deductions.import');
    }
 
    /**
     * Handle the file upload and import with streaming progress
     */
    public function import(Request $request)
{
    set_time_limit(300); // or 0 for unlimited (use with caution)
    ini_set('max_execution_time', 300);
    
    $request->validate([
        'excelFile' => 'required|file|mimes:xlsx,xls|max:10240',
        'importMode' => 'required|in:fresh,update'
    ]);

    $file = $request->file('excelFile');
    $importMode = $request->input('importMode', 'update');

    try {
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Generate a unique download token
        $downloadToken = uniqid('import_exceptions_', true);

        return Response::stream(function () use ($file, $importMode, $downloadToken) {
            try {
                $filePath = $file->getRealPath();
                $lastProgress = null;
                
                foreach ($this->importService->import($filePath, $importMode) as $progress) {
                    $lastProgress = $progress;
                    
                    if ($progress['status'] !== 'success') {
                        echo json_encode($progress) . "\n";
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }
                
                if ($lastProgress && $lastProgress['status'] === 'success') {
                    // Get missing employees AND duplicates from the service
                    $missingEmployees = $this->importService->getMissingEmployees();
                    $duplicates = $this->importService->getDuplicates();
                    
                    // Check if we have ANY exceptions (missing employees OR duplicates)
                    $hasExceptions = !empty($missingEmployees) || !empty($duplicates);
                    
                    $finalResponse = [
                        'status' => 'completed',
                        'message' => $lastProgress['message'],
                        'success' => $lastProgress['success'],
                        'errors' => $lastProgress['errors'],
                        'errorDetails' => $lastProgress['errorDetails'] ?? [],
                        'hasExceptions' => $hasExceptions,
                        'missingEmployeesCount' => count($missingEmployees),
                        'duplicatesCount' => count($duplicates),
                        'downloadUrl' => null,
                        'downloadToken' => null
                    ];
                    
                    // Generate report if there are ANY exceptions
                    if ($hasExceptions) {
                        $reportData = $this->importService->generateMissingEmployeesReport($missingEmployees, $duplicates);
                        
                        if ($reportData) {
                            // Store filename in cache with token
                            Cache::put($downloadToken, $reportData['filename'], now()->addMinutes(30));
                            
                            $finalResponse['hasExceptions'] = true;
                            $finalResponse['missingEmployeesCount'] = count($missingEmployees);
                            $finalResponse['duplicatesCount'] = count($duplicates);
                            $finalResponse['downloadUrl'] = route('deductions.download.missing.employees', ['token' => $downloadToken]);
                            $finalResponse['downloadToken'] = $downloadToken;
                            
                            // Add descriptive message based on what was found
                            if (!empty($missingEmployees) && !empty($duplicates)) {
                                $finalResponse['exceptionMessage'] = "Found " . count($missingEmployees) . " missing employees and " . count($duplicates) . " duplicate records.";
                            } elseif (!empty($missingEmployees)) {
                                $finalResponse['exceptionMessage'] = "Found " . count($missingEmployees) . " missing employees.";
                            } elseif (!empty($duplicates)) {
                                $finalResponse['exceptionMessage'] = "Found " . count($duplicates) . " duplicate records.";
                            }
                        }
                    }
                    
                    echo json_encode($finalResponse) . "\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();

                    $this->importService->sendEarningsImportationEmail();
                }
                
            } catch (\Exception $e) {
                Log::error('Deduction Import Error: ' . $e->getMessage(), [
                    'file' => $file->getClientOriginalName(),
                    'trace' => $e->getTraceAsString()
                ]);

                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]) . "\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type' => 'application/json',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache'
        ]);

    } catch (\Exception $e) {
        Log::error('Deduction Import Error: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Download missing employees report
     */
    public function downloadMissingEmployees($token = null)
    {
        // Get token from request if not in URL
        $token = $token ?? request('token');
        
        if (!$token) {
            return response()->json(['error' => 'No download token provided'], 400);
        }
        
        $filename = Cache::get($token);
        
        if (!$filename) {
            return response()->json(['error' => 'Report not found or expired'], 404);
        }
        
        $filepath = storage_path('app/temp/' . $filename);
        
        if (!file_exists($filepath)) {
            return response()->json(['error' => 'Report file not found'], 404);
        }
        
        // Clean up cache
        Cache::forget($token);
        
        return response()->download($filepath, 'import_exception_report.xlsx')->deleteFileAfterSend(true);
    }
}