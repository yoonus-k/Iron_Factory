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
        Schema::create('warehouse_intake_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique()->comment('رقم الطلب: WIR-2025-0001');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('الحالة');
            $table->foreignId('requested_by')->constrained('users')->comment('مسؤول الوردية - منشئ الطلب');
            $table->foreignId('approved_by')->nullable()->constrained('users')->comment('أمين المستودع - المعتمد');
            $table->timestamp('approved_at')->nullable()->comment('تاريخ الاعتماد');
            $table->text('notes')->nullable()->comment('ملاحظات الطلب');
            $table->text('rejection_reason')->nullable()->comment('سبب الرفض');
            $table->integer('boxes_count')->default(0)->comment('عدد الصناديق');
            $table->decimal('total_weight', 10, 2)->default(0)->comment('الوزن الإجمالي');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('requested_by');
        });

        Schema::create('warehouse_intake_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intake_request_id')->constrained('warehouse_intake_requests')->onDelete('cascade');
            $table->foreignId('stage4_box_id')->constrained('stage4_boxes');
            $table->string('barcode')->comment('باركود الصندوق');
            $table->string('packaging_type')->comment('نوع التغليف');
            $table->decimal('weight', 10, 2)->comment('الوزن');
            $table->timestamps();
            
            $table->index('intake_request_id');
            $table->index('stage4_box_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_intake_items');
        Schema::dropIfExists('warehouse_intake_requests');
    }
};
