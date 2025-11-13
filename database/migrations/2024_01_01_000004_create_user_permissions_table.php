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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('المستخدم');
            $table->string('permission_name', 100)->comment('اسم الصلاحية بالعربية');
            $table->string('permission_name_en', 100)->nullable()->comment('اسم الصلاحية بالإنجليزية');
            $table->boolean('can_create')->default(false)->comment('يمكنه الإنشاء');
            $table->boolean('can_read')->default(true)->comment('يمكنه القراءة');
            $table->boolean('can_update')->default(false)->comment('يمكنه التعديل');
            $table->boolean('can_delete')->default(false)->comment('يمكنه الحذف');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
