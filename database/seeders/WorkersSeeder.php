<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Worker;

class WorkersSeeder extends Seeder
{
    public function run(): void
    {
        $workers = [
            [
                'worker_code' => 'W-001',
                'name' => 'علي محمد',
                'national_id' => '12345678901234',
                'phone' => '0100000001',
                'position' => 'worker',
                'shift_preference' => 'morning',
                'hourly_rate' => 50,
                'hire_date' => now(),
                'is_active' => true,
            ],
            [
                'worker_code' => 'W-002',
                'name' => 'محمود حسن',
                'national_id' => '12345678901235',
                'phone' => '0100000002',
                'position' => 'technician',
                'shift_preference' => 'evening',
                'hourly_rate' => 60,
                'hire_date' => now(),
                'is_active' => true,
            ],
            [
                'worker_code' => 'W-003',
                'name' => 'أحمد سعيد',
                'national_id' => '12345678901236',
                'phone' => '0100000003',
                'position' => 'supervisor',
                'shift_preference' => 'morning',
                'hourly_rate' => 80,
                'hire_date' => now(),
                'is_active' => true,
            ],
            [
                'worker_code' => 'W-004',
                'name' => 'حسن علي',
                'national_id' => '12345678901237',
                'phone' => '0100000004',
                'position' => 'quality_inspector',
                'shift_preference' => 'night',
                'hourly_rate' => 70,
                'hire_date' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($workers as $worker) {
            Worker::firstOrCreate(
                ['worker_code' => $worker['worker_code']],
                $worker
            );
        }

        $this->command->info('✅ تم إنشاء العمال بنجاح');
    }
}
