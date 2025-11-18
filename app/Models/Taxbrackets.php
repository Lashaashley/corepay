<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Taxbrackets extends Model
{
    protected $table = 'taxbrackets';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'taxband',
        'minamount',
        'maxamount',
        'taxrate'
    ];

    protected $casts = [
        'minamount' => 'decimal:1',
        'maxamount' => 'decimal:1',
        'taxrate' => 'decimal:1'
    ];

}