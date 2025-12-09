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
        Schema::create('coil_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coil_id')->constrained('delivery_note_coils')->onDelete('cascade');
            $table->decimal('transfer_weight', 10, 3)->comment('الوزن المنقول (كجم)');
            $table->string('production_barcode')->unique()->comment('باركود الإنتاج');
            $table->string('warehouse_barcode')->nullable()->unique()->comment('باركود المخزن للجزء المتبقي');
            $table->foreignId('transferred_by')->constrained('users')->comment('من قام بالنقل');
            $table->timestamp('transferred_at')->useCurrent()->comment('وقت النقل');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();

            // Indexes
            $table->index('coil_id');
            $table->index('production_barcode');
            $table->index('warehouse_barcode');
            $table->index('transferred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coil_transfers');
    }
};
