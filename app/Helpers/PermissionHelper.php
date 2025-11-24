<?php

if (!function_exists('hasPermission')) {
    /**
     * Check if user has a specific permission
     */
    function hasPermission($permissionCode, $action = 'read')
    {
        $user = auth()->user();
        if (!$user || !$user->role) {
            return false;
        }

        // Admin has all permissions
        if ($user->isAdmin()) {
            return true;
        }

        // Check user's exceptional permissions first
        $userPermission = $user->userPermissions()
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
        $rolePermission = $user->role->permissions()
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
}

if (!function_exists('canCreate')) {
    function canCreate($permissionCode)
    {
        return hasPermission($permissionCode, 'create');
    }
}

if (!function_exists('canRead')) {
    function canRead($permissionCode)
    {
        return hasPermission($permissionCode, 'read');
    }
}

if (!function_exists('canUpdate')) {
    function canUpdate($permissionCode)
    {
        return hasPermission($permissionCode, 'update');
    }
}

if (!function_exists('canDelete')) {
    function canDelete($permissionCode)
    {
        return hasPermission($permissionCode, 'delete');
    }
}

if (!function_exists('canApprove')) {
    function canApprove($permissionCode)
    {
        return hasPermission($permissionCode, 'approve');
    }
}

if (!function_exists('canExport')) {
    function canExport($permissionCode)
    {
        return hasPermission($permissionCode, 'export');
    }
}

if (!function_exists('hasRole')) {
    function hasRole($roleCode)
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Check if user has role relationship (new system)
        if ($user->role_id && $user->role) {
            return $user->role->role_code === $roleCode;
        }
        
        // Fallback to old system (role as string)
        return false;
    }
}

if (!function_exists('hasAnyRole')) {
    function hasAnyRole(...$roleCodes)
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Check if user has role relationship (new system)
        if ($user->role_id && $user->role) {
            return in_array($user->role->role_code, $roleCodes);
        }
        
        // Fallback to old system (role as string)
        return false;
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Check if user has role relationship (new system)
        if ($user->role_id && $user->role) {
            return $user->role->role_code === 'ADMIN';
        }
        
        // Fallback: check if username is admin (temporary solution)
        return $user->username === 'admin';
    }
}

if (!function_exists('getRoleLevel')) {
    function getRoleLevel()
    {
        $user = auth()->user();
        return $user && $user->roleRelation ? $user->roleRelation->level : 0;
    }
}

if (!function_exists('hasMinLevel')) {
    /**
     * Check if user has minimum level
     */
    function hasMinLevel($minLevel)
    {
        return getRoleLevel() >= $minLevel;
    }
}

if (!function_exists('canManageUser')) {
    /**
     * Check if current user can manage another user (based on level)
     */
    function canManageUser($userId)
    {
        $targetUser = \App\Models\User::find($userId);
        if (!$targetUser || !$targetUser->roleRelation) {
            return false;
        }
        
        // Admin can manage everyone
        if (isAdmin()) {
            return true;
        }
        
        $currentLevel = getRoleLevel();
        return $currentLevel > $targetUser->roleRelation->level;
    }
}

if (!function_exists('canAssignRole')) {
    /**
     * Check if current user can assign a specific role
     */
    function canAssignRole($roleId)
    {
        $role = \App\Models\Role::find($roleId);
        if (!$role) {
            return false;
        }
        
        // Admin can assign any role
        if (isAdmin()) {
            return true;
        }
        
        // User can only assign roles lower than their level
        return getRoleLevel() > $role->level;
    }
}
