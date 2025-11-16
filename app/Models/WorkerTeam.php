<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerTeam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_code',
        'name',
        'description',
        'worker_ids',
        'workers_count',
        'is_active',
    ];

    protected $casts = [
        'worker_ids' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get workers in this team
     */
    public function workers()
    {
        if (!$this->worker_ids) {
            return collect();
        }
        return User::whereIn('id', $this->worker_ids)->get();
    }

    /**
     * Get status name in Arabic
     */
    public function getStatusNameAttribute(): string
    {
        return $this->is_active ? 'نشطة' : 'غير نشطة';
    }

    /**
     * Scope: Active teams
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
