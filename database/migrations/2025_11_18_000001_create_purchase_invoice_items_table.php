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
        Schema::create('purchase_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->onDelete('cascade')->comment('فاتورة الشراء');
            $table->foreignId('material_id')->nullable()->constrained('materials')->onDelete('set null')->comment('المادة');
            $table->string('item_name')->comment('اسم المنتج');
            $table->text('description')->nullable()->comment('وصف المنتج');
            $table->decimal('quantity', 12, 3)->comment('الكمية');
            $table->string('unit', 50)->default('طن')->comment('الوحدة');
            $table->decimal('unit_price', 12, 2)->comment('سعر الوحدة');
            $table->decimal('subtotal', 12, 2)->comment('المجموع الفرعي');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('نسبة الضريبة');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('مبلغ الضريبة');
            $table->decimal('discount_rate', 5, 2)->default(0)->comment('نسبة الخصم');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('مبلغ الخصم');
            $table->decimal('total', 12, 2)->comment('الإجمالي');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();

            $table->index('purchase_invoice_id');
            $table->index('material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_items');
    }
};
