<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pension extends Model
{
    protected $table = 'pensionbracket';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'cname',
        'code',
        'dualdeduc',
        'emppercentage',
        'emplopercentage',
        'maxcont',
        'hstatus',
        'relief'
    ];

    protected $casts = [
        'emppercentage' => 'decimal:1'
    ];

}