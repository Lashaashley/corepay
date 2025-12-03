<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $table = 'audittrail';
    
    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'context_data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];
}