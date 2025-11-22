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
        Schema::table('notifications', function (Blueprint $table) {
            // إضافة indexes لتحسين الأداء عند البحث والفلترة
            // إذا كانت هذه الأعمدة موجودة بالفعل

            // البحث السريع عن الإشعارات غير المقروءة
            if (!Schema::hasIndex('notifications', 'notifications_is_read_created_at_index')) {
                $table->index(['is_read', 'created_at']);
            }

            // البحث السريع عن الإشعارات حسب النوع
            if (!Schema::hasIndex('notifications', 'notifications_type_index')) {
                $table->index('type');
            }

            // البحث السريع عن الإشعارات حسب المستخدم المنشئ
            if (!Schema::hasIndex('notifications', 'notifications_created_by_index')) {
                $table->index('created_by');
            }

            // البحث السريع عن الإشعارات حسب المستخدم المستقبل
            if (!Schema::hasIndex('notifications', 'notifications_user_id_index')) {
                $table->index('user_id');
            }

            // تحسين استعلامات الفرز والتصفية المعقدة
            if (!Schema::hasIndex('notifications', 'notifications_compound_index')) {
                $table->index(['is_read', 'created_at', 'created_by']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndexIfExists('notifications_is_read_created_at_index');
            $table->dropIndexIfExists('notifications_type_index');
            $table->dropIndexIfExists('notifications_created_by_index');
            $table->dropIndexIfExists('notifications_user_id_index');
            $table->dropIndexIfExists('notifications_compound_index');
        });
    }
};
