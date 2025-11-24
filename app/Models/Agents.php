<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    protected $table = 'tblemployees';
    protected $primaryKey = 'emp_id';
    public $incrementing = false;
    protected $keyType = 'string';
    //public $timestamps = false; // Add if you don't have created_at/updated_at
    
    protected $fillable = [
        'emp_id', 
        'FirstName',
        'LastName', 
        'EmailId',
        'Password',
        'Gender',
        'Dob',
        'Department',
        'Phonenumber',
        'Status',
        'RegDate',
        'role',
        'location',
        'dateemp',
        'brid',
        'desigid',
        'stafftype',
    ];

    /**
     * Get the department for the agent
     */
    public function department()
    {
        return $this->belongsTo(Depts::class, 'Department', 'ID');
    }

    /*
     
    public function designation()
    {
        return $this->belongsTo(StaffType::class, 'desigid', 'ID');
    }*/

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return $this->FirstName . ' ' . $this->LastName;
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        if (!empty($this->location)) {
            return asset('uploads/' . $this->location);
        }
        return asset('uploads/NO-IMAGE-AVAILABLE.jpg');
    }

    /**
     * Scope to exclude specific employee
     */
    public function scopeExcludeEmployee($query, $empId)
    {
        return $query->where('emp_id', '!=', '1');
    }

    /**
     * Scope for active agents
     */
    public function scopeActive($query)
    {
        return $query->where('Status', 'ACTIVE');
    }
    public function registration()
    {
        return $this->hasMany(Registration::class, 'empid', 'emp_id');
    }
     public function contact()
    {
        return $this->hasOne(Contact::class, 'empid', 'emp_id');
    }
}