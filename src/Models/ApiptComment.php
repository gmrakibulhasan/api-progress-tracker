<?php

namespace Gmrakibulhasan\ApiProgressTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApiptComment extends Model
{
    protected $connection = 'apipt';
    protected $table = 'apipt_comments';

    protected $fillable = [
        'description',
        'attachments',
        'mentions',
        'parent_id',
        'commentable_type',
        'commentable_id',
        'developer_id',
    ];

    protected $casts = [
        'attachments' => 'array',
        'mentions' => 'array',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(ApiptDeveloper::class, 'developer_id');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeParentComments($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    // Update mention seen status
    public function markMentionAsSeen($developerId)
    {
        $mentions = $this->mentions ?? [];

        foreach ($mentions as &$mention) {
            if ($mention['id'] == $developerId) {
                $mention['seen_at'] = now()->toDateTimeString();
                break;
            }
        }

        $this->update(['mentions' => $mentions]);
    }

    // Check if developer has unseen mentions
    public function hasUnseenMention($developerId)
    {
        $mentions = $this->mentions ?? [];

        foreach ($mentions as $mention) {
            if ($mention['id'] == $developerId && is_null($mention['seen_at'] ?? null)) {
                return true;
            }
        }

        return false;
    }
}
