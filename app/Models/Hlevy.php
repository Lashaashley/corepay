<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hlevy extends Model
{
    protected $table = 'hlevy';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'cname',
        'code',
        'percentage',
        'hstatus',
        'relief'
    ];

    protected $casts = [
        'percentage' => 'decimal:1'
    ];

}