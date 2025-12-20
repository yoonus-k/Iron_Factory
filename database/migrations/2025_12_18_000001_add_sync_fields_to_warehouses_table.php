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
        Schema::table('warehouses', function (Blueprint $table) {
            $table->boolean('is_synced')->default(false)->after('is_active')->comment('هل تمت المزامنة');
            $table->string('sync_status', 50)->default('pending')->after('is_synced')->comment('حالة المزامنة');
            $table->timestamp('synced_at')->nullable()->after('sync_status')->comment('وقت المزامنة');
            $table->string('local_id', 100)->nullable()->after('synced_at')->comment('المعرف المحلي');
            $table->string('device_id', 100)->nullable()->after('local_id')->comment('معرف الجهاز');
            
            $table->index('is_synced');
            $table->index('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropIndex(['is_synced']);
            $table->dropIndex(['sync_status']);
            
            $table->dropColumn([
                'is_synced',
                'sync_status',
                'synced_at',
                'local_id',
                'device_id',
            ]);
        });
    }
};
