<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جداول المزامنة الأساسية
     * 
     * الجداول:
     * 1. sync_logs - سجل عمليات المزامنة
     * 2. sync_history - السجل المركزي للمزامنة
     * 3. pending_syncs - العمليات المعلقة (الأوفلاين)
     */
    public function up(): void
    {
        // 1. جدول سجل المزامنة (لتتبع عمليات المزامنة)
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('المستخدم');
            
            // نوع البيانات التي تمت مزامنتها
            $table->string('entity_type', 50)->comment('نوع الكيان (material, delivery_note, etc)');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('معرف الكيان');
            
            // حالة المزامنة
            $table->enum('status', ['pending', 'synced', 'failed'])->default('pending')->comment('حالة المزامنة');
            
            // تفاصيل الخطأ إن وجد
            $table->text('error_message')->nullable()->comment('رسالة الخطأ');
            
            // البيانات
            $table->json('data_payload')->nullable()->comment('البيانات التي تمت مزامنتها');
            
            // الأوقات
            $table->timestamp('synced_at')->nullable()->comment('وقت المزامنة');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('status');
            $table->index('created_at');
        });

        // 2. جدول السجل المركزي (للمدير - لتتبع كل التغييرات)
        Schema::create('sync_histories', function (Blueprint $table) {
            $table->id();
            
            // من الموظف/المدير
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('المستخدم');
            $table->enum('user_type', ['staff', 'manager', 'admin'])->default('staff')->comment('نوع المستخدم');
            
            // نوع البيانة
            $table->string('entity_type', 50)->comment('نوع الكيان');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('معرف الكيان');
            
            // البيانة الفعلية (JSON)
            $table->json('data')->nullable()->comment('البيانات الكاملة');
            
            // معلومات المزامنة
            $table->enum('action', ['create', 'update', 'delete'])->comment('نوع العملية');
            $table->timestamp('synced_from_local')->nullable()->comment('وقت المزامنة من الجهاز المحلي');
            $table->timestamp('synced_to_server')->nullable()->comment('وقت المزامنة للسيرفر');
            
            // للمدير: متى سحبها
            $table->timestamp('pulled_by_manager_at')->nullable()->comment('متى سحبها المدير');
            
            // معلومات إضافية
            $table->string('device_id', 100)->nullable()->comment('معرف الجهاز');
            $table->ipAddress('ip_address')->nullable()->comment('عنوان IP');
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index(['entity_type', 'entity_id']);
            $table->index('synced_to_server');
            $table->index('action');
            $table->index('created_at');
        });

        // 3. جدول العمليات المعلقة (للأوفلاين)
        Schema::create('pending_syncs', function (Blueprint $table) {
            $table->id();
            
            // معلومات المستخدم
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('المستخدم');
            
            // معلومات الكيان
            $table->string('entity_type', 50)->comment('نوع الكيان');
            $table->string('local_id', 100)->nullable()->comment('المعرف المحلي (UUID)');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('المعرف بعد المزامنة');
            
            // العملية والبيانات
            $table->enum('action', ['create', 'update', 'delete'])->comment('نوع العملية');
            $table->json('data')->comment('البيانات المطلوب مزامنتها');
            $table->json('related_data')->nullable()->comment('بيانات مرتبطة (علاقات)');
            
            // الأولوية والحالة
            $table->integer('priority')->default(0)->comment('الأولوية (كلما زاد الرقم زادت الأولوية)');
            $table->enum('status', ['pending', 'processing', 'failed', 'synced'])->default('pending')->comment('الحالة');
            
            // محاولات المزامنة
            $table->integer('retry_count')->default(0)->comment('عدد المحاولات');
            $table->integer('max_retries')->default(3)->comment('أقصى عدد للمحاولات');
            $table->text('last_error')->nullable()->comment('آخر خطأ');
            
            // الأوقات
            $table->timestamp('created_at_local')->nullable()->comment('وقت الإنشاء المحلي');
            $table->timestamp('last_attempt_at')->nullable()->comment('آخر محاولة مزامنة');
            $table->timestamp('synced_at')->nullable()->comment('وقت المزامنة الناجحة');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['entity_type', 'status']);
            $table->index('local_id');
            $table->index(['status', 'priority']);
            $table->index('created_at');
        });

        // 4. جدول تتبع آخر مزامنة للمستخدم (لتحسين الأداء)
        Schema::create('user_last_syncs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade')->comment('المستخدم');
            
            $table->timestamp('last_pull_at')->nullable()->comment('آخر سحب من السيرفر');
            $table->timestamp('last_push_at')->nullable()->comment('آخر رفع للسيرفر');
            
            $table->integer('pending_count')->default(0)->comment('عدد العمليات المعلقة');
            $table->integer('failed_count')->default(0)->comment('عدد العمليات الفاشلة');
            
            $table->json('sync_stats')->nullable()->comment('إحصائيات المزامنة');
            
            $table->timestamps();
            
            $table->index('last_pull_at');
            $table->index('last_push_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_last_syncs');
        Schema::dropIfExists('pending_syncs');
        Schema::dropIfExists('sync_histories');
        Schema::dropIfExists('sync_logs');
    }
};
