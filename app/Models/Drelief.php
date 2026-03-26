<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Drelief extends Model
{
    protected $table = 'defrelief';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'cname',
        'Maxcont',
        'percer'
    ];

    protected $casts = [
        'Maxcont' => 'decimal:2',
         'percer' => 'decimal:2'
    ];

}