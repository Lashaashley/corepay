<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BranchesController extends Controller
{
    public function create()
{
    $branches = Branches::distinct()->get(['ID', 'branchname']);
    dd($branches); // Debug data
    return view('students.static', compact('branches'));
}


    public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'branchname' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Branches::create([
        'branchname' => $request->branchname,
    ]);

    return response()->json([
        'message' => 'Campus Saved!',
    ]);
}

public function getAll()
{
    $branches = Branches::paginate(3); // 3 records per page

    return response()->json([
        'data' => $branches->items(),
        'pagination' => [
            'current_page' => $branches->currentPage(),
            'last_page' => $branches->lastPage(),
            'per_page' => $branches->perPage(),
            'total' => $branches->total(),

        ],
    ]);
}

public function getAllBranches()
{
    // Fetch all branches
    $branches = Branches::all();

    return response()->json([
        'data' => $branches,
    ]);
}




public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $branches = Branches::findOrFail($id);
    
    $data = $request->validate([
        'branchname' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $branches->update($data);
    
    Log::info('After update:', $branches->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Branch updated successfully',
        'data' => $branches
    ]);
}

public function destroy($id)
{
    $branch = Branches::find($id);

    if (!$branch) {
        return response()->json([
            'success' => false,
            'message' => 'Branch not found.'
        ], 404);
    }

    try {
        $branch->delete();

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
}



}

