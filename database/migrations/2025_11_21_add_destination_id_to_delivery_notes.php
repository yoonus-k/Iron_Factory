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
        Schema::table('delivery_notes', function (Blueprint $table) {
            // إضافة عمود destination_id للأذون الصادرة
            if (!Schema::hasColumn('delivery_notes', 'destination_id')) {
                $table->string('destination_id')->nullable()->comment('الوجهة: stage_1, stage_2, warehouse_id, customer_external');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'destination_id')) {
                $table->dropColumn('destination_id');
            }
        });
    }
};
