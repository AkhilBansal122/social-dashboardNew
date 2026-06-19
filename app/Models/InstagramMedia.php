<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstagramMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_account_id',
        'instagram_media_id',
        'media_type',
        'media_url',
        'thumbnail_url',
        'permalink',
        'caption',
        'like_count',
        'comments_count',
        'reach',
        'impressions',
        'posted_at',
    ];

    protected $casts = [
        'posted_at'      => 'datetime',
        'like_count'     => 'integer',
        'comments_count' => 'integer',
        'reach'          => 'integer',
        'impressions'    => 'integer',
    ];

    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail_url
            ?? ($this->media_type !== 'VIDEO' ? $this->media_url : null);
    }

    public function getCaptionSnippet(int $length = 120): string
    {
        if (! $this->caption) return '';
        return mb_strlen($this->caption) > $length
            ? mb_substr($this->caption, 0, $length) . '…'
            : $this->caption;
    }

    public function hasInsights(): bool
    {
        return ($this->reach > 0) || ($this->impressions > 0);
    }

    public function mediaTypeIcon(): string
    {
        return match ($this->media_type) {
            'VIDEO'          => '🎥',
            'CAROUSEL_ALBUM' => '🖼️',
            default          => '📷',
        };
    }
}
