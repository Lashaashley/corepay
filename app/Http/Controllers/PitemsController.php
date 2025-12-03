<?php

namespace App\Http\Controllers;

use App\Models\Ptype;
use App\Models\EmployeeDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class PitemsController extends Controller
{
    public function index()
    {

        $payrollItems = Ptype::all();
        return view('students.pitems', compact('payrollItems'));
    }

    public function store(Request $request)
    {
        try {
            // Validate (example)
             $data = $request->all();
            if (empty($data['code']) || empty($data['description'])) {
                throw new Exception("Code and Description are required.");
            }

            // Check duplicates
            if (Ptype::where('code', $data['code'])
                ->orWhere('cname', $data['description'])
                ->exists()) {
                echo json_encode(['status' => 'duplicate', 'message' => 'An Item with this code or description already exists.']);
                return;
            }

            // Create record
            Ptype::create([
                'code' => $data['code'],
                'cname' => $data['description'],
                'procctype' => $data['processt'] ?? '',
                'category' => $data['category'] ?? '',
                'rate' => $data['rate'] ?? 0,
                'prossty'     => $data['prossty'] ?? '',
                'varorfixed'  => $data['varorfixed'] ?? '',
                'taxaornon'   => $data['taxaornon'] ?? '',
                'relief'      => $data['relief'] ?? '',
                'recintres'   => $data['recintres'] ?? '',
                'formularinpu'=> $data['formularinpu'] ?? '',
                'cumcas'      => $data['calctype'] ?? '',
                'intrestcode' => $data['interestcode'] ?? '',
                'codename'    => $data['interestdesc'] ?? '',
                'issaccorel'  => $data['saccocheck'] ?? '',
                'sposter'     => $data['staffSelect7'] ?? '',
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Payroll item added successfully']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


     public function update(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->all();
            // Validate required fields
            if (empty($data['id']) || empty($data['code']) || empty($data['cname'])) {
                throw new Exception("ID, Code and Name are required.");
            }

            $id = $data['id'];
            
            // Find the payroll item
            $ptype = Ptype::findOrFail($id);
            
            // Check for duplicates (excluding current record)
            $duplicate = Ptype::where('id', '!=', $id)
                ->where(function($query) use ($data) {
                    $query->where('code', $data['code'])
                          ->orWhere('cname', $data['cname']);
                })
                ->exists();
            
            if ($duplicate) {
                throw new Exception('A record with this code or name already exists.');
            }

            // Update the main ptypes record
            $ptype->update([
                'code' => $data['code'],
                'cname' => $data['cname'],
                'procctype' => $data['procctype'] ?? '',
                'varorfixed' => $data['varorfixed'] ?? '',
                'taxaornon' => $data['taxaornon'] ?? '',
                'category' => $data['category'] ?? '',
                'increREDU' => $data['increREDU'] ?? '',
                'rate' => $data['rate'] ?? 0,
                'prossty' => $data['prossty'] ?? '',
                'relief' => $data['relief'] ?? '',
                'recintres' => $data['recintres'] ?? '',
                'formularinpu' => $data['formula'] ?? '',
                'cumcas' => $data['cumcas'] ?? '',
                'intrestcode' => $data['intrestcode'] ?? '',
                'codename' => $data['codename'] ?? '',
                'issaccorel' => $data['saccocheck'] ?? '',
                'sposter' => $data['poster'] ?? ''
            ]);

            $messages = ['Payroll code updated successfully'];

            // Update employeedeductions table
            DB::table('employeedeductions')
                ->where('pcate', $data['cname'])
                ->update([
                    'PCode' => $data['code'],
                    'loanshares' => $data['category'] ?? '',
                    'procctype' => $data['procctype'] ?? '',
                    'varorfixed' => $data['varorfixed'] ?? '',
                    'taxaornon' => $data['taxaornon'] ?? '',
                    'increREDU' => $data['increREDU'] ?? '',
                    'rate' => $data['rate'] ?? 0,
                    'prossty' => $data['prossty'] ?? ''
                ]);

            $messages[] = 'Employee deductions updated successfully';

            // Conditional updates based on cname
            switch ($data['cname']) {
                case 'NSSF':
                    DB::table('nssfbracket')
                        ->limit(1)
                        ->update([
                            'code' => $data['code'],
                            'relief' => $data['relief'] ?? ''
                        ]);
                    $messages[] = 'NSSF bracket updated successfully';
                    break;

                case 'Pension':
                    DB::table('pensionbracket')
                        ->limit(1)
                        ->update([
                            'code' => $data['code'],
                            'relief' => $data['relief'] ?? ''
                        ]);
                    $messages[] = 'Pension bracket updated successfully';
                    break;

                case 'SHIF':
                    DB::table('shifbracket')
                        ->update([
                            'code' => $data['code'],
                            'relief' => $data['relief'] ?? ''
                        ]);
                    $messages[] = 'SHIF bracket updated successfully';
                    break;

                case 'NHIF':
                    DB::table('nhifbrack')
                        ->limit(1)
                        ->update([
                            'nhifcode' => $data['code'],
                            'relief' => $data['relief'] ?? ''
                        ]);
                    $messages[] = 'NHIF bracket updated successfully';
                    break;
            }

            DB::commit();

            echo json_encode([
                'success' => true,
                'status' => 'success',
                'message' => implode('; ', $messages)
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            echo json_encode([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // app/Http/Controllers/PtypeController.php

public function getDeductionPriorities()
{
    $deductions = Ptype::where('prossty', 'Deduction')
        ->orderBy('priority', 'asc')
        ->get(['id', 'code', 'cname', 'priority']);
    
    return response()->json([
        'status' => 'success',
        'deductions' => $deductions
    ]);
}

public function updateDeductionPriorities(Request $request)
{
    $priorities = $request->input('priorities', []);
    
    DB::beginTransaction();
    try {
        foreach ($priorities as $item) {
            Ptype::where('id', $item['id'])
                ->update(['priority' => $item['priority']]);
        }
        
        DB::commit();
        
        // Log audit trail
        logAuditTrail(
            session('user_id') ?? Auth::id(),
            'UPDATE',
            'ptypes',
            'bulk_priority_update',
            null,
            null,
            [
                'action' => 'deduction_priorities_reordered',
                'updated_count' => count($priorities)
            ]
        );
        
        return response()->json([
            'status' => 'success',
            'message' => 'Priorities updated successfully'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
