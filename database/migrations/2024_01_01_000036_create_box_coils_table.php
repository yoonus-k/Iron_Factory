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
        Schema::create('box_coils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('stage4_boxes')->onDelete('cascade');
            $table->foreignId('coil_id')->constrained('stage3_coils')->onDelete('cascade');
            $table->timestamp('added_at')->useCurrent();
            
            $table->unique(['box_id', 'coil_id'], 'unique_box_coil');
            $table->index('box_id');
            $table->index('coil_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_coils');
    }
};
