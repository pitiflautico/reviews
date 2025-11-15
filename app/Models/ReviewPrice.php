<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewPrice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'review_id',
        'amount',
        'currency',
        'ticket_photo_url',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relación: Review al que pertenece este precio
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        $symbols = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;

        return number_format($this->amount, 2, ',', '.') . ' ' . $symbol;
    }
}
