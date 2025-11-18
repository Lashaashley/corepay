<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpPensionRate extends Model
{
    protected $table = 'emppensionrates';
    
    protected $primaryKey = 'ID';
    
    public $timestamps = false;
    
    protected $fillable = [
        'WorkNo',
        'epmpenperce',
        'emplopenperce'
    ];

    protected $casts = [
        'epmpenperce' => 'decimal:2',
        'emplopenperce' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Agents::class, 'WorkNo', 'emp_id');
    }
}