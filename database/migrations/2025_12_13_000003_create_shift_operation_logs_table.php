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
        Schema::create('shift_operation_logs', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('shift_id')
                ->constrained('shift_assignments')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Operation details
            $table->string('operation_type'); // create, update, transfer, assign_stage, etc
            $table->string('stage_number')->nullable(); // 1, 2, 3, 4

            // Previous data (JSON)
            $table->json('old_data')->nullable();

            // New data (JSON)
            $table->json('new_data')->nullable();

            // Change details
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // Status
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->text('error_message')->nullable();

            // IP and User Agent for audit
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('shift_id');
            $table->index('user_id');
            $table->index('operation_type');
            $table->index('stage_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_operation_logs');
    }
};
