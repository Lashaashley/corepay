<?php

namespace App\Http\Controllers;
use App\Models\Withholding;
use App\Models\Whgroups;
use App\Models\Ptype;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhholdingController extends Controller
{
    public function index()
    {
        return view('students.ritems');
    }

      public function show()
{
    try {
        // Disable any unwanted output buffering or notices
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $withholding = Withholding::first();
        if (!$withholding) {
            echo json_encode([
                'success' => false,
                'message' => 'No data found in Withholding bracket'
            ]);
            return;
        }

        $whgroups = Whgroups::select('ID', 'code', 'cname')->get();

        echo json_encode([
            'success' => true,
            'cname' => $withholding->cname,
            'code' => $withholding->code,
            'wpercentage' => $withholding->wpercentage,
            'groups' => $whgroups->toArray()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching data: ' . $e->getMessage()
        ]);
    }
}

public function update(Request $request)
{
    try {
        $data = $request->all();

        // Validate required fields
        if (empty($data['cnamewh']) || empty($data['codewh']) || empty($data['Percentagewl'])) {
            throw new Exception("Code, Name and Percentage are required.");
        }

        // Find the record
        $withholding = Withholding::where('cname', $data['cnamewh'])->first();

        if (!$withholding) {
            throw new Exception("Withholding record not found.");
        }

        // Update data
        $withholding->wpercentage = $data['Percentagewl'];
        $withholding->code = $data['codewh'];

        if ($withholding->isDirty()) {
            $withholding->save();
        } else {
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'No changes detected'
            ]);
        }

        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'Withholding updated successfully'
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

public function storeGroup(Request $request)
{
    try {
        $request->validate([
            'pitem' => 'required|string',
            'code'  => 'required|string'
        ]);

        $code  = $request->code;
        $pitem = $request->pitem;

        // Check if code already exists
        $exists = Whgroups::where('code', $code)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'The Withholding code already exists.'
            ]);
        }

        // Insert into WH groups
        $group = Whgroups::create([
            'code'  => $code,
            'cname' => $pitem
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WH group added successfully.',
            'ID'      => $group->id
        ]);

    } catch (Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
public function deleteGroup(Request $request)
{
    try {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $id = $request->id;

        // Find record
        $group = Whgroups::find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Withholding group not found.'
            ]);
        }

        // Delete it
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Withholding group removed successfully.'
        ]);

    } catch (Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function getcodes(Request $request) {
    $type = 'Payment';
    
    // Fetch classes filtered by campus ID (caid)
    $statutoryOptions = Ptype::where('prossty', $type)->get();
    
    return response()->json([
        'data' => $statutoryOptions,
    ]);
}

}
