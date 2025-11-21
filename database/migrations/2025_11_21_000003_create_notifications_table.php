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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // نوع الاشعار
            $table->string('type'); // e.g., 'material_added', 'purchase_invoice_created', 'delivery_note_registered'
            $table->string('title'); // عنوان الاشعار
            $table->longText('message'); // رسالة الاشعار
            $table->string('icon')->nullable(); // أيقونة الاشعار
            $table->string('color')->default('info'); // لون الاشعار (success, danger, warning, info)

            // بيانات العملية
            $table->string('action_type')->nullable(); // مثل: create, update, delete, transfer
            $table->string('model_type')->nullable(); // اسم الموديل
            $table->unsignedBigInteger('model_id')->nullable(); // ID الموديل

            // بيانات المستخدم الذي قام بالعملية
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // حالة الاشعار
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // الاجراء المرتبط
            $table->string('action_url')->nullable(); // رابط الاجراء

            // البيانات الإضافية
            $table->json('metadata')->nullable();

            $table->timestamps();

            // الفهارس
            $table->index('user_id');
            $table->index('type');
            $table->index('is_read');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
