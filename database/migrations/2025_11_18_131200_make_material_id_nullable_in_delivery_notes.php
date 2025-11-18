<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جعل material_id اختياري في delivery_notes
     * لأن الأذن الواردة لا تحتاج لتحديد المادة في المرحلة الأولى
     */
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // حذف المفتاح الخارجي القديم
            $table->dropForeign(['material_id']);

            // تعديل الحقل ليكون nullable
            $table->foreignId('material_id')->nullable()->change();

            // إعادة إضافة المفتاح الخارجي
            $table->foreign('material_id')
                  ->references('id')
                  ->on('materials')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // حذف المفتاح الخارجي
            $table->dropForeign(['material_id']);

            // إرجاع الحقل ليكون إجباري
            $table->foreignId('material_id')->nullable(false)->change();

            // إعادة إضافة المفتاح الخارجي
            $table->foreign('material_id')
                  ->references('id')
                  ->on('materials')
                  ->onDelete('cascade');
        });
    }
};
