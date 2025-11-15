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
        Schema::create('waste_tracking', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('stage_number');
            $table->string('item_barcode', 50);
            $table->decimal('waste_amount', 10, 3);
            $table->decimal('waste_percentage', 5, 2)->nullable();
            $table->text('waste_reason')->nullable()->comment('سبب الفاقد بالعربية');
            $table->text('waste_reason_en')->nullable()->comment('سبب الفاقد بالإنجليزية');
            $table->foreignId('reported_by')->constrained('users');
            $table->boolean('supervisor_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('stage_number');
            $table->index('item_barcode');
            $table->index('supervisor_approved');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_tracking');
    }
};
