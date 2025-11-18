<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withholding extends Model
{
    protected $table = 'withholding';
    
    public $timestamps = false;

    protected $primaryKey = 'ID';
    
    protected $fillable = [
        'cname',
        'code',
        'wpercentage'
    ];

    protected $casts = [
        'wpercentage' => 'decimal:2'
    ];
}