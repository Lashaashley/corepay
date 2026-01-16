<?php

namespace App\Http\Controllers;

use App\Models\Banks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BanksController extends Controller
{
    public function create()
{
    $banks = Banks::distinct()->get(['ID', 'BankCode', 'Bank','Branch','BranchCode','swiftcode']);
    dd($banks); // Debug data
    return view('students.static', compact('Banks'));
}


    public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'BankCode' => 'required|string|max:255',
        'Bank' => 'required|string|max:255',
        'Branch' => 'required|string|max:255',
        'BranchCode' => 'required|string|max:255',
        'swiftcode' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Banks::create([
        'BankCode' => $request->BankCode,
        'Bank' => $request->Bank,
        'Branch' => $request->Branch,
        'BranchCode' => $request->BranchCode,
        'swiftcode' => $request->swiftcode,
    ]);

    return response()->json([
        'message' => 'Bank Saved!',
    ]);
}

public function getAll()
{
    // Join houses with branches to include branchname
    $banks = Banks::paginate(5); // Paginate the results

    return response()->json([
        'data' => $banks->items(),
        'pagination' => [
            'current_page' => $banks->currentPage(),
            'last_page' => $banks->lastPage(),
            'per_page' => $banks->perPage(),
            'total' => $banks->total(),
        ],
    ]);
}

public function getAllBanks()
{
    // Fetch all branches
    $banks = DB::table('banks')
    ->select('Bank')
    ->distinct()
        ->get();

    return response()->json([
        'data' => $banks,
    ]);
}
public function getBranchesDepts()
{
    // Fetch all branches
    $branches = Banks::all();

    return response()->json([
        'data' => $branches,
    ]);
}
public function getBranchesByBank(Request $request) {
    $campusId = $request->input('campusId');
    
    // Fetch classes filtered by campus ID (caid)
    $branches = Banks::where('Bank', $campusId)->get();
    
    return response()->json([
        'data' => $branches,
    ]);
}

public function getCodesBank(Request $request)
{
    $request->validate([
        'bank' => 'required|string',
        'branch' => 'required|string',
    ]);

    $branches = Banks::where('Bank', $request->bank)
        ->where('Branch', $request->branch)
        ->get();

    return response()->json([
        'data' => $branches,
    ]);
}



public function update(Request $request, $id)
{
    $userId = session('user_id') ?? Auth::id();
    
    
    $banks = Banks::findOrFail($id);
    
    $data = $request->validate([
        'bankName' => 'required|string|max:255',
        'bankCode' => 'required|string|max:255',
        'branchName' => 'required|string|max:255',
        'branchCode' => 'required|string|max:255',
        'swiftcode' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $banks->update($data);
    
    Log::info('After update:', $banks->toArray()); // Add logging for debugging
    
    return response()->json([
        'message' => 'Bank updated successfully',
        'data' => $banks
    ]);
}
/*
public function destroy($id)
{
    $depts = Depts::find($id);

    if (!$depts) {
        return response()->json([
            'success' => false,
            'message' => 'Branch not found.'
        ], 404);
    }

    try {
        $depts->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete branch. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}*/



}

