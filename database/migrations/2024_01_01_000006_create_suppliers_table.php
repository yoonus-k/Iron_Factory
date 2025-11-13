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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم المورد بالعربية');
            $table->string('name_en')->nullable()->comment('اسم المورد بالإنجليزية');
            $table->string('contact_person')->nullable()->comment('جهة التواصل بالعربية');
            $table->string('contact_person_en')->nullable()->comment('جهة التواصل بالإنجليزية');
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable()->comment('العنوان بالعربية');
            $table->text('address_en')->nullable()->comment('العنوان بالإنجليزية');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
