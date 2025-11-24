<?php

namespace App\Http\Controllers;

use App\Services\DeductionImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

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
        $request->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls|max:10240',
            'importMode' => 'required|in:fresh,update'
        ]);

        $file = $request->file('excelFile');
        $importMode = $request->input('importMode', 'update');

        try {
            // Disable output buffering for streaming
            if (ob_get_level()) {
                ob_end_clean();
            }

            return Response::stream(function () use ($file, $importMode) {
                try {
                    $filePath = $file->getRealPath();
                    
                    foreach ($this->importService->import($filePath, $importMode) as $progress) {
                        echo json_encode($progress) . "\n";
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                } catch (\Exception $e) {
                    Log::error('Deduction Import Error: ' . $e->getMessage(), [
                        'file' => $file->getClientOriginalName(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    echo json_encode([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ]);
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
}