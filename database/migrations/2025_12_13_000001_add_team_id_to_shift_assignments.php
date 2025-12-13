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
        Schema::table('shift_assignments', function (Blueprint $table) {
            // Add team_id field to link shift with worker team
            $table->foreignId('team_id')
                ->nullable()
                ->constrained('worker_teams')
                ->onDelete('set null')
                ->after('supervisor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\WorkerTeam::class);
        });
    }
};
