<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paytracker extends Model
{
    protected $table = 'paytracker';
    
    protected $fillable = [
        'month',
        'year',
        'sstatus',
        'paytype',
        'creator',
        'approver',
        'approved_at',
        'total_netpay',
        'employee_count',
        'netpay_status',
        'netpay_approver',
        'netpay_submitted_at',
        'netpay_approved_at',
        'netpay_rejection_reason'
    ];
    
    protected $casts = [
        'approved_at' => 'datetime',
        'netpay_submitted_at' => 'datetime',
        'netpay_approved_at' => 'datetime',
        'total_netpay' => 'decimal:2'
    ];
    
    // Relationships
    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'creator');
    }
    
    public function approverUser()
    {
        return $this->belongsTo(User::class, 'approver');
    }
    
    public function netpayApproverUser()
    {
        return $this->belongsTo(User::class, 'netpay_approver');
    }
}