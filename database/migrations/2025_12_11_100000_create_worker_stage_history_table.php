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
        Schema::create('worker_stage_history', function (Blueprint $table) {
            $table->id();

            // معلومات المرحلة
            $table->string('stage_type'); // stage1_stands, stage2_processed, stage3_coils, stage4_boxes
            $table->unsignedBigInteger('stage_record_id'); // ID السجل في جدول المرحلة
            $table->string('barcode')->nullable(); // الباركود للوصول السريع

            // معلومات العامل
            $table->unsignedBigInteger('worker_id')->nullable(); // العامل الفردي
            $table->unsignedBigInteger('worker_team_id')->nullable(); // أو الفريق
            $table->enum('worker_type', ['individual', 'team'])->default('individual');

            // معلومات التوقيت
            $table->timestamp('started_at'); // متى بدأ العامل العمل على هذه المرحلة
            $table->timestamp('ended_at')->nullable(); // متى انتهى (null = لا زال يعمل)
            $table->integer('duration_minutes')->nullable(); // المدة بالدقائق (تُحسب عند الانتهاء)

            // معلومات الحالة
            $table->string('status_before')->nullable(); // حالة المرحلة قبل العمل
            $table->string('status_after')->nullable(); // حالة المرحلة بعد العمل
            $table->boolean('is_active')->default(true); // هل لا زال يعمل على هذه المرحلة

            // معلومات إضافية
            $table->text('notes')->nullable(); // ملاحظات
            $table->unsignedBigInteger('shift_assignment_id')->nullable(); // ربط بالشيفت
            $table->unsignedBigInteger('assigned_by')->nullable(); // من قام بالتعيين

            $table->timestamps();
            $table->softDeletes();

            // Indexes للبحث السريع
            $table->index(['stage_type', 'stage_record_id']);
            $table->index('barcode');
            $table->index('worker_id');
            $table->index('worker_team_id');
            $table->index('is_active');
            $table->index(['started_at', 'ended_at']);

            // Foreign keys
            $table->foreign('worker_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('worker_team_id')->references('id')->on('worker_teams')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_stage_history');
    }
};
