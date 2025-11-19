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
        Schema::create('material_movements', function (Blueprint $table) {
            $table->id();

            // معلومات الحركة الأساسية
            $table->string('movement_number')->unique()->comment('رقم الحركة التلقائي');
            $table->enum('movement_type', [
                'incoming',           // دخول بضاعة
                'outgoing',           // خروج بضاعة
                'transfer',           // نقل بين مستودعات
                'to_production',      // نقل للإنتاج
                'from_production',    // إرجاع من الإنتاج
                'adjustment',         // تسوية
                'reconciliation',     // تعديل بعد التسوية
                'waste',             // هدر
                'return'             // إرجاع للمورد
            ])->comment('نوع الحركة');

            $table->enum('source', [
                'registration',       // من تسجيل البضاعة
                'reconciliation',     // من التسوية
                'production',         // من/إلى الإنتاج
                'transfer',          // نقل بين مستودعات
                'manual',            // تعديل يدوي
                'system'             // من النظام
            ])->comment('مصدر الحركة');

            // المراجع
            $table->foreignId('delivery_note_id')->nullable()->constrained('delivery_notes')->onDelete('cascade');
            $table->foreignId('reconciliation_log_id')->nullable()->constrained('reconciliation_logs')->onDelete('set null');
            $table->foreignId('material_detail_id')->nullable()->constrained('material_details')->onDelete('cascade');

            // معلومات المادة
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('quantity', 15, 3)->comment('الكمية');
            $table->decimal('unit_price', 15, 2)->nullable()->comment('سعر الوحدة');
            $table->decimal('total_value', 15, 2)->nullable()->comment('القيمة الإجمالية');

            // المستودعات
            $table->foreignId('from_warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null')->comment('من مستودع');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null')->comment('إلى مستودع');

            // المورد/الجهة
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('destination')->nullable()->comment('الجهة المستلمة');

            // تفاصيل الحركة
            $table->text('description')->nullable()->comment('وصف الحركة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->string('reference_number')->nullable()->comment('رقم مرجعي خارجي');

            // معلومات المستخدم والوقت
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('movement_date')->useCurrent()->comment('تاريخ الحركة');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // حالة الحركة
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index('movement_number');
            $table->index('movement_type');
            $table->index('source');
            $table->index('movement_date');
            $table->index('material_id');
            $table->index(['from_warehouse_id', 'to_warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_movements');
    }
};
