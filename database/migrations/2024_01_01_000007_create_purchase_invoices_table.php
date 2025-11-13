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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 100)->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 10)->default('SAR');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('invoice_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
