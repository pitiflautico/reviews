<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Network extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'allow_member_invites',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'allow_member_invites' => 'boolean',
    ];

    /**
     * Boot method - Auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($network) {
            if (empty($network->slug)) {
                $network->slug = Str::slug($network->name);
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
     * Relación: Los usuarios que pertenecen a esta red
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memberships')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Relación: Membresías de esta red
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Relación: Reseñas de esta red
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relación: Lista de deseos de esta red
     */
    public function visitWishes(): HasMany
    {
        return $this->hasMany(VisitWish::class);
    }

    /**
     * Relación: Invitaciones de esta red
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get the owner of the network
     */
    public function owner()
    {
        return $this->members()->wherePivot('role', 'owner')->first();
    }

    /**
     * Check if a user is a member of this network
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Get member role in this network
     */
    public function getMemberRole(User $user): ?string
    {
        $membership = $this->members()
            ->where('users.id', $user->id)
            ->first();

        return $membership?->pivot->role;
    }
}
