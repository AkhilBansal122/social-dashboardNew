<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Crypt;

class SocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'platform_user_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'status',
        'status_message',
        'last_synced_at',
        'scopes',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_synced_at'   => 'datetime',
    ];

    // ── Token encryption ──────────────────────────────────────────────────

    public function setAccessTokenAttribute(string $value): void
    {
        $this->attributes['access_token'] = Crypt::encryptString($value);
    }

    public function getAccessTokenAttribute(?string $value): string
    {
        if (! $value) return '';
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return '';
        }
    }

    public function setRefreshTokenAttribute(?string $value): void
    {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getRefreshTokenAttribute(?string $value): ?string
    {
        if (! $value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }

    // ── Relationships ─────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(SocialProfile::class);
    }

    public function instagramMedia(): HasMany
    {
        return $this->hasMany(InstagramMedia::class)->orderByDesc('posted_at');
    }

    // ── Status helpers ────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at !== null && $this->token_expires_at->isPast();
    }

    public function markExpired(string $message = 'Token expired — please reconnect'): void
    {
        $this->update(['status' => 'expired', 'status_message' => $message]);
    }

    public function markError(string $message): void
    {
        $this->update(['status' => 'error', 'status_message' => $message]);
    }

    public function markActive(): void
    {
        $this->update(['status' => 'active', 'status_message' => null]);
    }

    // ── Display helpers ───────────────────────────────────────────────────

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'active'  => 'status-connected',
            'expired' => 'status-expired',
            'error'   => 'status-error',
            default   => 'status-disconnected',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'active'  => 'Connected',
            'expired' => 'Token Expired',
            'error'   => 'Error',
            default   => 'Disconnected',
        };
    }

    public function platformLabel(): string
    {
        return match ($this->platform) {
            'instagram' => 'Instagram',
            'snapchat'  => 'Snapchat',
            default     => ucfirst($this->platform),
        };
    }
}
