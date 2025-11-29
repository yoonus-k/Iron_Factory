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
        DB::statement("ALTER TABLE `stage4_boxes` MODIFY COLUMN `status` ENUM('packed', 'completed', 'intake_pending', 'in_warehouse', 'shipped') NOT NULL DEFAULT 'packed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `stage4_boxes` MODIFY COLUMN `status` ENUM('packed', 'completed', 'shipped') NOT NULL DEFAULT 'packed'");
    }
};
