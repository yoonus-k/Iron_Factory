<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * تحديث جدول التسليم ليتكامل مع نظام الورديات 24 ساعة
     * - ربط التسليم بالوردية الحالية والقادمة
     * - دعم Offline/Online sync
     */
    public function up(): void
    {
        Schema::table('shift_handovers', function (Blueprint $table) {
            // ربط بالورديتين (الحالية والقادمة)
            $table->foreignId('from_shift_id')->nullable()->after('id')->constrained('shift_assignments')->nullOnDelete()->comment('الوردية المسلمة (الحالية)');
            $table->foreignId('to_shift_id')->nullable()->after('from_shift_id')->constrained('shift_assignments')->nullOnDelete()->comment('الوردية المستلمة (القادمة)');
            
            // حالة المزامنة (للدعم Offline/Online)
            $table->boolean('is_synced')->default(false)->after('supervisor_approved')->comment('تم المزامنة مع السيرفر');
            $table->timestamp('synced_at')->nullable()->after('is_synced')->comment('وقت المزامنة');
            
            // معلومات إضافية للتسليم
            $table->json('production_summary')->nullable()->after('handover_items')->comment('ملخص الإنتاج في الوردية');
            $table->json('issues')->nullable()->after('production_summary')->comment('المشاكل والملاحظات الهامة');
            $table->integer('workers_count')->default(0)->after('stage_number')->comment('عدد العمال في الوردية');
            
            // Indexes for performance
            $table->index('from_shift_id');
            $table->index('to_shift_id');
            $table->index('is_synced');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_handovers', function (Blueprint $table) {
            $table->dropIndex(['from_shift_id']);
            $table->dropIndex(['to_shift_id']);
            $table->dropIndex(['is_synced']);
            
            $table->dropForeign(['from_shift_id']);
            $table->dropForeign(['to_shift_id']);
            
            $table->dropColumn([
                'from_shift_id',
                'to_shift_id',
                'is_synced',
                'synced_at',
                'production_summary',
                'issues',
                'workers_count',
            ]);
        });
    }
};
