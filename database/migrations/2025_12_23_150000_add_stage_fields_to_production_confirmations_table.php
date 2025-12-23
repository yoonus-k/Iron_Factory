<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // اجعل delivery_note_id اختيارياً
        Schema::table('production_confirmations', function (Blueprint $table) {
            $table->dropForeign(['delivery_note_id']);
        });

        DB::statement('ALTER TABLE production_confirmations MODIFY delivery_note_id BIGINT UNSIGNED NULL');

        Schema::table('production_confirmations', function (Blueprint $table) {
            $table->foreign('delivery_note_id')
                ->references('id')
                ->on('delivery_notes')
                ->onDelete('cascade');
        });

        Schema::table('production_confirmations', function (Blueprint $table) {
            $table->string('barcode', 100)->nullable()->after('batch_id');
            $table->unsignedBigInteger('stage_record_id')->nullable()->after('stage_code');
            $table->string('stage_type', 50)->nullable()->after('stage_code');
            $table->foreignId('worker_stage_history_id')->nullable()->after('stage_record_id')->constrained('worker_stage_history')->nullOnDelete();
            $table->string('confirmation_type', 50)->default('transfer')->after('status');
            $table->foreignId('assigned_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable()->after('notes');

            $table->index('barcode');
            $table->index('stage_type');
            $table->index('worker_stage_history_id');
            $table->index('confirmation_type');
        });
    }

    public function down(): void
    {
        Schema::table('production_confirmations', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->dropForeign(['worker_stage_history_id']);
            $table->dropIndex(['barcode']);
            $table->dropIndex(['stage_type']);
            $table->dropIndex(['worker_stage_history_id']);
            $table->dropIndex(['confirmation_type']);

            $table->dropColumn([
                'barcode',
                'stage_record_id',
                'stage_type',
                'worker_stage_history_id',
                'confirmation_type',
                'assigned_by',
                'metadata',
            ]);
        });

        Schema::table('production_confirmations', function (Blueprint $table) {
            $table->dropForeign(['delivery_note_id']);
        });

        $fallbackDeliveryNoteId = DB::table('delivery_notes')->min('id');

        if ($fallbackDeliveryNoteId) {
            DB::table('production_confirmations')
                ->whereNull('delivery_note_id')
                ->update(['delivery_note_id' => $fallbackDeliveryNoteId]);
        } else {
            DB::table('production_confirmations')
                ->whereNull('delivery_note_id')
                ->delete();
        }

        DB::statement('ALTER TABLE production_confirmations MODIFY delivery_note_id BIGINT UNSIGNED NOT NULL');

        Schema::table('production_confirmations', function (Blueprint $table) {
            $table->foreign('delivery_note_id')
                ->references('id')
                ->on('delivery_notes')
                ->onDelete('cascade');
        });
    }
};
