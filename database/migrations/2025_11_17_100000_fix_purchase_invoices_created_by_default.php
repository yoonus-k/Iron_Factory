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
        // Update existing records that might have NULL created_by values
        DB::table('purchase_invoices')
            ->whereNull('created_by')
            ->update(['created_by' => 1]);
            
        // Modify the column to have a default value
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->default(null)->change();
        });
    }
};