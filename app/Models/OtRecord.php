<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtRecord extends Model
{
    protected $table = 'otrecords';
    
    protected $primaryKey = 'ID';
    
    public $timestamps = false;
    
    protected $fillable = [
        'WorkNo',
        'Pcode',
        'tamount',
        'cname',
        'quantity',
        'odate'
    ];

    protected $casts = [
        'tamount' => 'decimal:2',
        'quantity' => 'decimal:2',
        'odate' => 'date'
    ];

    /**
     * Get the employee for this OT record
     */
    public function employee()
    {
        return $this->belongsTo(Agents::class, 'WorkNo', 'emp_id');
    }

    /**
     * Scope for filtering by month and year
     */
    public function scopeForPeriod($query, $month, $year)
    {
        return $query->whereMonth('odate', $month)
                     ->whereYear('odate', $year);
    }
}