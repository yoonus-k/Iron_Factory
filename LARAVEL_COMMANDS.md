# 🏭 Iron Factory - أوامر المشروع الخاصة

دليل سريع لأوامر Laravel والأوامر المخصصة للمشروع.

---

## 🚀 البدء السريع

### الخطوة الأولى - التثبيت الكامل
```bash
# 1. انتقل إلى مجلد المشروع
cd c:\xampp\htdocs\fawtmaintest\Iron_Factory

# 2. تثبيت المكتبات (من الملف composer.lock)
composer install

# 3. نسخ ملف الإعدادات
copy .env.example .env

# 4. توليد مفتاح التطبيق
php artisan key:generate

# 5. إعداد قاعدة البيانات وملئها بالبيانات
php artisan migrate:fresh --seed

# 6. ربط مجلد التخزين (للملفات المرفوعة)
php artisan storage:link

# 7. تشغيل الخادم
php artisan serve
```

---

## 🗄️ أوامر قاعدة البيانات (Database)

### المايجريشن
```bash
# تشغيل جميع المايجريشن
php artisan migrate

# تشغيل مايجريشن معينة
php artisan migrate --path=database/migrations/2025_11_17_130000_*.php

# التراجع عن آخر مايجريشن
php artisan migrate:rollback

# التراجع عن خطوة معينة
php artisan migrate:rollback --step=3

# التراجع عن جميع المايجريشن
php artisan migrate:reset

# حذف جميع الجداول وإعادة المايجريشن
php artisan migrate:refresh

# حذف وإعادة مع البيانات الأساسية
php artisan migrate:refresh --seed

# البدء من الصفر (حذف كل شيء)
php artisan migrate:fresh
php artisan migrate:fresh --seed
```

### السيدر (البيانات الأساسية)
```bash
# تشغيل جميع السيدر
php artisan db:seed

# تشغيل سيدر معين
php artisan db:seed --class=SupplierSeeder
php artisan db:seed --class=MaterialSeeder
php artisan db:seed --class=WarehouseSeeder

# تشغيل سيدرز للوحدة
php artisan module:seed Manufacturing
```

---

## 🔄 أوامر Cache والتنظيف

### مسح الـ Cache
```bash
# مسح جميع أنواع الـ cache
php artisan optimize:clear

# مسح الـ application cache فقط
php artisan cache:clear

# مسح الـ configuration cache
php artisan config:clear

# مسح الـ route cache
php artisan route:clear

# مسح الـ view cache
php artisan view:clear

# مسح الـ config cache ثم إعادة بناؤه
php artisan config:cache

# مسح الـ route cache ثم إعادة بناؤه
php artisan route:cache
```

### مسح البيانات المؤقتة
```bash
# حذف محتويات المجلدات المؤقتة
rm -r bootstrap/cache/*
rm -r storage/logs/*

# من PowerShell (Windows)
Remove-Item bootstrap\cache\* -Force -Recurse
Remove-Item storage\logs\* -Force -Recurse
```

---

## 📱 أوامر التطوير والخادم

### تشغيل خادم التطوير
```bash
# تشغيل على المنفذ الافتراضي (8000)
php artisan serve

# تشغيل على منفذ معين
php artisan serve --port=8080

# تشغيل على جميع الواجهات (للوصول من أجهزة أخرى)
php artisan serve --host=0.0.0.0 --port=8000

# عرض معلومات اتصال الخادم
php artisan serve --info
```

### Tinker (Interactive Shell)
```bash
# فتح Tinker
php artisan tinker

# أمثلة في Tinker
> User::all()
> User::find(1)
> DB::table('delivery_notes')->count()
> Material::where('is_active', true)->get()
```

---

## 📊 أوامر المسار والأوامر

### عرض الـ Routes
```bash
# عرض جميع الـ routes
php artisan route:list

# عرض الـ routes بتفاصيل
php artisan route:list --verbose

# عرض الـ routes لـ Controller معين
php artisan route:list --name=warehouse

# حفظ الـ routes في ملف
php artisan route:list > routes.txt
```

### قائمة الأوامر المتاحة
```bash
# عرض جميع أوامر Artisan المتاحة
php artisan list

# عرض معلومات أمر معين
php artisan help migrate
php artisan help make:model
```

