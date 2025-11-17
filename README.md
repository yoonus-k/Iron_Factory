<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

## 🚀 Iron Factory - نظام إدارة المصنع

نظام متطور لإدارة المستودع والتسليمات والإنتاج في مصنع الحديد.

---

## 🛠️ أوامر Laravel الأساسية

### 📋 إعداد المشروع (Setup)

#### تثبيت المشروع لأول مرة
```bash
# تثبيت المكتبات
composer install

# نسخ ملف الإعدادات
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# تشغيل المايجريشن
php artisan migrate

# إدراج البيانات الأساسية
php artisan db:seed

# ربط المجلدات العامة
php artisan storage:link
```

---

### 🗄️ أوامر Database (قاعدة البيانات)

#### المايجريشن (Migrations)
```bash
# تشغيل جميع المايجريشن
php artisan migrate

# تشغيل مايجريشن معين
php artisan migrate --path=database/migrations/2025_11_17_*.php

# التراجع عن آخر مايجريشن
php artisan migrate:rollback

# التراجع عن آخر batch
php artisan migrate:rollback --step=1

# التراجع عن جميع المايجريشن
php artisan migrate:reset

# إعادة تشغيل جميع المايجريشن (خطر!)
php artisan migrate:refresh

# إعادة تشغيل مع البيانات الأساسية
php artisan migrate:refresh --seed
```

#### السيدر (Seeds)
```bash
# تشغيل جميع السيدر
php artisan db:seed

# تشغيل سيدر معين
php artisan db:seed --class=SupplierSeeder

# إعادة تشغيل مع المايجريشن
php artisan migrate:refresh --seed
```

---

### 🔄 أوامر Cache والتنظيف

#### مسح الـ Cache
```bash
# مسح جميع الـ cache
php artisan cache:clear

# مسح الـ config cache
php artisan config:cache

# مسح الـ view cache
php artisan view:clear

# مسح الـ route cache
php artisan route:cache

# مسح جميع الـ cache والـ views
php artisan optimize:clear
```

#### إعادة بناء الـ Cache
```bash
# إعادة بناء الـ cache
php artisan cache:clear && php artisan config:cache && php artisan route:cache

# إعادة بناء الـ autoload
composer dump-autoload

# إعادة بناء مع optimization
php artisan optimize
```

---

### 📱 أوامر التطوير (Development)

#### تشغيل خادم التطوير
```bash
# تشغيل الخادم على المنفذ الافتراضي (8000)
php artisan serve

# تشغيل على منفذ معين
php artisan serve --port=8001

# تشغيل على جميع الواجهات
php artisan serve --host=0.0.0.0 --port=8000
```

#### Tinker (REPL)
```bash
# فتح Tinker (PHP interactive shell)
php artisan tinker

# أمثلة في Tinker
User::all();
User::find(1)->update(['name' => 'أحمد']);
DB::table('users')->count();
```

---

### 📊 أوامر Artisan المفيدة

#### إدارة الأوامر
```bash
# عرض جميع الأوامر المتاحة
php artisan list

# عرض معلومات أمر معين
php artisan help migrate
```

#### توليد الملفات
```bash
# إنشاء Model مع Migration و Controller
php artisan make:model Material -mcr

# إنشاء Controller
php artisan make:controller WarehouseController

# إنشاء Migration
php artisan make:migration create_materials_table

# إنشاء Seeder
php artisan make:seeder MaterialSeeder

# إنشاء Request (Form Request)
php artisan make:request StoreWarehouseRequest

# إنشاء Service Class
php artisan make:class Services/WarehouseService
```

#### إدارة Routes
```bash
# عرض جميع الـ routes
php artisan route:list

# عرض الـ routes بتفاصيل
php artisan route:list --verbose

# عرض الـ routes لـ Controller معين
php artisan route:list --name=warehouse
```

---

### 🧪 أوامر الاختبار

#### تشغيل الاختبارات
```bash
# تشغيل جميع الاختبارات
php artisan test

# تشغيل ملف اختبار معين
php artisan test tests/Unit/Services/DuplicatePreventionServiceTest.php

# تشغيل مع التفاصيل
php artisan test --verbose

# تشغيل مع Coverage
php artisan test --coverage

# التوقف عند أول فشل
php artisan test --stop-on-failure
```

#### PHPUnit مباشرة
```bash
# تشغيل PHPUnit
./vendor/bin/phpunit

# تشغيل ملف معين
./vendor/bin/phpunit tests/Unit/Services/DuplicatePreventionServiceTest.php
```

---

### 📦 أوامر Composer

