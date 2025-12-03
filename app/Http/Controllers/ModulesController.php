<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ModuleAsd;
use App\Models\Button;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ModulesController extends Controller
{
    /**
     * Display module assignment page
     */
    public function index()
    {
        $users = User::select('id', 'name')->get();
        $buttons = Button::orderBy('ID')->get();
        
        return view('students.massign', compact('users', 'buttons'));
    }

    /**
     * Get assigned modules for a user
     */
    public function getUserModules(Request $request)
    {
        $request->validate([
            'workNo' => 'required|exists:users,id'
        ]);

        try {
            $buttonIds = ModuleAsd::where('WorkNo', $request->workNo)
                ->pluck('buttonid')
                ->toArray();

            return response()->json([
                'status' => 'success',
                'buttonIds' => $buttonIds
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user modules', [
                'user' => $request->workNo,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user modules'
            ], 500);
        }
    }

    /**
     * Assign modules to user
     */
    public function assignModules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workNo' => 'required|exists:users,id',
            'modules' => 'required|array|min:1',
            'modules.*' => 'exists:buttons,ID'
        ], [
            'workNo.required' => 'Please select a user',
            'workNo.exists' => 'Selected user does not exist',
            'modules.required' => 'Please select at least one module',
            'modules.min' => 'Please select at least one module',
            'modules.*.exists' => 'Selected module does not exist'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Delete existing modules for this user
            ModuleAsd::where('WorkNo', $request->workNo)->delete();

            // Insert new modules
            $modulesToInsert = [];
            foreach ($request->modules as $buttonId) {
                $modulesToInsert[] = [
                    'WorkNo' => $request->workNo,
                    'buttonid' => $buttonId
                ];
            }

            ModuleAsd::insert($modulesToInsert);

            DB::commit();

            $user = User::find($request->workNo);

            Log::info('Modules assigned successfully', [
                'user_id' => $request->workNo,
                'user_name' => $user->name,
                'modules_count' => count($request->modules)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Modules assigned successfully to ' . $user->name . '!',
                'assigned_count' => count($request->modules)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Module assignment failed', [
                'user' => $request->workNo,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to assign modules. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove specific module from user
     */
    public function removeModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workNo' => 'required|exists:users,id',
            'buttonId' => 'required|exists:buttons,ID'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            ModuleAsd::where('WorkNo', $request->workNo)
                ->where('buttonid', $request->buttonId)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Module removed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Module removal failed', [
                'user' => $request->workNo,
                'button' => $request->buttonId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove module'
            ], 500);
        }
    }
}