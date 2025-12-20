<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehousesSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'warehouse_code' => 'WH-001',
                'warehouse_name' => 'المستودع الرئيسي',
                'warehouse_name_en' => 'Main Warehouse',
                'warehouse_type' => 'general',
                'location' => 'المصنع - الدور الأرضي',
                'location_en' => 'Factory - Ground Floor',
                'capacity' => 10000,
                'capacity_unit' => 'kg',
                'is_active' => true,
            ],
            [
                'warehouse_code' => 'WH-002',
                'warehouse_name' => 'مستودع المواد الخام',
                'warehouse_name_en' => 'Raw Materials Warehouse',
                'warehouse_type' => 'raw_materials',
                'location' => 'المصنع - الدور الأول',
                'location_en' => 'Factory - First Floor',
                'capacity' => 5000,
                'capacity_unit' => 'kg',
                'is_active' => true,
            ],
            [
                'warehouse_code' => 'WH-003',
                'warehouse_name' => 'مستودع المنتجات النهائية',
                'warehouse_name_en' => 'Finished Products Warehouse',
                'warehouse_type' => 'finished_goods',
                'location' => 'المصنع - الدور الثاني',
                'location_en' => 'Factory - Second Floor',
                'capacity' => 8000,
                'capacity_unit' => 'kg',
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate(
                ['warehouse_code' => $warehouse['warehouse_code']],
                $warehouse
            );
        }

        $this->command->info('✅ تم إنشاء المستودعات بنجاح');
    }
}
