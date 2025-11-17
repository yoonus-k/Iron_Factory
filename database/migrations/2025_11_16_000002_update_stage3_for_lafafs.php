<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * تحديث المرحلة الثالثة:
     * 1. تغيير coil إلى lafaf (لفاف)
     * 2. إزالة dye_weight و plastic_weight من الإدخال
     * 3. حساب الفرق تلقائياً (total_weight - base_weight)
     */
    public function up(): void
    {
        // تحديث الحقول
        Schema::table('stage3_coils', function (Blueprint $table) {
            // تغيير التعليقات
            $table->string('coil_number', 50)->nullable()->comment('رقم اللفاف')->change();
            $table->string('coil_number_en', 50)->nullable()->comment('Lafaf number')->change();
            
            // إضافة حقل input_weight (من المرحلة السابقة)
            if (!Schema::hasColumn('stage3_coils', 'input_weight')) {
                $table->decimal('input_weight', 10, 3)->after('wire_size_en')->comment('وزن الدخول من المرحلة السابقة');
            }
            
            // تغيير base_weight إلى output_weight للتوافق
            // base_weight الآن هو نفسه total_weight (الوزن الشامل)
            // الفرق بينهما هو dye_weight + plastic_weight
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage3_coils', function (Blueprint $table) {
            $table->string('coil_number', 50)->nullable()->comment('رقم الملف')->change();
            $table->string('coil_number_en', 50)->nullable()->comment('Coil number')->change();
            
            if (Schema::hasColumn('stage3_coils', 'input_weight')) {
                $table->dropColumn('input_weight');
            }
        });
    }
};
