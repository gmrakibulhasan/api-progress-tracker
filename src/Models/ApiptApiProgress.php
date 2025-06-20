<?php

namespace Gmrakibulhasan\ApiProgressTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ApiptApiProgress extends Model
{
    protected $table = 'apipt_api_progress';

    protected $fillable = [
        'method',
        'endpoint',
        'group_name',
        'description',
        'priority',
        'estimated_completion_time',
        'completion_time',
        'status',
    ];

    protected $casts = [
        'estimated_completion_time' => 'datetime',
        'completion_time' => 'datetime',
    ];

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(
            ApiptDeveloper::class,
            'apipt_developer_api_progress',
            'api_progress_id',
            'developer_id'
        )->withPivot(['assigned_by', 'viewed_at'])->withTimestamps();
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(ApiptDeveloper::class, 'assigned_by');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(ApiptComment::class, 'commentable');
    }

    // Automatically update completion_time when status changes to complete
    protected static function booted()
    {
        static::updating(function ($apiProgress) {
            if ($apiProgress->isDirty('status') && $apiProgress->status === 'complete') {
                $apiProgress->completion_time = now();
            }
        });
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group_name', $group);
    }
}
