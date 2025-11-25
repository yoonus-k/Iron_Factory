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
        Schema::create('production_stages', function (Blueprint $table) {
            $table->id();
            $table->string('stage_code', 50)->unique()->comment('كود المرحلة');
            $table->string('stage_name', 100)->comment('اسم المرحلة بالعربي');
            $table->string('stage_name_en', 100)->nullable()->comment('اسم المرحلة بالإنجليزي');
            $table->integer('stage_order')->comment('ترتيب المرحلة');
            $table->text('description')->nullable()->comment('وصف المرحلة');
            $table->integer('estimated_duration')->nullable()->comment('الوقت المتوقع بالدقائق');
            $table->boolean('is_active')->default(true)->comment('المرحلة نشطة');
            $table->timestamps();
            
            $table->index('stage_code');
            $table->index('stage_order');
        });

        // إدراج المراحل الافتراضية
        DB::table('production_stages')->insert([
            [
                'stage_code' => 'stage_1',
                'stage_name' => 'المرحلة الأولى - القطع',
                'stage_name_en' => 'Stage 1 - Cutting',
                'stage_order' => 1,
                'description' => 'مرحلة قطع المواد الخام',
                'estimated_duration' => 120,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stage_code' => 'stage_2',
                'stage_name' => 'المرحلة الثانية - التشكيل',
                'stage_name_en' => 'Stage 2 - Forming',
                'stage_order' => 2,
                'description' => 'مرحلة تشكيل المعادن',
                'estimated_duration' => 180,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stage_code' => 'stage_3',
                'stage_name' => 'المرحلة الثالثة - اللحام',
                'stage_name_en' => 'Stage 3 - Welding',
                'stage_order' => 3,
                'description' => 'مرحلة لحام القطع',
                'estimated_duration' => 240,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stage_code' => 'stage_4',
                'stage_name' => 'المرحلة الرابعة - الدهان',
                'stage_name_en' => 'Stage 4 - Painting',
                'stage_order' => 4,
                'description' => 'مرحلة الدهان والتشطيب',
                'estimated_duration' => 300,
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
        Schema::dropIfExists('production_stages');
    }
};
