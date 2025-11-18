<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Prelief extends Model
{
    protected $table = 'prelief';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'cname',
        'Amount',
        'rperiod'
    ];

    protected $casts = [
        'Amount' => 'decimal:2'
    ];

}