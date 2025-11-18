<?php

namespace App\Http\Controllers;

use App\Models\Paytypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaytypesController extends Controller
{
    public function create()
{
    $paytypes = Paytypes::distinct()->get(['pname']);
    dd($paytypes); // Debug data
    return view('students.static', compact('paytypes'));
}


    public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'pname' => 'required|string|max:255',
       
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Paytypes::create([
        'pname' => $request->pname,
    ]);

    return response()->json([
        'message' => 'Payroll Type Saved!',
    ]);
}

public function getAll()
{
    // Join houses with branches to include branchname
    $paytypes = Paytypes::paginate(5); // Paginate the results

    return response()->json([
        'data' => $paytypes->items(),
        'pagination' => [
            'current_page' => $paytypes->currentPage(),
            'last_page' => $paytypes->lastPage(),
            'per_page' => $paytypes->perPage(),
            'total' => $paytypes->total(),
        ],
    ]);
}






public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $paytypes = Paytypes::findOrFail($id);
    
    $data = $request->validate([
        'pname' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $paytypes->update($data);
    
    Log::info('After update:', $paytypes->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Payroll Type updated successfully'
        
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

