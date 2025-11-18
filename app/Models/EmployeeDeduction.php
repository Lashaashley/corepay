<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    protected $table = 'employeedeductions';
    protected $primaryKey = 'ID';
   
    
    protected $fillable = [
        'Surname',
        'othername',
        'WorkNo',
        'dept',
        'PCode',
        'pcate',
        'Amount',
        'balance',
        'loanshares',
        'month',
        'year',
        'procctype',
        'varorfixed',
        'taxaornon',
        'increREDU',
        'rate',
        'prossty',
        'dateposted',
        'statdeduc',
        'quantity',
        'relief',
        'recintres',
        'parent'
    ];

    protected $casts = [
        'Amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'rate' => 'decimal:2',
        'dateposted' => 'date'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Agents::class, 'WorkNo', 'emp_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(Ptype::class, 'PCode', 'code');
    }

        public function department()
    {
        return $this->belongsTo(Depts::class, 'dept', 'ID');
    }

    /**
     * Get registration data for this employee
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'WorkNo', 'empid');
    }

    /**
     * Scope for filtering by month and year
     */
    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope for filtering by payroll types
     */
    public function scopeByPayrollTypes($query, $payrollTypes)
    {
        return $query->whereHas('registration', function($q) use ($payrollTypes) {
            $q->whereIn('payrolty', $payrollTypes);
        });
    }

    /**
     * Scope for positive amounts only
     */
    public function scopePositiveAmount($query)
    {
        return $query->where('Amount', '>', 0);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('Surname', 'like', "%{$searchTerm}%")
              ->orWhere('WorkNo', 'like', "%{$searchTerm}%")
              ->orWhere('othername', 'like', "%{$searchTerm}%");
        });
    }

}