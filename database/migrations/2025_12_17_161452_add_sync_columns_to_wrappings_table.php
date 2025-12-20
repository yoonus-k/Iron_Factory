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
        Schema::table('wrappings', function (Blueprint $table) {
            // أعمدة المزامنة - تحقق من عدم وجودها أولاً
            if (!Schema::hasColumn('wrappings', 'local_id')) {
                $table->string('local_id')->unique()->nullable()->after('id')->comment('UUID للمزامنة');
            }
            if (!Schema::hasColumn('wrappings', 'is_synced')) {
                $table->boolean('is_synced')->default(false)->after('is_active')->comment('تمت المزامنة');
            }
            if (!Schema::hasColumn('wrappings', 'sync_status')) {
                $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending')->after('is_synced')->comment('حالة المزامنة');
            }
            if (!Schema::hasColumn('wrappings', 'device_id')) {
                $table->string('device_id')->nullable()->after('sync_status')->comment('معرف الجهاز');
            }
            if (!Schema::hasColumn('wrappings', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('device_id')->comment('آخر مزامنة');
            }
            if (!Schema::hasColumn('wrappings', 'sync_error')) {
                $table->text('sync_error')->nullable()->after('last_synced_at')->comment('خطأ المزامنة');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wrappings', function (Blueprint $table) {
            $table->dropColumn(['local_id', 'is_synced', 'sync_status', 'device_id', 'last_synced_at', 'sync_error']);
        });
    }
};
