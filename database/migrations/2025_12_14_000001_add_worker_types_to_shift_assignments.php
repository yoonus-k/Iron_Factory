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
        Schema::table('shift_assignments', function (Blueprint $table) {
            // تمييز نوع العمال
            if (!Schema::hasColumn('shift_assignments', 'individual_worker_ids')) {
                $table->json('individual_worker_ids')->nullable()->comment('قائمة العمال الأفراد');
            }
            if (!Schema::hasColumn('shift_assignments', 'team_worker_ids')) {
                $table->json('team_worker_ids')->nullable()->comment('قائمة فرق العمال');
            }
            if (!Schema::hasColumn('shift_assignments', 'team_groups')) {
                $table->json('team_groups')->nullable()->comment('مجموعات العمال مع بيانات الفريق');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('shift_assignments', 'individual_worker_ids')) {
                $table->dropColumn('individual_worker_ids');
            }
            if (Schema::hasColumn('shift_assignments', 'team_worker_ids')) {
                $table->dropColumn('team_worker_ids');
            }
            if (Schema::hasColumn('shift_assignments', 'team_groups')) {
                $table->dropColumn('team_groups');
            }
        });
    }
};
