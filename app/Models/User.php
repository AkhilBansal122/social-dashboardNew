<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function getSocialAccount(string $platform): ?SocialAccount
    {
        return $this->socialAccounts()->where('platform', $platform)->first();
    }

    public function hasPlatformConnected(string $platform): bool
    {
        return $this->socialAccounts()
            ->where('platform', $platform)
            ->whereIn('status', ['active', 'expired', 'error'])
            ->exists();
    }
}