---

## 🎨 توليد الملفات والأكواد

### إنشاء Models
```bash
# Model فقط
php artisan make:model Material

# Model مع Migration
php artisan make:model Material -m

# Model مع Controller و Migration
php artisan make:model Material -mcr

# Model مع كل شيء
php artisan make:model Material -a
```

### إنشاء Controllers
```bash
# Controller عادي
php artisan make:controller WarehouseController

# Resource Controller (مع جميع الـ methods)
php artisan make:controller MaterialController -r

# Resource Controller للـ API
php artisan make:controller WarehouseController -r --api

# مع Model مرتبط
php artisan make:controller WarehouseController -m Material
```

### إنشاء Migrations
```bash
# Migration جديد
php artisan make:migration create_warehouses_table

# Migration لإضافة أعمدة
php artisan make:migration add_status_to_materials_table

# Migration لحذف أعمدة
php artisan make:migration drop_old_field_from_materials_table
```

### إنشاء Seeders
```bash
# Seeder جديد
php artisan make:seeder SupplierSeeder

# ثم تشغيله
php artisan db:seed --class=SupplierSeeder
```

### إنشاء Requests (Form Validation)
```bash
# Request جديد
php artisan make:request StoreWarehouseRequest

# يتم استخدامه في Controllers
```

### إنشاء Service Classes
```bash
# Class عادي في app/Services
php artisan make:class Services/WarehouseService
```

---

## 🔐 أوامر الأمان والمفاتيح

### إدارة المفاتيح
```bash
# توليد مفتاح التطبيق
php artisan key:generate

# إظهار المفتاح الحالي
php artisan key:show

# عرض قيمة APP_KEY من .env
php artisan tinker
> config('app.key')
```

### إدارة Permissions و Roles
```bash
# في Tinker
php artisan tinker

# إنشاء permission جديد
> Permission::create(['name' => 'register-material', 'guard_name' => 'web']);

# إنشاء role جديد
> Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);

# ربط permission مع role
> $role->givePermissionTo('register-material');

# إعطاء role للمستخدم
> $user->assignRole('warehouse-manager');
```

---

## 📧 أوامر البريد الإلكتروني

### اختبار البريد
```bash
# في Tinker لإرسال بريد تجريبي
php artisan tinker

> Mail::raw('Hello World', function($msg) {
    $msg->to('test@example.com')
        ->subject('Test Email');
});
```

### إعدادات البريد
```bash
# في .env، تغيير خادم البريد
MAIL_DRIVER=smtp      # SMTP server
MAIL_DRIVER=mailtrap  # Mailtrap (للاختبار)
MAIL_DRIVER=log       # Log to file (للتطوير)

# أو استخدام Mailtrap (خدمة مجانية للاختبار)
```

---

## 🌐 أوامر الوحدات (Modules)

### إدارة الوحدات
```bash
# عرض الوحدات المثبتة
php artisan module:list

# إنشاء وحدة جديدة
php artisan module:make Manufacturing

# حذف وحدة
php artisan module:delete Manufacturing

# تفعيل/تعطيل وحدة
php artisan module:enable Manufacturing
php artisan module:disable Manufacturing

# تشغيل مايجريشن الوحدة
php artisan module:migrate Manufacturing

# بذر بيانات الوحدة
php artisan module:seed Manufacturing

# إنشاء Model في الوحدة
php artisan make:model Manufacturing/Material -m
```

---

## 🧪 أوامر الاختبار

### تشغيل الاختبارات
```bash
# تشغيل جميع الاختبارات
php artisan test

# تشغيل ملف اختبار معين
php artisan test tests/Unit/Services/DuplicatePreventionServiceTest.php

# تشغيل مع تفاصيل كاملة
php artisan test --verbose

# التوقف عند أول فشل
php artisan test --stop-on-failure

# عرض Coverage (النسبة المئوية للكود المختبر)
php artisan test --coverage
```

### استخدام PHPUnit مباشرة
```bash
# تشغيل PHPUnit
./vendor/bin/phpunit

# ملف معين
./vendor/bin/phpunit tests/Unit/Services/DuplicatePreventionServiceTest.php

# بخيارات مختلفة
./vendor/bin/phpunit --verbose
./vendor/bin/phpunit --stop-on-failure
```

