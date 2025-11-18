<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LoanShedule extends Model
{
    protected $table = 'loanshedule';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'empid',
        'loantype',
        'interest',
        'totalmonths',
        'Period',
        'precovery',
        'mpay',
        'deposit',
        'mintrest',
        'balance',
        'loanamount',
        'paidcheck',
        'statlon',
        'recintres'
    ];

    protected $casts = [
        'interest' => 'decimal:2',
        'precovery' => 'decimal:2',
        'mpay' => 'decimal:2',
        'deposit' => 'decimal:2',
        'mintrest' => 'decimal:2',
        'balance' => 'decimal:2',
        'loanamount' => 'decimal:2'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Agents::class, 'empid', 'emp_id');
    }

    public function loanType()
    {
        return $this->belongsTo(Ptype::class, 'loantype', 'code');
    }
}