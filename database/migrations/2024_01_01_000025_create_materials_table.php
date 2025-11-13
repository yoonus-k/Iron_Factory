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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->foreignId('material_type_id')->nullable()->constrained('material_types')->onDelete('set null');
            $table->string('barcode', 50)->unique()->comment('WH-XXX-2025');
            $table->string('batch_number', 100)->nullable()->comment('رقم الدفعة');
            $table->string('material_type', 100)->comment('نوع المادة (نصي للتوافقية)');
            $table->decimal('original_weight', 10, 3);
            $table->decimal('remaining_weight', 10, 3);
            $table->enum('unit', ['kg', 'ton', 'gram'])->default('kg')->comment('الوحدة (نصية للتوافقية)');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('delivery_note_number', 100)->nullable();
            $table->date('manufacture_date')->nullable()->comment('تاريخ التصنيع');
            $table->date('expiry_date')->nullable()->comment('تاريخ الانتهاء');
            $table->string('shelf_location', 100)->nullable()->comment('موقع الرف في المستودع');
            $table->foreignId('purchase_invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['available', 'in_use', 'consumed', 'expired'])->default('available');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('barcode');
            $table->index('warehouse_id');
            $table->index('material_type_id');
            $table->index('unit_id');
            $table->index('batch_number');
            $table->index('status');
            $table->index('material_type');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
