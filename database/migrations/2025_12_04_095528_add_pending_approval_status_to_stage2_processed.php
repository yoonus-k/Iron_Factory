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
        Schema::table('stage2_processed', function (Blueprint $table) {
            DB::statement("ALTER TABLE `stage2_processed` MODIFY COLUMN `status` ENUM('started', 'in_progress', 'completed', 'consumed', 'pending_approval') NOT NULL DEFAULT 'started'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage2_processed', function (Blueprint $table) {
            DB::statement("ALTER TABLE `stage2_processed` MODIFY COLUMN `status` ENUM('started', 'in_progress', 'completed', 'consumed') NOT NULL DEFAULT 'started'");
        });
    }
};
