<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barcode_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique()->comment('raw_material, stage1, stage2, stage3, stage4');
            $table->string('prefix', 10)->comment('RW, ST1, ST2, CO3, BOX4');
            $table->integer('current_number')->default(0)->comment('آخر رقم مستخدم');
            $table->year('year')->default(date('Y'))->comment('السنة الحالية');
            $table->string('format')->default('{prefix}-{year}-{number}')->comment('صيغة الباركود');
            $table->boolean('auto_increment')->default(true)->comment('زيادة تلقائية');
            $table->integer('padding')->default(3)->comment('عدد الأصفار (001, 0001)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // إدراج الإعدادات الافتراضية
        DB::table('barcode_settings')->insert([
            [
                'type' => 'raw_material',
                'prefix' => 'RW',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stage1',
                'prefix' => 'ST1',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stage2',
                'prefix' => 'ST2',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stage3',
                'prefix' => 'CO3',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stage4',
                'prefix' => 'BOX4',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_settings');
    }
};
