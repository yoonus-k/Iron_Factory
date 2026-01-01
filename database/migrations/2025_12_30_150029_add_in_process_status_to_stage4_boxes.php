<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة حالة in_process لجدول stage4_boxes
        DB::statement("ALTER TABLE `stage4_boxes` MODIFY COLUMN `status` ENUM('in_process', 'pending_approval', 'packed', 'completed', 'intake_pending', 'in_warehouse', 'shipped') NOT NULL DEFAULT 'packed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `stage4_boxes` MODIFY COLUMN `status` ENUM('pending_approval', 'packed', 'completed', 'intake_pending', 'in_warehouse', 'shipped') NOT NULL DEFAULT 'packed'");
    }
};
