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
        Schema::table('shift_assignments', function (Blueprint $table) {
            $table->string('shift_code')->nullable()->after('id')->comment('كود الوردية');
            $table->enum('shift_type', ['morning', 'evening', 'night'])->default('morning')->after('shift_code')->comment('نوع الوردية');
            $table->foreignId('supervisor_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete()->comment('المشرف على الوردية');
            $table->text('notes')->nullable()->after('status')->comment('ملاحظات');
            $table->boolean('is_active')->default(true)->after('notes')->comment('حالة التفعيل');
            $table->integer('total_workers')->default(0)->after('is_active')->comment('عدد العمال المشاركين');
            $table->json('worker_ids')->nullable()->after('total_workers')->comment('معرفات العمال المشاركين');
            
            $table->index('shift_code');
            $table->index('shift_type');
            $table->index('supervisor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            $table->dropIndex(['shift_code']);
            $table->dropIndex(['shift_type']);
            $table->dropIndex(['supervisor_id']);
            
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn([
                'shift_code',
                'shift_type',
                'supervisor_id',
                'notes',
                'is_active',
                'total_workers',
                'worker_ids'
            ]);
        });
    }
};
