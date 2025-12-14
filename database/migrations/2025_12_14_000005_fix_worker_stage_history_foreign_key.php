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
        // إزالة الـ Foreign Key الخاطئ
        Schema::table('worker_stage_history', function (Blueprint $table) {
            $table->dropForeign(['worker_id']);
        });

        // إضافة الـ Foreign Key الصحيح
        Schema::table('worker_stage_history', function (Blueprint $table) {
            $table->foreign('worker_id')
                ->references('id')
                ->on('workers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_stage_history', function (Blueprint $table) {
            $table->dropForeign(['worker_id']);
        });

        Schema::table('worker_stage_history', function (Blueprint $table) {
            $table->foreign('worker_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};
