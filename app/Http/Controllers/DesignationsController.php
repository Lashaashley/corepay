<?php

namespace App\Http\Controllers;

use App\Models\Designations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DesignationsController extends Controller
{
    public function create()
{
    $designations = Designations::distinct()->get(['ID', 'vehiID', 'desig', 'pickup', 'Dropof']);
    dd($designations); // Debug data
    return view('students.set', compact('designations'));
}




public function store(Request $request) 
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'vehiID' => 'required|string|max:255',
        
        'desig' => 'required|string|max:255|unique:designations,desig',
        'pickup' => 'required|string|max:255',
        'Dropof' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Designations::create([
        'vehiID' => $request->vehiID,
        'desig' => $request->desig,
        'pickup' => $request->pickup,
        'Dropof' => $request->Dropof,
    ]);

    return response()->json([
        'message' => 'Pick up/ Drop point Saved!',
    ]);
}




public function getAll()
{
// 3 records per page
    $designations = Designations::join('buses', 'designations.vehiID', '=', 'buses.ID')
        ->select('designations.*', 'buses.busna') // Select desired columns
        ->paginate(3); // Paginate the results

    return response()->json([
        'data' => $designations->items(),
        'pagination' => [
            'current_page' => $designations->currentPage(),
            'last_page' => $designations->lastPage(),
            'per_page' => $designations->perPage(),
            'total' => $designations->total(),

        ],
    ]);
}

public function getAllDesignations()
{
    // Fetch all branches
    $designations = Designations::all();

    return response()->json([
        'data' => $designations,
    ]);
}




public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $designations = Designations::findOrFail($id);
    
    $data = $request->validate([
        'vehiID' => 'required|string|max:255',
        'desig' => [
            'required', 
            'string', 
            'max:255',
            Rule::unique('designations')->where(function ($query) use ($request) {
                return $query->where('desig', $request->desig);
            }),
        ],
        'pickup' => 'required|string|max:255',
        'Dropof' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $designations->update($data);
    
    Log::info('After update:', $designations->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Designation updated successfully',
        'data' => $designations
    ]);
}

public function destroy($id)
{
    $designations = Designations::find($id);

    if (!$designations) {
        return response()->json([
            'success' => false,
            'message' => 'Designation not found.'
        ], 404);
    }

    try {
        $designations->delete();

        return response()->json([
            'success' => true,
            'message' => 'Designation deleted successfully!'
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

