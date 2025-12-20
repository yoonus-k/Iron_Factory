<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على مستخدم افتراضي للإنشاء
        $defaultUser = \App\Models\User::first();
        
        $suppliers = [
            [
                'name' => 'شركة الحديد المصرية',
                'name_en' => 'Egyptian Iron Company',
                'contact_person' => 'أحمد محمد',
                'phone' => '0123456789',
                'email' => 'info@egyptianiron.com',
                'address' => 'القاهرة - مصر الجديدة',
                'is_active' => true,
                'created_by' => $defaultUser?->id,
            ],
            [
                'name' => 'مصنع الأسلاك المتحدة',
                'name_en' => 'United Wires Factory',
                'contact_person' => 'محمود علي',
                'phone' => '0123456780',
                'email' => 'contact@unitedwires.com',
                'address' => 'الإسكندرية - برج العرب',
                'is_active' => true,
                'created_by' => $defaultUser?->id,
            ],
            [
                'name' => 'شركة المواد الكيميائية',
                'name_en' => 'Chemical Materials Co.',
                'contact_person' => 'سارة حسن',
                'phone' => '0123456781',
                'email' => 'sales@chemicals.com',
                'address' => '6 أكتوبر - القاهرة',
                'is_active' => true,
                'created_by' => $defaultUser?->id,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['email' => $supplier['email']],
                $supplier
            );
        }

        $this->command->info('✅ تم إنشاء الموردين بنجاح');
    }
}
