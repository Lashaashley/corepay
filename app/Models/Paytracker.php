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
        'approved_at'
    ];
    
    protected $casts = [
        'approved_at' => 'datetime'
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
}