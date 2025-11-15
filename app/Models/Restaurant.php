<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_normalized',
        'slug',
        'address',
        'address_normalized',
        'city',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'cuisine_type',
        'price_range',
        'phone',
        'website',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Boot method - Auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($restaurant) {
            if (empty($restaurant->slug)) {
                $restaurant->slug = Str::slug($restaurant->name) . '-' . Str::random(8);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relación: Reseñas de este restaurante (GLOBAL - de todas las redes)
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relación: Listas de deseos que incluyen este restaurante
     */
    public function visitWishes(): HasMany
    {
        return $this->hasMany(VisitWish::class);
    }

    /**
     * Relación: Candidatos de duplicados donde este restaurante es A
     */
    public function duplicateCandidatesAsA(): HasMany
    {
        return $this->hasMany(DuplicateRestaurantCandidate::class, 'restaurant_a_id');
    }

    /**
     * Relación: Candidatos de duplicados donde este restaurante es B
     */
    public function duplicateCandidatesAsB(): HasMany
    {
        return $this->hasMany(DuplicateRestaurantCandidate::class, 'restaurant_b_id');
    }

    /**
     * Get reviews for a specific network
     */
    public function reviewsForNetwork(Network $network)
    {
        return $this->reviews()->where('network_id', $network->id);
    }

    /**
     * Get average rating (global - across all networks)
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) $this->reviews()->avg('rating') ?? 0.0;
    }

    /**
     * Get average rating for a specific network
     */
    public function averageRatingForNetwork(Network $network): float
    {
        return (float) $this->reviewsForNetwork($network)->avg('rating') ?? 0.0;
    }

    /**
     * Get total reviews count (global)
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get reviews count for a specific network
     */
    public function reviewsCountForNetwork(Network $network): int
    {
        return $this->reviewsForNetwork($network)->count();
    }

    /**
     * Check if restaurant has coordinates
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get full address as string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->postal_code,
            $this->city,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
