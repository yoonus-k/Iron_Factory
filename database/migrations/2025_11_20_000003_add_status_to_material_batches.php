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
            if (!Schema::hasColumn('material_batches', 'status')) {
                $table->enum('status', ['available', 'in_production', 'consumed'])
                    ->default('available')
                    ->after('total_value')
                    ->comment('حالة الدفعة');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_batches', function (Blueprint $table) {
            if (Schema::hasColumn('material_batches', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
