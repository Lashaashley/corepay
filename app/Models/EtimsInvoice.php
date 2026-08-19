<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtimsInvoice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'etims_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'PIN',
        'Etimsinv',
        'TransDateTime',
        'WorkNo',
        'SystemInvoiceNo',
        'month',
        'year',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'TransDateTime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the payment status for this invoice.
     */
    public function paymentStatus()
    {
        return $this->hasOne(PaymentStatus::class, 'WorkNo', 'WorkNo')
            ->where('month', $this->month)
            ->where('year', $this->year);
    }

    /**
     * Scope a query to filter by employee work number.
     */
    public function scopeForEmployee($query, $workNo)
    {
        return $query->where('WorkNo', $workNo);
    }

    /**
     * Scope a query to filter by period (month/year).
     */
    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope a query to filter by PIN.
     */
    public function scopeByPin($query, $pin)
    {
        return $query->where('PIN', $pin);
    }

    /**
     * Get the full period label.
     */
    public function getPeriodAttribute()
    {
        return $this->month . '/' . $this->year;
    }
}