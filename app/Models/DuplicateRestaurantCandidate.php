<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuplicateRestaurantCandidate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'restaurant_a_id',
        'restaurant_b_id',
        'similarity_score',
        'detection_method',
        'status',
        'merged_into_id',
        'merged_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'similarity_score' => 'decimal:2',
        'merged_at' => 'datetime',
    ];

    /**
     * Relación: Restaurante A
     */
    public function restaurantA(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_a_id');
    }

    /**
     * Relación: Restaurante B
     */
    public function restaurantB(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_b_id');
    }

    /**
     * Relación: Restaurante al que se fusionó
     */
    public function mergedInto(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'merged_into_id');
    }

    /**
     * Check if is pending review
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if is confirmed duplicate
     */
    public function isConfirmedDuplicate(): bool
    {
        return $this->status === 'confirmed_duplicate';
    }

    /**
     * Check if is merged
     */
    public function isMerged(): bool
    {
        return $this->status === 'merged';
    }

    /**
     * Mark as confirmed duplicate
     */
    public function confirmDuplicate(): void
    {
        $this->update(['status' => 'confirmed_duplicate']);
    }

    /**
     * Mark as not duplicate
     */
    public function markAsNotDuplicate(): void
    {
        $this->update(['status' => 'not_duplicate']);
    }

    /**
     * Mark as merged
     */
    public function markAsMerged(Restaurant $mergedInto): void
    {
        $this->update([
            'status' => 'merged',
            'merged_into_id' => $mergedInto->id,
            'merged_at' => now(),
        ]);
    }

    /**
     * Scope: Only pending candidates
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: High similarity (>= 90%)
     */
    public function scopeHighSimilarity($query)
    {
        return $query->where('similarity_score', '>=', 90);
    }

    /**
     * Scope: Order by similarity desc
     */
    public function scopeBySimilarity($query)
    {
        return $query->orderBy('similarity_score', 'desc');
    }
}
