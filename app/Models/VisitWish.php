<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitWish extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'network_id',
        'user_id',
        'restaurant_id',
        'notes',
        'priority',
        'visited',
        'visited_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visited' => 'boolean',
        'visited_at' => 'datetime',
    ];

    /**
     * Relación: Red a la que pertenece
     */
    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }

    /**
     * Relación: Usuario que agregó a la wishlist
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Restaurante deseado (GLOBAL)
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Mark as visited
     */
    public function markAsVisited(): void
    {
        $this->update([
            'visited' => true,
            'visited_at' => now(),
        ]);
    }

    /**
     * Scope: Only pending (not visited)
     */
    public function scopePending($query)
    {
        return $query->where('visited', false);
    }

    /**
     * Scope: Only visited
     */
    public function scopeVisited($query)
    {
        return $query->where('visited', true);
    }

    /**
     * Scope: High priority
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }
}
