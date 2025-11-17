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
        Schema::create('registration_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('delivery_note_id');

            // ========== البيانات ==========
            $table->decimal('weight_recorded', 10, 2);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('material_type_id')->nullable();
            $table->string('location')->nullable();

            // ========== من فعل ==========
            $table->unsignedBigInteger('registered_by');
            $table->timestamp('registered_at')->nullable();

            // ========== Metadata ==========
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // ========== Foreign Keys ==========
            $table->foreign('delivery_note_id')
                ->references('id')
                ->on('delivery_notes')
                ->onDelete('cascade');

            $table->foreign('registered_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('set null');

            $table->foreign('material_type_id')
                ->references('id')
                ->on('material_types')
                ->onDelete('set null');

            // ========== Indexes ==========
            $table->index('delivery_note_id');
            $table->index('registered_by');
            $table->index('registered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_logs');
    }
};
