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
        Schema::table('material_batches', function (Blueprint $table) {
            $table->string('latest_production_barcode', 100)->nullable()->after('batch_code')->comment('آخر باركود إنتاج تم إنشاؤه من هذه الدفعة');
            $table->index('latest_production_barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_batches', function (Blueprint $table) {
            $table->dropIndex(['latest_production_barcode']);
            $table->dropColumn('latest_production_barcode');
        });
    }
};
