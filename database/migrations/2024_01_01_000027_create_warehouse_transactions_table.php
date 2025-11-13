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
        Schema::create('warehouse_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 100)->unique()->comment('رقم العملية');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('material_id')->nullable()->constrained('materials')->onDelete('cascade');
            $table->enum('transaction_type', [
                'receive',      // استلام
                'issue',        // صرف
                'transfer',     // نقل
                'adjustment',   // تسوية
                'return',       // مرتجع
                'damage'        // تالف
            ])->comment('نوع العملية');
            $table->decimal('quantity', 10, 3)->comment('الكمية');
            $table->foreignId('unit_id')->constrained('units')->comment('الوحدة');
            $table->foreignId('from_warehouse_id')->nullable()->constrained('warehouses')->comment('من مستودع (للنقل)');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses')->comment('إلى مستودع (للنقل)');
            $table->string('reference_number', 100)->nullable()->comment('رقم المرجع');
            $table->text('notes')->nullable()->comment('ملاحظات بالعربية');
            $table->text('notes_en')->nullable()->comment('ملاحظات بالإنجليزية');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('transaction_number');
            $table->index('transaction_type');
            $table->index('warehouse_id');
            $table->index('material_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_transactions');
    }
};
