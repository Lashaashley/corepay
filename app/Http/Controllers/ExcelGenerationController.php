<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\IFTReportService;
use App\Services\EFTReportService;
use App\Services\RTGSReportService;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelGenerationController extends Controller
{
    public function generateIFTReport(Request $request)
{
    $request->validate([
        'period' => 'required|string'
    ]);

    $allowedPayrollTypes = session('allowedPayroll', []);
    $userId = Auth::id();

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
        $fileName = $reportService->getFileName(); // now returns .xlsx

        $writer = new Xlsx($spreadsheet); // ✅ swapped from Csv

        logAuditTrail(
            $userId,
            'OTHER',
            'ift_report_generation',
            $period,
            null,
            null,
            [
                'action' => 'ift_report_generated',
                'period' => $period,
                'allowed_payrolls' => $allowedPayrollTypes,
                'file_name' => $fileName
            ]
        );

        return Response::stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // ✅ xlsx mime
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'public'
        ]);

    } catch (\Throwable $e) {  // was \Exception
    Log::error("RTGS Report Controller Error: " . $e->getMessage(), [
        'type' => get_class($e),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

        logAuditTrail(
            $userId,
            'OTHER',
            'ift_report_generation',
            $period,
            null,
            null,
            [
                'action' => 'ift_report_generation_failed',
                'period' => $period,
                'allowed_payrolls' => $allowedPayrollTypes,
                'error' => $e->getMessage()
            ]
        );

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

    $allowedPayrollTypes = session('allowedPayroll', []);
    $userId = Auth::id();

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
        $fileName = $reportService->getFileName(); // now returns .xlsx

        $writer = new Xlsx($spreadsheet); // ✅ swapped from Csv

        logAuditTrail(
            $userId,
            'OTHER',
            'rtgs_report_generation',
            $period,
            null,
            null,
            [
                'action' => 'rtgs_report_generated',
                'period' => $period,
                'allowed_payrolls' => $allowedPayrollTypes,
                'file_name' => $fileName
            ]
        );

        return Response::stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // ✅ xlsx mime
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'public'
        ]);

    } catch (\Throwable $e) {  // was \Exception
    Log::error("RTGS Report Controller Error: " . $e->getMessage(), [
        'type' => get_class($e),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

        logAuditTrail(
            $userId,
            'OTHER',
            'rtgs_report_generation',
            $period,
            null,
            null,
            [
                'action' => 'rtgs_report_generation_failed',
                'period' => $period,
                'allowed_payrolls' => $allowedPayrollTypes,
                'error' => $e->getMessage()
            ]
        );

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

    $allowedPayrollTypes = session('allowedPayroll', []);
    $userId = Auth::id();

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
        $fileName = $reportService->getFileName(); // now returns .xlsx

        $writer = new Xlsx($spreadsheet); // ✅ swapped from Csv

        logAuditTrail(
            $userId,
            'OTHER',
            'eft_report_generation',
            $period,
            null,
            null,
            [
                'action' => 'eft_report_generated',
                'period' => $period,
                'allowed_payrolls' => $allowedPayrollTypes,
                'file_name' => $fileName
            ]
        );

        return Response::stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // ✅ xlsx mime
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'public'
        ]);

    } catch (\Throwable $e) {  // was \Exception
    Log::error("EFT Report Controller Error: " . $e->getMessage(), [
        'type' => get_class($e),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

        logAuditTrail(
            $userId,
            'OTHER',
            'eft_report_generation',
            $period,
            null,
            null,
            [
                'action' => 'eft_report_generation_failed',
                'period' => $period,
                'allowed_payrolls' => $allowedPayrollTypes,
                'error' => $e->getMessage()
            ]
        );

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to generate Excel file'
        ], 500);
    }
}
}