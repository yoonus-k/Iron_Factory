# خطوات رفع الملفات على السيرفر الأونلاين

## ✅ تم على السيرفر المحلي:
- ✅ تثبيت Sanctum
- ✅ تشغيل migrations
- ✅ تعديل bootstrap/app.php
- ✅ تعديل config/auth.php
- ✅ تعديل routes/api.php
- ✅ الاختبار: http://127.0.0.1:8000/api/sync يعمل بنجاح

---

## 📤 الآن: رفع على السيرفر الأونلاين

### الخطوة 1: رفع الملفات عبر cPanel File Manager

افتح cPanel → File Manager → اذهب لمجلد المشروع

**ارفع هذه الملفات (استبدل القديم بالجديد):**

1. ✅ `bootstrap/app.php`
2. ✅ `config/auth.php`
3. ✅ `config/sanctum.php` (ملف جديد)
4. ✅ `routes/api.php`

---

### الخطوة 2: رفع Migrations الخاصة بـ Sanctum

ارفع هذا الملف إلى `database/migrations/`:
```
2025_12_17_120033_create_personal_access_tokens_table.php
```

---

### الخطوة 3: تثبيت Sanctum على السيرفر الأونلاين

افتح **cPanel Terminal** وشغّل:

```bash
cd hitstest  # أو اسم مجلد مشروعك

# تثبيت Sanctum
composer require laravel/sanctum

# تشغيل Migrations
php artisan migrate

# تنظيف Cache
php artisan optimize:clear
```

---

### الخطوة 4: تحديث ملف .env على السيرفر الأونلاين

افتح `.env` على السيرفر الأونلاين وتأكد من:

```env
IS_CENTRAL_SERVER=true

# احذف هذه الأسطر (للأجهزة المحلية فقط)
# CENTRAL_SERVER_URL=...
# CENTRAL_SERVER_TOKEN=...
# DEVICE_ID=...
```

---

### الخطوة 5: اختبار السيرفر الأونلاين

افتح المتصفح:
```
https://hitstest.sehoool.com/api/sync
```

**يجب أن تظهر:**
```json
{
    "status": "ok",
    "message": "Sync API is working",
    "version": "1.0",
    "endpoints": [...]
}
```

---

### الخطوة 6: اختبار الاتصال من السيرفر المحلي

من Windows، شغّل:
```bash
php artisan sync:test-connection
```

**النتيجة المتوقعة:**
```
✓ Connection: Success
✓ Authentication: Success
✓ Push Test: Success
✓ Pull Test: Success
```

---

## 📋 ملخص الملفات المطلوب رفعها:

```
bootstrap/app.php
config/auth.php
config/sanctum.php (جديد)
routes/api.php
database/migrations/2025_12_17_120033_create_personal_access_tokens_table.php (جديد)
```

---

## 🎯 الأوامر على السيرفر الأونلاين (cPanel Terminal):

```bash
cd hitstest
composer require laravel/sanctum
php artisan migrate
php artisan optimize:clear
```

---

✅ **بعد اكتمال هذه الخطوات، سيعمل نظام المزامنة بالكامل!**
