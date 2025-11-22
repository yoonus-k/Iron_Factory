<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'role',
        'shift',
        'is_active',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['roleRelation'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * العلاقات
     */
    public function roleRelation(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Accessor for role to maintain backwards compatibility
    public function getRoleAttribute()
    {
        // If role_id exists, return the relationship
        if ($this->role_id) {
            return $this->roleRelation;
        }
        
        // Otherwise return the old string value
        return $this->attributes['role'] ?? null;
    }

    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    public function shiftAssignments(): HasMany
    {
        return $this->hasMany(ShiftAssignment::class);
    }

    public function worker(): HasOne
    {
        return $this->hasOne(Worker::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class, 'created_by');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'created_by');
    }

    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * التحقق من الصلاحيات
     */
    public function hasPermission($permissionCode, $action = 'read'): bool
    {
        if (!$this->role) {
            return false;
        }

        // Admin has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Check user's exceptional permissions first
        $userPermission = $this->userPermissions()
            ->where('permission_name', $permissionCode)
            ->first();
        
        if ($userPermission) {
            return match($action) {
                'create' => $userPermission->can_create,
                'read' => $userPermission->can_read,
                'update' => $userPermission->can_update,
                'delete' => $userPermission->can_delete,
                default => false,
            };
        }

        // Check role permissions
        $rolePermission = $this->role->permissions()
            ->where('permission_code', $permissionCode)
            ->first();

        if (!$rolePermission) {
            return false;
        }

        return match($action) {
            'create' => $rolePermission->pivot->can_create ?? false,
            'read' => $rolePermission->pivot->can_read ?? false,
            'update' => $rolePermission->pivot->can_update ?? false,
            'delete' => $rolePermission->pivot->can_delete ?? false,
            'approve' => $rolePermission->pivot->can_approve ?? false,
            'export' => $rolePermission->pivot->can_export ?? false,
            default => false,
        };
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->role_code === 'ADMIN';
    }
}
