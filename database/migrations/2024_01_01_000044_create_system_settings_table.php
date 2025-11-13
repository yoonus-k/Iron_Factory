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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value');
            $table->text('setting_value_en')->nullable();
            $table->enum('setting_type', ['string', 'number', 'boolean', 'json'])->default('string');
            $table->string('category', 50)->default('general');
            $table->text('description')->nullable()->comment('وصف الإعداد بالعربية');
            $table->text('description_en')->nullable()->comment('وصف الإعداد بالإنجليزية');
            $table->boolean('is_public')->default(false)->comment('هل يمكن عرضها للمستخدمين العاديين');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('category');
        });

        // إدراج الإعدادات الافتراضية

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
