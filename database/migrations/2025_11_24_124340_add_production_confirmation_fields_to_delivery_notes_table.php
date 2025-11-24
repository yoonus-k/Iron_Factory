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
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->string('production_stage', 50)->nullable()->after('production_barcode')->comment('كود المرحلة الإنتاجية');
            $table->string('production_stage_name', 100)->nullable()->after('production_stage')->comment('اسم المرحلة الإنتاجية');
            $table->foreignId('assigned_to')->nullable()->after('production_stage_name')->constrained('users')->comment('الموظف المستلم');
            $table->enum('transfer_status', ['pending', 'confirmed', 'rejected'])->nullable()->after('assigned_to')->comment('حالة النقل');
            $table->foreignId('confirmed_by')->nullable()->after('transfer_status')->constrained('users')->comment('من أكد الاستلام');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by')->comment('وقت التأكيد');
            $table->foreignId('rejected_by')->nullable()->after('confirmed_at')->constrained('users')->comment('من رفض الاستلام');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by')->comment('وقت الرفض');
            $table->text('rejection_reason')->nullable()->after('rejected_at')->comment('سبب الرفض');
            $table->decimal('actual_received_quantity', 10, 2)->nullable()->after('rejection_reason')->comment('الكمية المستلمة فعلياً');
            
            $table->index('production_stage');
            $table->index('assigned_to');
            $table->index('transfer_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['confirmed_by']);
            $table->dropForeign(['rejected_by']);
            
            $table->dropIndex(['production_stage']);
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['transfer_status']);
            
            $table->dropColumn([
                'production_stage',
                'production_stage_name',
                'assigned_to',
                'transfer_status',
                'confirmed_by',
                'confirmed_at',
                'rejected_by',
                'rejected_at',
                'rejection_reason',
                'actual_received_quantity',
            ]);
        });
    }
};
