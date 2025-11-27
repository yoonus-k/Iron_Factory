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
        Schema::table('delivery_notes', function (Blueprint $table) {
            // إضافة ملاحظات فقط (العمود الوحيد المفقود)
            if (!Schema::hasColumn('delivery_notes', 'notes')) {
                $table->text('notes')->nullable()->after('vehicle_number');
            }
            
            // إضافة prepared_by إذا لم يكن موجوداً
            if (!Schema::hasColumn('delivery_notes', 'prepared_by')) {
                $table->foreignId('prepared_by')->nullable()->after('received_by')->constrained('users')->comment('من أنشأ الإذن');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'notes')) {
                $table->dropColumn('notes');
            }
            
            if (Schema::hasColumn('delivery_notes', 'prepared_by')) {
                $table->dropForeign(['prepared_by']);
                $table->dropColumn('prepared_by');
            }
        });
    }
};
