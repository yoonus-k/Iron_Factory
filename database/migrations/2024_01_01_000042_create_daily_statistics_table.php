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
        Schema::create('daily_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('statistics_date')->unique();
            $table->decimal('total_materials_received', 12, 3)->default(0);
            $table->integer('total_stands_created')->default(0);
            $table->integer('total_coils_produced')->default(0);
            $table->integer('total_boxes_packed')->default(0);
            $table->decimal('total_waste_amount', 10, 3)->default(0);
            $table->decimal('waste_percentage', 5, 2)->default(0);
            $table->integer('active_workers')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamp('calculated_at')->useCurrent();

            $table->index('statistics_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_statistics');
    }
};
