<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Nssf extends Model
{
    protected $table = 'nssfbracket';
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
        'LEL',
        'UEL',
        'relief'
    ];

    protected $casts = [
        'emppercentage' => 'decimal:1'
    ];

}