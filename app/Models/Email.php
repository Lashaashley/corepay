<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table = 'email_config';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_email',
    ];

}