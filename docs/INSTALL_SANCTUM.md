# تثبيت Laravel Sanctum

## المشكلة
Laravel Sanctum غير مثبت، وهو مطلوب لعمل API authentication

## الحل

### الخطوة 1: تثبيت Sanctum

افتح Terminal (PowerShell أو CMD) وشغّل:

```bash
# إذا كان Composer مثبت عالمياً
composer require laravel/sanctum

# أو إذا كنت تستخدم PHP مباشرة
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar require laravel/sanctum
```

### الخطوة 2: نشر ملفات Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### الخطوة 3: تشغيل Migrations

```bash
php artisan migrate
```

### الخطوة 4: إضافة Sanctum middleware

الملف `bootstrap/app.php` - تأكد من إضافة:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
    
    // ... بقية الـ middleware
})
```

### الخطوة 5: تحديث User Model

في ملف `app/Models/User.php`:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    // ... بقية الكود
}
```

---

## إذا لم يكن لديك Composer

### تحميل Composer:

1. اذهب إلى: https://getcomposer.org/download/
2. حمّل **Composer-Setup.exe** لـ Windows
3. ثبّت Composer
4. أعد فتح PowerShell
5. شغّل الأوامر أعلاه

---

## البديل: تثبيت يدوي

إذا لم تستطع تثبيت Composer، يمكنك:

1. تحميل Sanctum من GitHub: https://github.com/laravel/sanctum
2. ضعه في `vendor/laravel/sanctum`
3. لكن هذا **غير موصى به** - يفضل استخدام Composer

---

## اختبار بعد التثبيت

```bash
# تنظيف الـ cache
php artisan optimize:clear

# اختبار Routes
php artisan route:list --path=api/sync

# اختبار الاتصال
php artisan sync:test-connection
```

---

## ملاحظة مهمة

⚠️ **يجب تثبيت Sanctum على السيرفر الأونلاين أيضاً!**

نفس الخطوات يجب تطبيقها على السيرفر الأونلاين عبر:
- cPanel Terminal
- أو SSH

---

## ملفات إضافية مطلوبة

بعد تثبيت Sanctum، تأكد من:

### 1. config/auth.php يحتوي على:
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    
    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => null,
    ],
],
```

### 2. bootstrap/app.php يحتوي على:
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',  // ✅ مهم!
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

---

✅ بعد التثبيت، سيعمل نظام المزامنة بشكل كامل!
