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
            $table->string('production_barcode', 100)->nullable()->after('batch_id')->comment('باركود الإنتاج المولد عند النقل للإنتاج');
            $table->index('production_barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropIndex(['production_barcode']);
            $table->dropColumn('production_barcode');
        });
    }
};
