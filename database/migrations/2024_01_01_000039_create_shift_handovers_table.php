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
        Schema::create('shift_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_user_id')->constrained('users');
            $table->tinyInteger('stage_number');
            $table->json('handover_items')->nullable()->comment('JSON format');
            $table->text('notes')->nullable()->comment('ملاحظات بالعربية');
            $table->text('notes_en')->nullable()->comment('ملاحظات بالإنجليزية');
            $table->timestamp('handover_time')->useCurrent();
            $table->boolean('supervisor_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('stage_number');
            $table->index('handover_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_handovers');
    }
};
