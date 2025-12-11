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
        Schema::table('shift_handovers', function (Blueprint $table) {
            $table->foreignId('shift_assignment_id')->nullable()->after('stage_number')->constrained('shift_assignments')->nullOnDelete();
            $table->boolean('auto_collected')->default(false)->after('handover_items')->comment('تم جمع الأشغال تلقائياً');
            $table->timestamp('acknowledged_at')->nullable()->after('handover_time')->comment('وقت استلام الوردية');
            $table->foreignId('acknowledged_by')->nullable()->after('acknowledged_at')->constrained('users')->nullOnDelete()->comment('من استلم الوردية');
            $table->integer('pending_items_count')->default(0)->after('auto_collected')->comment('عدد الأشغال المعلقة');

            $table->index('shift_assignment_id');
            $table->index('auto_collected');
            $table->index('acknowledged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_handovers', function (Blueprint $table) {
            $table->dropForeign(['shift_assignment_id']);
            $table->dropForeign(['acknowledged_by']);
            $table->dropIndex(['shift_assignment_id']);
            $table->dropIndex(['auto_collected']);
            $table->dropIndex(['acknowledged_at']);
            $table->dropColumn([
                'shift_assignment_id',
                'auto_collected',
                'acknowledged_at',
                'acknowledged_by',
                'pending_items_count',
            ]);
        });
    }
};
