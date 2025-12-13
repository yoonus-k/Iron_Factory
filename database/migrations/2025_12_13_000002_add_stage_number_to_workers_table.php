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
        Schema::table('workers', function (Blueprint $table) {
            // إضافة حقل رقم المرحلة للعامل
            if (!Schema::hasColumn('workers', 'assigned_stage')) {
                $table->integer('assigned_stage')->nullable()->comment('المرحلة المعينة للعامل: 1, 2, 3, 4');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            if (Schema::hasColumn('workers', 'assigned_stage')) {
                $table->dropColumn('assigned_stage');
            }
        });
    }
};
