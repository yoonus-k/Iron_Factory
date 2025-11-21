<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * أضافة حقل الكمية لكل أذن تسليم بشكل منفصل
     * بدل الاعتماد على material_details الموحدة فقط
     */
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // الكمية المخصصة لهذه الأذن بالذات
            $table->decimal('quantity', 12, 3)->default(0)->after('delivered_weight')->comment('كمية هذه الأذن المسجلة');

            // لتتبع الكمية المستخدمة من هذه الأذن
            $table->decimal('quantity_used', 12, 3)->default(0)->after('quantity')->comment('الكمية المستخدمة/المنقولة من هذه الأذن');

            // الكمية المتبقية
            $table->decimal('quantity_remaining', 12, 3)->default(0)->after('quantity_used')->comment('الكمية المتبقية من هذه الأذن');

            // إضافة index للبحث السريع
            $table->index('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropIndex(['quantity']);
            $table->dropColumn(['quantity', 'quantity_used', 'quantity_remaining']);
        });
    }
};
