<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'display_name_en',
        'group_name',
        'group_name_en',
        'description',
        'description_en',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقات
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الترجمة والتصفية
     */
    public function getDisplayName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale === 'en' && !empty($this->display_name_en)) {
            return $this->display_name_en;
        }

        return $this->display_name;
    }

    /**
     * الحصول على اسم المجموعة المترجم
     */
    public function getGroupName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale === 'en' && !empty($this->group_name_en)) {
            return $this->group_name_en;
        }

        return $this->group_name;
    }

    /**
     * الحصول على الوصف المترجم
     */
    public function getDescription($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }

        return $this->description;
    }

    /**
     * Accessor لاسم العرض المترجم تلقائياً
     */
    public function getTranslatedDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    /**
     * Accessor لاسم المجموعة المترجم تلقائياً
     */
    public function getTranslatedGroupNameAttribute()
    {
        return $this->getGroupName();
    }
}
