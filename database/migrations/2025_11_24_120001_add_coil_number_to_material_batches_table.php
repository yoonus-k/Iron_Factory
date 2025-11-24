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
            $table->string('coil_number', 100)->nullable()->after('batch_code')->comment('رقم الكويل من المورد');
            $table->index('coil_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_batches', function (Blueprint $table) {
            $table->dropIndex(['coil_number']);
            $table->dropColumn('coil_number');
        });
    }
};
