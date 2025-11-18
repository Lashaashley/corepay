<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnionGroup extends Model
{
    protected $table = 'uniongroups';
    
    public $timestamps = false;
    
    protected $primaryKey = 'ID';
    
    protected $fillable = [
        'code',
        'cname'
    ];
}