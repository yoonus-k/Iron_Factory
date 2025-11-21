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
        // إضافة الوزن إلى جدول الفواتير
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices', 'weight')) {
                $table->decimal('weight', 12, 3)->nullable()->comment('الوزن الإجمالي')->after('total_amount');
            }
            if (!Schema::hasColumn('purchase_invoices', 'weight_unit')) {
                $table->string('weight_unit', 50)->default('كجم')->comment('وحدة الوزن')->after('weight');
            }
        });

        // إضافة الوزن إلى جدول بنود الفاتورة
        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoice_items', 'weight')) {
                $table->decimal('weight', 12, 3)->nullable()->comment('وزن المنتج')->after('unit');
            }
            if (!Schema::hasColumn('purchase_invoice_items', 'weight_unit')) {
                $table->string('weight_unit', 50)->default('كجم')->comment('وحدة الوزن')->after('weight');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices', 'weight')) {
                $table->dropColumn('weight');
            }
            if (Schema::hasColumn('purchase_invoices', 'weight_unit')) {
                $table->dropColumn('weight_unit');
            }
        });

        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoice_items', 'weight')) {
                $table->dropColumn('weight');
            }
            if (Schema::hasColumn('purchase_invoice_items', 'weight_unit')) {
                $table->dropColumn('weight_unit');
            }
        });
    }
};
