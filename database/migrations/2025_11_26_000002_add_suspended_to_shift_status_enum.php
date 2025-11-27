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
        // Change the enum to include 'suspended'
        DB::statement("ALTER TABLE `shift_assignments` MODIFY `status` ENUM('scheduled', 'active', 'completed', 'cancelled', 'suspended') DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (remove 'suspended')
        DB::statement("ALTER TABLE `shift_assignments` MODIFY `status` ENUM('scheduled', 'active', 'completed', 'cancelled') DEFAULT 'scheduled'");
    }
};
