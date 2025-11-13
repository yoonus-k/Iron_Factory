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
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->string('detail_key', 100)->comment('مفتاح التفصيل');
            $table->text('detail_value')->comment('قيمة التفصيل');
            $table->enum('data_type', ['string', 'number', 'date', 'boolean', 'json'])->default('string')->comment('نوع البيانات');
            $table->boolean('is_visible')->default(true)->comment('ظاهر للمستخدم');
            $table->integer('display_order')->default(0)->comment('ترتيب العرض');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('material_id');
            $table->index('detail_key');
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
