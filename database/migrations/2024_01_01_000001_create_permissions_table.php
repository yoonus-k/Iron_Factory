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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('permission_name')->unique()->comment('اسم الصلاحية');
            $table->string('permission_name_en')->unique()->comment('اسم الصلاحية بالإنجليزية');
            $table->string('permission_code', 100)->unique()->comment('رمز الصلاحية');
            $table->string('module', 100)->comment('القسم/الوحدة');
            $table->text('description')->nullable()->comment('وصف الصلاحية');
            $table->boolean('is_system')->default(false)->comment('صلاحية نظام');
            $table->boolean('is_active')->default(true)->comment('حالة الصلاحية');
            $table->bigInteger('created_by')->unsigned()->nullable()->comment('من أنشأ الصلاحية');
            $table->timestamps();

            $table->index('permission_code');
            $table->index('module');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
