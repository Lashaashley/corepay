<?php
namespace App\Models;

use App\Models\Agents;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $table = 'registration';
    protected $primaryKey = 'ID';
   

    protected $fillable = [
        'ID', 'IDNO', 'nhif', 'passport', 'pension', 'kra', 'nssf',
        'Bank', 'BankCode', 'Branch', 'BranchCode', 'swiftcode', 'AccountNo',
        'empid', 'tscno', 'tscfile', 'dlno', 'dlfile', 'expry_date', 'idfile',
        'nhiffile', 'passfile', 'penfile', 'krafile', 'nssffile',
        'nhif_shif', 'nssfp', 'contractor', 'paymode', 'unionized', 'payrolty',
        'unionno', 'penyes', 'nssfopt', 'fddate', 'foodln', 'tscdate',
        'contexdate'
    ];

    public function employee()
    {
        return $this->belongsTo(Agents::class, 'empid', 'emp_id');
    }
    public function employeeDeductions()
{
    return $this->hasMany(EmployeeDeduction::class, 'WorkNo', 'empid');
}
}
