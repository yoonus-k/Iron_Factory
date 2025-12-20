<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // الترتيب مهم جداً - يجب إنشاء الأساسيات أولاً
        $this->call([
            // 1. الأدوار والصلاحيات
            RolesAndPermissionsSeeder::class,
            PermissionsSeeder::class,
            RolePermissionsSeeder::class,
            UpdatePermissionsTranslations::class,
            
            // 2. المستخدمين
            AdminUserSeeder::class,
            UsersSeeder::class,
            
            // 3. إعدادات النظام
            SystemSettingsSeeder::class,
            BarcodeSettingsSeeder::class,
            
            // 4. البيانات الأساسية
            WarehousesSeeder::class,
            MaterialTypesAndUnitsSeeder::class,
            MaterialTypeTranslationSeeder::class,
            UnitTranslationSeeder::class,
            
            // 5. الموردين والعملاء والعمال
            SuppliersSeeder::class,
            TestSupplierSeeder::class,
            CustomersSeeder::class,
            WorkersSeeder::class,
            
            // 6. المواد والإنتاج
            MaterialsSeeder::class,
            DefaultMaterialsSeeder::class,
            ProductionStageSeeder::class,
            
            // 7. وحدات النظام (Modules)
            \Modules\Manufacturing\Database\Seeders\ManufacturingDatabaseSeeder::class,
        ]);
    }
}
