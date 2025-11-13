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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->boolean('can_create')->default(false)->comment('يمكنه الإنشاء');
            $table->boolean('can_read')->default(true)->comment('يمكنه القراءة');
            $table->boolean('can_update')->default(false)->comment('يمكنه التعديل');
            $table->boolean('can_delete')->default(false)->comment('يمكنه الحذف');
            $table->boolean('can_approve')->default(false)->comment('يمكنه الموافقة');
            $table->boolean('can_export')->default(false)->comment('يمكنه التصدير');
            $table->bigInteger('created_by')->unsigned()->nullable()->comment('من أنشأ العلاقة');
            $table->timestamps();

            // منع التكرار: كل دور يمكن أن يرتبط بصلاحية واحدة فقط
            $table->unique(['role_id', 'permission_id'], 'unique_role_permission');

            $table->index('role_id');
            $table->index('permission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
