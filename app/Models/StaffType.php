<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffType extends Model
{
    protected $table = 'stafftypes';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'Desig',
        'Description'
    ];

    /**
     * Get agents with this staff type
     */
    public function agents()
    {
        return $this->hasMany(Agents::class, 'desigid', 'ID');
    }
}