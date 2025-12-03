<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ptype extends Model
{
    protected $table = 'ptypes';
    
    protected $primaryKey = 'ID'; // Adjust if different
    
    protected $fillable = [
        'code',
        'cname',
        'procctype',
        'varorfixed',
        'taxaornon',
        'category',
        'increREDU',
        'rate',
        'prossty',
        'relief',
        'recintres',
        'formularinpu',
        'cumcas',
        'intrestcode',
        'codename',
        'issaccorel',
        'sposter',
        'priority'
    ];

    /**
     * Check if a payroll item with the given code or name exists
     */
    public static function isDuplicate($code, $description, $excludeId = null)
    {
        $query = self::where('code', $code)
                    ->orWhere('cname', $description);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Get payroll item by code
     */
    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by process type
     */
    public function scopeByProcessType($query, $type)
    {
        return $query->where('procctype', $type);
    }
     public static function findByName($name)
    {
        return self::where('cname', $name)->first();
    }
}