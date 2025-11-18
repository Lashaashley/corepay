<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paytypes extends Model
{
    protected $table = 'prolltypes';
    
    // Specify the correct primary key name
    protected $primaryKey = 'ID'; // Your DB uses uppercase ID
    
    protected $fillable = [
        'pname'
    ];
}
