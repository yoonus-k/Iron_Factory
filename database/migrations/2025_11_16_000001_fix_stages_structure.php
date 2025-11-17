<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * إصلاح هيكلة جداول المراحل لدعم:
     * 1. القفز بين المراحل (warehouse → stage2 مباشرة)
     * 2. توحيد الحقول في جميع المراحل
     * 3. جعل العلاقات اختيارية بدلاً من إلزامية
     */
    public function up(): void
    {
        // Fix stage2_processed table
        Schema::table('stage2_processed', function (Blueprint $table) {
            // Make stage1_id nullable (optional)
            $table->dropForeign(['stage1_id']);
            $table->foreignId('stage1_id')->nullable()->change();
            $table->foreign('stage1_id')->references('id')->on('stage1_stands')->onDelete('set null');
            
            // Add material_id for direct warehouse → stage2
            $table->foreignId('material_id')->nullable()->after('stage1_id')->constrained()->onDelete('cascade');
            
            // Add wire_size (unified with stage1)
            if (!Schema::hasColumn('stage2_processed', 'wire_size')) {
                $table->string('wire_size', 20)->nullable()->after('material_id')->comment('حجم السلك');
            }
            
            // Add notes field
            if (!Schema::hasColumn('stage2_processed', 'notes')) {
                $table->text('notes')->nullable()->after('process_details_en');
            }
        });

        // Fix stage3_coils table
        Schema::table('stage3_coils', function (Blueprint $table) {
            // Make stage2_id nullable (optional)
            $table->dropForeign(['stage2_id']);
            $table->foreignId('stage2_id')->nullable()->change();
            $table->foreign('stage2_id')->references('id')->on('stage2_processed')->onDelete('set null');
            
            // Add material_id for direct jumps
            if (!Schema::hasColumn('stage3_coils', 'material_id')) {
                $table->foreignId('material_id')->nullable()->after('stage2_id')->constrained()->onDelete('cascade');
            }
            
            // Add stage1_id for stage1 → stage3 jump
            if (!Schema::hasColumn('stage3_coils', 'stage1_id')) {
                $table->foreignId('stage1_id')->nullable()->after('material_id')->constrained('stage1_stands')->onDelete('set null');
            }
            
            // Add notes
            if (!Schema::hasColumn('stage3_coils', 'notes')) {
                $table->text('notes')->nullable()->after('plastic_type_en');
            }
        });

        // Fix stage4_boxes table
        Schema::table('stage4_boxes', function (Blueprint $table) {
            // Add parent_barcode for consistency
            if (!Schema::hasColumn('stage4_boxes', 'parent_barcode')) {
                $table->string('parent_barcode', 50)->nullable()->after('barcode')->comment('آخر باركود في السلسلة');
            }
            
            // Add material_id for direct jumps
            if (!Schema::hasColumn('stage4_boxes', 'material_id')) {
                $table->foreignId('material_id')->nullable()->after('parent_barcode')->constrained()->onDelete('cascade');
            }
            
            // Add notes
            if (!Schema::hasColumn('stage4_boxes', 'notes')) {
                $table->text('notes')->nullable()->after('shipping_address_en');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage2_processed', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropColumn(['material_id', 'wire_size', 'notes']);
            
            $table->dropForeign(['stage1_id']);
            $table->foreignId('stage1_id')->nullable(false)->change();
            $table->foreign('stage1_id')->references('id')->on('stage1_stands')->onDelete('cascade');
        });

        Schema::table('stage3_coils', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropForeign(['stage1_id']);
            $table->dropColumn(['material_id', 'stage1_id', 'notes']);
            
            $table->dropForeign(['stage2_id']);
            $table->foreignId('stage2_id')->nullable(false)->change();
            $table->foreign('stage2_id')->references('id')->on('stage2_processed')->onDelete('cascade');
        });

        Schema::table('stage4_boxes', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropColumn(['parent_barcode', 'material_id', 'notes']);
        });
    }
};
