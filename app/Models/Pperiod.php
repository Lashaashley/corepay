<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pperiod extends Model
{
    protected $table = 'pperiods';

     protected $fillable = [
        'mmonth', 'yyear','sstatus'
    ];

    public static function getActivePeriod()
    {
        return self::where('sstatus', 'Active')->first();
    }

    /**
     * Scope for active periods
     */
    public function scopeActive($query)
    {
        return $query->where('sstatus', 'Active');
    }
}
