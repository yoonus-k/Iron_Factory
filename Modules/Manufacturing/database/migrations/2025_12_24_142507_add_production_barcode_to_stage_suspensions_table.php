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
        Schema::table('stage_suspensions', function (Blueprint $table) {
            $table->string('production_barcode')->nullable()->after('batch_barcode')->comment('باركود المنتج المولد (ST1-XXX, ST2-XXX, ST4-XXX)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_suspensions', function (Blueprint $table) {
            $table->dropColumn('production_barcode');
        });
    }
};
