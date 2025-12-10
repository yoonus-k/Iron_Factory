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
        Schema::table('stage3_coils', function (Blueprint $table) {
            $table->foreignId('wrapping_id')->nullable()->after('stage2_id')->constrained('wrappings')->onDelete('set null')->comment('معرف اللفاف');
            $table->decimal('wrapping_weight', 10, 2)->nullable()->after('wrapping_id')->comment('وزن اللفاف المستخدم');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage3_coils', function (Blueprint $table) {
            $table->dropForeign(['wrapping_id']);
            $table->dropColumn(['wrapping_id', 'wrapping_weight']);
        });
    }
};
