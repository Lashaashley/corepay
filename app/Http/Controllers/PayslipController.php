<?php

namespace App\Http\Controllers;

use App\Services\PayslipService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PayslipController extends Controller
{
    protected $payslipService;

    public function __construct(PayslipService $payslipService)
    {
        $this->payslipService = $payslipService;
    }

    /**
     * Generate payslip PDF
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'staffid' => 'required|string',
            'period' => 'required|string'
        ]);

        try {
            $staffid = $request->input('staffid');
            $period = $request->input('period');

            // Extract month and year from period (format: "August2024")
            $month = substr($period, 0, -4);
            $year = substr($period, -4);

            $pdfData = $this->payslipService->generatePayslip($staffid, $month, $year);

            return response()->json([
                'pdf' => base64_encode($pdfData)
            ]);

        } catch (\Exception $e) {
            Log::error('Payslip generation error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to generate PDF'
            ], 500);
        }
    }
}