<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        // Business rule: staff who handled existing orders cannot be deleted.
        static::deleting(function (User $user) {
            if ($user->orders()->exists()) {
                throw new \DomainException('This staff member has handled orders and cannot be deleted.');
            }
        });
    }

    /** Orders handled by this staff member. */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
