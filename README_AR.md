# مشروع Iron Factory

مشروع Laravel متطور لإدارة مصنع الحديد

## معلومات المشروع

- **إصدار Laravel**: 12.x
- **إصدار PHP**: 8.2+
- **قاعدة البيانات**: SQLite (افتراضي)
- **Frontend**: Blade Templates + Vite

## متطلبات التشغيل

- PHP 8.2 أو أحدث
- Composer
- XAMPP أو أي خادم ويب محلي
- Node.js (اختياري للتطوير)

## التثبيت والإعداد

### 1. تشغيل المشروع

```bash
cd "c:\xampp\htdocs\fawtmaintest\Iron_Factory\iron-factory-app"
php artisan serve
```

### 2. رابط المشروع

بعد تشغيل الأمر أعلاه، سيكون المشروع متاحاً على:
- http://localhost:8000

أو يمكنك الوصول إليه عبر XAMPP على:
- http://localhost/fawtmaintest/Iron_Factory/iron-factory-app/public

### 3. إعدادات قاعدة البيانات

تم إعداد قاعدة بيانات SQLite افتراضياً في:
```
database/database.sqlite
```

### 4. الجداول الأساسية

تم إنشاء الجداول التالية:
- users (المستخدمين)
- cache (التخزين المؤقت)
- jobs (المهام)

### 5. تشغيل Migrations إضافية

```bash
php artisan migrate
```

### 6. إنشاء البيانات التجريبية (اختياري)

```bash
php artisan migrate:fresh --seed
```

## الأوامر المفيدة

### تطوير Frontend
```bash
npm install
npm run dev
```

### تشغيل Artisan Console
```bash
php artisan tinker
```

### إنشاء Controller جديد
```bash
php artisan make:controller ControllerName
```

### إنشاء Model جديد
```bash
php artisan make:model ModelName -m
```

### إنشاء Migration جديد
```bash
php artisan make:migration create_table_name
```

## هيكل المشروع

```
iron-factory-app/
├── app/                 # منطق التطبيق
├── config/             # ملفات الإعدادات
├── database/           # Migrations و Seeds
├── public/             # الملفات العامة
├── resources/          # Views و Assets
├── routes/             # ملفات التوجيه
├── storage/            # ملفات التخزين
└── tests/              # الاختبارات
```

## المساهمة

لأي استفسارات أو مساهمات، يرجى التواصل مع فريق التطوير.

---

تم الإنشاء بواسطة Laravel Framework
