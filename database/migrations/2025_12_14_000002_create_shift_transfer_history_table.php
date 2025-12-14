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
        Schema::create('shift_transfer_history', function (Blueprint $table) {
            $table->id();

            // معلومات النقل الأساسية
            $table->foreignId('shift_id')
                ->constrained('shift_assignments')
                ->onDelete('cascade');

            $table->foreignId('from_supervisor_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('to_supervisor_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // البيانات القديمة والجديدة
            $table->json('old_data')->nullable()->comment('البيانات القديمة (العمال القدامى والمجموعات)');
            $table->json('new_data')->nullable()->comment('البيانات الجديدة (العمال الجدد والمجموعات)');

            // تفاصيل النقل
            $table->text('transfer_notes')->nullable();
            $table->string('transfer_type')->comment('individual, team, mixed'); // نوع النقل

            // المستخدم الذي قام بالنقل
            $table->foreignId('transferred_by')
                ->constrained('users')
                ->onDelete('cascade');

            // حالة النقل
            $table->enum('status', ['pending', 'completed', 'approved', 'rejected'])->default('completed');

            // معلومات الموافقة
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('shift_id');
            $table->index('from_supervisor_id');
            $table->index('to_supervisor_id');
            $table->index('transferred_by');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_transfer_history');
    }
};
