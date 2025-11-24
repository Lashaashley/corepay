<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use App\Services\IFTReportService;
use App\Services\EFTReportService;
use App\Services\RTGSReportService;
use PhpOffice\PhpSpreadsheet\Writer\Csv; // Add this import

class ExcelGenerationController extends Controller
{
    public function generateIFTReport(Request $request)
    {
        $request->validate([
            'period' => 'required|string'
        ]);

        // Get allowed payroll types from session
        $allowedPayrollTypes = session('allowedPayroll', []);

        if (empty($allowedPayrollTypes)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $period = $request->period;

        try {
            $reportService = new IFTReportService($period, $allowedPayrollTypes);
            $spreadsheet = $reportService->generate();
            $fileName = $reportService->getFileName();

            // Create CSV writer - this should now work with the import
            $writer = new Csv($spreadsheet);
            $writer->setEnclosure('"');
            $writer->setDelimiter(',');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);
            $writer->setUseBOM(true);

            return Response::stream(function() use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'public'
            ]);

        } catch (\Exception $e) {
            Log::error("IFT Report Controller Error: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate Excel file'
            ], 500);
        }
    }
    public function generateRTGSReport(Request $request)
    {
        $request->validate([
            'period' => 'required|string'
        ]);

        // Get allowed payroll types from session
        $allowedPayrollTypes = session('allowedPayroll', []);

        if (empty($allowedPayrollTypes)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $period = $request->period;

        try {
            $reportService = new RTGSReportService($period, $allowedPayrollTypes);
            $spreadsheet = $reportService->generate();
            $fileName = $reportService->getFileName();

            $writer = new Csv($spreadsheet);
            $writer->setEnclosure('"');
            $writer->setDelimiter(',');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);
            $writer->setUseBOM(true);

            return Response::stream(function() use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'public'
            ]);

        } catch (\Exception $e) {
            Log::error("RTGS Report Controller Error: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate Excel file'
            ], 500);
        }
    }
    public function generateEFTReport(Request $request)
    {
        $request->validate([
            'period' => 'required|string'
        ]);

        // Get allowed payroll types from session
        $allowedPayrollTypes = session('allowedPayroll', []);

        if (empty($allowedPayrollTypes)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $period = $request->period;

        try {
            $reportService = new EFTReportService($period, $allowedPayrollTypes);
            $spreadsheet = $reportService->generate();
            $fileName = $reportService->getFileName();

            $writer = new Csv($spreadsheet);
            $writer->setEnclosure('"');
            $writer->setDelimiter(',');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);
            $writer->setUseBOM(true);

            return Response::stream(function() use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'public'
            ]);

        } catch (\Exception $e) {
            Log::error("EFT Report Controller Error: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate Excel file'
            ], 500);
        }
    }
}