<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'permission_name',
        'permission_name_en',
        'permission_code',
        'module',
        'description',
        'is_system',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقات
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')->withPivot('can_create', 'can_read', 'can_update', 'can_delete', 'can_approve', 'can_export');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الترجمة والتصفية
     */
    public function getName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->permission_name : $this->permission_name_en ?? $this->permission_name;
    }
}
