<?php

namespace App\Http\Controllers;
use App\Models\Paytypes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index()
    {
        $payrollTypes = Paytypes::select('ID', 'pname')->get();
        
        return view('students.newuser', compact('payrollTypes'));
    }

     public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            //'newpass' => 'required|string|min:6|confirmed',
            'confirm' => 'required|same:newpass',
            'allowedPayroll' => 'nullable|array',
            'allowedPayroll.*' => 'exists:prolltypes,ID', // Adjust table name
            'profilepic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // 2MB max
        ], [
            'newpass.required' => 'Password is required',
            'newpass.min' => 'Password must be at least 6 characters',
            'confirm.same' => 'Passwords do not match',
            'email.unique' => 'This email is already registered',
            'profilepic.image' => 'Profile photo must be an image',
            'profilepic.max' => 'Profile photo must not exceed 2MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle profile photo upload
            $profilePhotoPath = null;
if ($request->hasFile('profilepic')) {
    $file = $request->file('profilepic');
    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    
    // Store in storage/app/public/profile-photos
    $file->storeAs('profile-photos', $filename, 'public');
    
    // Also copy to public/storage/profile-photos
    $file->move(public_path('storage/profile-photos'), $filename);
    
    $profilePhotoPath = 'profile-photos/' . $filename;
}

            // Convert allowed payrolls array to comma-separated string or JSON
            $allowedPayrolls = $request->allowedPayroll 
                ? implode(',', $request->allowedPayroll) 
                : null;

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->newpass),
                'profile_photo' => $profilePhotoPath,
                'allowedprol' => $allowedPayrolls
            ]);

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo' => $user->profile_photo 
                        ? asset('storage/' . $user->profile_photo) 
                        : null
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user. Please try again.'
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'newpass' => 'nullable|string|min:6|confirmed',
            'allowedPayroll' => 'nullable|array',
            'profilepic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle profile photo update
            if ($request->hasFile('profilepic')) {
                // Delete old photo
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                
                // Upload new photo
                $user->profile_photo = $request->file('profilepic')
                    ->store('profile-photos', 'public');
            }

            // Update user data
            $user->name = $request->name;
            $user->email = $request->email;
            
            if ($request->filled('newpass')) {
                $user->password = Hash::make($request->newpass);
            }
            
            $user->allowedprol = $request->allowedPayroll 
                ? implode(',', $request->allowedPayroll) 
                : null;
            
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('User update failed', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user.'
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Delete profile photo
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('User deletion failed', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user.'
            ], 500);
        }
    }
}

