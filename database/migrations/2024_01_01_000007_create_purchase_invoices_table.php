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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 100)->unique()->comment('رقم الفاتورة');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade')->comment('المورد');
            $table->decimal('total_amount', 12, 2)->comment('المبلغ الإجمالي');
            $table->string('currency', 10)->default('SAR')->comment('العملة');
            $table->date('invoice_date')->comment('تاريخ الفاتورة');
            $table->date('due_date')->nullable()->comment('تاريخ الاستحقاق');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending')->comment('الحالة');
            $table->text('notes')->nullable()->comment('ملاحظات بالعربية');
            $table->text('notes_en')->nullable()->comment('ملاحظات بالإنجليزية');
            $table->foreignId('created_by')->constrained('users')->comment('من أنشأ السجل');
            $table->timestamps();

            $table->index('invoice_date');
            $table->index('status');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
