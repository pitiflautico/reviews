<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Membership extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'network_id',
        'role',
        'joined_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Relación: Usuario de esta membresía
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Red de esta membresía
     */
    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }

    /**
     * Check if this membership is owner
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if this membership is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if this membership is member
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Check if can invite members
     */
    public function canInviteMembers(): bool
    {
        // Owner and admin can always invite
        if ($this->isOwner() || $this->isAdmin()) {
            return true;
        }

        // Members can only invite if network allows it
        return $this->network->allow_member_invites ?? false;
    }
}
