<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type')->comment('اسم الموديل - App\\Models\\Material');
            $table->unsignedBigInteger('translatable_id')->comment('ID الموديل');
            $table->string('locale', 10)->comment('اللغة: ar, en');
            $table->string('key')->comment('مفتاح الترجمة: name, notes, shelf_location');
            $table->longText('value')->comment('قيمة الترجمة');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['translatable_type', 'translatable_id']);
            $table->index(['translatable_type', 'translatable_id', 'locale']);
            $table->index('locale');
            $table->index('key');

            // Unique constraint
            $table->unique(['translatable_type', 'translatable_id', 'locale', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
