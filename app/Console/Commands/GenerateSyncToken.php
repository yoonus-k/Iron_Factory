<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateSyncToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:generate-token 
                            {device-name=Local-Device-1 : اسم الجهاز}
                            {--user-email=admin@system.com : بريد المستخدم}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'توليد API Token للأجهزة المحلية للاتصال بالسيرفر المركزي';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deviceName = $this->argument('device-name');
        $userEmail = $this->option('user-email');

        // البحث عن المستخدم
        $user = User::where('email', $userEmail)->first();

        if (!$user) {
            $this->error("❌ المستخدم غير موجود: {$userEmail}");
            $this->info("💡 المستخدمون المتاحون:");
            
            User::select('id', 'name', 'email')->get()->each(function ($u) {
                $this->line("  - {$u->email} ({$u->name})");
            });
            
            return 1;
        }

        // توليد التوكن
        $token = $user->createToken($deviceName, ['sync:*']);
        $plainTextToken = $token->plainTextToken;

        // عرض النتيجة
        $this->info("✅ تم توليد التوكن بنجاح!");
        $this->newLine();
        
        $this->line("═══════════════════════════════════════════════════════");
        $this->line("📱 اسم الجهاز: {$deviceName}");
        $this->line("👤 المستخدم: {$user->name} ({$user->email})");
        $this->line("🆔 Token ID: {$token->accessToken->id}");
        $this->line("═══════════════════════════════════════════════════════");
        $this->newLine();
        
        $this->warn("🔑 API Token (انسخه إلى .env):");
        $this->newLine();
        $this->line("CENTRAL_SERVER_TOKEN={$plainTextToken}");
        $this->newLine();
        
        $this->line("═══════════════════════════════════════════════════════");
        $this->info("📋 انسخ هذا السطر إلى ملف .env الخاص بالجهاز المحلي:");
        $this->line("CENTRAL_SERVER_TOKEN={$plainTextToken}");
        $this->line("═══════════════════════════════════════════════════════");
        
        return 0;
    }
}
