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
        Schema::create('additives_inventory', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['dye', 'plastic']);
            $table->string('name');
            $table->string('color', 50)->nullable()->comment('للصبغة فقط');
            $table->decimal('quantity', 10, 3);
            $table->string('unit', 20)->default('kg');
            $table->decimal('cost_per_unit', 8, 2)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additives_inventory');
    }
};
