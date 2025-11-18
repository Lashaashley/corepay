<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'allowedprol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 11
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
}