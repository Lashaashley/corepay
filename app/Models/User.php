<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
    'name',
    'email',
    'password',
    'profile_photo',
    'allowedprol',
    'approvelvl',
    'password_changed_at',
    'password_expires_at',
    'must_change_password',
    'failed_login_attempts',
    'locked_until',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'password_changed_at' => 'datetime',
    'password_expires_at' => 'datetime',
    'must_change_password' => 'boolean',
    'locked_until' => 'datetime',
];

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        // Option 1: Check by email
        return strtolower($this->email) === 'saadmin@example.com';
        
        // Option 2: Check by ID
        // return $this->id === 1;
        
        // Option 3: Check by a role field (if you have one)
        // return $this->role === 'super_admin';
        
        // Option 4: Check by multiple emails
        // return in_array(strtolower($this->email), ['saadmin@example.com', 'admin@example.com']);
    }

    /**
     * Get user's allowed payroll IDs as array
     */
    public function getAllowedPayrollIds(): array
    {
        if (empty($this->allowedprol)) {
            return [];
        }
        
        return array_map('intval', explode(',', $this->allowedprol));
    }

    /**
     * Get user's allowed payroll types with names
     */
    public function getAllowedPayrollTypes()
    {
        $ids = $this->getAllowedPayrollIds();
        
        if (empty($ids)) {
            return collect();
        }
        
        return \App\Models\Paytypes::whereIn('ID', $ids)->get();
    }

    

    /**
     * Check if user has access to a specific payroll type
     */
    public function hasPayrollAccess(int $payrollId): bool
    {
        return in_array($payrollId, $this->getAllowedPayrollIds());
    }

    public function passwordHistories()
{
    return $this->hasMany(PasswordHistory::class);
}

/**
 * Check if password has been used before
 */
public function hasUsedPassword(string $newPassword, int $historyCount = 5): bool
{
    $recentPasswords = $this->passwordHistories()
        ->latest('created_at')
        ->take($historyCount)
        ->get();
    
    foreach ($recentPasswords as $history) {
        if (Hash::check($newPassword, $history->password)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Save password to history
 */
public function savePasswordHistory(string $hashedPassword): void
{
    $this->passwordHistories()->create([
        'password' => $hashedPassword,
        'created_at' => now(),
    ]);
    
    // Keep only last 5 passwords (configurable)
    $keepCount = config('auth.password_history_count', 5);
    $oldPasswords = $this->passwordHistories()
        ->oldest('created_at')
        ->skip($keepCount)
        ->take(1000)
        ->get();
    
    foreach ($oldPasswords as $old) {
        $old->delete();
    }
}

/**
 * Check if password has expired
 */
public function isPasswordExpired(): bool
{
    if (!$this->password_expires_at) {
        return false;
    }
    
    return now()->greaterThan($this->password_expires_at);
}

/**
 * Check if account is locked
 */
public function isLocked(): bool
{
    if (!$this->locked_until) {
        return false;
    }
    
    return now()->lessThan($this->locked_until);
}

/**
 * Update password with all tracking
 */
public function updatePassword(string $newPassword, int $expiryDays = 90): void
{
    $hashedPassword = Hash::make($newPassword);
    
    // Save to history
    $this->savePasswordHistory($hashedPassword);
    
    // Update user
    $this->password = $hashedPassword;
    $this->password_changed_at = now();
    $this->password_expires_at = now()->addDays($expiryDays);
    $this->must_change_password = false;
    $this->failed_login_attempts = 0;
    $this->locked_until = null;
    $this->save();
}
// Add this to your User model
public function moduleAssignments()
{
    return $this->hasMany(ModuleAsd::class, 'WorkNo', 'id');
}
}