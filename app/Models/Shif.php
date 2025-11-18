<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Shif extends Model
{
    protected $table = 'shifbracket';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'cname',
        'code',
        'percentage',
        'minimumcont',
        'hstatus',
        'relief'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'minimumcont' => 'decimal:2'
    ];

}