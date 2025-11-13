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
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('المستخدم');
            $table->string('action', 100)->comment('العملية: إنشاء، تعديل، حذف، مسح، إلخ');
            $table->string('action_en', 100)->nullable()->comment('Action: create, update, delete, scan, etc');
            $table->string('table_name', 100)->comment('اسم الجدول');
            $table->bigInteger('record_id')->nullable()->comment('معرف السجل');
            $table->json('old_values')->nullable()->comment('القيم السابقة');
            $table->json('new_values')->nullable()->comment('القيم الجديدة');
            $table->string('ip_address', 45)->nullable()->comment('عنوان IP');
            $table->text('user_agent')->nullable()->comment('معلومات المتصفح');
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('action');
            $table->index('table_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_logs');
    }
};
