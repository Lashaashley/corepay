<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contact';
    protected $primaryKey = 'ID';
    
    protected $fillable = [
        'empid',
        'PhysicalAddress'
    ];

    public function employee()
    {
        return $this->belongsTo(Agents::class, 'empid', 'emp_id');
    }
}