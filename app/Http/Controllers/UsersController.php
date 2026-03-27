<?php

namespace App\Http\Controllers;
use App\Models\Paytypes;
use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Http;

class UsersController extends Controller
{
     protected $emailConfig;
     
    public function index()
    {
        $payrollTypes = Paytypes::select('ID', 'pname')->get();
        
        return view('students.newuser', compact('payrollTypes'));
    }
    private function loadEmailConfig(): void
{
    $config = DB::table('email_config')->first();
    
    if (!$config) {
        throw new \Exception('Email configuration not found in database');
    }
    
    $this->emailConfig = (array) $config;
}
    public function indexfun()
    {
        return view('students.musers');
    }

     public function store(Request $request)
{
    // Validation
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email|max:255',
        /*'newpass' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            function ($attribute, $value, $fail) {
                // Check password complexity: 3 out of 4 rules
                $rules = [
                    'uppercase' => preg_match('/[A-Z]/', $value),
                    'lowercase' => preg_match('/[a-z]/', $value),
                    'numbers' => preg_match('/[0-9]/', $value),
                    'symbols' => preg_match('/[~!@#$%^*_\-+=`|(){}\[\]:;"<>,.?\/&]/', $value),
                ];
                
                $metRules = count(array_filter($rules));
                
                if ($metRules < 3) {
                    $fail('Password must match at least 3 of 4 character rules (uppercase, lowercase, numbers, symbols).');
                }
            },
        ],*/
        'confirm' => 'required|same:newpass',
        'allowedPayroll' => 'nullable|array',
        'approvelvl' => 'nullable|string|max:255',
        'allowedPayroll.*' => 'exists:prolltypes,ID',
        'profilepic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ], [
        'newpass.required' => 'Password is required',
        'newpass.min' => 'Password must be at least 8 characters',
        'newpass.confirmed' => 'Password confirmation does not match',
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

        // Convert allowed payrolls array to comma-separated string
        $allowedPayrolls = $request->allowedPayroll 
            ? implode(',', $request->allowedPayroll) 
            : null;

        // Hash the password
        $hashedPassword = Hash::make($request->newpass);
        
        // Password expiry configuration (in days)
        $passwordExpiryDays = config('auth.password_expiry_days', 90);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $hashedPassword,
            'profile_photo' => $profilePhotoPath,
            'allowedprol' => $allowedPayrolls,
            'approvelvl' => $request->approvelvl ?? "No",
            'password_changed_at' => now(),
            'password_expires_at' => now()->addDays($passwordExpiryDays),
            'must_change_password' => false,
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
        

        // Save password to history
        \App\Models\PasswordHistory::create([
            'user_id' => $user->id,
            'password' => $hashedPassword,
            'created_at' => now()
        ]);

        try {
            $this->sendWelcomeEmail($user, $request->newpass);
            Log::info('Welcome email sent successfully', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            // Log email error but don't fail user creation
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            
            // You can still notify admin via audit trail
            logAuditTrail(
                session('user_id') ?? Auth::id(),
                'WARNING',
                'email',
                $user->id,
                null,
                ['email' => $user->email],
                [
                    'action_type' => 'welcome_email_failed',
                    'error_message' => $e->getMessage()
                ]
            );
        }
         
        logAuditTrail(
             session('user_id') ?? Auth::id(), // Current authenticated user who created this user
            'INSERT',
            'users',
            $user->id,
            null, // No old values for new record
            [
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo' => $user->profile_photo,
                'allowedprol' => $user->allowedprol,
                'password_expires_at' => $user->password_expires_at,
            ],
            [
                'action_type' => 'user_creation',
                'password_expiry_days' => $passwordExpiryDays,
                'has_profile_photo' => !is_null($profilePhotoPath),
                'allowed_payroll_count' => $request->allowedPayroll ? count($request->allowedPayroll) : 0,
            ]
        );
       

       

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully! Password will expire in ' . $passwordExpiryDays . ' days.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo' => $user->profile_photo 
                    ? asset('storage/' . $user->profile_photo) 
                    : null,
                'password_expires_at' => $user->password_expires_at->format('Y-m-d')
            ]
        ], 201);

    } catch (\Exception $e) {
        Log::error('User creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        logAuditTrail(
             session('user_id') ?? Auth::id(),
            'ERROR',
            'users',
            null,
            null,
            [
                'attempted_email' => $request->email,
                'attempted_name' => $request->name,
            ],
            [
                'action_type' => 'user_creation_failed',
                'error_message' => $e->getMessage(),
            ]
        );

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create user. Please try again.'
        ], 500);
    }
}
private function sendWelcomeEmail($user, $plainPassword): void
{
    Log::info("sendWelcomeEmail: Initiating welcome email for user [{$user->email}]");

    try {
        $accessToken = $this->getJubiPayAccessToken();
        $this->dispatchJubiPayEmail($accessToken, $user, $plainPassword);

        Log::info("sendWelcomeEmail: Welcome email flow completed successfully for [{$user->email}]");

    } catch (\Exception $e) {
        Log::error("sendWelcomeEmail: Failed for [{$user->email}] — {$e->getMessage()}");
        throw $e;
    }
}


