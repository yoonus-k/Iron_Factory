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
            // Add stage record barcode and ID fields if they don't exist
            if (!Schema::hasColumn('shift_assignments', 'stage_record_barcode')) {
                $table->string('stage_record_barcode')->nullable()->after('stage_number')
                    ->comment('Barcode of the stage record (stand, processed, coil, or box)');
                $table->index('stage_record_barcode');
            }

            if (!Schema::hasColumn('shift_assignments', 'stage_record_id')) {
                $table->unsignedBigInteger('stage_record_id')->nullable()->after('stage_record_barcode')
                    ->comment('ID of the stage record');
                $table->index('stage_record_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('shift_assignments', 'stage_record_barcode')) {
                $table->dropColumn('stage_record_barcode');
            }
            if (Schema::hasColumn('shift_assignments', 'stage_record_id')) {
                $table->dropColumn('stage_record_id');
            }
        });
    }
};
