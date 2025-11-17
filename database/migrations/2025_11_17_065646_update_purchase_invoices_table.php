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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            // Add new columns
            $table->string('invoice_reference_number')->nullable()->after('invoice_number')->comment('رقم المرجع للفاتورة');
            $table->string('payment_terms')->nullable()->after('due_date')->comment('شروط الدفع');
            $table->unsignedBigInteger('recorded_by')->nullable()->after('status')->comment('من سجل الفاتورة');
            $table->unsignedBigInteger('approved_by')->nullable()->after('recorded_by')->comment('من وافق على الفاتورة');
            $table->timestamp('approved_at')->nullable()->after('approved_by')->comment('وقت الموافقة');
            $table->timestamp('paid_at')->nullable()->after('approved_at')->comment('تاريخ الدفع');
            $table->boolean('is_active')->default(true)->after('paid_at')->comment('هل الفاتورة نشطة');

            // Update status column to enum
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'paid'])
                  ->default('draft')
                  ->change();

            // Add foreign keys
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropForeignKey(['recorded_by']);
            $table->dropForeignKey(['approved_by']);
            $table->dropColumn([
                'invoice_reference_number',
                'payment_terms',
                'recorded_by',
                'approved_by',
                'approved_at',
                'paid_at',
                'is_active',
            ]);
        });
    }
};
