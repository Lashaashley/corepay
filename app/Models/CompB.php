<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompB extends Model
{
    use HasFactory;

    protected $table = 'compbank';
    protected $primaryKey = 'ID';
    protected $fillable = ['BankCode', 'Bank','Branch','BranchCode','swiftcode', 'accno'];
}
