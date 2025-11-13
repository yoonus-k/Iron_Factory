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
        Schema::create('system_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('formula_name', 100)->unique();
            $table->tinyInteger('stage_number')->nullable();
            $table->text('formula_expression')->comment('الصيغة الرياضية');
            $table->json('variables')->nullable()->comment('متغيرات الصيغة');
            $table->json('default_values')->nullable()->comment('القيم الافتراضية');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // إدراج المعادلات الافتراضية

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_formulas');
    }
};
