<?php

namespace App\Services;

use App\Models\Agents;
use App\Models\EmployeeDeduction;
use App\Models\Pperiod;
use App\Models\Payhouse;
use App\Models\LoanShedule;
use App\Models\BalanceSched;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeriodClosingService
{
    protected $month;
    protected $year;
    protected $monthNumber;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
        $this->monthNumber = $this->monthNameToNumber($month);
    }

    /**
     * Convert month name to number
     */
    private function monthNameToNumber($monthName)
    {
        $months = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
            'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8,
            'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
        ];

        if (!isset($months[$monthName])) {
            throw new \Exception("Invalid month name: {$monthName}");
        }

        return $months[$monthName];
    }

    /**
     * Convert month number to name
     */
    private function numberToMonthName($monthNumber)
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        if (!isset($months[$monthNumber])) {
            throw new \Exception("Invalid month number: {$monthNumber}");
        }

        return $months[$monthNumber];
    }

    /**
     * Clear variable deductions for inactive employees
     */
    public function clearVariables()
    {
        try {
            // Get all inactive employee IDs
            $inactiveEmployeeIds = Agents::where('Status', 'INACTIVE')
                ->pluck('emp_id')
                ->toArray();

                 Log::info("Cleared {} variable deductions for inactive employees");

            // Delete variable deductions for inactive employees
            $deletedCount = EmployeeDeduction::where(function($query) use ($inactiveEmployeeIds) {
                $query->where('varorfixed', 'Variable')
                      ->orWhereIn('WorkNo', $inactiveEmployeeIds);
            })->delete();

            Log::info("Cleared {$deletedCount} variable deductions for inactive employees");
            return $deletedCount;

        } catch (\Exception $e) {
            Log::error("Error clearing variables: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Close the current period and open the next period
     */
    public function closePeriod()
    {
        try {
            // Calculate next period
            $nextMonthNumber = ($this->monthNumber % 12) + 1;
            $nextYear = ($this->monthNumber == 12) ? $this->year + 1 : $this->year;
            $nextMonthName = $this->numberToMonthName($nextMonthNumber);

            // Use transaction to ensure data consistency
            return DB::transaction(function () use ($nextMonthName, $nextYear) {
                // Close current active period
                $updated = Pperiod::where('sstatus', 'Active')->update(['sstatus' => 'Inactive']);
                
                if ($updated === false) {
                    throw new \Exception("Failed to close current active period");
                }

                // Create new active period
                $newPeriod = Pperiod::create([
                    'mmonth' => $nextMonthName,
                    'yyear' => $nextYear,
                    'sstatus' => 'Active'
                ]);

                if (!$newPeriod) {
                    throw new \Exception("Failed to create new active period");
                }

                return [
                    'next_month' => $nextMonthName,
                    'next_year' => $nextYear,
                    'closed_periods' => $updated
                ];
            });

        } catch (\Exception $e) {
            Log::error("Error closing period: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process loans (placeholder - implement based on your business logic)
     */
    public function processLoans()
    {
        try {
            $formattedPeriod = $this->month . $this->year;
            $allowedTypes =  session('allowedPayroll', []);

            
            $loans = DB::table('employeedeductions as ed')
                ->join('loanshedule as ls', function($join) use ($formattedPeriod) {
                    $join->on('ed.WorkNo', '=', 'ls.empid')
                         ->on('ed.PCode', '=', 'ls.loantype')
                         ->where('ls.Period', '=', $formattedPeriod);
                })
                ->join('registration as r', 'ed.WorkNo', '=', 'r.empid')
                ->where('ed.loanshares', 'loan')
                ->where('ed.month', $this->month)
                ->where('ed.year', $this->year)
                ->where('ed.statdeduc', '1')
                ->where('ls.statlon', '1')
                ->where('ed.recintres', '0')
                ->whereIn('r.payrolty', $allowedTypes)
                ->select(
                    'ed.ID as edID',
                    'ed.WorkNo',
                    'ed.PCode',
                    'ed.Amount',
                    'ed.balance',
                    'ed.rate',
                    'ls.ID as loanID',
                    'ls.interest',
                    'ls.loanamount',
                    'ls.precovery',
                    'ls.totalmonths',
                    'ls.balance as loanBalance',
                    'ls.statlon'
                )
                ->get();

            Log::info("Found " . $loans->count() . " loans to process for {$this->month} {$this->year}");

            $processedCount = 0;
            $errorCount = 0;

            foreach ($loans as $loan) {
                try {
                    DB::transaction(function () use ($loan, $processedCount) {
                        $this->processSingleLoan($loan);
                        $processedCount++;
                    });
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Loan processing error for WorkNo {$loan->WorkNo}: " . $e->getMessage());
                }
            }

            return [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'total' => $loans->count()
            ];

        } catch (\Exception $e) {
            Log::error("Error in processLoans: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process a single loan record
     */
    private function processSingleLoan($loan)
    {
        // Calculate next period
        $nextMonthNumber = ($this->monthNumber % 12) + 1;
        $nextYear = ($this->monthNumber == 12) ? $this->year + 1 : $this->year;
        $nextMonthName = $this->numberToMonthName($nextMonthNumber);
        $newPeriod = $nextMonthName . $nextYear;

        // Calculate loan values
        $adjustedPrecovery = min($loan->precovery, $loan->loanBalance);
        $newbalance = max(0, $loan->loanBalance - $adjustedPrecovery);
        $mintrest = (($loan->rate / 100) / 12) * $newbalance;
        $mpay = $adjustedPrecovery + $mintrest;

        // Update current loan schedule balance (commented out in original)
        // LoanSchedule::where('ID', $loan->loanID)->update(['balance' => $newbalance]);

        // Insert new loan schedule row for next period
        LoanShedule::create([
            'empid' => $loan->WorkNo,
            'loantype' => $loan->PCode,
            'interest' => $loan->interest,
            'loanamount' => $loan->loanamount,
            'precovery' => $loan->precovery,
            'totalmonths' => $loan->totalmonths,
            'balance' => $newbalance,
            'Period' => $newPeriod,
            'paidcheck' => 'NO',
            'mpay' => $mpay,
            'mintrest' => $mintrest,
            'statlon' => $loan->statlon
        ]);

        // Mark current loan schedule as paid
        LoanShedule::where('ID', $loan->loanID)->update(['paidcheck' => 'Yes']);

        // Update principal in employee deductions
        EmployeeDeduction::where('ID', $loan->edID)->update([
            'Amount' => $adjustedPrecovery,
            'balance' => $newbalance,
            'month' => $nextMonthName,
            'year' => $nextYear
        ]);

        // Update interest in employee deductions
        EmployeeDeduction::where('WorkNo', $loan->WorkNo)
            ->where('loanshares', 'interest')
            ->where('statdeduc', '1')
            ->where('parent', $loan->PCode)
            ->update([
                'Amount' => $mintrest,
                'month' => $nextMonthName,
                'year' => $nextYear
            ]);

        Log::info("Processed loan for WorkNo: {$loan->WorkNo}, PCode: {$loan->PCode}");
    }

    /**
     * Process loans with interest (placeholder)
     */
    /**
 * Process loans with interest - converted from procloanswithintrest function
 */
public function processLoansWithInterest()
{
    try {
        $formattedPeriod = $this->month . $this->year;
        $allowedTypes =  session('allowedPayroll', []);

        // Get loans with interest that need processing
        $loans = DB::table('employeedeductions as ed')
            ->join('loanshedule as ls', function($join) use ($formattedPeriod) {
                $join->on('ed.WorkNo', '=', 'ls.empid')
                     ->on('ed.PCode', '=', 'ls.loantype')
                     ->where('ls.Period', '=', $formattedPeriod);
            })
            ->join('registration as r', 'ed.WorkNo', '=', 'r.empid')
            ->where('ed.loanshares', 'loan')
            ->where('ed.month', $this->month)
            ->where('ed.year', $this->year)
            ->where('ed.statdeduc', '1')
            ->where('ls.statlon', '1')
            ->where('ed.recintres', '1')
            ->whereIn('r.payrolty', $allowedTypes)
            ->select(
                'ed.ID',
                'ed.WorkNo',
                'ed.PCode',
                'ed.Amount',
                'ed.balance',
                'ls.ID as loanID',
                'ls.interest',
                'ls.precovery',
                'ls.loanamount',
                'ls.totalmonths',
                'ls.balance as loanBalance',
                'ls.statlon',
                'ls.mpay'
            )
            ->distinct()
            ->get();

        Log::info("Found " . $loans->count() . " loans with interest to process for {$this->month} {$this->year}");

        // Calculate next period
        $nextMonthNumber = ($this->monthNumber % 12) + 1;
        $nextYear = ($this->monthNumber == 12) ? $this->year + 1 : $this->year;
        $nextMonthName = $this->numberToMonthName($nextMonthNumber);
        $newPeriod = $nextMonthName . $nextYear;

        $processedCount = 0;
        $errorCount = 0;

        foreach ($loans as $loan) {
            try {
                DB::transaction(function () use ($loan, $nextMonthName, $nextYear, $newPeriod, &$processedCount) {
                    $this->processSingleLoanWithInterest($loan, $nextMonthName, $nextYear, $newPeriod);
                    $processedCount++;
                });
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("ERROR processing WorkNo {$loan->WorkNo}: " . $e->getMessage());
            }
        }

        Log::info("Completed processing loans with interest. Success: {$processedCount}, Errors: {$errorCount}");

        return [
            'processed' => $processedCount,
            'errors' => $errorCount,
            'total' => $loans->count()
        ];

    } catch (\Exception $e) {
        Log::error("FATAL ERROR in processLoansWithInterest(): " . $e->getMessage());
        throw $e;
    }
}

/**
 * Process a single loan record with interest
 */
private function processSingleLoanWithInterest($loan, $nextMonthName, $nextYear, $newPeriod)
{
    // Calculate loan values
    $adjustedPrecovery = min($loan->precovery, $loan->loanBalance);
    $newbalance = max(0, $loan->loanBalance - $adjustedPrecovery);
    $mintrest = (($loan->interest / 100) / 12) * $newbalance;
    $mpay = $adjustedPrecovery + $mintrest;

    // 1. Update loanshedule balance
    LoanShedule::where('ID', $loan->loanID)->update(['balance' => $newbalance]);

    // 2. Insert new loanshedule record for next period
    LoanShedule::create([
        'empid' => $loan->WorkNo,
        'loantype' => $loan->PCode,
        'interest' => $loan->interest,
        'loanamount' => $loan->loanamount,
        'precovery' => $loan->precovery,
        'totalmonths' => $loan->totalmonths,
        'balance' => $newbalance,
        'Period' => $newPeriod,
        'paidcheck' => 'NO',
        'mpay' => $mpay,
        'mintrest' => $mintrest,
        'statlon' => $loan->statlon
    ]);

    // 3. Mark current loanshedule as paid
    LoanShedule::where('ID', $loan->loanID)->update(['paidcheck' => 'Yes']);

    // 4. Update employee deductions
    EmployeeDeduction::where('ID', $loan->ID)->update([
        'Amount' => $mpay,
        'balance' => $newbalance,
        'month' => $nextMonthName,
        'year' => $nextYear
    ]);

    // 5. Update payhouse balance
    DB::table('payhouse')
        ->where('WorkNo', $loan->WorkNo)
        ->where('itemcode', $loan->PCode)
        ->where('month', $this->month)
        ->where('year', $this->year)
        ->update(['balance' => $newbalance]);

    Log::info("Processed loan with interest for WorkNo: {$loan->WorkNo}, PCode: {$loan->PCode}");
}

    /**
     * Process inactive loans (placeholder)
     */
    /**
 * Process inactive loans - converted from processLoansInactive function
 */
public function processLoansInactive()
{
    try {
        $formattedPeriod = $this->month . $this->year;
        $allowedTypes =  session('allowedPayroll', []);

        // Get inactive loans that need processing
        $loans = DB::table('employeedeductions as ed')
            ->join('loanshedule as ls', function($join) use ($formattedPeriod) {
                $join->on('ed.WorkNo', '=', 'ls.empid')
                     ->on('ed.PCode', '=', 'ls.loantype')
                     ->where('ls.Period', '=', $formattedPeriod);
            })
            ->join('registration as r', 'ed.WorkNo', '=', 'r.empid')
            ->where('ed.loanshares', 'loan')
            ->where('ed.month', $this->month)
            ->where('ed.year', $this->year)
            ->where('ed.statdeduc', '0') // Inactive deductions
            ->where('ls.statlon', '0')   // Inactive loans
            ->whereIn('r.payrolty', $allowedTypes)
            ->select(
                'ed.ID',
                'ed.WorkNo',
                'ed.PCode',
                'ed.Amount',
                'ed.balance',
                'ls.interest'
            )
            ->get();

        Log::info("Found " . $loans->count() . " inactive loans to process for {$this->month} {$this->year}");

        $processedCount = 0;
        $errorCount = 0;

        foreach ($loans as $loan) {
            try {
                DB::transaction(function () use ($loan, &$processedCount) {
                    $this->processSingleInactiveLoan($loan);
                    $processedCount++;
                });
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Error processing inactive loan for WorkNo {$loan->WorkNo}: " . $e->getMessage());
            }
        }

        return [
            'processed' => $processedCount,
            'errors' => $errorCount,
            'total' => $loans->count()
        ];

    } catch (\Exception $e) {
        Log::error("Error in processLoansInactive: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Process a single inactive loan record
 */
private function processSingleInactiveLoan($loan)
{
    // Calculate interest and new amounts
    $monthlyInterest = ($loan->interest / 100) / 12 * $loan->balance;
    $amountWithInterest = $loan->Amount + $monthlyInterest;

    if ($amountWithInterest > $loan->balance) {
        $newAmount = $loan->balance;
        $newBalance = 0;
    } else {
        $newAmount = $amountWithInterest;
        $newBalance = $loan->balance - $loan->Amount; // Deduct only principal
    }

    // Calculate next period
    $nextMonthNumber = ($this->monthNumber % 12) + 1;
    $nextYear = ($this->monthNumber == 12) ? $this->year + 1 : $this->year;
    $nextMonthName = $this->numberToMonthName($nextMonthNumber);
    $newPeriod = $nextMonthName . $this->year; // Note: Using current year for period

    // Get loan schedule details
    $loanSchedules = LoanShedule::where('loantype', $loan->PCode)
        ->where('empid', $loan->WorkNo)
        ->where('Period', $this->month . $this->year) // Current period
        ->where('statlon', '0')
        ->get();

    foreach ($loanSchedules as $loanSchedule) {
        // Calculate loan values
        $adjustedPrecovery = min($loanSchedule->precovery, $loanSchedule->balance);
        $mintrest = (($loanSchedule->interest / 100) / 12) * $loanSchedule->balance;
        $mpay = $adjustedPrecovery + $mintrest;
        $newLoanBalance = max(0, $loanSchedule->balance - $adjustedPrecovery);

        // Insert new loan schedule entry for next period
        LoanShedule::create([
            'empid' => $loan->WorkNo,
            'loantype' => $loan->PCode,
            'interest' => $loanSchedule->interest,
            'loanamount' => $loanSchedule->loanamount,
            'precovery' => $loanSchedule->precovery,
            'totalmonths' => $loanSchedule->totalmonths,
            'balance' => $loanSchedule->balance, // Using original balance as per original logic
            'Period' => $newPeriod,
            'paidcheck' => 'NO',
            'mpay' => $mpay,
            'mintrest' => $mintrest,
            'statlon' => $loanSchedule->statlon
        ]);

        // Update payhouse table with the new balance
        DB::table('payhouse')
            ->where('WorkNo', $loan->WorkNo)
            ->where('itemcode', $loan->PCode)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->update(['balance' => $loanSchedule->balance]); // Using original balance

        Log::info("Processed inactive loan for WorkNo: {$loan->WorkNo}, PCode: {$loan->PCode}, Balance: {$loanSchedule->balance}");
    }

    // Note: The original function didn't update employeedeductions for inactive loans
    // If you need to update employeedeductions, uncomment and modify the following:
    /*
    EmployeeDeduction::where('ID', $loan->ID)->update([
        'Amount' => $newAmount,
        'balance' => $newBalance,
        'month' => $nextMonthName,
        'year' => $nextYear
    ]);
    */
}

    /**
     * Process balances (placeholder)
     */
    /**
 * Process balances - converted from processBalances function
 */
public function processBalances()
{
    try {
        $formattedPeriod = $this->month . $this->year;
        $allowedTypes =  session('allowedPayroll', []);

        // Get balances that need processing
        $balances = DB::table('employeedeductions as ed')
            ->join('registration as r', 'ed.WorkNo', '=', 'r.empid')
            ->where('ed.loanshares', 'balance')
            ->where('ed.month', $this->month)
            ->where('ed.year', $this->year)
            ->where('ed.statdeduc', '1')
            ->whereIn('r.payrolty', $allowedTypes)
            ->select(
                'ed.ID',
                'ed.WorkNo',
                'ed.PCode',
                'ed.Amount',
                'ed.balance',
                'ed.increREDU'
            )
            ->get();

        Log::info("Found " . $balances->count() . " balances to process for {$this->month} {$this->year}");

        $processedCount = 0;
        $errorCount = 0;

        foreach ($balances as $balance) {
            try {
                DB::transaction(function () use ($balance, &$processedCount) {
                    $this->processSingleBalance($balance);
                    $processedCount++;
                });
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Error processing balance for WorkNo {$balance->WorkNo}: " . $e->getMessage());
            }
        }

        return [
            'processed' => $processedCount,
            'errors' => $errorCount,
            'total' => $balances->count()
        ];

    } catch (\Exception $e) {
        Log::error("Error in processBalances: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Process a single balance record
 */
private function processSingleBalance($balance)
{
    // Calculate next period
    $nextMonthNumber = ($this->monthNumber % 12) + 1;
    $nextYear = ($this->monthNumber == 12) ? $this->year + 1 : $this->year;
    $nextMonthName = $this->numberToMonthName($nextMonthNumber);
    $newPeriod = $nextMonthName . $nextYear;

    // Calculate new amount and balance based on increREDU type
    if ($balance->increREDU === 'Reducing') {
        if ($balance->Amount > $balance->balance) {
            $newAmount = $balance->balance;
            $newBalance = 0;
        } else {
            $newAmount = $balance->Amount;
            $newBalance = $balance->balance - $balance->Amount;
        }
    } elseif ($balance->increREDU === 'Increasing') {
        $newAmount = $balance->Amount;
        $newBalance = $balance->balance + $balance->Amount;
    } else {
        throw new \Exception("Invalid increREDU value: {$balance->increREDU}");
    }

    // Update employee deductions
    EmployeeDeduction::where('ID', $balance->ID)->update([
        'Amount' => $newAmount,
        'balance' => $newBalance,
        'month' => $nextMonthName,
        'year' => $nextYear
    ]);

    // Get related balance schedules
    $balanceSchedules = DB::table('balancsched')
        ->where('empid', $balance->WorkNo)
        ->where('balancecode', $balance->PCode)
        ->where('Pperiod', $this->month . $this->year)
        ->where('stat', '1')
        ->get();

    foreach ($balanceSchedules as $schedule) {
        // Calculate recovery amount and new balance based on increREDU type
        if ($schedule->increREDU === 'Reducing') {
            $recoveryAmount = min($schedule->rrecovery, $schedule->balance);
            $newScheduleBalance = max(0, $schedule->balance - $recoveryAmount);
        } elseif ($schedule->increREDU === 'Increasing') {
            $recoveryAmount = $schedule->rrecovery;
            $newScheduleBalance = $schedule->balance + $schedule->rrecovery;
        } else {
            throw new \Exception("Invalid increREDU value in balance schedule: {$schedule->increREDU}");
        }

        // Insert new balance schedule record
        DB::table('balancsched')->insert([
            'empid' => $schedule->empid,
            'balancecode' => $schedule->balancecode,
            'totalmonths' => $schedule->totalmonths,
            'Pperiod' => $newPeriod,
            'rrecovery' => $recoveryAmount,
            'balance' => $newScheduleBalance,
            'targeloan' => $schedule->targeloan,
            'increREDU' => $schedule->increREDU,
            'paidcheck' => 'NO',
            'stat' => $schedule->stat
        ]);

        // Mark current balance schedule as paid
        DB::table('balancsched')
            ->where('ID', $schedule->ID)
            ->update(['paidcheck' => 'Yes']);

        // Update payhouse balance
        DB::table('payhouse')
            ->where('WorkNo', $balance->WorkNo)
            ->where('itemcode', $balance->PCode)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->update(['balance' => $newScheduleBalance]);

        Log::info("Processed balance for WorkNo: {$balance->WorkNo}, PCode: {$balance->PCode}, Type: {$balance->increREDU}, New Balance: {$newScheduleBalance}");
    }
}

    /**
     * Process inactive balances (placeholder)
     */
  /**
 * Process inactive balances - converted from processBalancesInactive function
 */
public function processBalancesInactive()
{
    try {
        $formattedPeriod = $this->month . $this->year;
        $allowedTypes =  session('allowedPayroll', []);

        // Get inactive balances that need processing
        $balances = DB::table('employeedeductions as ed')
            ->join('registration as r', 'ed.WorkNo', '=', 'r.empid')
            ->where('ed.loanshares', 'balance')
            ->where('ed.month', $this->month)
            ->where('ed.year', $this->year)
            ->where('ed.statdeduc', '0') // Inactive deductions
            ->whereIn('r.payrolty', $allowedTypes)
            ->select(
                'ed.ID',
                'ed.WorkNo',
                'ed.PCode',
                'ed.Amount',
                'ed.balance',
                'ed.increREDU'
            )
            ->get();

        Log::info("Found " . $balances->count() . " inactive balances to process for {$this->month} {$this->year}");

        $processedCount = 0;
        $errorCount = 0;

        foreach ($balances as $balance) {
            try {
                DB::transaction(function () use ($balance, &$processedCount) {
                    $this->processSingleInactiveBalance($balance);
                    $processedCount++;
                });
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Error processing inactive balance for WorkNo {$balance->WorkNo}: " . $e->getMessage());
            }
        }

        return [
            'processed' => $processedCount,
            'errors' => $errorCount,
            'total' => $balances->count()
        ];

    } catch (\Exception $e) {
        Log::error("Error in processBalancesInactive: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Process a single inactive balance record
 */
private function processSingleInactiveBalance($balance)
{
    // Calculate next period
    $nextMonthNumber = ($this->monthNumber % 12) + 1;
    $nextYear = ($this->monthNumber == 12) ? $this->year + 1 : $this->year;
    $nextMonthName = $this->numberToMonthName($nextMonthNumber);
    $newPeriod = $nextMonthName . $nextYear;

    // Calculate new amount and balance based on increREDU type
    if ($balance->increREDU === 'Reducing') {
        if ($balance->Amount > $balance->balance) {
            $newAmount = $balance->balance;
            $newBalance = 0;
        } else {
            $newAmount = $balance->Amount;
            $newBalance = $balance->balance - $balance->Amount;
        }
    } elseif ($balance->increREDU === 'Increasing') {
        $newAmount = $balance->Amount;
        $newBalance = $balance->balance + $balance->Amount;
    } else {
        throw new \Exception("Invalid increREDU value: {$balance->increREDU}");
    }

    // Note: The original function doesn't update employeedeductions for inactive balances
    // If you need to update employeedeductions, uncomment the following:
    /*
    EmployeeDeduction::where('ID', $balance->ID)->update([
        'Amount' => $newAmount,
        'balance' => $newBalance,
        'month' => $nextMonthName,
        'year' => $nextYear
    ]);
    */

    // Get related inactive balance schedules
    $balanceSchedules = DB::table('balancsched')
        ->where('empid', $balance->WorkNo)
        ->where('balancecode', $balance->PCode)
        ->where('Pperiod', $this->month . $this->year)
        ->where('stat', '0') // Inactive schedules
        ->get();

    foreach ($balanceSchedules as $schedule) {
        // Calculate recovery amount and new balance based on increREDU type
        if ($schedule->increREDU === 'Reducing') {
            $recoveryAmount = min($schedule->rrecovery, $schedule->balance);
            $newScheduleBalance = max(0, $schedule->balance - $recoveryAmount);
        } elseif ($schedule->increREDU === 'Increasing') {
            $recoveryAmount = $schedule->rrecovery;
            $newScheduleBalance = $schedule->balance + $schedule->rrecovery;
        } else {
            throw new \Exception("Invalid increREDU value in balance schedule: {$schedule->increREDU}");
        }

        // Insert new inactive balance schedule record
        DB::table('balancsched')->insert([
            'empid' => $schedule->empid,
            'balancecode' => $schedule->balancecode,
            'totalmonths' => $schedule->totalmonths,
            'Pperiod' => $newPeriod,
            'rrecovery' => $schedule->rrecovery, // Using original rrecovery as per original logic
            'balance' => $schedule->balance, // Using original balance as per original logic
            'targeloan' => $schedule->targeloan,
            'increREDU' => $schedule->increREDU,
            'paidcheck' => 'NO',
            'stat' => $schedule->stat
        ]);

        // Note: The original function doesn't update paidcheck for inactive balances
        // If you need to mark as paid, uncomment the following:
        /*
        DB::table('balancsched')
            ->where('ID', $schedule->ID)
            ->update(['paidcheck' => 'Yes']);
        */

        // Update payhouse balance
        DB::table('payhouse')
            ->where('WorkNo', $balance->WorkNo)
            ->where('itemcode', $balance->PCode)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->update(['balance' => $newScheduleBalance]);

        Log::info("Processed inactive balance for WorkNo: {$balance->WorkNo}, PCode: {$balance->PCode}, Type: {$balance->increREDU}, New Balance: {$newScheduleBalance}");
    }
}

    /**
     * Update all records (placeholder)
     */
    /**
 * Update all records - converted from updateAll function
 */
public function updateAll()
{
    try {
        // Calculate next period
        $nextMonthNumber = ($this->monthNumber % 12) + 1;
        $nextYear = ($this->monthNumber == 12) ? $this->year + 1 : $this->year;
        $nextMonthName = $this->numberToMonthName($nextMonthNumber);

        // Use transaction to ensure data consistency
        return DB::transaction(function () use ($nextMonthName, $nextYear) {
            // Update all employee deductions for the current period to the next period
            $updatedCount = DB::table('employeedeductions as ed')
                ->join('registration as r', 'ed.WorkNo', '=', 'r.empid')
                ->where('ed.month', $this->month)
                ->where('ed.year', $this->year)
                ->whereIn('r.payrolty',  session('allowedPayroll', []))
                ->update([
                    'ed.month' => $nextMonthName,
                    'ed.year' => $nextYear
                ]);

            Log::info("Updated {$updatedCount} employee deduction records from {$this->month} {$this->year} to {$nextMonthName} {$nextYear}");

            return $updatedCount;

        });

    } catch (\Exception $e) {
        Log::error("Error in updateAll: " . $e->getMessage());
        throw $e;
    }
}

    /**
     * Process negative payments (placeholder)
     */
    public function processNegativePayments()
    {
        try {
            // Implement negative payments handling
            Log::info("Processing negative payments for {$this->month} {$this->year}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error processing negative payments: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Execute the complete period closing process
     */
    public function executePeriodClosing()
    {
        try {
            $results = [];

            // Execute all period closing steps
            $results['cleared_variables'] = $this->clearVariables();
            $results['processed_loans'] = $this->processLoans();
            $results['processed_loans_with_interest'] = $this->processLoansWithInterest();
            $results['processed_loans_inactive'] = $this->processLoansInactive();
            $results['processed_balances'] = $this->processBalances();
            $results['processed_balances_inactive'] = $this->processBalancesInactive();
            $results['updated_all'] = $this->updateAll();
            $results['processed_negative_payments'] = $this->processNegativePayments();
            $results['period_closed'] = $this->closePeriod();

            Log::info("Period closing completed successfully for {$this->month} {$this->year}");
            return $results;

        } catch (\Exception $e) {
            Log::error("Period closing failed: " . $e->getMessage());
            throw $e;
        }
    }
}