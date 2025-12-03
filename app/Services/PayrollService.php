<?php

namespace App\Services;

use App\Models\Agents;
use App\Models\EmployeeDeduction;
use App\Models\Payhouse;
use App\Models\Registration;
use App\Models\Nhif;
use App\Models\Shif;
use App\Models\Hlevy;
use App\Models\Pension;
use App\Models\Prelief;
use App\Models\EmpPensionRate;
use App\Models\Taxbrackets;
use App\Models\Whgroups;
use App\Models\Withholding;
use App\Models\Union; // uninde table
use App\Models\UnionGroup; // uniongroups table
use App\Models\Cotu; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PayrollService
{
    /**
     * Clear variables for inactive employees for a given month & year
     */
    public function clearVariables(string $month, string $year): void
    {
        // 1. Get inactive employees
        $inactiveWorkNos = Agents::where('Status', 'INACTIVE')
            ->pluck('emp_id')
            ->toArray();

        if (empty($inactiveWorkNos)) {
            return;
        }

        DB::transaction(function () use ($inactiveWorkNos, $month, $year) {

            // 2. Delete from employeedeductions
            EmployeeDeduction::whereIn('WorkNo', $inactiveWorkNos)->delete();

            // 3. Delete from payhouse for given month and year
            Payhouse::whereIn('WorkNo', $inactiveWorkNos)
                ->where('month', $month)
                ->where('year', $year)
                ->delete();
        });
    }

    public function calcGrossPay(string $month, string $year, array $allowedPayrollIds): array
{
    $grossPays = [];

    if (empty($allowedPayrollIds)) {
        return ['status' => 'error', 'message' => 'No payroll type selected'];
    }

    if (count($allowedPayrollIds) > 1) {
        return ['status' => 'error', 'message' => 'Can only process one Payroll type at a time'];
    }

    // Find WorkNos matching allowed payroll type
    $workNos = EmployeeDeduction::query()
        ->select('employeedeductions.WorkNo')
        ->distinct()
        ->join('registration', 'employeedeductions.WorkNo', '=', 'registration.empid')
        ->where('registration.contractor', 'NO')
        ->where('employeedeductions.prossty', 'Payment')
        ->whereIn('registration.payrolty', $allowedPayrollIds)
        ->pluck('WorkNo')
        ->toArray();

    if (empty($workNos)) {
        return [];
    }

    foreach ($workNos as $workNo) {

        DB::transaction(function () use ($workNo, $month, $year, &$grossPays) {

            // 1. Delete previous payhouse rows
            Payhouse::where('WorkNo', $workNo)
                ->where('month', $month)
                ->where('year', $year)
                ->delete();

            // 2. Retrieve all payment items
            $items = EmployeeDeduction::query()
                ->where('WorkNo', $workNo)
                ->where('prossty', 'Payment')
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            $grossSalary = 0;

            // 3. Insert each payment item into payhouse
            foreach ($items as $item) {

                Payhouse::create([
                    'WorkNo'    => $workNo,
                    'pname'     => $item->pcate,
                    'itemcode'  => $item->PCode,
                    'pcategory' => 'Payment',
                    'loanshares'=> $item->loanshares,
                    'tamount'   => $item->Amount,
                    'month'     => $month,
                    'year'      => $year,
                ]);

                $grossSalary += $item->Amount;
            }

            // 4. Insert total gross salary
            Payhouse::create([
                'WorkNo'    => $workNo,
                'pname'     => 'Gross Salary',
                'pcategory' => 'Gross Salary',
                'itemcode'  => 'P98',
                'tamount'   => $grossSalary,
                'month'     => $month,
                'year'      => $year,
            ]);

            // 5. Build result array
            $grossPays[] = [
                'WorkNo'       => $workNo,
                'grossSalary'  => $grossSalary,
            ];
        });
    }

    return $grossPays;
}
public function processBenefits(string $month, string $year, array $grossPays): array
{
    $totalGrossPays = [];

    foreach ($grossPays as $row) {

        $workNo = $row['WorkNo'];
        $grossSalary = $row['grossSalary'];

        DB::transaction(function () use ($workNo, $month, $year, $grossSalary, &$totalGrossPays) {

            // 1. Retrieve all benefit items for this employee
            $benefits = EmployeeDeduction::query()
                ->where('WorkNo', $workNo)
                ->where('prossty', 'Benefit')
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            $benefitSum = 0;

            // 2. Insert each benefit into Payhouse table
            foreach ($benefits as $item) {
                Payhouse::create([
                    'WorkNo'    => $workNo,
                    'pname'     => $item->pcate,
                    'itemcode'  => $item->PCode,
                    'pcategory' => 'Benefit',
                    'loanshares'=> $item->loanshares,
                    'tamount'   => $item->Amount,
                    'month'     => $month,
                    'year'      => $year,
                ]);

                $benefitSum += $item->Amount;
            }

            // 3. Total Gross = Gross Salary + Total Benefits
            $totalGross = $grossSalary + $benefitSum;

            // 4. Insert total gross into payhouse
            Payhouse::create([
                'WorkNo'    => $workNo,
                'pname'     => 'Total Gross Salary',
                'pcategory' => 'Gross',
                'tamount'   => $totalGross,
                'month'     => $month,
                'year'      => $year,
            ]);

            // 5. Store in return array
            $totalGrossPays[] = [
                'WorkNo'     => $workNo,
                'totalGross' => $totalGross,
            ];
        });
    }

    return $totalGrossPays;
}

public function calculateMedCover(string $month, string $year,  array $allowedPayrollIds): array
    {
        // Allowed payrolls (replace session with auth or request)
         if (empty($allowedPayrollIds)) {
        throw new \Exception('No payroll access granted');
    }

        // STEP 1: Get list of employees with gross pay in payhouse
        $grossPays = Payhouse::query()
            ->select('payhouse.WorkNo', 'payhouse.tamount as totalGrossPay')
            ->join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->where('registration.contractor', 'NO')
            ->where('payhouse.pname', 'Total Gross Salary')
            ->where('payhouse.month', $month)
            ->where('payhouse.year', $year)
            ->where('registration.nhif_shif', 'YES')
            ->whereIn('registration.payrolty', $allowedPayrollIds)
            ->get();

        if ($grossPays->isEmpty()) {
            return [];
        }

        // STEP 2: Determine which cover is active
        $nhifActive  = Nhif::where('hstatus', 'ACTIVE')->exists();
        $shifActive  = Shif::where('hstatus', 'ACTIVE')->exists();

        if (!$nhifActive && !$shifActive) {
            throw new \Exception("No active NHIF or SHIF configuration found.");
        }

        // NHIF OR SHIF CONFIG
        $reliefType = null;
        $nhifCode   = null;

        if ($nhifActive) {
            $activeNhif = Nhif::where('hstatus', 'ACTIVE')->first();
            $reliefType = $activeNhif->relief;
            $nhifCode   = $activeNhif->nhifcode;

            $brackets = Nhif::orderBy('lowerlimit')->get();
        }

        if ($shifActive) {
            $activeShif = Shif::where('hstatus', 'ACTIVE')->first();
            $reliefType = $activeShif->relief;
            $shifBrackets = Shif::orderBy('code')->get();
        }

        // Store results
        $output = [];

        DB::beginTransaction();

        try {
            foreach ($grossPays as $gp) {
                $workNo = $gp->WorkNo;
                $gross  = $gp->totalGrossPay;
                $medCover = 0;

                /*
                |--------------------------------------------------------------------------
                | NHIF PROCESSING
                |--------------------------------------------------------------------------
                */
                if ($nhifActive) {
                    foreach ($brackets as $b) {
                        if ($gross >= $b->lowerlimit && $gross <= $b->upperlimit) {
                            $medCover = $b->amount;
                            break;
                        }
                    }

                    // INSERT MEDICAL DEDUCTION
                    Payhouse::create([
                        'WorkNo'   => $workNo,
                        'pname'    => 'NHIF',
                        'itemcode' => $nhifCode,
                        'pcategory'=> 'Deduction',
                        'tamount'  => $medCover,
                        'month'    => $month,
                        'year'     => $year
                    ]);

                    // Relief
                    if (in_array($reliefType, ['RELIEF ON TAXABLE', 'Relief on Paye'])) {
                        Payhouse::create([
                            'WorkNo'   => $workNo,
                            'pname'    => 'NHIF',
                            'itemcode' => $nhifCode,
                            'pcategory'=> $reliefType,
                            'tamount'  => $medCover,
                            'month'    => $month,
                            'year'     => $year
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | SHIF PROCESSING
                |--------------------------------------------------------------------------
                */
                if ($shifActive) {
                    foreach ($shifBrackets as $sb) {
                        $medCover = max(($gross * $sb->percentage) / 100, $sb->minimumcont);

                        Payhouse::create([
                            'WorkNo'   => $workNo,
                            'pname'    => $sb->cname,
                            'itemcode' => $sb->code,
                            'pcategory'=> 'Deduction',
                            'tamount'  => $medCover,
                            'month'    => $month,
                            'year'     => $year
                        ]);

                        if (in_array($reliefType, ['RELIEF ON TAXABLE', 'Relief on Paye'])) {
                            Payhouse::create([
                                'WorkNo'   => $workNo,
                                'pname'    => $sb->cname,
                                'itemcode' => $sb->code,
                                'pcategory'=> $reliefType,
                                'tamount'  => $medCover,
                                'month'    => $month,
                                'year'     => $year
                            ]);
                        }
                    }
                }

                $output[] = [
                    'WorkNo'   => $workNo,
                    'medCover' => $medCover
                ];
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $output;
    }

    public function calcAffodHousingLevy(string $month, string $year, array $allowedPayrollIds): array
    {
        $levyResults = [];
        $batchSize   = 1000;
        $pendingRows = [];

        // Allowed payroll types (from session)
        if (empty($allowedPayrollIds)) {
        throw new \Exception('No payroll access granted');
    }

        return DB::transaction(function () use ($month, $year, $allowedPayrollIds, &$pendingRows, $batchSize) {

            // STEP 1: Get active levy config
            $levies = Hlevy::where('hstatus', 'ACTIVE')->get();
            if ($levies->isEmpty()) {
                return [];
            }

            // STEP 2: Fetch total gross salary per employee
            $grossPays = Payhouse::query()
                ->select('payhouse.WorkNo', 'payhouse.tamount as totalGrossPay')
                ->join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
                ->where('registration.contractor', 'NO')
                ->where('payhouse.pname', 'Total Gross Salary')
                ->where('payhouse.month', $month)
                ->where('payhouse.year', $year)
                ->whereIn('registration.payrolty', $allowedPayrollIds)
                ->distinct()
                ->get();

            if ($grossPays->isEmpty()) {
                return [];
            }

            // STEP 3: Loop employees + compute levy
            foreach ($grossPays as $gp) {
                $workNo = $gp->WorkNo;
                $gross  = $gp->totalGrossPay;

                foreach ($levies as $levy) {
                    $cname      = $levy->cname;
                    $perc       = $levy->percentage;
                    $code       = $levy->code;
                    $reliefType = $levy->relief;

                    // Compute levy
                    $amount = ($perc / 100) * $gross;

                    // 1st record (Deduction)
                    $pendingRows[] = [
                        'WorkNo'   => $workNo,
                        'pname'    => $cname,
                        'itemcode' => $code,
                        'tamount'  => $amount,
                        'month'    => $month,
                        'year'     => $year,
                        'pcategory'=> 'Deduction'
                    ];

                    // 2nd record (Relief)
                    if (in_array($reliefType, ['RELIEF ON TAXABLE', 'Relief on Paye'])) {
                        $pendingRows[] = [
                            'WorkNo'   => $workNo,
                            'pname'    => $cname,
                            'itemcode' => $code,
                            'tamount'  => $amount,
                            'month'    => $month,
                            'year'     => $year,
                            'pcategory'=> $reliefType
                        ];
                    }

                    // Do batch insert every 1000 rows
                    if (count($pendingRows) >= $batchSize) {
                        Payhouse::insert($pendingRows);
                        $pendingRows = [];
                    }
                }
            }

            // Insert remaining rows
            if (!empty($pendingRows)) {
                Payhouse::insert($pendingRows);
            }

            return ['status' => 'success'];
        });
    }



function calcGrelief($month, $year, $totalGrossPays)
{
    // Load static data ONCE
    $nssfBrackets    = DB::table('nssfbracket')->where('hstatus', 'ACTIVE')->get();
    $pensionBrackets = Pension::where('hstatus', 'ACTIVE')->get();
    $defaultRelief   = DB::table('defrelief')->first();

    $maxReliefLimit = $defaultRelief->Maxcont;
    $reliefCname    = $defaultRelief->cname;

    $taxables = [];

    foreach ($totalGrossPays as $item) {

        $workNo     = $item['WorkNo'];
        $totalGross = $item['totalGross'];

        $totalRelief  = 0;
        $totalRAmount = 0;

        // ---------- NSSF ELIGIBILITY ----------
        $nssfEligible = DB::table('registration')
            ->where('empid', $workNo)
            ->value('nssfp') === 'YES';

        // ---------- NSSF ----------
        if ($nssfEligible) {
            foreach ($nssfBrackets as $nssf) {
                $contribution = min(($nssf->emppercentage / 100) * $totalGross, $nssf->maxcont);

                // Insert deduction
                DB::table('payhouse')->insert([
                    'WorkNo'    => $workNo,
                    'pname'     => $nssf->cname,
                    'itemcode'  => $nssf->code,
                    'pcategory' => 'Deduction',
                    'tamount'   => $contribution,
                    'month'     => $month,
                    'year'      => $year,
                ]);

                // Add relief only if marked as RELIEF ON TAXABLE
                if ($nssf->relief === 'RELIEF ON TAXABLE') {
                    $totalRelief += $contribution;
                } else {
                    DB::table('payhouse')->insert([
                        'WorkNo'    => $workNo,
                        'pname'     => $nssf->cname,
                        'itemcode'  => $nssf->code,
                        'pcategory' => $nssf->relief,
                        'tamount'   => $contribution,
                        'month'     => $month,
                        'year'      => $year,
                    ]);
                }
            }
        }

        // ---------- PENSION ELIGIBILITY ----------
        $pensionEligible = DB::table('registration')
            ->where('empid', $workNo)
            ->value('penyes') === 'YES';

        // ---------- PENSION ----------
        if ($pensionEligible) {

            // Employee personal pension %
            $empRate = EmpPensionRate::where('WorkNo', $workNo)->value('epmpenperce');

            // Pension base (default = gross)
            $baseGroup = DB::table('pensiongroups')->value('cname');
            $pensionBase = $totalGross;

            if ($baseGroup === 'Salary Basic') {
                $pensionBase = DB::table('employeedeductions')
                    ->where('WorkNo', $workNo)
                    ->where('pcate', 'Salary Basic')
                    ->value('Amount');
            }

            foreach ($pensionBrackets as $pb) {

                $contribution = min(($empRate / 100) * $pensionBase, $pb->maxcont);

                // Insert pension deduction
                DB::table('payhouse')->insert([
                    'WorkNo'    => $workNo,
                    'pname'     => $pb->cname,
                    'itemcode'  => $pb->code,
                    'pcategory' => 'Deduction',
                    'tamount'   => $contribution,
                    'month'     => $month,
                    'year'      => $year,
                ]);

                if ($pb->relief === 'RELIEF ON TAXABLE') {
                    $totalRelief += $contribution;
                } else {
                    DB::table('payhouse')->insert([
                        'WorkNo'    => $workNo,
                        'pname'     => $pb->cname,
                        'itemcode'  => $pb->code,
                        'pcategory' => $pb->relief,
                        'tamount'   => $contribution,
                        'month'     => $month,
                        'year'      => $year,
                    ]);
                }
            }
        }

        // ---------- CAP RELIEF ----------
        if ($totalRelief > $maxReliefLimit) {
            $totalRelief = $maxReliefLimit;
        }

        // Insert final relief
        DB::table('payhouse')->insert([
            'WorkNo'    => $workNo,
            'pname'     => $reliefCname,
            'pcategory' => 'RELIEF ON TAXABLE',
            'tamount'   => $totalRelief,
            'month'     => $month,
            'year'      => $year,
        ]);

        // ---------- OTHER RELIEFS ----------
        $otherReliefs = DB::table('employeedeductions')
            ->where('WorkNo', $workNo)
            ->where('relief', 'RELIEF ON TAXABLE')
            ->get();

        foreach ($otherReliefs as $rel) {
            DB::table('payhouse')->insert([
                'WorkNo'    => $workNo,
                'itemcode'  => $rel->PCode,
                'pname'     => $rel->pcate,
                'pcategory' => 'RELIEF ON TAXABLE',
                'tamount'   => $rel->Amount,
                'month'     => $month,
                'year'      => $year,
            ]);

            $totalRAmount += $rel->Amount;
        }

        // ---------- GET TOTAL RELIEF INSERTED ----------
        $existingRelief = DB::table('payhouse')
            ->where('WorkNo', $workNo)
            ->where('pcategory', 'RELIEF ON TAXABLE')
            ->where('month', $month)
            ->where('year', $year)
            ->sum('tamount');

        $totalRAmount += $existingRelief;

        // ---------- FINAL TAXABLE ----------
        $taxable = $totalGross - $totalRAmount;

        DB::table('payhouse')->insert([
            'WorkNo'    => $workNo,
            'pname'     => 'Taxable',
            'pcategory' => 'Taxable',
            'tamount'   => $taxable,
            'month'     => $month,
            'year'      => $year,
        ]);

        $taxables[] = [
            'WorkNo'  => $workNo,
            'taxable' => $taxable,
        ];
    }

    return $taxables;
}

/**
 * Calculate tax for all employees and handle withholding tax for contractors
 * 
 * @param string $month
 * @param string $year
 * @param array $taxables Array of ['WorkNo' => ..., 'taxable' => ...]
 * @return array Array of tax charged per employee
 */
public function calculateTax($month, $year, $taxables)
{
    try {
        DB::beginTransaction();

        $taxCharged = [];
        
        // Fetch tax brackets once (cached for performance)
        $taxBrackets = Taxbrackets::orderBy('ID')->get()->toArray();
        
        if (empty($taxBrackets)) {
            throw new \Exception('No tax brackets found');
        }

        // Process regular income tax in batches
        $taxCharged = $this->processIncomeTax($month, $year, $taxables, $taxBrackets);
        
        // Process withholding tax for contractors
        $this->processWithholdingTax($month, $year);

        DB::commit();
        
        return $taxCharged;
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in calculateTax: ' . $e->getMessage(), [
            'month' => $month,
            'year' => $year,
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

/**
 * Process income tax for all employees
 * 
 * @param string $month
 * @param string $year
 * @param array $taxables
 * @param array $taxBrackets
 * @return array
 */
private function processIncomeTax($month, $year, $taxables, $taxBrackets)
{
    $taxCharged = [];
    $taxInserts = [];
    $batchSize = 500; // Optimized batch size
    
    foreach ($taxables as $taxableInfo) {
        $workNo = $taxableInfo['WorkNo'];
        $taxableAmount = $taxableInfo['taxable'];
        
        // Calculate tax using progressive tax brackets
        $totalTax = $this->calculateTaxForEmployee($taxableAmount, $taxBrackets);
        
        $taxInserts[] = [
            'WorkNo' => $workNo,
            'pname' => 'Income Tax',
            'pcategory' => 'Tax',
            'tamount' => $totalTax,
            'month' => $month,
            'year' => $year,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        $taxCharged[] = [
            'WorkNo' => $workNo,
            'taxCharged' => $totalTax
        ];
        
        // Batch insert when threshold is reached
        if (count($taxInserts) >= $batchSize) {
            Payhouse::insert($taxInserts);
            $taxInserts = [];
        }
    }
    
    // Insert remaining records
    if (!empty($taxInserts)) {
        Payhouse::insert($taxInserts);
    }
    
    return $taxCharged;
}

/**
 * Calculate tax for a single employee using progressive tax brackets
 * 
 * @param float $taxableAmount
 * @param array $taxBrackets
 * @return float
 */
private function calculateTaxForEmployee($taxableAmount, $taxBrackets)
{
    $totalTax = 0.0;
    $remainingTaxable = $taxableAmount;
    
    foreach ($taxBrackets as $bracket) {
        $minAmount = $bracket['minamount'];
        $maxAmount = $bracket['maxamount'];
        $taxRate = $bracket['taxrate'] / 100;
        
        if ($taxableAmount > $minAmount) {
            $taxablePortion = min($remainingTaxable, $maxAmount - $minAmount);
            $totalTax += $taxablePortion * $taxRate;
            $remainingTaxable -= $taxablePortion;
            
            if ($remainingTaxable <= 0) {
                break;
            }
        }
    }
    
    return round($totalTax, 2);
}

/**
 * Process withholding tax for contractors
 * 
 * @param string $month
 * @param string $year
 * @return void
 */
private function processWithholdingTax($month, $year)
{
    // Get contractors with their withholding groups
    $contractors = DB::table('employeedeductions as e')
        ->select(
            'e.WorkNo',
            'r.contractor',
            DB::raw("GROUP_CONCAT(DISTINCT CONCAT(w.code, ':', w.cname) SEPARATOR ';') as whgroups")
        )
        ->join('registration as r', 'e.WorkNo', '=', 'r.empid')
        ->crossJoin('whgroups as w')
        ->where('r.contractor', 'YES')
        ->groupBy('e.WorkNo', 'r.contractor')
        ->get();
    
    if ($contractors->isEmpty()) {
        return;
    }
    
    // Fetch withholding tax details once
    $withholdingTax = Withholding::first();
    
    if (!$withholdingTax) {
        Log::warning('No withholding tax configuration found');
        return;
    }
    
    $wpercentage = $withholdingTax->wpercentage / 100;
    $withcname = $withholdingTax->cname;
    $withcode = $withholdingTax->code;
    
    $withholdingInserts = [];
    $batchSize = 500;
    
    foreach ($contractors as $contractor) {
        $workNo = $contractor->WorkNo;
        
        // Delete existing records for this contractor
        Payhouse::where('WorkNo', $workNo)
            ->where('month', $month)
            ->where('year', $year)
            ->delete();
        
        $whgroups = explode(';', $contractor->whgroups);
        
        foreach ($whgroups as $whgroup) {
            if (empty($whgroup)) continue;
            
            list($code, $cname) = explode(':', $whgroup);
            
            // Fetch earnings based on withholding group type
            $earning = $this->getContractorEarnings($workNo, $code, $cname, $month, $year);
            
            // Calculate withholding tax
            $withholding = $wpercentage * $earning;
            
            $withholdingInserts[] = [
                'WorkNo' => $workNo,
                'pname' => $withcname,
                'itemcode' => $withcode,
                'pcategory' => 'Deduction',
                'tamount' => $withholding,
                'month' => $month,
                'year' => $year,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Batch insert when threshold is reached
            if (count($withholdingInserts) >= $batchSize) {
                Payhouse::insert($withholdingInserts);
                $withholdingInserts = [];
            }
        }
    }
    
    // Insert remaining records
    if (!empty($withholdingInserts)) {
        Payhouse::insert($withholdingInserts);
    }
}

/**
 * Get contractor earnings based on withholding group type
 * 
 * @param string $workNo
 * @param string $code
 * @param string $cname
 * @param string $month
 * @param string $year
 * @return float
 */
private function getContractorEarnings($workNo, $code, $cname, $month, $year)
{
    if ($cname == 'Gross Salary') {
        // Sum all payments for this contractor
        $earning = EmployeeDeduction::where('WorkNo', $workNo)
            ->where('prossty', 'Payment')
            ->sum('Amount') ?? 0;
        
        // Insert total gross salary record
        Payhouse::create([
            'WorkNo' => $workNo,
            'pname' => 'Total Gross Salary',
            'itemcode' => $code,
            'pcategory' => 'Gross',
            'tamount' => $earning,
            'month' => $month,
            'year' => $year
        ]);
        
        // Insert individual payment records
        $payments = EmployeeDeduction::where('WorkNo', $workNo)
            ->where('prossty', 'Payment')
            ->select('PCode', 'pcate', 'Amount')
            ->get();
        
        $individualInserts = [];
        foreach ($payments as $payment) {
            $individualInserts[] = [
                'WorkNo' => $workNo,
                'pname' => $payment->pcate,
                'itemcode' => $payment->PCode,
                'pcategory' => 'Payment',
                'tamount' => $payment->Amount,
                'month' => $month,
                'year' => $year,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        if (!empty($individualInserts)) {
            Payhouse::insert($individualInserts);
        }
        
    } else {
        // Get specific payment code amount
        $earning = EmployeeDeduction::where('WorkNo', $workNo)
            ->where('PCode', $code)
            ->where('pcate', $cname)
            ->value('Amount') ?? 0;
        
        // Insert payment record
        Payhouse::create([
            'WorkNo' => $workNo,
            'pname' => 'Total Gross Salary',
            'itemcode' => $code,
            'pcategory' => 'Payment',
            'tamount' => $earning,
            'month' => $month,
            'year' => $year
        ]);
    }
    
    return $earning;
}

/**
 * Calculate taxable relief for each employee and calculate PAYE
 * 
 * @param string $month
 * @param string $year
 * @return array Array of PAYE results for each employee
 * @throws \Exception
 */
public function calcTaxableRelief($month, $year, array $allowedPayrollIds)
{
    try {
        DB::beginTransaction();
        
        $payeResults = [];

        if (empty($allowedPayrollIds)) {
        throw new \Exception('No payroll access granted');
    }
        
        
        
        // Fetch insurance categories once
        $insuranceCategories = DB::table('insurancergroups')
            ->distinct()
            ->pluck('pcate')
            ->toArray();
        
        // Fetch insurance relief configurations once
        $insuranceReliefs = DB::table('insurancerelief')
            ->select('Iname', 'percentage', 'Maxamount')
            ->get()
            ->toArray();
        
        // Fetch personal relief configurations once
        $personalReliefs = Prelief::select('cname', 'Amount')->get()->toArray();
        
        // Get all non-contractor employees for the month/year with allowed payroll types
        $employees = DB::table('payhouse')
            ->select('payhouse.WorkNo')
            ->join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->where('registration.contractor', 'NO')
            ->where('payhouse.month', $month)
            ->where('payhouse.year', $year)
            ->whereIn('registration.payrolty', $allowedPayrollIds)
            ->distinct()
            ->pluck('WorkNo')
            ->toArray();
        
        if (empty($employees)) {
            DB::commit();
            return $payeResults;
        }
        
        // Process employees in chunks for better memory management
        $chunks = array_chunk($employees, 100);
        
        foreach ($chunks as $employeeChunk) {
            foreach ($employeeChunk as $workNo) {
                $payeAmount = $this->processEmployeeRelief(
                    $workNo, 
                    $month, 
                    $year, 
                    $insuranceCategories,
                    $insuranceReliefs,
                    $personalReliefs
                );
                
                $payeResults[] = [
                    'WorkNo' => $workNo,
                    'PAYE' => $payeAmount
                ];
            }
        }
        
        DB::commit();
        
        return $payeResults;
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in calcTaxableRelief: ' . $e->getMessage(), [
            'month' => $month,
            'year' => $year,
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

/**
 * Process relief calculations for a single employee
 * 
 * @param string $workNo
 * @param string $month
 * @param string $year
 * @param array $insuranceCategories
 * @param array $insuranceReliefs
 * @param array $personalReliefs
 * @return float Final PAYE amount
 */
private function processEmployeeRelief(
    $workNo, 
    $month, 
    $year, 
    $insuranceCategories,
    $insuranceReliefs,
    $personalReliefs
)
{
    // Step 1: Calculate total insurance deductions
    $totalDeductions = $this->calculateInsuranceDeductions(
        $workNo, 
        $month, 
        $year, 
        $insuranceCategories
    );
    
    // Step 2: Insert insurance relief records
    $this->insertInsuranceRelief(
        $workNo, 
        $month, 
        $year, 
        $totalDeductions, 
        $insuranceReliefs
    );
    
    // Step 3: Insert employee-specific relief items
    $this->insertEmployeeReliefItems($workNo, $month, $year);
    
    // Step 4: Get income tax amount
    $incomeTaxAmount = Payhouse::where('WorkNo', $workNo)
        ->where('pname', 'Income Tax')
        ->where('month', $month)
        ->where('year', $year)
        ->value('tamount') ?? 0;
    
    // Step 5: Insert personal relief (capped at income tax)
    $this->insertPersonalRelief(
        $workNo, 
        $month, 
        $year, 
        $incomeTaxAmount, 
        $personalReliefs
    );
    
    // Step 6: Calculate and insert final PAYE
    $finalPAYE = $this->calculateAndInsertPAYE(
        $workNo, 
        $month, 
        $year, 
        $incomeTaxAmount
    );
    
    return $finalPAYE;
}

/**
 * Calculate total insurance deductions for an employee
 * 
 * @param string $workNo
 * @param string $month
 * @param string $year
 * @param array $insuranceCategories
 * @return float
 */
private function calculateInsuranceDeductions($workNo, $month, $year, $insuranceCategories)
{
    if (empty($insuranceCategories)) {
        return 0;
    }
    
    return Payhouse::where('WorkNo', $workNo)
        ->whereIn('pname', $insuranceCategories)
        ->where('pcategory', 'Deduction')
        ->where('month', $month)
        ->where('year', $year)
        ->sum('tamount') ?? 0;
}

/**
 * Insert insurance relief records
 * 
 * @param string $workNo
 * @param string $month
 * @param string $year
 * @param float $totalDeductions
 * @param array $insuranceReliefs
 * @return void
 */
private function insertInsuranceRelief($workNo, $month, $year, $totalDeductions, $insuranceReliefs)
{
    if ($totalDeductions <= 0 || empty($insuranceReliefs)) {
        return;
    }
    
    $reliefInserts = [];
    
    foreach ($insuranceReliefs as $relief) {
        $percentage = $relief->percentage ?? $relief['percentage'];
        $maxAmount = $relief->Maxamount ?? $relief['Maxamount'];
        $iName = $relief->Iname ?? $relief['Iname'];
        
        $reliefAmount = min(
            $totalDeductions * ($percentage / 100), 
            $maxAmount
        );
        
        $reliefInserts[] = [
            'WorkNo' => $workNo,
            'pname' => $iName,
            'pcategory' => 'Relief on Paye',
            'tamount' => round($reliefAmount, 2),
            'month' => $month,
            'year' => $year,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
    
    if (!empty($reliefInserts)) {
        Payhouse::insert($reliefInserts);
    }
}

/**
 * Insert employee-specific relief items from employeedeductions
 * 
 * @param string $workNo
 * @param string $month
 * @param string $year
 * @return void
 */
private function insertEmployeeReliefItems($workNo, $month, $year)
{
    $reliefItems = EmployeeDeduction::where('WorkNo', $workNo)
        ->where('prossty', 'Relief')
        ->where('relief', 'Relief on Paye')
        ->where('month', $month)
        ->where('year', $year)
        ->select('PCode', 'pcate', 'Amount')
        ->get();
    
    if ($reliefItems->isEmpty()) {
        return;
    }
    
    $itemInserts = [];
    
    foreach ($reliefItems as $item) {
        $itemInserts[] = [
            'WorkNo' => $workNo,
            'pname' => $item->pcate,
            'itemcode' => $item->PCode,
            'pcategory' => 'Relief on Paye',
            'tamount' => $item->Amount,
            'month' => $month,
            'year' => $year,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
    
    if (!empty($itemInserts)) {
        Payhouse::insert($itemInserts);
    }
}

/**
 * Insert personal relief records (capped at income tax amount)
 * 
 * @param string $workNo
 * @param string $month
 * @param string $year
 * @param float $incomeTaxAmount
 * @param array $personalReliefs
 * @return void
 */
private function insertPersonalRelief($workNo, $month, $year, $incomeTaxAmount, $personalReliefs)
{
    if (empty($personalReliefs)) {
        return;
    }
    
    $preliefInserts = [];
    
    foreach ($personalReliefs as $prelief) {
        $reliefAmount = $prelief['Amount'];
        
        // Cap relief at income tax amount
        if ($incomeTaxAmount < $reliefAmount) {
            $reliefAmount = $incomeTaxAmount;
        }
        
        $preliefInserts[] = [
            'WorkNo' => $workNo,
            'pname' => $prelief['cname'],
            'pcategory' => 'Relief on Paye',
            'tamount' => round($reliefAmount, 2),
            'month' => $month,
            'year' => $year,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
    
    if (!empty($preliefInserts)) {
        Payhouse::insert($preliefInserts);
    }
}

/**
 * Calculate final PAYE and insert into payhouse
 * 
 * @param string $workNo
 * @param string $month
 * @param string $year
 * @param float $incomeTaxAmount
 * @return float Final PAYE amount
 */
private function calculateAndInsertPAYE($workNo, $month, $year, $incomeTaxAmount)
{
    // Calculate total relief on PAYE
    $totalRelief = Payhouse::where('WorkNo', $workNo)
        ->where('pcategory', 'Relief on Paye')
        ->where('month', $month)
        ->where('year', $year)
        ->sum('tamount') ?? 0;
    
    // Calculate final PAYE
    $finalPAYE = $incomeTaxAmount - $totalRelief;
    
    // Ensure PAYE is not negative
    $finalPAYE = max(0, $finalPAYE);
    
    // Insert PAYE record
    Payhouse::create([
        'WorkNo' => $workNo,
        'pname' => 'PAYE',
        'pcategory' => 'Deduction',
        'itemcode' => 'D93',
        'tamount' => round($finalPAYE, 2),
        'month' => $month,
        'year' => $year
    ]);
    
    return round($finalPAYE, 2);
}

public function calcUnionDues($month, $year, $allowedPayrollIds)
    {
        

        try {
            DB::beginTransaction();

            // Get unionized employees with allowed payroll types
            $unionizedEmployees = EmployeeDeduction::join('registration', 'employeedeductions.WorkNo', '=', 'registration.empid')
                ->where('registration.unionized', 'YES')
                ->whereIn('registration.payrolty', $allowedPayrollIds)
                ->distinct()
                ->pluck('employeedeductions.WorkNo');

            if ($unionizedEmployees->isEmpty()) {
                return true;
            }

            // Get active union deduction configuration
            $unionDeduction = Union::where('cstatus', 'YES')->first();
            if (!$unionDeduction) {
                return true; // Union not active, stop silently
            }

            // Get union group configuration
            $unionGroup = UnionGroup::first();
            if (!$unionGroup) {
                return true; // No union group found, stop silently
            }

            $batchInserts = [];

            // Process each unionized employee
            foreach ($unionizedEmployees as $workNo) {
                $earning = $this->getUnionEarningAmount($workNo, $month, $year, $unionGroup);
                $unionAmount = $this->calculateUnionAmount($earning, $unionDeduction);

                if ($unionAmount > 0) {
                    $batchInserts[] = [
                        'WorkNo' => $workNo,
                        'pname' => $unionDeduction->cname,
                        'itemcode' => $unionDeduction->code,
                        'pcategory' => 'Deduction',
                        'tamount' => $unionAmount,
                        'month' => $month,
                        'year' => $year,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            // Batch insert union dues
            if (!empty($batchInserts)) {
                foreach (array_chunk($batchInserts, 1000) as $chunk) {
                    Payhouse::insert($chunk);
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Union dues calculation failed', [
                'month' => $month,
                'year' => $year,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get earning amount for union calculation
     */
    private function getUnionEarningAmount($workNo, $month, $year, $unionGroup)
    {
        if ($unionGroup->cname === 'Gross Salary') {
            return Payhouse::where('WorkNo', $workNo)
                ->where('pname', 'Total Gross Salary')
                ->where('month', $month)
                ->where('year', $year)
                ->value('tamount') ?? 0;
        } else {
            return EmployeeDeduction::where('WorkNo', $workNo)
                ->where('PCode', $unionGroup->code)
                ->where('pcate', $unionGroup->cname)
                ->value('Amount') ?? 0;
        }
    }

    /**
     * Calculate union amount based on percentage and maximum contribution
     */
    private function calculateUnionAmount($earning, $unionDeduction)
    {
        $percentage = $unionDeduction->percentage ?? 0;
        $maxcont = $unionDeduction->maxcont ?? 0;

        if ($percentage <= 0 || $earning <= 0) {
            return 0;
        }

        $calculatedAmount = ($percentage / 100) * $earning;

        return $maxcont > 0 ? min($calculatedAmount, $maxcont) : $calculatedAmount;
    }

    /**
     * Calculate fixed amount union dues (COTU)
     *
     * @param string $month
     * @param string $year
     * @return bool
     */
    public function calcUnionDues2($month, $year, $allowedPayrollIds)
    {
        

        try {
            DB::beginTransaction();

            // Get unionized employees with allowed payroll types
            $unionizedEmployees = EmployeeDeduction::join('registration', 'employeedeductions.WorkNo', '=', 'registration.empid')
                ->where('registration.unionized', 'YES')
                ->whereIn('registration.payrolty', $allowedPayrollIds)
                ->distinct()
                ->pluck('employeedeductions.WorkNo');

            if ($unionizedEmployees->isEmpty()) {
                return true;
            }

            // Get active COTU union deduction configuration
            $cotuDeduction = Cotu::where('cstatus', 'YES')->first();
            if (!$cotuDeduction) {
                return true; // COTU not active, stop silently
            }

            $batchInserts = [];

            // Process each unionized employee with fixed amount
            foreach ($unionizedEmployees as $workNo) {
                $batchInserts[] = [
                    'WorkNo' => $workNo,
                    'pname' => $cotuDeduction->cname,
                    'itemcode' => $cotuDeduction->code,
                    'pcategory' => 'Deduction',
                    'tamount' => $cotuDeduction->camount,
                    'month' => $month,
                    'year' => $year,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Batch insert COTU dues
            if (!empty($batchInserts)) {
                foreach (array_chunk($batchInserts, 1000) as $chunk) {
                    Payhouse::insert($chunk);
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('COTU union dues calculation failed', [
                'month' => $month,
                'year' => $year,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Combined method to process both types of union dues
     * This can be called from your controller for convenience
     */
    public function processAllUnionDues($month, $year, array $allowedPayrollIds)
    {
         if (empty($allowedPayrollIds)) {
        throw new \Exception('No payroll access granted');
    }

        $results = [];

        // Process percentage-based union dues
        $results['union_dues'] = $this->calcUnionDues($month, $year, $allowedPayrollIds);

        // Process fixed amount COTU dues
        $results['cotu_dues'] = $this->calcUnionDues2($month, $year, $allowedPayrollIds);

        return $results;
    }
public function calcNetPay($month, $year, array $allowedPayrollIds)
{
    try {
        DB::beginTransaction();

        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }
       
        // Get all employees for the given month & year with allowed payroll types
        $employees = DB::table('payhouse as p')
            ->select('p.WorkNo')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->whereIn('r.payrolty', $allowedPayrollIds)
            ->distinct()
            ->pluck('WorkNo')
            ->toArray();
        
        if (empty($employees)) {
            Log::warning('No employees found for net pay calculation');
            DB::commit();
            return;
        }
        
        Log::info('Processing employees for net pay', ['count' => count($employees)]);
        
        // Process each employee
        foreach ($employees as $workNo) {
            
            // ✅ Get deductions from employeedeductions table
            $edDeductions = DB::table('employeedeductions as ed')
                ->join('ptypes as pt', 'ed.PCode', '=', 'pt.code')
                ->select(
                    'ed.PCode', 
                    'ed.pcate', 
                    'ed.Amount', 
                    'ed.balance', 
                    'ed.loanshares', 
                    'ed.increREDU',
                    'pt.priority',
                    'ed.ID as deduction_id',
                    DB::raw("'employeedeductions' as source_table")
                )
                ->where('ed.prossty', 'Deduction')
                ->where('ed.month', $month)
                ->where('ed.year', $year)
                ->where('ed.WorkNo', $workNo)
                ->where('ed.statdeduc', '1')
                ->get();

            // ✅ Get deductions already in payhouse table
            $phDeductions = DB::table('payhouse as p')
                ->leftJoin('ptypes as pt', 'p.itemcode', '=', 'pt.code')
                ->leftJoin('employeedeductions as ed', function($join) use ($workNo, $month, $year) {
                    $join->on('ed.PCode', '=', 'p.itemcode')
                        ->where('ed.WorkNo', $workNo)
                        ->where('ed.month', $month)
                        ->where('ed.year', $year);
                })
                ->select(
                    'p.itemcode as PCode',
                    'p.pname as pcate',
                    'p.tamount as Amount',
                    'p.balance',
                    'p.loanshares',
                    DB::raw("NULL as increREDU"),
                    'pt.priority',
                    'p.ID as deduction_id',
                    DB::raw("'payhouse' as source_table")
                )
                ->where('p.WorkNo', $workNo)
                ->where('p.month', $month)
                ->where('p.year', $year)
                ->where('p.pcategory', 'Deduction')
                ->whereNull('ed.ID') // Only get payhouse deductions not in employeedeductions
                ->get();

            // ✅ Merge both collections and sort by priority
            $deductions = $edDeductions->merge($phDeductions)
                ->sortBy('priority')
                ->values();
            
            // Fetch Total Gross Salary
            $totalGrossSalary = DB::table('payhouse')
                ->where('pname', 'Total Gross Salary')
                ->where('WorkNo', $workNo)
                ->where('month', $month)
                ->where('year', $year)
                ->sum('tamount') ?? 0;
            
            // ✅ Initialize available amount (starts with gross salary)
            $availableAmount = $totalGrossSalary;
            
            Log::info("Processing deductions for employee", [
                'WorkNo' => $workNo,
                'gross_salary' => $totalGrossSalary,
                'deduction_count' => count($deductions)
            ]);
            
            // ✅ Process deductions by priority
            $insertDeductions = [];

            foreach ($deductions as $deduction) {
                $pcate = $deduction->pcate;
                $requestedAmount = (float) $deduction->Amount;
                $itemcode = $deduction->PCode;
                $loanshares = $deduction->loanshares ?? '';
                $increREDU = $deduction->increREDU;
                $currentBalance = (float) $deduction->balance;
                $priority = $deduction->priority;
                $deductionId = $deduction->deduction_id;
                $sourceTable = $deduction->source_table; // ✅ Track source
                
                // ✅ Determine actual deduction amount based on available funds
                $actualDeduction = 0;
                $remainingRecovery = 0;
                
                if ($availableAmount >= $requestedAmount) {
                    // Full deduction possible
                    $actualDeduction = $requestedAmount;
                    $availableAmount -= $actualDeduction;
                } else if ($availableAmount > 0) {
                    // Partial deduction (whatever is left)
                    $actualDeduction = $availableAmount;
                    $remainingRecovery = $requestedAmount - $actualDeduction;
                    $availableAmount = 0;
                } else {
                    // No funds left - nothing deducted
                    $actualDeduction = 0;
                    $remainingRecovery = $requestedAmount;
                }
                
                Log::info("Processing deduction", [
                    'WorkNo' => $workNo,
                    'code' => $itemcode,
                    'name' => $pcate,
                    'source' => $sourceTable,
                    'priority' => $priority,
                    'requested' => $requestedAmount,
                    'actual_deducted' => $actualDeduction,
                    'remaining' => $remainingRecovery,
                    'available_after' => $availableAmount
                ]);
                
                // ✅ Handle balance updates based on category
                $newBalance = null;
                
                if ($loanshares == 'balance' || $loanshares == 'loan' || $loanshares == 'interest') {
                    if ($increREDU == 'Increasing') {
                        // Increasing balance (e.g., savings) - only increase by what was actually deducted
                        $newBalance = $currentBalance + $actualDeduction;
                    } else {
                        // Reducing balance (e.g., loan repayment)
                        if ($actualDeduction > 0) {
                            $newBalance = $currentBalance - $actualDeduction;
                            
                            // Add remaining recovery back to balance (unpaid portion)
                            if ($remainingRecovery > 0) {
                                $newBalance += $remainingRecovery;
                            }
                        } else {
                            // No deduction happened - balance unchanged
                            $newBalance = $currentBalance;
                        }
                    }
                }
                
                // ✅ CRITICAL FIX: Handle based on source table
                if ($sourceTable === 'employeedeductions') {
                    // ✅ Update employeedeductions table
                    $updateData = ['Amount' => $actualDeduction];
                    if ($newBalance !== null) {
                        $updateData['balance'] = $newBalance;
                    }
                    
                    DB::table('employeedeductions')
                        ->where('ID', $deductionId)
                        ->update($updateData);
                    
                    Log::info("Updated employeedeductions", [
                        'deduction_id' => $deductionId,
                        'code' => $itemcode,
                        'updated_amount' => $actualDeduction,
                        'updated_balance' => $newBalance
                    ]);
                    
                    // ✅ Only INSERT into payhouse if actual deduction > 0
                    if ($actualDeduction > 0) {
                        $insertDeductions[] = [
                            'WorkNo' => $workNo,
                            'pname' => $pcate,
                            'itemcode' => $itemcode,
                            'pcategory' => 'Deduction',
                            'loanshares' => $loanshares,
                            'tamount' => $actualDeduction,
                            'balance' => $newBalance,
                            'month' => $month,
                            'year' => $year
                        ];
                    }
                    
                } else {
                    // ✅ Source is 'payhouse' - UPDATE existing record instead of inserting
                    $updateData = ['tamount' => $actualDeduction];
                    if ($newBalance !== null) {
                        $updateData['balance'] = $newBalance;
                    }
                    
                    DB::table('payhouse')
                        ->where('ID', $deductionId)
                        ->update($updateData);
                    
                    Log::info("Updated existing payhouse record", [
                        'payhouse_id' => $deductionId,
                        'code' => $itemcode,
                        'updated_amount' => $actualDeduction,
                        'updated_balance' => $newBalance
                    ]);
                }
                
                // ✅ Update loan/balance schedules if applicable
                if ($actualDeduction > 0 || $remainingRecovery > 0) {
                    if ($loanshares == 'loan') {
                        $this->updateLoanSchedule($workNo, $itemcode, $month, $year, $newBalance, $actualDeduction);
                    } elseif ($loanshares == 'balance') {
                        $this->updateBalanceSchedule($workNo, $itemcode, $month, $year, $newBalance, $actualDeduction);
                    }
                }
                
                // ✅ If no funds left, log remaining deductions that couldn't be processed
                if ($availableAmount <= 0 && $remainingRecovery > 0) {
                    Log::warning("Insufficient funds for full deduction", [
                        'WorkNo' => $workNo,
                        'deduction' => $pcate,
                        'code' => $itemcode,
                        'priority' => $priority,
                        'requested' => $requestedAmount,
                        'deducted' => $actualDeduction,
                        'unpaid' => $remainingRecovery
                    ]);
                }
            }
            
            // ✅ Batch insert ONLY new deductions from employeedeductions
            if (!empty($insertDeductions)) {
                DB::table('payhouse')->insert($insertDeductions);
                
                Log::info('Inserted new deductions into payhouse', [
                    'WorkNo' => $workNo,
                    'count' => count($insertDeductions)
                ]);
            }
            
            // ✅ Calculate total deductions (what was actually deducted)
            $totalDeductions = DB::table('payhouse')
                ->where('WorkNo', $workNo)
                ->where('pcategory', 'Deduction')
                ->where('month', $month)
                ->where('year', $year)
                ->sum('tamount') ?? 0;
            
            // ✅ Calculate net pay (should never be negative)
            $netPay = $totalGrossSalary - $totalDeductions;
            
            // ✅ Sanity check
            if ($netPay < 0) {
                Log::warning("Negative net pay detected, setting to 0", [
                    'WorkNo' => $workNo,
                    'gross' => $totalGrossSalary,
                    'deductions' => $totalDeductions,
                    'calculated_net' => $netPay
                ]);
                $netPay = 0;
            }
            
            // ✅ Insert Net Pay
            DB::table('payhouse')->insert([
                'WorkNo' => $workNo,
                'pname' => 'NET PAY',
                'pcategory' => 'NET',
                'itemcode' => 'P99',
                'tamount' => $netPay,
                'month' => $month,
                'year' => $year
            ]);
            
            Log::info('Calculated net pay', [
                'WorkNo' => $workNo,
                'gross' => $totalGrossSalary,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay
            ]);
        }
        
        DB::commit();
        
        Log::info('calcNetPay completed successfully', [
            'month' => $month,
            'year' => $year,
            'employees_processed' => count($employees)
        ]);
        
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

/**
 * ✅ Helper: Update loan schedule with new balance
 */
private function updateLoanSchedule($empid, $loantype, $month, $year, $newBalance, $actualPayment)
{
    $period = $month . $year;
    
    DB::table('loanshedule')
        ->where('empid', $empid)
        ->where('loantype', $loantype)
        ->where('Period', $period)
        ->update([
            'balance' => $newBalance,
            'mpay' => $actualPayment,
            'paidcheck' => $actualPayment > 0 ? 'YES' : 'NO'
        ]);
}

/**
 * ✅ Helper: Update balance schedule with new balance
 */
private function updateBalanceSchedule($empid, $balancecode, $month, $year, $newBalance, $actualRecovery)
{
    $period = $month . $year;
    
    DB::table('balancsched')
        ->where('empid', $empid)
        ->where('balancecode', $balancecode)
        ->where('Pperiod', $period)
        ->update([
            'balance' => $newBalance,
            'rrecovery' => $actualRecovery,
            'paidcheck' => $actualRecovery > 0 ? 'YES' : 'NO'
        ]);
}





}
