<?php

// ============================================================================
// نظام الصلاحيات الهرمي مع الشروط - Hierarchical Permissions with Conditions
// ============================================================================
// الفكرة: تنظيم الصلاحيات في مجموعات هرمية مع شروط
// مثال: المستودع (رئيسي) -> المواد الخام (فرعي) يحتاج لـ MENU_WAREHOUSE
// ============================================================================

/**
 * تعريفات المجموعات والصلاحيات الهرمية مع الشروط
 * يمكن للمستخدم إضافة شروط جديدة هنا بسهولة
 */
$PERMISSION_HIERARCHY = [
    // المستودع - Warehouse
    'MENU_WAREHOUSE' => [
        'label_ar' => 'المستودع',
        'label_en' => 'Warehouse',
        'children' => [
            'MENU_WAREHOUSE_MATERIALS' => [
                'label_ar' => 'المواد الخام',
                'label_en' => 'Raw Materials',
                'requires' => ['MENU_WAREHOUSE'] // الشرط: يحتاج MENU_WAREHOUSE
            ],
            'MENU_WAREHOUSE_STORES' => [
                'label_ar' => 'المخازن',
                'label_en' => 'Stores',
                'requires' => ['MENU_WAREHOUSE']
            ],
            'MENU_WAREHOUSE_DELIVERY_NOTES' => [
                'label_ar' => 'مذكرات التسليم',
                'label_en' => 'Delivery Notes',
                'requires' => ['MENU_WAREHOUSE']
            ],
            'MENU_WAREHOUSE_PURCHASE_INVOICES' => [
                'label_ar' => 'فواتير الشراء',
                'label_en' => 'Purchase Invoices',
                'requires' => ['MENU_WAREHOUSE']
            ],
            'MENU_WAREHOUSE_SUPPLIERS' => [
                'label_ar' => 'الموردين',
                'label_en' => 'Suppliers',
                'requires' => ['MENU_WAREHOUSE']
            ],
            'MENU_WAREHOUSE_SETTINGS' => [
                'label_ar' => 'إعدادات المستودع',
                'label_en' => 'Warehouse Settings',
                'requires' => ['MENU_WAREHOUSE']
            ],
            'MENU_WAREHOUSE_REPORTS' => [
                'label_ar' => 'تقارير المستودع',
                'label_en' => 'Warehouse Reports',
                'requires' => ['MENU_WAREHOUSE']
            ],
        ]
    ],

    // مراحل الإنتاج - Production Stages
    'MENU_STAGE1_STANDS' => [
        'label_ar' => 'المرحلة الأولى - الاستاندات',
        'label_en' => 'Stage 1 - Stands',
        'children' => [
            'STAGE1_STANDS' => [
                'label_ar' => 'إنشاء استاندات',
                'label_en' => 'Create Stands',
                'requires' => ['MENU_STAGE1_STANDS']
            ]
        ]
    ],

    'MENU_STAGE2_PROCESSING' => [
        'label_ar' => 'المرحلة الثانية - المعالجة',
        'label_en' => 'Stage 2 - Processing',
        'children' => [
            'STAGE2_PROCESSING' => [
                'label_ar' => 'إنشاء معالجة',
                'label_en' => 'Create Processing',
                'requires' => ['MENU_STAGE2_PROCESSING']
            ]
        ]
    ],

    'MENU_STAGE3_COILS' => [
        'label_ar' => 'المرحلة الثالثة - اللفائف',
        'label_en' => 'Stage 3 - Coils',
        'children' => [
            'STAGE3_COILS' => [
                'label_ar' => 'إنشاء لفافة',
                'label_en' => 'Create Coil',
                'requires' => ['MENU_STAGE3_COILS']
            ]
        ]
    ],

    'MENU_STAGE4_PACKAGING' => [
        'label_ar' => 'المرحلة الرابعة - التعبئة',
        'label_en' => 'Stage 4 - Packaging',
        'children' => [
            'STAGE4_PACKAGING' => [
                'label_ar' => 'إنشاء تعبئة',
                'label_en' => 'Create Packaging',
                'requires' => ['MENU_STAGE4_PACKAGING']
            ]
        ]
    ],

    // الإدارة - Management
    'MENU_MANAGEMENT' => [
        'label_ar' => 'الإدارة',
        'label_en' => 'Management',
        'children' => [
            'MENU_MANAGE_USERS' => [
                'label_ar' => 'إدارة المستخدمين',
                'label_en' => 'Manage Users',
                'requires' => ['MENU_MANAGEMENT']
            ],
            'MENU_MANAGE_ROLES' => [
                'label_ar' => 'إدارة الأدوار',
                'label_en' => 'Manage Roles',
                'requires' => ['MENU_MANAGEMENT', 'isAdmin'] // شرط خاص: يجب أن يكون admin
            ],
            'MENU_MANAGE_PERMISSIONS' => [
                'label_ar' => 'إدارة الصلاحيات',
                'label_en' => 'Manage Permissions',
                'requires' => ['MENU_MANAGEMENT', 'isAdmin']
            ]
        ]
    ],
];

if (!function_exists('hasPermission')) {
    /**
     * التحقق من صلاحية معينة مع الشروط الهرمية
     *
     * @param string $permissionCode رمز الصلاحية
     * @return bool
     *
     * الطريقة:
     * 1. التحقق من أن لديه صلاحية هذا الـ code
     * 2. التحقق من الشروط الهرمية (requires)
     */
    function hasPermission($permissionCode)
    {
        global $PERMISSION_HIERARCHY;

        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Admin has all permissions
        if ($user->isAdmin()) {
            return true;
        }

        // Step 1: Check if user's role has this permission
        if (!$user->hasPermission($permissionCode)) {
            return false;
        }

        // Step 2: Check hierarchical conditions/requirements
        return checkConditions($permissionCode, $PERMISSION_HIERARCHY);
    }
}

