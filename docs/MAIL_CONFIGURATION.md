# ⚙️ إعدادات البريد الإلكتروني للنظام

## 📧 الإعدادات الحالية

يتم تخزين إعدادات البريد في ملف `.env` في جذر المشروع:

```
c:\xampp\htdocs\fawtmaintest\Iron_Factory\.env
```

---

## 🔧 الإعدادات الموجودة حالياً

```env
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Iron Factory"
```

**الحالة الحالية**: البريد يُحفظ في السجلات فقط (testing mode)

---

## 🚀 لتفعيل البريد الفعلي اختر أحد الخيارات التالية:

### ✅ الخيار 1: استخدام Gmail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Iron Factory"
```

**خطوات:**
1. افتح [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
2. انسخ كلمة المرور
3. الصقها في `MAIL_PASSWORD`

---

### ✅ الخيار 2: استخدام Mailtrap (للاختبار)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="Iron Factory"
```

**خطوات:**
1. سجل في [mailtrap.io](https://mailtrap.io)
2. انسخ بيانات SMTP
3. ضعها في الملف

---

### ✅ الخيار 3: استخدام Sendmail (Linux/Mac)

```env
MAIL_MAILER=sendmail
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="Iron Factory"
SENDMAIL_PATH="/usr/sbin/sendmail -t -i"
```

---

### ✅ الخيار 4: استخدام Postfix

```env
MAIL_MAILER=sendmail
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="Iron Factory"
```

---

## 📝 التعديل على الملف

### Windows (XAMPP):
```
C:\xampp\htdocs\fawtmaintest\Iron_Factory\.env
```

### مثال كامل:
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:NyKkbwZsG3+e0rwAZRdjYLR9ZVN0osk7Q5ynugjgCdY=
APP_DEBUG=true
APP_URL=http://localhost:8000

...

# إعدادات البريد
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Iron Factory"

QUEUE_CONNECTION=sync
```

---

## 🧪 اختبار البريد

بعد التعديل على الإعدادات:

### 1. امسح التخزين المؤقت
```bash
php artisan config:clear
php artisan cache:clear
```

### 2. اختبر الإرسال
```bash
php artisan tinker

# في Tinker:
$user = App\Models\User::first();
Mail::send(new App\Mail\UserCredentialsMail($user, 'TestPassword123'));
```

### 3. تحقق من الإرسال
- Gmail: تحقق من الرسائل المرسلة
- Mailtrap: اعرضها في لوحة التحكم
- Sendmail: تحقق من السجلات

---

## 🔐 نصائح الأمان

| ⚠️ | لا تفعل | ✅ | افعل |
|----|--------|------|-----|
| ❌ | ضع كلمات المرور في Git | ✅ | استخدم `.env` فقط |
| ❌ | اترك كلمات المرور في الكود | ✅ | استخدم المتغيرات |
| ❌ | شارك `.env` مع أحد | ✅ | احفظه محلياً فقط |
| ❌ | استخدم كلمات مرور ضعيفة | ✅ | استخدم كلمات قوية |

---

## 📋 قائمة تحقق

- [ ] تم اختيار خدمة البريد
- [ ] تم الحصول على بيانات الوصول
- [ ] تم تحديث ملف `.env`
- [ ] تم مسح التخزين المؤقت
- [ ] تم اختبار الإرسال
- [ ] تم التحقق من استقبال البريد

---

## 🆘 استكشاف الأخطاء

### المشكلة: البريد لا يُرسل
**الحل:**
1. تحقق من إعدادات `.env`
2. تحقق من بيانات الوصول
3. تحقق من اتصال الإنترنت
4. تحقق من رسائل الخطأ في السجلات

### المشكلة: البريد يذهب للرسائل غير المرغوب (Spam)
**الحل:**
1. تحقق من عنوان البريد
2. أضفه للجهات الموثوقة
3. استخدم نطاقك الخاص

### المشكلة: اتصال مرفوض
**الحل:**
1. تحقق من رقم المنفذ (Port)
2. تحقق من بيانات الوصول
3. تحقق من جدار الحماية

---

## 📚 موارد مفيدة

- [Laravel Mail Documentation](https://laravel.com/docs/mail)
- [Gmail App Passwords](https://myaccount.google.com/apppasswords)
- [Mailtrap.io](https://mailtrap.io)
- [Mailgun](https://www.mailgun.com/)
- [SendGrid](https://sendgrid.com/)

---

## ✨ الحالة الموصى بها للإنتاج

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-secure-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Iron Factory"
```

---

**آخر تحديث**: 24 نوفمبر 2025
**الإصدار**: 1.0
