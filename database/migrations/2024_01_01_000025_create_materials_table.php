<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null')->comment('المستودع');
            $table->foreignId('material_type_id')->nullable()->constrained('material_types')->onDelete('set null')->comment('نوع المادة');
            $table->string('barcode', 50)->unique()->comment('WH-XXX-2025');
            $table->string('batch_number', 100)->nullable()->comment('رقم الدفعة');
            $table->string('name_ar', 100)->comment('نوع المادة (نصي للتوافقية)');
            $table->string('name_en', 100)->nullable()->comment('نوع المادة بالإنجليزية');
            $table
                ->enum('type', ['raw', 'manufactured'])
                ->default('raw')
                ->comment('تصنيف المادة: خام/مصنع');
            $table->decimal('original_weight', 10, 3)->comment('الوزن الأصلي');
            $table->decimal('remaining_weight', 10, 3)->comment('الوزن المتبقي');

            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null')->comment('الوحدة');

            $table->string('delivery_note_number', 100)->nullable()->comment('رقم إشعار التسليم');

            $table->string('shelf_location', 100)->nullable()->comment('موقع الرف في المستودع');
            $table->string('shelf_location_en', 100)->nullable()->comment('موقع الرف بالإنجليزية');
            $table->foreignId('purchase_invoice_id')->nullable()->constrained('purchase_invoices')->onDelete('set null')->comment('فاتورة الشراء');
            $table
                ->enum('status', ['available', 'in_use', 'consumed', 'expired'])
                ->default('available')
                ->comment('حالة المادة');
            $table->text('notes')->nullable()->comment('ملاحظات بالعربية');
            $table->text('notes_en')->nullable()->comment('ملاحظات بالإنجليزية');
            $table->foreignId('created_by')->constrained('users')->comment('من أنشأ السجل');
            $table->timestamps();

            $table->index('barcode');
            $table->index('warehouse_id');
            $table->index('material_type_id');
            $table->index('unit_id');
            $table->index('batch_number');
            $table->index('status');
            $table->index('name_ar');


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
