<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'WorkNo',
        'month',
        'year',
        'net_amount',
        'status',
        'report_type',
        'invoiced_at',
        'paid_at',
        'paid_atmonth',
        'paid_atyear',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'net_amount' => 'decimal:2',
        'invoiced_at' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The possible status values.
     *
     * @var array<string>
     */
    const STATUSES = [
        'UNPAID' => 'UNPAID',
        'TO_BE_PAID' => 'TO BE PAID',
        'PAID' => 'PAID',
    ];

    /**
     * Get the invoice associated with this payment status.
     */
    public function etimsInvoice()
    {
        return $this->hasOne(EtimsInvoice::class, 'WorkNo', 'WorkNo')
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
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get only paid records.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUSES['PAID']);
    }

    /**
     * Scope a query to get only unpaid records.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', '!=', self::STATUSES['PAID']);
    }

    /**
     * Scope a query to get records that need to be paid.
     */
    public function scopeToBePaid($query)
    {
        return $query->where('status', self::STATUSES['TO_BE_PAID']);
    }

    /**
     * Check if the payment is paid.
     */
    public function isPaid()
    {
        return $this->status === self::STATUSES['PAID'];
    }

    /**
     * Check if the payment is not paid.
     */
    public function isNotPaid()
    {
        return $this->status === self::STATUSES['NOT_PAID'];
    }

    /**
     * Check if the payment is to be paid.
     */
    public function isToBePaid()
    {
        return $this->status === self::STATUSES['TO_BE_PAID'];
    }

    /**
     * Mark the payment as paid.
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => self::STATUSES['PAID'],
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark the payment as to be paid.
     */
    public function markAsToBePaid()
    {
        $this->update([
            'status' => self::STATUSES['TO_BE_PAID'],
            'invoiced_at' => now(),
        ]);
    }

    /**
     * Mark the payment as not paid.
     */
    public function markAsNotPaid()
    {
        $this->update([
            'status' => self::STATUSES['NOT_PAID'],
            'invoiced_at' => null,
            'paid_at' => null,
        ]);
    }

    /**
     * Get the full period label.
     */
    public function getPeriodAttribute()
    {
        return $this->month . '/' . $this->year;
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUSES['PAID'] => 'success',
            self::STATUSES['TO_BE_PAID'] => 'warning',
            default => 'danger',
        };
    }
}