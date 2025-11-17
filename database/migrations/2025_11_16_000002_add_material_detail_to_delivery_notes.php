<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Link delivery notes to warehouse material details to avoid data duplication
     */
    public function up(): void
    {
        if (Schema::hasTable('delivery_notes')) {
            Schema::table('delivery_notes', function (Blueprint $table) {
                // Add reference to MaterialDetail to use existing warehouse data
                if (!Schema::hasColumn('delivery_notes', 'material_detail_id')) {
                    $table->foreignId('material_detail_id')
                        ->nullable()
                        ->constrained('material_details')
                        ->onDelete('set null')
                        ->after('material_id')
                        ->comment('ربط مع تفاصيل المادة في المستودع لتجنب التكرار');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'material_detail_id')) {
                $table->dropForeign(['material_detail_id']);
                $table->dropColumn('material_detail_id');
            }
        });
    }
};
