<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class DetailedPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $this->command->info('🔄 جاري إضافة الصلاحيات التفصيلية...');

            // صلاحيات تفصيلية للمراحل
            $detailedPermissions = [
                // المرحلة الأولى - صلاحيات تفصيلية
                [
                    'permission_name' => 'عرض تفاصيل الوزن - المرحلة الأولى',
                    'permission_name_en' => 'View Weight Details - Stage 1',
                    'permission_code' => 'STAGE1_VIEW_WEIGHT',
                    'module' => 'Manufacturing',
                    'description' => 'عرض تفاصيل الوزن والهدر في المرحلة الأولى',
                ],
                [
                    'permission_name' => 'تعديل الوزن - المرحلة الأولى',
                    'permission_name_en' => 'Edit Weight - Stage 1',
                    'permission_code' => 'STAGE1_EDIT_WEIGHT',
                    'module' => 'Manufacturing',
                    'description' => 'تعديل أوزان المواد في المرحلة الأولى',
                ],
                [
                    'permission_name' => 'عرض معلومات العامل - المرحلة الأولى',
                    'permission_name_en' => 'View Worker Info - Stage 1',
                    'permission_code' => 'STAGE1_VIEW_WORKER',
                    'module' => 'Manufacturing',
                    'description' => 'عرض معلومات العامل المسؤول',
                ],

                // المرحلة الثانية - صلاحيات تفصيلية
                [
                    'permission_name' => 'عرض تفاصيل الوزن - المرحلة الثانية',
                    'permission_name_en' => 'View Weight Details - Stage 2',
                    'permission_code' => 'STAGE2_VIEW_WEIGHT',
                    'module' => 'Manufacturing',
                    'description' => 'عرض تفاصيل الوزن والهدر في المرحلة الثانية',
                ],
                [
                    'permission_name' => 'تعديل الوزن - المرحلة الثانية',
                    'permission_name_en' => 'Edit Weight - Stage 2',
                    'permission_code' => 'STAGE2_EDIT_WEIGHT',
                    'module' => 'Manufacturing',
                    'description' => 'تعديل أوزان المعالجة في المرحلة الثانية',
                ],
                [
                    'permission_name' => 'عرض معلومات العامل - المرحلة الثانية',
                    'permission_name_en' => 'View Worker Info - Stage 2',
                    'permission_code' => 'STAGE2_VIEW_WORKER',
                    'module' => 'Manufacturing',
                    'description' => 'عرض معلومات العامل المسؤول',
                ],

                // المرحلة الثالثة - صلاحيات تفصيلية
                [
                    'permission_name' => 'عرض تفاصيل الوزن - المرحلة الثالثة',
                    'permission_name_en' => 'View Weight Details - Stage 3',
                    'permission_code' => 'STAGE3_VIEW_WEIGHT',
                    'module' => 'Manufacturing',
                    'description' => 'عرض تفاصيل الوزن المضاف في المرحلة الثالثة',
                ],
                [
                    'permission_name' => 'تعديل الوزن - المرحلة الثالثة',
                    'permission_name_en' => 'Edit Weight - Stage 3',
                    'permission_code' => 'STAGE3_EDIT_WEIGHT',
                    'module' => 'Manufacturing',
                    'description' => 'تعديل أوزان اللفائف في المرحلة الثالثة',
                ],
                [
                    'permission_name' => 'عرض معلومات العامل - المرحلة الثالثة',
                    'permission_name_en' => 'View Worker Info - Stage 3',
                    'permission_code' => 'STAGE3_VIEW_WORKER',
                    'module' => 'Manufacturing',
                    'description' => 'عرض معلومات العامل المسؤول',
                ],

                // المرحلة الرابعة - صلاحيات تفصيلية
                [
                    'permission_name' => 'عرض تفاصيل الوزن - المرحلة الرابعة',
                    'permission_name_en' => 'View Weight Details - Stage 4',
                    'permission_code' => 'STAGE4_VIEW_WEIGHT',
                    'module' => 'Manufacturing',
                    'description' => 'عرض تفاصيل الوزن في المرحلة الرابعة',
                ],
                [
                    'permission_name' => 'تعديل الوزن - المرحلة الرابعة',
                    'permission_name_en' => 'Edit Weight - Stage 4',
                    'permission_code' => 'STAGE4_EDIT_WEIGHT',
                    'module' => 'Manufacturing',
                    'description' => 'تعديل أوزان الكراتين في المرحلة الرابعة',
                ],
                [
                    'permission_name' => 'عرض معلومات العامل - المرحلة الرابعة',
                    'permission_name_en' => 'View Worker Info - Stage 4',
                    'permission_code' => 'STAGE4_VIEW_WORKER',
                    'module' => 'Manufacturing',
                    'description' => 'عرض معلومات العامل المسؤول',
                ],

                // صلاحيات عامة تفصيلية
                [
                    'permission_name' => 'عرض الأسعار',
                    'permission_name_en' => 'View Prices',
                    'permission_code' => 'VIEW_PRICES',
                    'module' => 'General',
                    'description' => 'عرض أسعار المواد والمنتجات',
                ],
                [
                    'permission_name' => 'تعديل الأسعار',
                    'permission_name_en' => 'Edit Prices',
                    'permission_code' => 'EDIT_PRICES',
                    'module' => 'General',
                    'description' => 'تعديل أسعار المواد والمنتجات',
                ],
                [
                    'permission_name' => 'عرض التكاليف',
                    'permission_name_en' => 'View Costs',
                    'permission_code' => 'VIEW_COSTS',
                    'module' => 'General',
                    'description' => 'عرض تكاليف الإنتاج',
                ],
                [
                    'permission_name' => 'حذف السجلات',
                    'permission_name_en' => 'Delete Records',
                    'permission_code' => 'DELETE_RECORDS',
                    'module' => 'General',
                    'description' => 'حذف سجلات الإنتاج',
                ],
            ];

            foreach ($detailedPermissions as $permission) {
                Permission::firstOrCreate(
                    ['permission_code' => $permission['permission_code']],
                    array_merge($permission, [
                        'is_system' => false,
                        'is_active' => true,
                        'created_by' => 1,
                    ])
                );
                $this->command->info("✅ {$permission['permission_name']}");
            }

            // ربط الصلاحيات التفصيلية بالأدوار
            $this->assignDetailedPermissions();

            DB::commit();
            $this->command->info('✅ تم إضافة الصلاحيات التفصيلية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ خطأ: ' . $e->getMessage());
        }
    }

    private function assignDetailedPermissions()
    {
        $admin = Role::where('role_code', 'ADMIN')->first();
        $manager = Role::where('role_code', 'MANAGER')->first();
        $supervisor = Role::where('role_code', 'SUPERVISOR')->first();
        $worker = Role::where('role_code', 'WORKER')->first();

        // Admin - كل الصلاحيات التفصيلية
        if ($admin) {
            $allDetailedPermissions = Permission::whereIn('permission_code', [
                'STAGE1_VIEW_WEIGHT', 'STAGE1_EDIT_WEIGHT', 'STAGE1_VIEW_WORKER',
                'STAGE2_VIEW_WEIGHT', 'STAGE2_EDIT_WEIGHT', 'STAGE2_VIEW_WORKER',
                'STAGE3_VIEW_WEIGHT', 'STAGE3_EDIT_WEIGHT', 'STAGE3_VIEW_WORKER',
                'STAGE4_VIEW_WEIGHT', 'STAGE4_EDIT_WEIGHT', 'STAGE4_VIEW_WORKER',
                'VIEW_PRICES', 'EDIT_PRICES', 'VIEW_COSTS', 'DELETE_RECORDS',
            ])->get();

            foreach ($allDetailedPermissions as $permission) {
                $admin->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => true,
                        'can_approve' => true,
                        'can_export' => true,
                    ]
                ]);
            }
        }

        // Manager - معظم الصلاحيات التفصيلية
        if ($manager) {
            $managerPermissions = Permission::whereIn('permission_code', [
                'STAGE1_VIEW_WEIGHT', 'STAGE1_EDIT_WEIGHT', 'STAGE1_VIEW_WORKER',
                'STAGE2_VIEW_WEIGHT', 'STAGE2_EDIT_WEIGHT', 'STAGE2_VIEW_WORKER',
                'STAGE3_VIEW_WEIGHT', 'STAGE3_EDIT_WEIGHT', 'STAGE3_VIEW_WORKER',
                'STAGE4_VIEW_WEIGHT', 'STAGE4_EDIT_WEIGHT', 'STAGE4_VIEW_WORKER',
                'VIEW_PRICES', 'EDIT_PRICES', 'VIEW_COSTS',
            ])->get();

            foreach ($managerPermissions as $permission) {
                $manager->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => true,
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                        'can_approve' => true,
                        'can_export' => true,
                    ]
                ]);
            }
        }

        // Supervisor - عرض فقط
        if ($supervisor) {
            $supervisorPermissions = Permission::whereIn('permission_code', [
                'STAGE1_VIEW_WEIGHT', 'STAGE1_VIEW_WORKER',
                'STAGE2_VIEW_WEIGHT', 'STAGE2_VIEW_WORKER',
                'STAGE3_VIEW_WEIGHT', 'STAGE3_VIEW_WORKER',
                'STAGE4_VIEW_WEIGHT', 'STAGE4_VIEW_WORKER',
                'VIEW_COSTS',
            ])->get();

            foreach ($supervisorPermissions as $permission) {
                $supervisor->permissions()->syncWithoutDetaching([
                    $permission->id => [
                        'can_create' => false,
                        'can_read' => true,
                        'can_update' => false,
                        'can_delete' => false,
                        'can_approve' => false,
                        'can_export' => false,
                    ]
                ]);
            }
        }

        // Worker - بدون صلاحيات تفصيلية (لا يرى التفاصيل)
    }
}
