<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación: Redes a las que pertenece el usuario
     */
    public function networks(): BelongsToMany
    {
        return $this->belongsToMany(Network::class, 'memberships')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Relación: Membresías del usuario
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Relación: Reseñas del usuario
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relación: Comentarios del usuario
     */
    public function reviewComments(): HasMany
    {
        return $this->hasMany(ReviewComment::class);
    }

    /**
     * Relación: Lista de deseos del usuario
     */
    public function visitWishes(): HasMany
    {
        return $this->hasMany(VisitWish::class);
    }

    /**
     * Relación: Invitaciones enviadas por el usuario
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'sender_id');
    }

    /**
     * Relación: Invitaciones recibidas por el usuario
     */
    public function receivedInvitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'user_id');
    }
}
