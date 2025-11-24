#!/usr/bin/env php
<?php
/**
 * اختبار سريع لإرسال البريد من سطر الأوامر
 * استخدام: php artisan tinker < quick_email_test.php
 */

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Str;

// الحصول على أول مستخدم
$user = User::first();

if (!$user) {
    echo "❌ لا يوجد مستخدمين في النظام!\n";
    exit;
}

echo "📧 إرسال اختبار البريد...\n";
echo "المستخدم: " . $user->name . "\n";
echo "البريد: " . $user->email . "\n";

try {
    // إنشاء كلمة مرور عشوائية
    $testPassword = Str::random(12);

    // محاولة الإرسال
    Mail::to($user->email)->send(new UserCredentialsMail($user, $testPassword));

    echo "\n✅ تم إرسال البريد بنجاح!\n";
    echo "يجب أن تصل الرسالة إلى البريد الإلكتروني خلال دقائق.\n";
} catch (\Exception $e) {
    echo "\n❌ فشل الإرسال:\n";
    echo "الخطأ: " . $e->getMessage() . "\n";
    echo "\nالسبب المحتمل:\n";

    if (strpos($e->getMessage(), 'Invalid credentials') !== false) {
        echo "- كلمة مرور Gmail غير صحيحة\n";
        echo "- استخدم App Password بدلاً من كلمة المرور العادية\n";
    } elseif (strpos($e->getMessage(), 'EHLO') !== false || strpos($e->getMessage(), 'tls') !== false) {
        echo "- مشكلة في الاتصال بـ SMTP server\n";
        echo "- تأكد من أن البيانات صحيحة في .env\n";
    } else {
        echo "- تحقق من ملف السجل: storage/logs/laravel.log\n";
    }
}
?>
