<?php
/**
 * ملف اختبار إرسال البريد
 * يجب تشغيله بعد تحديث الكود
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;

// البحث عن أول مستخدم نشط
$user = User::where('is_active', true)->first();

if (!$user) {
    echo "❌ لم يتم العثور على مستخدم نشط!";
    exit;
}

echo "🔍 اختبار إرسال البريد للمستخدم: " . $user->name . "\n";
echo "البريد: " . $user->email . "\n\n";

try {
    // محاولة الإرسال
    Mail::to($user->email)->send(new UserCredentialsMail($user, 'TestPassword123'));
    echo "✅ تم إرسال البريد بنجاح!\n";
    echo "يجب أن تصل الرسالة إلى: " . $user->email . "\n";
} catch (\Exception $e) {
    echo "❌ فشل الإرسال:\n";
    echo $e->getMessage() . "\n";
    echo "\nمعلومات التتبع:\n";
    echo $e->getTraceAsString();
}
?>
