<?php

namespace App\Http\Controllers;

use App\Models\CompB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CompbController extends Controller
{
    public function create()
{
    $compb = CompB::distinct()->get(['ID', 'BankCode', 'Bank','Branch','BranchCode','swiftcode', 'accno']);
    dd($compb); // Debug data
    return view('students.static', compact('compb'));
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
        'accno' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    CompB::create([
        'BankCode' => $request->BankCode,
        'Bank' => $request->Bank,
        'Branch' => $request->Branch,
        'BranchCode' => $request->BranchCode,
        'swiftcode' => $request->swiftcode,
        'accno' => $request->accno,
    ]);

    return response()->json([
        'message' => 'Bank Saved!',
    ]);
}

public function getAll()
{
    // Join houses with branches to include branchname
    $compb = CompB::paginate(5); // Paginate the results

    return response()->json([
        'data' => $compb->items(),
        'pagination' => [
            'current_page' => $compb->currentPage(),
            'last_page' => $compb->lastPage(),
            'per_page' => $compb->perPage(),
            'total' => $compb->total(),
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