#### إدارة المكتبات
```bash
# تثبيت جميع المكتبات
composer install

# تحديث جميع المكتبات
composer update

# تثبيت مكتبة جديدة
composer require vendor/package-name

# حذف مكتبة
composer remove vendor/package-name

# إعادة بناء autoload
composer dump-autoload

# تحسين الأداء
composer dump-autoload --optimize

# التحقق من الأمان
composer audit
```

---

### 🔐 أوامر الأمان

#### إدارة المفاتيح
```bash
# توليد مفتاح جديد
php artisan key:generate

# توليد مفتاح JWT (إذا كنت تستخدمه)
php artisan jwt:secret

# عرض المفتاح الحالي
php artisan key:show
```

#### إدارة قوائم التحكم
```bash
# إنشاء permissions جديدة
php artisan tinker
> Permission::create(['name' => 'register-material']);

# ربط permission مع role
php artisan tinker
> $role->givePermissionTo('register-material');
```

---

### 📧 أوامر البريد الإلكتروني

#### اختبار البريد
```bash
# إرسال بريد تجريبي
php artisan tinker
> Mail::raw('Test', fn($msg) => $msg->to('test@test.com'));

# استخدام Mailtrap للاختبار (بدون إرسال فعلي)
# ✓ قم بتعديل MAIL_DRIVER في .env إلى mailtrap
```

---

### 🎯 أوامر مهمة للإنتاج (Production)

#### التحضير للإنتاج
```bash
# تعطيل وضع التصحيح
APP_DEBUG=false

# تفعيل الـ cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ضغط autoloader
composer install --optimize-autoloader --no-dev

# تشغيل المايجريشن
php artisan migrate --force
```

#### المراقبة والصيانة
```bash
# دخول وضع الصيانة
php artisan down

# الخروج من وضع الصيانة
php artisan up

# عرض سجلات الخطأ
tail -f storage/logs/laravel.log

# مراقبة في الوقت الفعلي
tail -f storage/logs/laravel.log | grep ERROR
```

---

### 🌐 أوامر الوحدات (Modules)

#### إدارة الوحدات (nwidart/laravel-modules)
```bash
# عرض جميع الوحدات
php artisan module:list

# إنشاء وحدة جديدة
php artisan module:make Manufacturing

# حذف وحدة
php artisan module:delete Manufacturing

# تفعيل وحدة
php artisan module:enable Manufacturing

# تعطيل وحدة
php artisan module:disable Manufacturing

# تشغيل مايجريشن وحدة
php artisan module:migrate Manufacturing

# بذر بيانات وحدة
php artisan module:seed Manufacturing
```

---

### 📝 أوامر مفيدة أخرى

#### الأداء والتحسين
```bash
# تحليل الأداء
php artisan optimize

# عرض معلومات الخادم
php artisan serve --info

# تنظيف الملفات المؤقتة
rm -rf bootstrap/cache/*
rm -rf storage/logs/*
```

#### العمل مع Storage
```bash
# ربط المجلد العام
php artisan storage:link

# فحص الملفات المرفوعة
ls -la storage/app/uploads/

# حذف الملفات المؤقتة
php artisan storage:prune
```

---

## 🚦 خطوات التطوير السريعة

### عند بدء العمل
```bash
# 1. تثبيت المكتبات
composer install

# 2. توليد المفتاح
php artisan key:generate

# 3. إعداد قاعدة البيانات
php artisan migrate:refresh --seed

# 4. تشغيل الخادم
php artisan serve

# 5. زيارة التطبيق
# http://localhost:8000
```

### عند الانتهاء من المايجريشن
```bash
# مسح الـ cache
php artisan optimize:clear

# إعادة تشغيل الخادم
# اضغط Ctrl+C ثم php artisan serve
```

### عند التحديث من GitHub
```bash
# سحب التحديثات
git pull origin main

# تثبيت المكتبات الجديدة
composer install

# تشغيل المايجريشن
php artisan migrate

# مسح الـ cache
php artisan optimize:clear

# إعادة تشغيل الخادم
```

---

## ⚠️ أوامر خطيرة (استخدم بحذر!)

```bash
# ⛔ حذف جميع الجداول وإعادة المايجريشن
php artisan migrate:refresh

# ⛔ حذف جميع الجداول والبيانات والمايجريشن
php artisan migrate:reset

# ⛔ مسح جميع البيانات من جدول معين
php artisan tinker
> DB::table('users')->truncate();

# ⛔ حذف كل شيء وإعادة إنشاء
php artisan migrate:fresh
php artisan migrate:refresh --seed
```

---

## 📚 موارد مفيدة

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Eloquent ORM](https://laravel.com/docs/eloquent)
- [Laravel Migrations](https://laravel.com/docs/migrations)
- [Laravel Artisan Commands](https://laravel.com/docs/artisan)

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.
