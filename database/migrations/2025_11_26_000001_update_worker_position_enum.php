<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تحديث enum ليشمل جميع الوظائف والمراحل
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE workers MODIFY position ENUM('worker', 'supervisor', 'technician', 'quality_inspector', 'stage1_worker', 'stage2_worker', 'stage3_worker', 'stage4_worker') DEFAULT 'worker'"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE workers MODIFY position ENUM('worker', 'supervisor', 'technician', 'quality_inspector') DEFAULT 'worker'"
            );
        }
    }
};
