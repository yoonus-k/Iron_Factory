<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationLog extends Model
{
    protected $table = 'operation_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'action_en',
        'description',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_SCAN = 'scan';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabel($locale = null)
    {
        $actions = [
            'create' => ['ar' => 'إنشاء', 'en' => 'Create'],
            'update' => ['ar' => 'تعديل', 'en' => 'Update'],
            'delete' => ['ar' => 'حذف', 'en' => 'Delete'],
            'scan' => ['ar' => 'مسح ضوئي', 'en' => 'Scan'],
        ];

        $locale = $locale ?? app()->getLocale();
        return $actions[$this->action][$locale] ?? $this->action;
    }
}
