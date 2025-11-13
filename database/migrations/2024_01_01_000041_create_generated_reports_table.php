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
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('report_type', ['daily', 'weekly', 'monthly', 'custom']);
            $table->string('report_title');
            $table->date('date_from');
            $table->date('date_to');
            $table->json('parameters')->nullable()->comment('فلاتر التقرير');
            $table->string('file_path', 500)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamp('generated_at')->useCurrent();
            
            $table->index('report_type');
            $table->index('generated_by');
            $table->index('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
