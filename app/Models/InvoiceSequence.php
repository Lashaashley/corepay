<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceSequence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'next_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'next_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the next number for a sequence and increment it atomically.
     *
     * @param string $name
     * @return int
     */
    public static function getNextNumber(string $name): int
    {
        return DB::transaction(function () use ($name) {
            $sequence = self::where('name', $name)->lockForUpdate()->first();
            
            if (!$sequence) {
                $sequence = self::create([
                    'name' => $name,
                    'next_number' => 1,
                ]);
            }
            
            $currentNumber = $sequence->next_number;
            $sequence->increment('next_number');
            
            return $currentNumber;
        });
    }

    /**
     * Get the next number and format it with padding.
     *
     * @param string $name
     * @param int $padding
     * @return string
     */
    public static function getNextFormattedNumber(string $name, int $padding = 6): string
    {
        $number = self::getNextNumber($name);
        return str_pad($number, $padding, '0', STR_PAD_LEFT);
    }

    /**
     * Reset a sequence to a specific number.
     *
     * @param string $name
     * @param int $number
     * @return bool
     */
    public static function resetSequence(string $name, int $number = 1): bool
    {
        return self::where('name', $name)->update(['next_number' => $number]) > 0;
    }

    /**
     * Get the current number without incrementing.
     *
     * @param string $name
     * @return int|null
     */
    public static function getCurrentNumber(string $name): ?int
    {
        $sequence = self::where('name', $name)->first();
        return $sequence ? $sequence->next_number : null;
    }

    /**
     * Check if a sequence exists.
     *
     * @param string $name
     * @return bool
     */
    public static function sequenceExists(string $name): bool
    {
        return self::where('name', $name)->exists();
    }
}