<?php
/**
 * اختبار إعدادات البريد
 * افتح هذا الملف في المتصفح للتحقق من إعدادات البريد
 */

// تحميل Laravel
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

// الحصول على المتحكم من التطبيق
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>🔍 فحص إعدادات البريد:</h2>";
echo "<pre style='background: #f5f5f5; padding: 20px; border-radius: 5px;'>";

// عرض إعدادات البريد الحالية
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n";

// التحقق من وجود البيانات
$errors = [];

if (empty(config('mail.mailers.smtp.username'))) {
    $errors[] = "❌ لم يتم تعيين MAIL_USERNAME";
}

if (empty(config('mail.mailers.smtp.password'))) {
    $errors[] = "❌ لم يتم تعيين MAIL_PASSWORD";
}

if (config('mail.mailers.smtp.host') !== 'smtp.gmail.com') {
    $errors[] = "⚠️ MAIL_HOST ليس smtp.gmail.com (الحالي: " . config('mail.mailers.smtp.host') . ")";
}

echo "\n\n";

if (!empty($errors)) {
    echo "❌ المشاكل المكتشفة:\n";
    foreach ($errors as $error) {
        echo "   - $error\n";
    }
} else {
    echo "✅ جميع الإعدادات صحيحة!\n";
}

echo "</pre>";

// محاولة إرسال بريد تجريبي
echo "<hr>";
echo "<h2>📧 محاولة إرسال بريد تجريبي:</h2>";

try {
    Mail::to(config('mail.mailers.smtp.username'))
        ->send(new \App\Mail\UserCredentialsMail(
            new \App\Models\User([
                'id' => 1,
                'name' => 'اختبار',
                'username' => 'test',
                'email' => config('mail.mailers.smtp.username')
            ]),
            'TestPassword123'
        ));

    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ تم إرسال البريد بنجاح إلى: " . config('mail.mailers.smtp.username');
    echo "</div>";
} catch (\Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ فشل الإرسال: " . $e->getMessage();
    echo "</div>";
}
?>
