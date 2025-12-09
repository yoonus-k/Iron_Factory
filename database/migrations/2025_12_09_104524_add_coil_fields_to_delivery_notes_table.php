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
            $table->string('vehicle_plate_number')->nullable()->after('vehicle_number')->comment('رقم لوحة السيارة');
            $table->string('received_from_person')->nullable()->after('received_by')->comment('اسم الشخص المستلم منه الشحنة');
            $table->integer('total_coils')->nullable()->after('delivery_quantity')->comment('عدد الكويلات في الشحنة');
            $table->boolean('has_coils')->default(false)->after('total_coils')->comment('هل الإذن يحتوي على كويلات');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropColumn(['vehicle_plate_number', 'received_from_person', 'total_coils', 'has_coils']);
        });
    }
};
