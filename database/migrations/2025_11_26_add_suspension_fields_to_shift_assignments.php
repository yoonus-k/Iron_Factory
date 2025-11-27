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
            // Add new columns for suspension handling
            if (!Schema::hasColumn('shift_assignments', 'actual_end_time')) {
                $table->time('actual_end_time')->nullable()->after('end_time');
            }

            if (!Schema::hasColumn('shift_assignments', 'suspension_reason')) {
                $table->text('suspension_reason')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('shift_assignments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('updated_at');
            }

            if (!Schema::hasColumn('shift_assignments', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable()->after('completed_at');
            }

            if (!Schema::hasColumn('shift_assignments', 'resumed_at')) {
                $table->timestamp('resumed_at')->nullable()->after('suspended_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            $columns = [
                'actual_end_time',
                'suspension_reason',
                'completed_at',
                'suspended_at',
                'resumed_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('shift_assignments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
