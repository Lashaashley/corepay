<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pmessage extends Model
{
    use HasFactory;
    
    protected $table = 'pmessage';
    protected $primaryKey = 'ID';
    protected $fillable = ['message', 'mmonth', 'yyear'];
    
   
}