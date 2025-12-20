<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CentralServerService;

class TestSyncConnection extends Command
{
    protected $signature = 'sync:test-connection';
    protected $description = 'اختبار الاتصال بالسيرفر الأونلاين';

    public function handle()
    {
        $this->info('🔄 جاري اختبار الاتصال بالسيرفر الأونلاين...');
        $this->newLine();

        try {
            $tests = CentralServerService::test();

            $this->line("═══════════════════════════════════════════════════════");
            $this->line("🌐 السيرفر: " . config('sync.central_server_url'));
            $this->line("💾 Device ID: " . (config('sync.device_id') ?? config('sync.local_server_id')));
            $this->line("═══════════════════════════════════════════════════════");
            $this->newLine();

            $this->line("📊 نتائج الاختبار:");
            $this->newLine();
            
            // Test 1: Connection
            if ($tests['connection']) {
                $this->info('✅ الاتصال بالسيرفر: ناجح');
            } else {
                $this->error('❌ الاتصال بالسيرفر: فشل');
            }

            // Test 2: Authentication
            if ($tests['authentication']) {
                $this->info('✅ المصادقة (Authentication): ناجح');
            } else {
                $this->error('❌ المصادقة (Authentication): فشل');
            }

            // Test 3: Push
            if ($tests['push']) {
                $this->info('✅ Push (رفع البيانات): ناجح');
            } else {
                $this->warn('⚠️  Push (رفع البيانات): فشل (متوقع للبيانات التجريبية)');
            }

            // Test 4: Pull
            if ($tests['pull']) {
                $this->info('✅ Pull (سحب البيانات): ناجح');
            } else {
                $this->warn('⚠️  Pull (سحب البيانات): فشل');
            }

            $this->newLine();
            
            if ($tests['connection'] && $tests['authentication']) {
                $this->info('🎉 الاتصال بالسيرفر الأونلاين يعمل بنجاح!');
                return 0;
            } else {
                $this->error('❌ فشل الاتصال بالسيرفر الأونلاين');
                $this->newLine();
                $this->warn('💡 تحقق من:');
                $this->line('  1. أن CENTRAL_SERVER_URL صحيح في .env');
                $this->line('  2. أن CENTRAL_SERVER_TOKEN صحيح');
                $this->line('  3. أن السيرفر الأونلاين يعمل');
                $this->line('  4. أن الإنترنت متصل');
                $this->line('  5. أن API routes موجودة على السيرفر الأونلاين');
                
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ خطأ في الاتصال:');
            $this->line($e->getMessage());
            $this->newLine();
            $this->warn('💡 تحقق من:');
            $this->line('  1. أن CENTRAL_SERVER_URL صحيح في .env: ' . config('sync.central_server_url'));
            $this->line('  2. أن CENTRAL_SERVER_TOKEN موجود: ' . (config('sync.central_server_token') ? 'نعم' : 'لا'));
            $this->line('  3. أن السيرفر الأونلاين يعمل');
            $this->line('  4. أن الإنترنت متصل');
            
            return 1;
        }
    }
}
