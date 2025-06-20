<?php

namespace Gmrakibulhasan\ApiProgressTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ApiptTask extends Model
{
    protected $table = 'apipt_tasks';

    protected $fillable = [
        'title',
        'description',
        'assigned_by',
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
            'apipt_developer_task',
            'task_id',
            'developer_id'
        )->withPivot(['viewed_at'])->withTimestamps();
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
        static::updating(function ($task) {
            if ($task->isDirty('status') && $task->status === 'complete') {
                $task->completion_time = now();
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

    public function scopeAssignedBy($query, $developerId)
    {
        return $query->where('assigned_by', $developerId);
    }
}