/**
 * التحقق من الشروط الهرمية للصلاحية
 *
 * @param string $permissionCode رمز الصلاحية
 * @param array $hierarchy البنية الهرمية
 * @return bool
 */
function checkConditions($permissionCode, $hierarchy)
{
    $user = auth()->user();

    // البحث عن الشروط (requires) المرتبطة بهذه الصلاحية
    $requirements = findRequirements($permissionCode, $hierarchy);

    if (empty($requirements)) {
        return true; // لا توجد شروط = الصلاحية متاحة مباشرة
    }

    // التحقق من جميع الشروط
    foreach ($requirements as $requirement) {
        if ($requirement === 'isAdmin') {
            // شرط خاص: يجب أن يكون admin
            if (!$user->isAdmin()) {
                return false;
            }
        } else {
            // شرط عادي: التحقق من وجود صلاحية معينة
            if (!$user->hasPermission($requirement)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * البحث عن شروط صلاحية معينة في البنية الهرمية
 *
 * @param string $permissionCode رمز الصلاحية المطلوبة
 * @param array $hierarchy البنية الهرمية
 * @return array الشروط (requirements) المطلوبة
 */
function findRequirements($permissionCode, $hierarchy)
{
    foreach ($hierarchy as $parentCode => $parentData) {
        // التحقق من الصلاحية الرئيسية
        if ($parentCode === $permissionCode) {
            return $parentData['requires'] ?? [];
        }

        // البحث في الصلاحيات الفرعية
        if (isset($parentData['children'])) {
            foreach ($parentData['children'] as $childCode => $childData) {
                if ($childCode === $permissionCode) {
                    return $childData['requires'] ?? [];
                }
            }
        }
    }

    return [];
}

if (!function_exists('canCreate')) {
    /**
     * التحقق من صلاحية الإنشاء
     */
    function canCreate($permissionCode)
    {
        return hasPermission($permissionCode);
    }
}

if (!function_exists('canRead')) {
    /**
     * التحقق من صلاحية القراءة/العرض
     * الاستخدام الأساسي: @if(canRead('MENU_DASHBOARD'))
     */
    function canRead($permissionCode)
    {
        return hasPermission($permissionCode);
    }
}

if (!function_exists('canUpdate')) {
    /**
     * التحقق من صلاحية التحديث
     */
    function canUpdate($permissionCode)
    {
        return hasPermission($permissionCode);
    }
}

if (!function_exists('canDelete')) {
    /**
     * التحقق من صلاحية الحذف
     */
    function canDelete($permissionCode)
    {
        return hasPermission($permissionCode);
    }
}

if (!function_exists('canApprove')) {
    /**
     * التحقق من صلاحية الموافقة
     */
    function canApprove($permissionCode)
    {
        return hasPermission($permissionCode);
    }
}

if (!function_exists('canExport')) {
    /**
     * التحقق من صلاحية التصدير
     */
    function canExport($permissionCode)
    {
        return hasPermission($permissionCode);
    }
}

if (!function_exists('hasRole')) {
    /**
     * التحقق من دور معين
     */
    function hasRole($roleCode)
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $role = $user->roleRelation ?? $user->role;
        if (!$role) {
            return false;
        }

        return $role->role_code === $roleCode;
    }
}

if (!function_exists('hasAnyRole')) {
    /**
     * التحقق من وجود أي دور من الأدوار المعطاة
     */
    function hasAnyRole(...$roleCodes)
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $role = $user->roleRelation ?? $user->role;
        if (!$role) {
            return false;
        }

        return in_array($role->role_code, $roleCodes);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * التحقق من كون المستخدم مدير عام
     */
    function isAdmin()
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $role = $user->roleRelation ?? $user->role;
        if (!$role) {
            return false;
        }

        return $role->role_code === 'ADMIN';
    }
}

if (!function_exists('getRoleLevel')) {
    /**
     * الحصول على مستوى الدور الحالي
     */
    function getRoleLevel()
    {
        $user = auth()->user();
        return $user && $user->roleRelation ? $user->roleRelation->level : 0;
    }
}

if (!function_exists('hasMinLevel')) {
    /**
     * التحقق من وجود الحد الأدنى من المستوى
     */
    function hasMinLevel($minLevel)
    {
        return getRoleLevel() >= $minLevel;
    }
}

if (!function_exists('canManageUser')) {
    /**
     * التحقق من إمكانية إدارة مستخدم آخر (بناءً على المستوى)
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
     * التحقق من إمكانية تعيين دور معين
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

/**
 * الحصول على البنية الهرمية للصلاحيات
 * مفيد للعرض والإدارة في الـ Controllers
 */
if (!function_exists('getPermissionHierarchy')) {
    function getPermissionHierarchy()
    {
        global $PERMISSION_HIERARCHY;
        return $PERMISSION_HIERARCHY;
    }
}

/**
 * الحصول على جميع صلاحيات المستخدم الحالي
 */
if (!function_exists('getUserPermissions')) {
    function getUserPermissions()
    {
        $user = auth()->user();
        if (!$user || !$user->role) {
            return [];
        }

        return $user->role->permissions()
            ->where('is_active', true)
            ->pluck('permission_code')
            ->toArray();
    }
}
