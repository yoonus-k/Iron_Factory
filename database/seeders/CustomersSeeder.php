<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        $defaultUser = \App\Models\User::first();
        
        $customers = [
            [
                'customer_code' => 'CUST-2025-001',
                'name' => 'شركة البناء الحديثة',
                'company_name' => 'شركة البناء الحديثة',
                'phone' => '0101234567',
                'email' => 'info@modernconstruction.com',
                'address' => 'القاهرة - مدينة نصر',
                'city' => 'القاهرة',
                'country' => 'مصر',
                'tax_number' => 'TAX-CUST-001',
                'is_active' => true,
                'created_by' => $defaultUser?->id,
            ],
            [
                'customer_code' => 'CUST-2025-002',
                'name' => 'مؤسسة التشييد الكبرى',
                'company_name' => 'مؤسسة التشييد الكبرى',
                'phone' => '0101234568',
                'email' => 'contact@majorbuilding.com',
                'address' => 'الإسكندرية - سموحة',
                'city' => 'الإسكندرية',
                'country' => 'مصر',
                'tax_number' => 'TAX-CUST-002',
                'is_active' => true,
                'created_by' => $defaultUser?->id,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['customer_code' => $customer['customer_code']],
                $customer
            );
        }

        $this->command->info('✅ تم إنشاء العملاء بنجاح');
    }
}
