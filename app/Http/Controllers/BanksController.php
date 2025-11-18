<?php

namespace App\Http\Controllers;

use App\Models\Banks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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






/*public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $depts = Depts::findOrFail($id);
    
    $data = $request->validate([
        'branchname' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $depts->update($data);
    
    Log::info('After update:', $depts->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Branch updated successfully',
        'data' => $depts
    ]);
}

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

