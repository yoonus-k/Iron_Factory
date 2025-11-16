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
        Schema::create('worker_teams', function (Blueprint $table) {
            $table->id();
            $table->string('team_code', 50)->unique()->comment('رقم المجموعة الفريد');
            $table->string('name')->comment('اسم المجموعة');
            $table->enum('shift_type', ['morning', 'evening', 'both'])->default('both')->comment('نوع الوردية المخصصة');
            $table->text('description')->nullable()->comment('وصف المجموعة');
            $table->json('worker_ids')->nullable()->comment('IDs العمال في المجموعة');
            $table->integer('workers_count')->default(0)->comment('عدد العمال');
            $table->boolean('is_active')->default(true)->comment('حالة النشاط');
            $table->timestamps();
            $table->softDeletes();

            $table->index('team_code');
            $table->index('shift_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_teams');
    }
};
