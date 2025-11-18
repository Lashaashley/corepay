<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cotu extends Model
{
    protected $table = 'cotu';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'cname',
        'code',
        'camount',
        'cstatus'
    ];

    protected $casts = [
        'camount' => 'decimal:2'
    ];

}