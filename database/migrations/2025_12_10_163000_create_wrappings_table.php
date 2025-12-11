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
        Schema::create('wrappings', function (Blueprint $table) {
            $table->id();
            $table->string('wrapping_number')->unique()->comment('رقم اللفاف');
            $table->decimal('weight', 10, 2)->comment('وزن اللفاف (كجم)');
            $table->text('description')->nullable()->comment('وصف اللفاف');
            $table->boolean('is_active')->default(true)->comment('حالة اللفاف');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wrappings');
    }
};
