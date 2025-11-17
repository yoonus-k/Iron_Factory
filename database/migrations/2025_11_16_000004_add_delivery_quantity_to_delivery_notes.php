<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقل delivery_quantity إلى جدول delivery_notes
     * لتسجيل كمية المنتج المسلمة في الأذن
     */
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_notes', 'delivery_quantity')) {
                $table->float('delivery_quantity')->default(0)->after('material_detail_id')->comment('كمية المنتج المسلمة في الأذن');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'delivery_quantity')) {
                $table->dropColumn('delivery_quantity');
            }
        });
    }
};
