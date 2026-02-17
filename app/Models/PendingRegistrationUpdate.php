<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistrationUpdate extends Model
{
    protected $table = 'pending_registration_updates';

    protected $fillable = [
        'empid',
        'submitted_by',
        'approved_by',
        'original_data',
        'pending_data',
        'status',
        'rejection_reason',
        'submission_notes',
        'submitted_at',
        'reviewed_at'
    ];

    protected $casts = [
        'original_data' => 'array',
        'pending_data' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Agents::class, 'empid', 'emp_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'empid', 'empid');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'REJECTED');
    }
}