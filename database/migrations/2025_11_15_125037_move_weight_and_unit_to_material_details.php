<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * نقل original_weight و remaining_weight و unit_id من materials إلى material_details
     */
    public function up(): void
    {
        // أولاً: إضافة الأعمدة إلى material_details
        Schema::table('material_details', function (Blueprint $table) {
            if (!Schema::hasColumn('material_details', 'original_weight')) {
                $table->float('original_weight')->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('material_details', 'remaining_weight')) {
                $table->float('remaining_weight')->default(0)->after('original_weight');
            }
            if (!Schema::hasColumn('material_details', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('remaining_weight');
                $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            }
        });

        // ثانياً: نقل البيانات من materials إلى material_details
        // لكل material_detail، نأخذ البيانات من المادة الأم
        \DB::statement('
            UPDATE material_details md
            JOIN materials m ON md.material_id = m.id
            SET
                md.original_weight = COALESCE(m.original_weight, 0),
                md.remaining_weight = COALESCE(m.remaining_weight, 0),
                md.unit_id = m.unit_id
            WHERE md.quantity > 0 OR md.quantity IS NULL
        ');

        // ثالثاً: حذف الأعمدة من materials
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'original_weight')) {
                $table->dropColumn('original_weight');
            }
            if (Schema::hasColumn('materials', 'remaining_weight')) {
                $table->dropColumn('remaining_weight');
            }
            if (Schema::hasColumn('materials', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
            if (Schema::hasColumn('materials', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // استرجاع الأعمدة في materials
        Schema::table('materials', function (Blueprint $table) {
            $table->float('original_weight')->default(0)->after('material_category');
            $table->float('remaining_weight')->default(0)->after('original_weight');
            $table->string('unit')->nullable()->after('remaining_weight');
            $table->unsignedBigInteger('unit_id')->nullable()->after('unit');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });

        // نقل البيانات عودة من material_details إلى materials
        \DB::statement('
            UPDATE materials m
            SET
                m.original_weight = (
                    SELECT COALESCE(SUM(md.original_weight), 0)
                    FROM material_details md
                    WHERE md.material_id = m.id
                ),
                m.remaining_weight = (
                    SELECT COALESCE(SUM(md.remaining_weight), 0)
                    FROM material_details md
                    WHERE md.material_id = m.id
                ),
                m.unit_id = (
                    SELECT md.unit_id
                    FROM material_details md
                    WHERE md.material_id = m.id
                    LIMIT 1
                )
        ');

        // حذف الأعمدة من material_details
        Schema::table('material_details', function (Blueprint $table) {
            if (Schema::hasColumn('material_details', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
            if (Schema::hasColumn('material_details', 'original_weight')) {
                $table->dropColumn('original_weight');
            }
            if (Schema::hasColumn('material_details', 'remaining_weight')) {
                $table->dropColumn('remaining_weight');
            }
        });
    }
};

