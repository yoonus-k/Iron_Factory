<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقل weight_from_scale لتسجيل الوزن المسجل من الميزان
     */
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_notes', 'weight_from_scale')) {
                $table->decimal('weight_from_scale', 10, 2)
                    ->nullable()
                    ->after('actual_weight')
                    ->comment('الوزن المسجل من الميزان (كجم)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'weight_from_scale')) {
                $table->dropColumn('weight_from_scale');
            }
        });
    }
};
