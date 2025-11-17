<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة آليات منع التكرار لتسجيل البضاعة
     */
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // إضافة فهرس فريد لمنع تسجيل نفس الشحنة مرتين
            // (نفس الشحنة + نفس المورد + نفس تاريخ الوصول)
            if (!Schema::hasColumn('delivery_notes', 'deduplicate_key')) {
                $table->string('deduplicate_key')->nullable()->unique()
                    ->comment('مفتاح فريد لمنع التكرار: note_number + supplier_id');
            }

            // تتبع محاولات التسجيل السابقة
            if (!Schema::hasColumn('delivery_notes', 'registration_attempts')) {
                $table->integer('registration_attempts')->default(0)
                    ->comment('عدد محاولات التسجيل');
            }

            // معرف سجل التسجيل الأخير المستخدم
            if (!Schema::hasColumn('delivery_notes', 'last_registration_log_id')) {
                $table->unsignedBigInteger('last_registration_log_id')->nullable()
                    ->comment('آخر سجل تسجيل تم استخدامه');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'deduplicate_key')) {
                $table->dropUnique(['deduplicate_key']);
                $table->dropColumn('deduplicate_key');
            }
            if (Schema::hasColumn('delivery_notes', 'registration_attempts')) {
                $table->dropColumn('registration_attempts');
            }
            if (Schema::hasColumn('delivery_notes', 'last_registration_log_id')) {
                $table->dropColumn('last_registration_log_id');
            }
        });
    }
};
