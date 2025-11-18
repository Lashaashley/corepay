<?php

namespace App\Services;

use App\Models\Agents;
use App\Models\EmployeeDeduction;
use App\Models\Payhouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NetpayService
{
        /**
 * Calculate net pay for all employees
 * 
 * @param string $month
 * @param string $year
 * @return void
 * @throws \Exception
 */
public function calcNetPay($month, $year, array $allowedPayrollIds)
{
    
    try {
        DB::beginTransaction();

        

        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }
       
        $worknotest= '11122';
        // Get all employees for the given month & year with allowed payroll types
        $employees = DB::table('payhouse as p')
            ->select('p.WorkNo')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->where('p.WorkNo', $worknotest)
            ->whereIn('r.payrolty', $allowedPayrollIds)
            ->distinct()
            ->pluck('WorkNo')
            ->toArray();
        
            
        
        if (empty($employees)) {
            Log::warning('No employees found for net pay calculation');
            DB::commit();
            return;
        }
        
        // Process employees in chunks for better performance
        $chunks = array_chunk($employees, 100);
        
        foreach ($chunks as $chunkIndex => $employeeChunk) {
            
            
            // Debug: Check if deductions exist for these employees
            $deductionCount = EmployeeDeduction::whereIn('WorkNo', $employeeChunk)
                ->where('prossty', 'Deduction')
                ->where('month', $month)
                ->where('year', $year)
                ->where('statdeduc', '1')
                ->count();
            
            Log::info("Deductions query check", [
                'chunk' => $chunkIndex,
                'total_deductions_found' => $deductionCount,
                'query_params' => [
                    'month' => $month,
                    'year' => $year,
                    'statdeduc' => '1',
                    'sample_workno' => $employeeChunk[0] ?? 'none'
                ]
            ]);
            
            // Fetch all deductions for this chunk at once
            $allDeductions = EmployeeDeduction::whereIn('WorkNo', $employeeChunk)
                ->where('prossty', 'Deduction')
                ->where('month', $month)
                ->where('year', $year)
                ->where('statdeduc', '1')
                ->select('WorkNo', 'PCode', 'pcate', 'Amount', 'balance', 'loanshares', 'increREDU')
                ->get();
            
            Log::info("Deductions fetched", [
                'chunk' => $chunkIndex,
                'total_records' => $allDeductions->count(),
                'sample' => $allDeductions->first()
            ]);
            
            // Group by WorkNo
            $groupedDeductions = $allDeductions->groupBy('WorkNo');
            
            Log::info("Deductions grouped", [
                'chunk' => $chunkIndex,
                'unique_employees_with_deductions' => $groupedDeductions->count(),
                'employees_keys' => $groupedDeductions->keys()->toArray()
            ]);
            
            // Process each employee in the chunk
            foreach ($employeeChunk as $workNo) {
                // Process deductions for this employee
                $deductions = $groupedDeductions->get($workNo, collect());
                
                Log::info("Processing employee", [
                    'WorkNo' => $workNo,
                    'deductions_count' => $deductions->count(),
                    'has_deductions' => $deductions->isNotEmpty()
                ]);
                
                $this->processEmployeeDeductions($workNo, $month, $year, $deductions);
                
                // Calculate and insert net pay
                $this->calculateEmployeeNetPay($workNo, $month, $year);
            }
        }
        
        DB::commit();
        
        Log::info('calcNetPay completed successfully');
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in calcNetPay: ' . $e->getMessage(), [
            'month' => $month,
            'year' => $year,
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}


private function processEmployeeDeductions($workNo, $month, $year, $deductions)
{
    if ($deductions->isEmpty()) {
        return;
    }
    
    $insertData = [];
    
    foreach ($deductions as $deduction) {
        $pcate = $deduction->pcate;
        $amount = (float) $deduction->Amount;
        $itemcode = $deduction->PCode;
        $loanshares = $deduction->loanshares;
        $increREDU = $deduction->increREDU;
        $balance = (float) $deduction->balance;
        
        $newBalance = null;
        
        if (in_array($loanshares, ['balance', 'loan', 'interest'])) {
            if ($increREDU === 'Increasing') {
                $newBalance = $balance + $amount;
            } else {
                if ($balance > 0) {
                    $newBalance = $balance - $amount;
                }
            }
        }
        
        $insertData[] = [
            'WorkNo' => $workNo,
            'pname' => $pcate,
            'itemcode' => $itemcode,
            'pcategory' => 'Deduction',
            'loanshares' => $loanshares ?? '',
            'tamount' => round($amount, 2),
            'balance' => $newBalance ? round($newBalance, 2) : null,
            'month' => $month,
            'year' => $year,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
    
    if (!empty($insertData)) {
        try {
            // Use try-catch to see any specific errors
            DB::table('payhouse')->insert($insertData);
            
            Log::info('Successfully inserted deductions', [
                'WorkNo' => $workNo,
                'count' => count($insertData),
                'sample_data' => $insertData[0] // Log first record for debugging
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to insert deductions', [
                'WorkNo' => $workNo,
                'error' => $e->getMessage(),
                'data_sample' => $insertData[0] ?? 'No data'
            ]);
            throw $e;
        }
    }
}

/**
 * Calculate and insert net pay for an employee
 * 
 * @param string $workNo
 * @param string $month
 * @param string $year
 * @return void
 */
private function calculateEmployeeNetPay($workNo, $month, $year)
{
    // Fetch Total Gross Salary
    $totalGrossSalary = Payhouse::where('pname', 'Total Gross Salary')
        ->where('WorkNo', $workNo)
        ->where('month', $month)
        ->where('year', $year)
        ->sum('tamount') ?? 0;
    
    // Fetch Total Deductions
    $totalDeductions = Payhouse::where('WorkNo', $workNo)
        ->where('pcategory', 'Deduction')
        ->where('month', $month)
        ->where('year', $year)
        ->sum('tamount') ?? 0;
    
    // Calculate Net Pay
    $netPay = $totalGrossSalary - $totalDeductions;
    
    // Insert Net Pay record
    Payhouse::create([
        'WorkNo' => $workNo,
        'pname' => 'NET PAY',
        'pcategory' => 'NET',
        'itemcode' => 'P99',
        'tamount' => round($netPay, 2),
        'month' => $month,
        'year' => $year
    ]);
}
}