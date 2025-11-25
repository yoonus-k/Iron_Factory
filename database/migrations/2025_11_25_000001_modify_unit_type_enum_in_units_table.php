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
        // تعديل unit_type enum ليقبل فقط weight و quantity
        DB::statement("ALTER TABLE units MODIFY COLUMN unit_type ENUM('weight', 'quantity') NOT NULL COMMENT 'نوع الوحدة'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع القيم القديمة
        DB::statement("ALTER TABLE units MODIFY COLUMN unit_type ENUM('weight', 'length', 'volume', 'count', 'area') NOT NULL COMMENT 'نوع الوحدة'");
    }
};
