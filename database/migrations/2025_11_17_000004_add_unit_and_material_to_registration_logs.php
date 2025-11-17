<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registration_logs', function (Blueprint $table) {
            // إضافة حقل material_id إذا لم يكن موجود
            if (!Schema::hasColumn('registration_logs', 'material_id')) {
                $table->unsignedBigInteger('material_id')->nullable()->after('material_type_id');
            }

            // إضافة حقل unit_id
            if (!Schema::hasColumn('registration_logs', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('material_id');
            }

            // إضافة foreign keys
            if (!Schema::hasColumn('registration_logs', 'material_id')) {
                // Skip foreign key إذا لم نضيف الحقل للتو
            } else {
                $table->foreign('material_id')
                    ->references('id')
                    ->on('materials')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('registration_logs', 'unit_id')) {
                // Skip foreign key إذا لم نضيف الحقل للتو
            } else {
                $table->foreign('unit_id')
                    ->references('id')
                    ->on('units')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_logs', function (Blueprint $table) {
            // حذف Foreign Keys أولاً
            if (Schema::hasColumn('registration_logs', 'material_id')) {
                try {
                    $table->dropForeign(['material_id']);
                } catch (\Exception $e) {
                    // تجاهل الخطأ إذا لم تكن Foreign Key موجودة
                }
            }

            if (Schema::hasColumn('registration_logs', 'unit_id')) {
                try {
                    $table->dropForeign(['unit_id']);
                } catch (\Exception $e) {
                    // تجاهل الخطأ إذا لم تكن Foreign Key موجودة
                }
            }

            // حذف الأعمدة
            if (Schema::hasColumn('registration_logs', 'unit_id')) {
                $table->dropColumn('unit_id');
            }

            if (Schema::hasColumn('registration_logs', 'material_id')) {
                $table->dropColumn('material_id');
            }
        });
    }
};
