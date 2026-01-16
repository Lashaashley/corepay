<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmailconfigController extends Controller
{
    public function getAll()
{
    // Join houses with branches to include branchname
    $econfig = Email::paginate(5); // Paginate the results

    return response()->json([
        'data' => $econfig->items(),
        'pagination' => [
            'current_page' => $econfig->currentPage(),
            'last_page' => $econfig->lastPage(),
            'per_page' => $econfig->perPage(),
            'total' => $econfig->total(),
        ],
    ]);
}






public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $econfig = Email::findOrFail($id);
    
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'host' => 'required|string|max:255',
        'port' => 'required|string|max:255',
        'username' => 'required|string|max:255',
        'password' => 'required|string|max:255',
        'encryption' => 'required|string|max:255',
        'from_email' => 'required|string|max:255',
    ]);

    
   
    $econfig->update($data);
    
   

    return response()->json([
        'message' => 'Email configured successfully',
        'data' => $econfig
    ]);
}
}