private function getJubiPayAccessToken(): string
{
    $baseUrl    = config('services.jubipay.base_url');
    $username   = config('services.jubipay.username');
    $signinPath = '/api/auth/signin';

    Log::info("getJubiPayAccessToken: Attempting authentication", [
        'url'      => "{$baseUrl}{$signinPath}",
        'username' => $username,
    ]);

    $response = Http::timeout(30)
        ->post("{$baseUrl}{$signinPath}", [
            'username' => $username,
            'password' => config('services.jubipay.password'),
        ]);

    Log::info("getJubiPayAccessToken: Signin response received", [
        'status' => $response->status(),
        'body'   => $response->body(),
    ]);

    if ($response->failed()) {
        Log::error("getJubiPayAccessToken: Authentication failed", [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
        throw new \Exception(
            "JubiPay authentication failed: HTTP {$response->status()} — {$response->body()}"
        );
    }

    $data = $response->json();

    if (empty($data['accessToken'])) {
        Log::error("getJubiPayAccessToken: accessToken missing from response", [
            'response_keys' => array_keys($data ?? []),
        ]);
        throw new \Exception('JubiPay authentication response missing accessToken.');
    }

    Log::info("getJubiPayAccessToken: Token acquired successfully", [
        'token_type' => $data['tokenType']  ?? 'unknown',
        'expires_in' => $data['expires_in'] ?? 'unknown',
        'issued_at'  => $data['issued_at']  ?? 'unknown',
    ]);

    return $data['accessToken'];
}


private function dispatchJubiPayEmail(string $accessToken, $user, string $plainPassword): void
{
    $baseUrl       = config('services.jubipay.base_url');
    $emailEndpoint = config('services.jubipay.email_endpoint');
    $loginUrl      = config('app.url') . '/login';

    $payload = [
        'to'      => $user->email,
        'name'    => $user->name,
        'subject' => 'Welcome to Corepay - Login Credentials',
        'body'    => $this->getWelcomeEmailBody($user, $plainPassword, $loginUrl),
        'altBody' => $this->getWelcomeEmailPlainText($user, $plainPassword, $loginUrl),
    ];

    Log::info("dispatchJubiPayEmail: Preparing email payload", [
        'to'           => $user->email,
        'name'         => $user->name,
        'endpoint'     => "{$baseUrl}{$emailEndpoint}",
        'content_type' => 'application/x-www-form-urlencoded', // ← track what we're sending
    ]);

    Log::info("dispatchJubiPayEmail: Dispatching request to JubiPay email endpoint");

    $response = Http::timeout(30)
        ->withToken($accessToken)
        ->asForm()              // ← THE FIX
        ->post("{$baseUrl}{$emailEndpoint}", $payload);

    Log::info("dispatchJubiPayEmail: Response received", [
        'status' => $response->status(),
        'body'   => $response->body(),
    ]);

    if ($response->failed()) {
        Log::error("dispatchJubiPayEmail: Email dispatch failed", [
            'status'    => $response->status(),
            'body'      => $response->body(),
            'recipient' => $user->email,
        ]);
        throw new \Exception(
            "JubiPay email dispatch failed: HTTP {$response->status()} — {$response->body()}"
        );
    }

    Log::info("dispatchJubiPayEmail: Email successfully dispatched via JubiPay", [
        'recipient' => $user->email,
        'status'    => $response->status(),
    ]);
}
/**
 * HTML Email Body
 */
private function getWelcomeEmailBody($user, $plainPassword, $loginUrl): string
{
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Welcome to Corepay</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
            <div style='background-color: #4CAF50; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0;'>
                <h2>Welcome to Corepay</h2>
            </div>
            
            <div style='padding: 20px;'>
                <p>Hello <strong>{$user->name}</strong>,</p>
                
                <p>Your account has been created successfully in the Corepay. Below are your login credentials:</p>
                
                <div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0;'>
                    <p><strong>Login URL:</strong> <a href='{$loginUrl}'>{$loginUrl}</a></p>
                    <p><strong>Email:</strong> {$user->email}</p>
                    <p><strong>Password:</strong> {$plainPassword}</p>
                </div>
                
                <div style='background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>⚠️ Important Security Information:</strong></p>
                    <ul>
                        <li>Your password expires on <strong>{$user->password_expires_at->format('F j, Y')}</strong></li>
                        <li>For security reasons, we recommend changing your password after first login</li>
                        <li>Never share your password with anyone</li>
                        <li>If you didn't request this account, please contact the system administrator immediately</li>
                    </ul>
                </div>
                
                <p>Click the button below to access your account:</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$loginUrl}' style='background-color: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Login to Your Account</a>
                </div>
                
                
                <strong>Corepay</strong></p>
            </div>
            
            <div style='background-color: #f1f1f1; padding: 10px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 5px 5px;'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Plain Text Email Body (for non-HTML clients)
 */
private function getWelcomeEmailPlainText($user, $plainPassword, $loginUrl): string
{
    return "Welcome to Corepay
        
Hello {$user->name},

Your account has been created successfully in the Corepay. Below are your login credentials:

Login URL: {$loginUrl}
Email: {$user->email}
Password: {$plainPassword}

IMPORTANT SECURITY INFORMATION:
- Your password expires on {$user->password_expires_at->format('F j, Y')}
- For security reasons, we recommend changing your password after first login
- Never share your password with anyone
- If you didn't request this account, please contact the system administrator immediately

Best regards,
Corepay

---
This is an automated message. Please do not reply to this email.";
} 

/**
 * Load email configuration from database
 */

public function getData(Request $request)
{
    try {
        

        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';

        

        // Column mapping for ordering
        $columns = [
            0 => 'name',
            1 => 'id',
            2 => 'email',
            3 => 'password_expires_at',
            4 => 'allowedprol',
            5 => 'approvelvl',
        ];

        // ✅ Base query with relationships
         $query = User::select(['id', 'name', 'email', 'profile_photo', 'password_expires_at', 'allowedprol', 'approvelvl']);

        

        // Search functionality
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('users.name', 'like', "%{$searchValue}%")
                  ->orWhere('users.id', 'like', "%{$searchValue}%")
                  ->orWhere('users.email', 'like', "%{$searchValue}%")
                  ->orWhere('users.password_expires_at', 'like', "%{$searchValue}%")
                  ->orWhere('users.allowedprol', 'like', "%{$searchValue}%")
                  ->orWhere('users.approvelvl', 'like', "%{$searchValue}%");
            });

            Log::info('AgentsController getData: Search applied', [
                'searchValue' => $searchValue
            ]);
        }

        // Get total records before pagination
        $totalRecords = User::where('id', '!=', '1')->count();
        $filteredRecords = $query->count();

        Log::info('AgentsController getData: Record counts', [
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords
        ]);

        // Apply ordering
        $orderColumnName = $columns[$orderColumn] ?? 'id';
        $query->orderBy($orderColumnName, $orderDir);

        Log::info('AgentsController getData: Ordering applied', [
            'column' => $orderColumnName,
            'direction' => $orderDir
        ]);

        // Apply pagination
        $agents = $query->skip($start)->take($length)->get();

        
        // Format data for DataTable
        $data = [];
        foreach ($agents as $agent) {
            $agentData = [
                'full_name' => $agent->name,
                'profile_photo' => $agent->profile_photo,
                'id' => $agent->id,
                'email' => $agent->email,
                'password_expires_at' => $agent->password_expires_at ?? 'N/A',
                'allowedprol' => $agent->allowedprol ?? 'N/A',
                'approvelvl' => $agent->approvelvl ?? 'No',
                'actions' => $agent->id
            ];
            
            $data[] = $agentData;
        }

       

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        Log::info('AgentsController getData: Response prepared', [
            'response_structure' => [
                'draw' => $response['draw'],
                'recordsTotal' => $response['recordsTotal'],
                
                'recordsFiltered' => $response['recordsFiltered'],
                'data_count' => count($response['data'])
            ]
        ]);

        return response()->json($response);

    } catch (\Exception $e) {
        Log::error('AgentsController getData error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'draw' => $request->get('draw', 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Error loading data: ' . $e->getMessage()
        ], 500);
    }
}
public function edit($id)
{
    try {
        $user = User::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo' => $user->profile_photo,
                'allowedprol' => $user->allowedprol,
                'approvelvl' => $user->approvelvl
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to load user for editing', [
            'user_id' => $id,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to load user data'
        ], 500);
    }
}
    /**
     * Update user
     */
   public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    // Capture old values before update for audit trail
    $oldValues = [
        'name' => $user->name,
        'email' => $user->email,
        'profile_photo' => $user->profile_photo,
        'allowedprol' => $user->allowedprol,
        'password_expires_at' => $user->password_expires_at,
        'approvelvl' => $user->approvelvl,
    ];
    
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id . '|max:255',
        'newpass' => 'nullable|string|min:8|confirmed',
        'allowedPayroll' => 'nullable|array',
        'profilepic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'approvelvl' => 'nullable|string|max:255'
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }
    
    try {
        DB::beginTransaction();
        
        // Track if password was changed
        $passwordChanged = false;
        $passwordExpiryDays = null;
        
        // Track if profile photo was changed
        $profilePhotoChanged = false;
        $oldProfilePhoto = $user->profile_photo;
        
        // Handle profile photo update
        if ($request->hasFile('profilepic')) {
            // Delete old photo
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            // Upload new photo
            $user->profile_photo = $request->file('profilepic')
                ->store('profile-photos', 'public');
            $profilePhotoChanged = true;
        }
        
        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->approvelvl = $request->approvelvl;
        
        // Update password if provided
        if ($request->filled('newpass')) {
            // Validate password complexity
            if (!$this->validatePasswordComplexity($request->newpass)) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password does not meet complexity requirements'
                ], 422);
            }
            
            $user->password = Hash::make($request->newpass);
            $user->password_expires_at = now()->addMonths(3); // Set expiry
            $passwordChanged = true;
            $passwordExpiryDays = 90; // 3 months
        }
        
        // Update allowed payrolls
        $user->allowedprol = $request->allowedPayroll 
            ? implode(',', $request->allowedPayroll) 
            : null;
        
        $user->save();
        
        // Prepare new values for audit trail
        $newValues = [
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => $user->profile_photo,
            'allowedprol' => $user->allowedprol,
            'password_expires_at' => $user->password_expires_at,
            'approvelvl' => $user->approvelvl,
        ];
        
        // Log audit trail
        logAuditTrail(
            session('user_id') ?? Auth::id(), // Current authenticated user who updated this user
            'UPDATE',
            'users',
            $user->id,
            $oldValues,
            $newValues,
            [
                'action_type' => 'user_update',
                'password_changed' => $passwordChanged,
                'password_expiry_days' => $passwordExpiryDays,
                'profile_photo_changed' => $profilePhotoChanged,
                'old_profile_photo' => $oldProfilePhoto,
                'new_profile_photo' => $user->profile_photo,
                'allowed_payroll_count' => $request->allowedPayroll ? count($request->allowedPayroll) : 0,
                'name_changed' => $oldValues['name'] !== $newValues['name'],
                'email_changed' => $oldValues['email'] !== $newValues['email'],
                'allowedprol_changed' => $oldValues['allowedprol'] !== $newValues['allowedprol'],
            ]
        );
        
        DB::commit();
        
        Log::info('User updated successfully', [
            'user_id' => $id,
            'updated_by' => session('user_id') ?? Auth::id(),
            'changes' => [
                'name_changed' => $oldValues['name'] !== $newValues['name'],
                'email_changed' => $oldValues['email'] !== $newValues['email'],
                'password_changed' => $passwordChanged,
                'profile_photo_changed' => $profilePhotoChanged,
                'allowedprol_changed' => $oldValues['allowedprol'] !== $newValues['allowedprol'],
            ]
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully!'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('User update failed', [
            'user_id' => $id,
            'updated_by' => session('user_id') ?? Auth::id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update user.'
        ], 500);
    }
}

