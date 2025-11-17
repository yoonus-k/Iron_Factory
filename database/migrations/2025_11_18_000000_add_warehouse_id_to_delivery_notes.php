<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقل warehouse_id لتسجيل الأذن بالمستودع بدون المادة
     * هذا يسمح بتسجيل البضاعة بدون الحاجة لاختيار منتج محدد
     */
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // جعل material_detail_id اختياري (بدلاً من الإجباري)
            if (Schema::hasColumn('delivery_notes', 'material_detail_id')) {
                $table->unsignedBigInteger('material_detail_id')->nullable()->change();
            }

            // إضافة حقل warehouse_id
            if (!Schema::hasColumn('delivery_notes', 'warehouse_id')) {
                $table->unsignedBigInteger('warehouse_id')
                    ->nullable()
                    ->after('material_id')
                    ->comment('معرف المستودع - يكون إجباري عند عدم وجود material_detail_id');

                $table->foreign('warehouse_id')
                    ->references('id')
                    ->on('warehouses')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'warehouse_id')) {
                $table->dropForeign('delivery_notes_warehouse_id_foreign');
                $table->dropColumn('warehouse_id');
            }

            // إعادة material_detail_id إلى الحالة السابقة
            if (Schema::hasColumn('delivery_notes', 'material_detail_id')) {
                $table->unsignedBigInteger('material_detail_id')->change();
            }
        });
    }
};
