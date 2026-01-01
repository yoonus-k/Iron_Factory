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
        Schema::table('coil_transfers', function (Blueprint $table) {
            $table->enum('status', ['in_production', 'completed', 'pending_approval'])
                ->default('in_production')
                ->after('notes')
                ->comment('حالة الكويل');
            $table->decimal('total_waste', 10, 3)
                ->nullable()
                ->after('status')
                ->comment('إجمالي الهدر (كجم)');
            $table->decimal('waste_percentage', 5, 2)
                ->nullable()
                ->after('total_waste')
                ->comment('نسبة الهدر (%)');
            $table->timestamp('completed_at')
                ->nullable()
                ->after('waste_percentage')
                ->comment('وقت إنهاء الكويل');

            // Indexes
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coil_transfers', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'total_waste', 'waste_percentage', 'completed_at']);
        });
    }
};
