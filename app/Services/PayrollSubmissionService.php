<?php

namespace App\Services;

use App\Models\EmployeeDeduction;
use App\Models\OtRecord;
use App\Models\Ptype;
use App\Models\LoanShedule;
use App\Models\BalanceSched;
use App\Models\EmpPensionRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PayrollSubmissionService
{
    /**
     * Process payroll submission
     */
    public function processSubmission(array $data, $userId)
    {
        DB::beginTransaction();
        
        try {
            // Extract and validate data
            $workNumber = $data['workNumber'];
            $month = $data['month'];
            $year = $data['year'];
            $parameter = $data['parameter'];
            
            // Get parameter details from ptypes
            $ptype = Ptype::findByName($parameter);
            
            if (!$ptype) {
                throw new \Exception('Parameter not found in system');
            }

            // Calculate interest if applicable
            $interestData = $this->calculateInterest(
                $ptype,
                $data['amount'] ?? 0,
                $data['balance'] ?? 0
            );

            // Check if record exists
            $existingDeduction = EmployeeDeduction::where('WorkNo', $workNumber)
                ->where('month', $month)
                ->where('year', $year)
                ->where('PCode', $ptype->code)
                ->first();

            $oldData = $existingDeduction ? $existingDeduction->toArray() : null;

            // Handle overtime if formula exists
            if (!empty($ptype->formularinpu)) {
                $this->handleOvertime($data, $ptype, $workNumber, $month, $year, $userId);
            }

            // Handle main deduction record
            if ($existingDeduction) {
                $this->updateDeduction($existingDeduction, $data, $ptype, $interestData, $oldData, $userId);
            } else {
                $this->createDeduction($data, $ptype, $interestData, $userId);
            }

            // Handle interest record if needed
            if ($interestData['shouldCreateSeparateRecord']) {
                $this->handleInterestRecord($data, $ptype, $interestData, $workNumber, $month, $year, $userId);
            }
             if ($ptype->category === 'loan') {
                $this->handleLoanSchedule($data, $ptype, $interestData, $workNumber, $month, $year);
            }

            // ✅ Handle balance schedule if category is 'balance'
            if ($ptype->category === 'balance') {
                $this->handleBalanceSched($data, $ptype, $workNumber, $month, $year);
            }

            // ✅ Handle pension rates if provided
            if (isset($data['epmpenperce']) && isset($data['emplopenperce'])) {
                $this->handlePensionRates($data, $workNumber);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => $existingDeduction ? 'Data updated successfully' : 'Data inserted successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payroll submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Calculate interest based on rate and balance
     */
    private function calculateInterest($ptype, $amount, $balance)
    {
        $interest = 0;
        $shouldCreateSeparateRecord = false;
        $finalAmount = $amount;
        $precovery = $amount;

        if (!empty($ptype->rate) && $ptype->rate > 0) {
            // Calculate monthly interest
            $interest = ($ptype->rate / 100) / 12 * $balance;

            if ($ptype->recintres == '1') {
                // Add interest to amount
                $finalAmount = $amount + $interest;
            } else {
                // Keep interest separate
                $shouldCreateSeparateRecord = true;
            }
        }

        return [
            'interest' => $interest,
            'finalAmount' => $finalAmount,
            'precovery' => $precovery,
            'shouldCreateSeparateRecord' => $shouldCreateSeparateRecord
        ];
    }

    /**
     * Handle overtime records
     */
    private function handleOvertime($data, $ptype, $workNumber, $month, $year, $userId)
    {
        $otdate = $data['otdate'] ?? now()->format('Y-m-d');
        $amount = $data['amount'] ?? 0;
        $quantity = $data['quantity'] ?? 0;
        $ccname = $ptype->cname . $otdate;

        // Create OT record
        $otRecord = OtRecord::create([
            'WorkNo' => $workNumber,
            'Pcode' => $ptype->code,
            'tamount' => $amount,
            'cname' => $ccname,
            'quantity' => $quantity,
            'odate' => $otdate
        ]);

        // Log audit
        //logAuditTrail($userId, 'INSERT', 'Over Time', $workNumber, null, $otRecord->toArray());

        // Calculate totals for the month
        $otDate = Carbon::parse($otdate);
        $totals = OtRecord::where('WorkNo', $workNumber)
            ->where('Pcode', $ptype->code)
            ->whereMonth('odate', $otDate->month)
            ->whereYear('odate', $otDate->year)
            ->selectRaw('SUM(tamount) as total_amount, SUM(quantity) as total_quantity')
            ->first();

        // Update employee deduction with totals
        $deduction = EmployeeDeduction::where('WorkNo', $workNumber)
            ->where('month', $month)
            ->where('year', $year)
            ->where('PCode', $ptype->code)
            ->first();

        if ($deduction) {
            $oldData = $deduction->toArray();
            
            $deduction->update([
                'Amount' => $totals->total_amount ?? 0,
                'quantity' => $totals->total_quantity ?? 0
            ]);

            $recordId = "{$workNumber}_{$month}_{$year}_{$ptype->code}";
           // logAuditTrail($userId, 'UPDATE', 'Employee Payroll items', $recordId, $oldData, $deduction->toArray());
        } else {
            // Create new deduction record
            $this->createDeductionFromOT($data, $ptype, $totals, $userId);
        }
    }

    /**
     * Create deduction from overtime totals
     */
    private function createDeductionFromOT($data, $ptype, $totals, $userId)
    {
        $deduction = EmployeeDeduction::create([
            'month' => $data['month'],
            'year' => $data['year'],
            'pcate' => $ptype->cname,
            'Surname' => $data['surname'],
            'othername' => $data['othername'],
            'WorkNo' => $data['workNumber'],
            'dept' => $data['department'],
            'Amount' => $totals->total_amount ?? 0,
            'balance' => $data['balance'] ?? 0,
            'PCode' => $ptype->code,
            'procctype' => $ptype->procctype,
            'varorfixed' => $ptype->varorfixed,
            'taxaornon' => $ptype->taxaornon,
            'loanshares' => $data['category'] ?? $ptype->category,
            'increREDU' => $ptype->increREDU,
            'rate' => $ptype->rate,
            'prossty' => $ptype->prossty,
            'dateposted' => now()->format('Y-m-d'),
            'statdeduc' => '1',
            'quantity' => $totals->total_quantity ?? 0
        ]);

        $recordId = "{$data['workNumber']}_{$data['month']}_{$data['year']}_{$ptype->code}";
        //logAuditTrail($userId, 'INSERT', 'Employee Payroll items', $recordId, null, $deduction->toArray());
    }

    /**
     * Update existing deduction
     */
    private function updateDeduction($deduction, $data, $ptype, $interestData, $oldData, $userId)
    {
        $deduction->update([
            'pcate' => $ptype->cname,
            'Surname' => $data['surname'],
            'othername' => $data['othername'],
            'dept' => $data['department'],
            'Amount' => $interestData['finalAmount'],
            'balance' => $data['balance'] ?? 0,
            'procctype' => $ptype->procctype,
            'varorfixed' => $ptype->varorfixed,
            'taxaornon' => $ptype->taxaornon,
            'loanshares' => $data['category'] ?? $ptype->category,
            'increREDU' => $ptype->increREDU,
            'rate' => $ptype->rate,
            'prossty' => $ptype->prossty,
            'statdeduc' => $data['openvalue'] ?? '1',
            'recintres' => $ptype->recintres
        ]);

        $recordId = $data['workNumber'];
        //logAuditTrail($userId, 'UPDATE', 'Employee Payroll items', $recordId, $oldData, $deduction->toArray());
    }

    /**
     * Create new deduction
     */
    private function createDeduction($data, $ptype, $interestData, $userId)
    {
        $deduction = EmployeeDeduction::create([
            'month' => $data['month'],
            'year' => $data['year'],
            'pcate' => $ptype->cname,
            'Surname' => $data['surname'],
            'othername' => $data['othername'],
            'WorkNo' => $data['workNumber'],
            'dept' => $data['department'],
            'Amount' => $interestData['finalAmount'],
            'balance' => $data['balance'] ?? 0,
            'PCode' => $ptype->code,
            'procctype' => $ptype->procctype,
            'varorfixed' => $ptype->varorfixed,
            'taxaornon' => $ptype->taxaornon,
            'loanshares' => $data['category'] ?? $ptype->category,
            'increREDU' => $ptype->increREDU,
            'rate' => $ptype->rate,
            'prossty' => $ptype->prossty,
            'dateposted' => now()->format('Y-m-d'),
            'statdeduc' => '1',
            'quantity' => $data['quantity'] ?? 0,
            'relief' => $ptype->relief,
            'recintres' => $ptype->recintres
        ]);

        $recordId = "{$data['workNumber']}_{$data['month']}_{$data['year']}_{$ptype->code}";
       // logAuditTrail($userId, 'INSERT', 'Employee Payroll items', $recordId, null, $deduction->toArray());
    }

    /**
     * Handle separate interest record
     */
    private function handleInterestRecord($data, $ptype, $interestData, $workNumber, $month, $year, $userId)
    {
        if ($interestData['interest'] <= 0) {
            return;
        }

        $interestCode = $ptype->intrestcode;
        $interestName = $ptype->codename;

        // Check if interest record exists
        $existingInterest = EmployeeDeduction::where('WorkNo', $workNumber)
            ->where('month', $month)
            ->where('year', $year)
            ->where('PCode', $interestCode)
            ->first();

        if ($existingInterest) {
            // Update existing interest record
            $existingInterest->update([
                'pcate' => $interestName,
                'Surname' => $data['surname'],
                'othername' => $data['othername'],
                'dept' => $data['department'],
                'Amount' => $interestData['interest'],
                'procctype' => $ptype->procctype,
                'varorfixed' => $ptype->varorfixed,
                'taxaornon' => $ptype->taxaornon,
                'increREDU' => $ptype->increREDU,
                'rate' => $ptype->rate,
                'prossty' => $ptype->prossty,
                'statdeduc' => $data['openvalue'] ?? '1',
                'recintres' => $ptype->recintres
            ]);
        } else {
            // Create new interest record
            EmployeeDeduction::create([
                'month' => $month,
                'year' => $year,
                'pcate' => $interestName,
                'Surname' => $data['surname'],
                'othername' => $data['othername'],
                'WorkNo' => $workNumber,
                'dept' => $data['department'],
                'Amount' => $interestData['interest'],
                'balance' => 0,
                'PCode' => $interestCode,
                'procctype' => $ptype->procctype,
                'varorfixed' => $ptype->varorfixed,
                'taxaornon' => $ptype->taxaornon,
                'loanshares' => 'interest',
                'increREDU' => $ptype->increREDU,
                'rate' => $ptype->rate,
                'prossty' => $ptype->prossty,
                'dateposted' => now()->format('Y-m-d'),
                'statdeduc' => '1',
                'quantity' => $data['quantity'] ?? 0,
                'relief' => $ptype->relief,
                'recintres' => $ptype->recintres,
                'parent' => $ptype->code
            ]);
        }
    }

     private function handleLoanSchedule($data, $ptype, $interestData, $workNumber, $month, $year)
    {
        $currentPeriod = $month . $year;
        $amount = $data['amount'] ?? 0;
        $balance = $data['balance'] ?? 0;
        $precovery = $interestData['precovery'];
        $openvalue = $data['openvalue'] ?? '1';
        $months = $data['months'] ?? 0;

        // Check if loan schedule record exists
        $loanSchedule = LoanShedule::where('empid', $workNumber)
            ->where('loantype', $ptype->code)
            ->where('Period', $currentPeriod)
            ->first();

        if ($loanSchedule) {
            // Record exists - update it
            if ($loanSchedule->balance != $balance) {
                // Balance has changed
                $loanSchedule->update([
                    'balance' => $balance,
                    'precovery' => $precovery,
                    'mpay' => $amount,
                    'statlon' => $openvalue
                ]);
            } else {
                // Balance is the same, just update precovery and statlon
                $loanSchedule->update([
                    'precovery' => $precovery,
                    'mpay' => $amount,
                    'statlon' => $openvalue
                ]);
            }
        } else {
            // Record doesn't exist - insert new one
            LoanShedule::create([
                'empid' => $workNumber,
                'loantype' => $ptype->code,
                'interest' => $ptype->rate,
                'totalmonths' => $months,
                'Period' => $currentPeriod,
                'precovery' => $precovery,
                'mpay' => $amount,
                'balance' => $balance,
                'loanamount' => $balance,
                'statlon' => '1'
            ]);
        }
    }

    /**
     * Handle balance schedule
     */
    private function handleBalanceSched($data, $ptype, $workNumber, $month, $year)
    {
        $currentPeriod = $month . $year;
        $amount = $data['amount'] ?? 0;
        $balance = $data['balance'] ?? 0;
        $openvalue = $data['openvalue'] ?? '1';
        $increREDU = $ptype->increREDU;

        // Calculate total months
        $totalmonths = ($amount > 0) ? ($balance / $amount) : 0;

        // Check if balance schedule record exists
        $BalanceSched = BalanceSched::where('balancecode', $ptype->code)
            ->where('empid', $workNumber)
            ->where('Pperiod', $currentPeriod)
            ->first();

        if ($BalanceSched) {
            // Record exists - update it
            if ($BalanceSched->balance != $balance) {
                // Balance has changed
                $BalanceSched->update([
                    'balance' => $balance,
                    'rrecovery' => $amount,
                    'stat' => $openvalue
                ]);
            } else {
                // Balance is the same, just update rrecovery and stat
                $BalanceSched->update([
                    'rrecovery' => $amount,
                    'stat' => $openvalue
                ]);
            }
        } else {
            // Record doesn't exist - insert new one
            BalanceSched::create([
                'empid' => $workNumber,
                'balancecode' => $ptype->code,
                'totalmonths' => $totalmonths,
                'Pperiod' => $currentPeriod,
                'rrecovery' => $amount,
                'balance' => $balance,
                'targeloan' => $balance,
                'increREDU' => $increREDU,
                'paidcheck' => 'NO',
                'stat' => '1'
            ]);
        }
    }

    /**
     * Handle pension rates
     */
    private function handlePensionRates($data, $workNumber)
    {
        $epmpenperce = $data['epmpenperce'] ?? 0;
        $emplopenperce = $data['emplopenperce'] ?? 0;

        // Check if pension rate record exists
        $pensionRate = EmpPensionRate::where('WorkNo', $workNumber)->first();

        if ($pensionRate) {
            // Update existing record
            $pensionRate->update([
                'epmpenperce' => $epmpenperce,
                'emplopenperce' => $emplopenperce
            ]);
        } else {
            // Insert new record
            EmpPensionRate::create([
                'WorkNo' => $workNumber,
                'epmpenperce' => $epmpenperce,
                'emplopenperce' => $emplopenperce
            ]);
        }
    }
}