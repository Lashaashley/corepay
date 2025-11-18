<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payincludes extends Model
{
    use HasFactory;
    
    protected $table = 'payincludes';
    protected $primaryKey = 'ID';
    protected $fillable = ['nhifno', 'krano', 'penno', 'nssfno', 'leavecat', 'sendslips', 'bankacc'];
    
    
}