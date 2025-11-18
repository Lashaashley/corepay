<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Whgroups extends Model
{
    protected $table = 'whgroups';
    
    public $timestamps = false;
    
    protected $primaryKey = 'ID';
    
    protected $fillable = [
        'code',
        'cname'
    ];
}