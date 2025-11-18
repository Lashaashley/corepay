<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Nhif extends Model
{
    protected $table = 'nhifbrack';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'lowerlimit',
        'upperlimit',
        'amount',
        'hstatus',
        'relief',
        'nhifcode'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

}