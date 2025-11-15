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
        Schema::create('stage4_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->unique()->comment('BOX4-XXX-2025');
            $table->string('packaging_type', 100)->default('cardboard_box')->comment('نوع التغليف');
            $table->string('packaging_type_en', 100)->nullable()->comment('نوع التغليف بالإنجليزية');
            $table->integer('coils_count');
            $table->decimal('total_weight', 10, 3);
            $table->decimal('waste', 10, 3)->default(0);
            $table->enum('status', ['packing', 'packed', 'shipped', 'delivered'])->default('packing');
            $table->text('customer_info')->nullable()->comment('بيانات العميل بالعربية');
            $table->text('customer_info_en')->nullable()->comment('بيانات العميل بالإنجليزية');
            $table->text('shipping_address')->nullable()->comment('عنوان الشحن بالعربية');
            $table->text('shipping_address_en')->nullable()->comment('عنوان الشحن بالإنجليزية');
            $table->string('tracking_number', 100)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('packed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();

            $table->index('barcode');
            $table->index('status');
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage4_boxes');
    }
};
