<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depts extends Model
{
    use HasFactory;

    protected $table = 'tbldepartments';
    protected $primaryKey = 'ID';
    protected $fillable = ['DepartmentName', 'brid'];


    public function agents()
    {
        return $this->hasMany(Agents::class, 'Department', 'ID');
    }
}
