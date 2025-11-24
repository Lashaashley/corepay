<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pperiod;
use Illuminate\Support\Facades\Log;
use App\Services\PeriodClosingService;

class PeriodClosingController extends Controller
{
    public function index()
    {
         $period = Pperiod::where('sstatus', 'Active')->first();

    return view('students.closep', [
            'month' => $period->mmonth ?? '',
            'year'  => $period->yyear ?? ''
        ]);
    }
    public function closePeriod(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer'
        ]);

        $month = $request->month;
        $year = $request->year;

        try {
            $closingService = new PeriodClosingService($month, $year);
            $results = $closingService->executePeriodClosing();

            return response()->json([
                'status' => 'success',
                'message' => "Period {$month} {$year} closed successfully",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error("Period closing error: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to close period: ' . $e->getMessage()
            ], 500);
        }
    }
}