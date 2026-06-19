<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_account_id',
        'platform_user_id',
        'username',
        'display_name',
        'account_type',
        'profile_picture_url',
        'meta',
    ];

    protected $casts = ['meta' => 'array'];

    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function getAvatarUrl(): string
    {
        if ($this->profile_picture_url) {
            return $this->profile_picture_url;
        }
        $name = urlencode($this->display_name ?? $this->username ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=fff&size=128";
    }

    public function getDisplayHandle(): string
    {
        if ($this->username) return '@' . $this->username;
        return $this->display_name ?? 'Unknown';
    }
}
