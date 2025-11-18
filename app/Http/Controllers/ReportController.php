<?php

namespace App\Http\Controllers;

use App\Services\StaffReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\OverallSummaryService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $staffReportService;
    protected $summaryService;

    public function __construct(
        StaffReportService $staffReportService,
        OverallSummaryService $summaryService
    ) {
        $this->staffReportService = $staffReportService;
        $this->summaryService = $summaryService;
    }
    

    public function fullStaffReport(Request $request)
    {
        try {
            $userId = session('user_id') ?? Auth::id();
            Log::info('Full Staff Report requested', [
                'user_id' => $userId,
                'ip' => $request->ip()
            ]);

            $pdfData = $this->staffReportService->generateFullStaffReport();
            
            return response()->json([
                'success' => true,
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Full Staff Report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }


    public function overallSummary(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|string'
        ]);

        try {
            $period = $request->input('period');
            
            // Extract month and year from period (format: "August2024")
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            $pdfData = $this->summaryService->generateOverallSummary($month, $year);

            return response()->json([
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Overall summary generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
}