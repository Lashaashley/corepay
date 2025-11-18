<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BalanceSched extends Model
{
    protected $table = 'balancsched';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'empid',
        'balancecode',
        'totalmonths',
        'Pperiod',
        'rrecovery',
        'deposit',
        'balance',
        'targeloan',
        'increREDU',
        'paidcheck',
        'stat'
    ];

    protected $casts = [
        'rrecovery' => 'decimal:2',
        'deposit' => 'decimal:2',
        'balance' => 'decimal:2',
        'targeloan' => 'decimal:2'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Agents::class, 'empid', 'emp_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(Ptype::class, 'balancecode', 'code');
    }
}