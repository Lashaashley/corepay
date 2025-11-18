<?php

namespace App\Http\Controllers;

use App\Models\Depts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DeptController extends Controller
{
    public function create()
{
    $depts = Depts::distinct()->get(['ID', 'DepartmentName', 'brid']);
    dd($depts); // Debug data
    return view('students.static', compact('depts'));
}


    public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'deptname' => 'required|string|max:255',
        'brid' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Depts::create([
        'DepartmentName' => $request->deptname,
        'brid' => $request->brid,
    ]);

    return response()->json([
        'message' => 'Department Saved!',
    ]);
}

public function getAll()
{
    // Join houses with branches to include branchname
    $depts = Depts::join('branches', 'tbldepartments.brid', '=', 'branches.ID')
        ->select('tbldepartments.*', 'branches.branchname') // Select desired columns
        ->paginate(3); // Paginate the results

    return response()->json([
        'data' => $depts->items(),
        'pagination' => [
            'current_page' => $depts->currentPage(),
            'last_page' => $depts->lastPage(),
            'per_page' => $depts->perPage(),
            'total' => $depts->total(),
        ],
    ]);
}

public function getAllDepts()
{
    // Fetch all branches
    $depts = Depts::all();

    return response()->json([
        'data' => $depts,
    ]);
}




public function update(Request $request, $id)
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
}



}

