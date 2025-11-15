<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftHandover extends Model
{
    protected $table = 'shift_handovers';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'stage_number',
        'handover_items',
        'notes',
        'notes_en',
        'handover_time',
        'supervisor_approved',
        'approved_by',
    ];

    protected $casts = [
        'handover_items' => 'array',
        'handover_time' => 'datetime',
        'supervisor_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
