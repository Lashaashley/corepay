<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payhouse extends Model
{
    protected $table = 'payhouse';
    protected $primaryKey = 'ID';
    //public $timestamps = false;
    
    protected $fillable = [
        'WorkNo',
        'pname',
        'itemcode',
        'pcategory',
        'loanshares',
        'tamount',
        'balance',
        'month',
        'year'
    ];

    protected $casts = [
        'tamount' => 'decimal:2',
        'balance' => 'decimal:2'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Agents::class, 'WorkNo', 'emp_id');
    }
    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }
    public function scopeDistinctPeriods($query)
    {
        return $query->select('month', 'year')
            ->distinct()
            ->orderByRaw("year DESC, FIELD(month, 'December', 'November', 'October', 'September', 'August', 'July', 'June', 'May', 'April', 'March', 'February', 'January')");
    }
}