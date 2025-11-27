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
            // إضافة customer_id بعد material_id
            if (!Schema::hasColumn('delivery_notes', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('material_id')->constrained('customers')->onDelete('set null')->comment('العميل (للإذونات الصادرة)');
            }
            
            // إضافة print_count لحساب عدد مرات الطباعة
            if (!Schema::hasColumn('delivery_notes', 'print_count')) {
                $table->integer('print_count')->default(0)->after('rejection_reason')->comment('عدد مرات الطباعة');
            }
            
            // إضافة source_type و source_id لربط الإذن بالمصدر (stage4_boxes مثلاً)
            if (!Schema::hasColumn('delivery_notes', 'source_type')) {
                $table->string('source_type', 50)->nullable()->after('print_count')->comment('نوع المصدر: stage4_box, material_batch, etc');
            }
            
            if (!Schema::hasColumn('delivery_notes', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type')->comment('ID المصدر');
                $table->index(['source_type', 'source_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            
            if (Schema::hasColumn('delivery_notes', 'print_count')) {
                $table->dropColumn('print_count');
            }
            
            if (Schema::hasColumn('delivery_notes', 'source_id')) {
                $table->dropIndex(['source_type', 'source_id']);
                $table->dropColumn('source_id');
            }
            
            if (Schema::hasColumn('delivery_notes', 'source_type')) {
                $table->dropColumn('source_type');
            }
        });
    }
};
