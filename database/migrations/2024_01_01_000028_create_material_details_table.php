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
        Schema::create('material_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade')->comment('المستودع');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade')->comment('المادة');
            $table->decimal('quantity', 12, 3)->default(0)->comment('الكمية المتوفرة');
            $table->decimal('min_quantity', 12, 3)->nullable()->comment('الحد الأدنى للكمية');
            $table->decimal('max_quantity', 12, 3)->nullable()->comment('الحد الأقصى للكمية');
            $table->string('location_in_warehouse')->nullable()->comment('الموقع داخل المستودع');
            $table->date('last_stock_check')->nullable()->comment('آخر جرد');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['warehouse_id', 'material_id'], 'warehouse_material_unique');
            $table->index('warehouse_id');
            $table->index('material_id');
            $table->index('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_details');
    }
};
