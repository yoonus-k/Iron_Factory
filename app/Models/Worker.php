<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use SoftDeletes, Syncable;

    protected $fillable = [
        'worker_code',
        'name',
        'national_id',
        'phone',
        'email',
        'position',
        'allowed_stages',
        'hourly_rate',
        'shift_preference',
        'is_active',
        'hire_date',
        'termination_date',
        'notes',
        'emergency_contact',
        'emergency_phone',
        'user_id',
        // Sync fields
        'is_synced',
        'sync_status',
        'synced_at',
        'local_id',
        'device_id',
    ];

    protected $casts = [
        'allowed_stages' => 'array',
        'is_active' => 'boolean',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'hourly_rate' => 'decimal:2',
    ];

    const POSITION_WORKER = 'worker';
    const POSITION_SUPERVISOR = 'supervisor';
    const POSITION_TECHNICIAN = 'technician';
    const POSITION_QUALITY_INSPECTOR = 'quality_inspector';

    const SHIFT_MORNING = 'morning';
    const SHIFT_EVENING = 'evening';
    const SHIFT_NIGHT = 'night';
    const SHIFT_ANY = 'any';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shiftAssignments(): HasMany
    {
        // shift_assignments table uses user_id, not worker_id
        return $this->hasMany(ShiftAssignment::class, 'user_id', 'user_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'worker_permissions');
    }

    public function getPositionNameAttribute(): string
    {
        return match($this->position) {
            self::POSITION_WORKER => 'عامل',
            self::POSITION_SUPERVISOR => 'مشرف',
            self::POSITION_TECHNICIAN => 'فني',
            self::POSITION_QUALITY_INSPECTOR => 'مراقب جودة',
            default => $this->position,
        };
    }

    public function getShiftPreferenceNameAttribute(): string
    {
        return match($this->shift_preference) {
            self::SHIFT_MORNING => 'الفترة الصباحية',
            self::SHIFT_EVENING => 'الفترة المسائية',
            self::SHIFT_NIGHT => 'الفترة الليلية',
            self::SHIFT_ANY => 'أي وردية',
            default => $this->shift_preference,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeByStage($query, $stageNumber)
    {
        return $query->whereJsonContains('allowed_stages', $stageNumber);
    }

    public function canWorkInStage($stageNumber): bool
    {
        if (empty($this->allowed_stages)) {
            return true; // يمكن العمل في أي مرحلة إذا لم يتم تحديد قيود
        }

        return in_array($stageNumber, $this->allowed_stages);
    }

    /**
     * الحصول على الصلاحيات الافتراضية حسب الوظيفة
     */
    public static function getDefaultPermissionsByRole($roleId)
    {
        $role = Role::find($roleId);

        if (!$role) {
            return [];
        }

        // الحصول على الصلاحيات المرتبطة بالدور
        return $role->permissions()->pluck('permissions.id')->toArray();
    }

    /**
     * تعيين الصلاحيات تلقائياً للعامل حسب دوره
     */
    public function assignDefaultPermissions()
    {
        if (!$this->user || !$this->user->role) {
            return;
        }

        $permissionIds = self::getDefaultPermissionsByRole($this->user->role_id);

        if (!empty($permissionIds)) {
            $this->permissions()->sync($permissionIds);
        }
    }
}
