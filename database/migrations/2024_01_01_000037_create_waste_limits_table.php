<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waste_limits', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('stage_number')->unique();
            $table->string('stage_name', 100);
            $table->decimal('max_waste_percentage', 5, 2)->default(3.00);
            $table->decimal('warning_percentage', 5, 2)->default(2.50);
            $table->boolean('alert_supervisors')->default(true);
            $table->boolean('stop_production')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_limits');
    }
};
