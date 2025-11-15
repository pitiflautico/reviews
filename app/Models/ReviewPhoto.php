<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewPhoto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'review_id',
        'photo_url',
        'caption',
        'order',
    ];

    /**
     * Relación: Review al que pertenece esta foto
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
