<?php

namespace Gmrakibulhasan\ApiProgressTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ApiptDeveloper extends Authenticatable
{
    use Notifiable;

    protected $connection = 'apipt';
    protected $table = 'apipt_developers';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function apiProgresses(): BelongsToMany
    {
        return $this->belongsToMany(
            ApiptApiProgress::class,
            'apipt_developer_api_progress',
            'developer_id',
            'api_progress_id'
        )->withPivot(['assigned_by', 'viewed_at'])->withTimestamps();
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(
            ApiptTask::class,
            'apipt_developer_task',
            'developer_id',
            'task_id'
        )->withPivot(['viewed_at'])->withTimestamps();
    }

    public function assignedApiProgresses(): HasMany
    {
        return $this->hasMany(ApiptApiProgress::class, 'assigned_by');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(ApiptTask::class, 'assigned_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ApiptComment::class, 'developer_id');
    }
}