---

## 📦 أوامر Composer

### إدارة المكتبات
```bash
# تثبيت جميع المكتبات من composer.lock
composer install

# تحديث جميع المكتبات
composer update

# تثبيت مكتبة جديدة
composer require laravel/passport

# حذف مكتبة
composer remove laravel/passport

# تثبيت مكتبة في مرحلة التطوير فقط
composer require --dev phpunit/phpunit

# إعادة بناء Autoload
composer dump-autoload

# إعادة بناء مع تحسين الأداء
composer dump-autoload --optimize

# التحقق من الأمان (تحديث الثغرات الأمنية)
composer audit

# عرض المكتبات المثبتة
composer show

# تحديث مكتبة محددة
composer update vendor/package-name
```

---

## 🚀 أوامر للإنتاج

### التحضير للإنتاج
```bash
# 1. تعطيل وضع التصحيح (في .env)
APP_DEBUG=false

# 2. تفعيل الـ cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. تحسين autoloader
composer install --optimize-autoloader --no-dev

# 4. تشغيل المايجريشن
php artisan migrate --force

# 5. بذر البيانات الأساسية (إذا لزم الحال)
php artisan db:seed --force
```

### المراقبة والصيانة
```bash
# دخول وضع الصيانة (الموقع غير متاح)
php artisan down

# الخروج من وضع الصيانة
php artisan up

# دخول الصيانة مع رسالة
php artisan down --message="Updating"

# عرض السجلات
tail -f storage/logs/laravel.log

# عرض الأخطاء فقط
tail -f storage/logs/laravel.log | grep ERROR

# واجهة لمراقبة السجلات (PowerShell)
Get-Content storage\logs\laravel.log -Wait -Tail 20
```

---

## ⚠️ أوامر خطيرة (استخدم بحذر!)

```bash
# ⛔ حذف جميع الجداول وإعادة المايجريشن
php artisan migrate:refresh

# ⛔ حذف جميع الجداول والبيانات
php artisan migrate:reset

# ⛔ حذف كل شيء والبدء من الصفر
php artisan migrate:fresh

# ⛔ حذف جدول بالكامل من Tinker
php artisan tinker
> DB::table('users')->truncate();

# ⛔ حذف سجل معين
> DB::table('users')->where('id', 1)->delete();
```

---

## 🎯 سير العمل الموصى به

### في بداية اليوم
```bash
# 1. التحديث من Git
git pull origin main

# 2. تثبيت المكتبات الجديدة (إن وجدت)
composer install

# 3. تشغيل المايجريشن الجديدة
php artisan migrate

# 4. مسح الـ cache
php artisan optimize:clear

# 5. تشغيل الخادم
php artisan serve
```

### بعد إضافة مايجريشن جديد
```bash
# 1. تشغيل المايجريشن
php artisan migrate

# 2. مسح الـ cache
php artisan optimize:clear

# 3. إعادة تشغيل الخادم
# اضغط Ctrl+C ثم php artisan serve
```

### قبل Push للـ Git
```bash
# 1. تشغيل الاختبارات
php artisan test

# 2. مسح الـ cache والملفات المؤقتة
php artisan optimize:clear

# 3. التأكد من عدم وجود debug statements
grep -r "dd(" app/
grep -r "dump(" app/
```

---

## 📚 موارد مفيدة

- [Laravel Documentation](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Database Migrations](https://laravel.com/docs/migrations)
- [Artisan CLI](https://laravel.com/docs/artisan)
- [Laravel Modules](https://nwidart.com/laravel-modules/)

---

## 💡 نصائح مهمة

1. **استخدم `.env` للإعدادات الحساسة** - لا تضعها في الكود
2. **تشغيل الاختبارات قبل الـ push** - تأكد من عدم كسر الكود
3. **استخدم Migrations للتغييرات في قاعدة البيانات** - لا تعدل الجداول مباشرة
4. **استخدم Seeders لملء البيانات الأساسية** - يسهل إعادة الإعداد
5. **مسح الـ cache بعد أي تغيير مهم** - خاصة الـ config و routes

---

**تم آخر تحديث**: November 17, 2025
