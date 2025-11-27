<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تغيير عمود type من ENUM إلى VARCHAR لدعم أنواع إضافية
        DB::statement("ALTER TABLE delivery_notes MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'incoming'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إرجاع العمود إلى ENUM (فقط إذا كانت القيم الموجودة متوافقة)
        DB::statement("ALTER TABLE delivery_notes MODIFY COLUMN type ENUM('incoming', 'outgoing') NOT NULL DEFAULT 'incoming'");
    }
};
