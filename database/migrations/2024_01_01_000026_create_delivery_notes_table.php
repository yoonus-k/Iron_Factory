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
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_number', 100)->unique();
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('delivered_weight', 10, 3);
            $table->date('delivery_date');
            $table->string('driver_name')->nullable()->comment('اسم السائق');
            $table->string('driver_name_en')->nullable()->comment('اسم السائق بالإنجليزية');
            $table->string('vehicle_number', 50)->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->timestamps();
            
            $table->index('note_number');
            $table->index('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};
