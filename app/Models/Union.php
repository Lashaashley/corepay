<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Union extends Model
{
    protected $table = 'uninde';
    
    public $timestamps = false;
    
    protected $fillable = [
        'cname',
        'code',
        'percentage',
        'maxcont',
        'cstatus'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'maxcont' => 'decimal:2'
    ];
}