private function validatePasswordComplexity($password)
{
    $hasUppercase = preg_match('/[A-Z]/', $password);
    $hasLowercase = preg_match('/[a-z]/', $password);
    $hasNumber = preg_match('/[0-9]/', $password);
    $hasSymbol = preg_match('/[~!@#$%^*_\-+=`|(){}[\]:;"\'<>,.?\/]/', $password);
    
    $rulesMatched = $hasUppercase + $hasLowercase + $hasNumber + $hasSymbol;
    
    return strlen($password) >= 8 && $rulesMatched >= 3;
}

// Method to get payroll types
public function getPayrollTypes()
{
    try {
        $payrollTypes = DB::table('prolltypes')
            ->select('ID', 'pname')
            ->orderBy('pname')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'payrollTypes' => $payrollTypes
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to load payroll types', [
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to load payroll types'
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
 


    public function changepassword(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    // Validate current password if it's the user changing their own password
    if (Auth::id() == $user->id) {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'newpass' => ['required', 'string', 'min:8', 'confirmed', 
                         function ($attribute, $value, $fail) use ($user) {
                             if (Hash::check($value, $user->password)) {
                                 $fail('New password must be different from current password.');
                             }
                         }]
        ]);
    } else {
        // Admin changing another user's password (no current password required)
        $validator = Validator::make($request->all(), [
            'newpass' => ['required', 'string', 'min:8', 'confirmed']
        ]);
    }
    
    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }
    
    // Validate password complexity
    if (!$this->validatePasswordComplexity($request->newpass)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Password does not meet complexity requirements'
        ], 422);
    }
    if ($user->hasUsedPassword($request->newpass)) { 
        return response()->json([ 
            'status' => 'error', 'message' => 'You have used this password recently. Please choose a different one.'
             ], 422);
         }
    
    try {
        DB::beginTransaction();
        
        // Capture old values before update for audit trail
        $oldValues = [
            'password' => $user->password,
            'password_expires_at' => $user->password_expires_at,
            'password_changed_at' => $user->password_changed_at,
            'must_change_password' => $user->must_change_password,
            'failed_login_attempts' => $user->failed_login_attempts,
            'locked_until' => $user->locked_until,
        ];
        
        // Use the model's updatePassword method to handle all logic
        $user->updatePassword($request->newpass, 90); // 90 days expiry
        
        // Prepare new values for audit trail
        $newValues = [
            'password' => $user->password,
            'password_expires_at' => $user->password_expires_at,
            'password_changed_at' => $user->password_changed_at,
            'must_change_password' => $user->must_change_password,
            'failed_login_attempts' => $user->failed_login_attempts,
            'locked_until' => $user->locked_until,
        ];
        
        // Log audit trail
        logAuditTrail(
            session('user_id') ?? Auth::id(),
            'UPDATE_PASSWORD',
            'users',
            $user->id,
            $oldValues,
            $newValues,
            [
                'action_type' => 'password_change',
                'password_changed' => true,
                'password_expiry_days' => 90,
                'changed_by_admin' => Auth::id() != $user->id,
            ]
        );
        
        DB::commit();
        
        Log::info('Password changed successfully', [
            'user_id' => $id,
            'changed_by' => session('user_id') ?? Auth::id(),
            'password_changed_at' => $user->password_changed_at,
            'password_expires_at' => $user->password_expires_at,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully!',
            'password_expires_at' => $user->password_expires_at->format('Y-m-d H:i:s'),
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Password change failed', [
            'user_id' => $id,
            'changed_by' => session('user_id') ?? Auth::id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update password. Please try again.'
        ], 500);
    }
}

/**
 * Validate password complexity
 */

}

