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
        Schema::create('worker_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')
                ->constrained('workers')
                ->cascadeOnDelete();
            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->cascadeOnDelete();
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['worker_id', 'permission_id']);

            // Index for faster queries
            $table->index('worker_id');
            $table->index('permission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_permissions');
    }
};
