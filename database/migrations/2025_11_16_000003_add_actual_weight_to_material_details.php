<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقل actual_weight إلى جدول material_details
     * لتخزين الوزن الفعلي للمنتج من الميزان
     */
    public function up(): void
    {
        Schema::table('material_details', function (Blueprint $table) {
            if (!Schema::hasColumn('material_details', 'actual_weight')) {
                $table->float('actual_weight')->default(0)->after('original_weight')->comment('الوزن الفعلي المقاس من الميزان');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_details', function (Blueprint $table) {
            if (Schema::hasColumn('material_details', 'actual_weight')) {
                $table->dropColumn('actual_weight');
            }
        });
    }
};
