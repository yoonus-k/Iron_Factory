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
        Schema::table('stage3_coils', function (Blueprint $table) {
            $table->decimal('net_weight', 10, 2)->nullable()->after('total_weight')->comment('الوزن الصافي بعد خصم اللفاف');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage3_coils', function (Blueprint $table) {
            $table->dropColumn('net_weight');
        });
    }
};
