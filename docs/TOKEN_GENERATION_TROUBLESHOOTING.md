# 🔑 دليل توليد API Token على السيرفر الأونلاين

## ⚠️ المشكلة التي واجهتك
```bash
Could not open input file: artisan
```
هذا يعني أنك لست في مجلد Laravel الصحيح!

---

## 📂 الخطوة 1: إيجاد مجلد Laravel

جرّب هذه الأوامر واحداً تلو الآخر:

```bash
# الخيار 1: المجلد الشائع
cd ~/public_html
ls -la artisan

# إذا لم يعمل، جرّب:
cd ~/laravel
ls -la artisan

# أو:
cd ~/domains/hitstest.sehoool.com
ls -la artisan

# أو ابحث عن الملف:
find ~ -name "artisan" -type f 2>/dev/null
```

بمجرد أن تجد المجلد الذي يحتوي على ملف `artisan`, ابقَ فيه!

---

## 🔑 الخطوة 2: توليد التوكن

### ✅ الطريقة الأسهل: phpMyAdmin (موصى بها!)

1. **افتح cPanel**
2. **اذهب إلى phpMyAdmin**
3. **اختر قاعدة البيانات:** `sehohoqm_fatwora`
4. **انقر على تبويب SQL**
5. **انسخ والصق هذا الكود:**

```sql
-- إنشاء التوكن
INSERT INTO personal_access_tokens 
(tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
SELECT 
    'App\\Models\\User',
    id,
    'Local-Device-1',
    SHA2(CONCAT('sync-token-', id, '-', NOW(), '-', RAND()), 256),
    '["sync:*"]',
    NOW(),
    NOW()
FROM users 
WHERE email = 'admin@factory.com'  -- أو admin@system.com
LIMIT 1;

-- عرض التوكن الكامل
SELECT 
    CONCAT(id, '|', token) as full_token,
    name,
    created_at
FROM personal_access_tokens 
WHERE name = 'Local-Device-1'
ORDER BY id DESC 
LIMIT 1;
```

6. **انسخ الـ `full_token` من النتيجة**

---

### الطريقة 2: Tinker (إذا كنت في المجلد الصحيح)

```bash
# 1. ادخل tinker
php artisan tinker

# 2. اكتب كل سطر على حدة واضغط Enter:
$user = App\Models\User::first();

$token = $user->createToken('Local-Device-1', ['sync:*'])->plainTextToken;

echo $token;

# 3. اخرج من tinker
exit
```

---

### الطريقة 3: الأمر المخصص

```bash
php artisan sync:generate-token
```

---

## 📋 الخطوة 3: استخدام التوكن

بعد الحصول على التوكن (مثلاً: `1|abc123xyz...`), ضعه في ملف `.env` للجهاز المحلي:

```env
# في الجهاز المحلي (Windows)
IS_CENTRAL_SERVER=false
CENTRAL_SERVER_URL=https://hitstest.sehoool.com/api/sync
CENTRAL_SERVER_TOKEN=1|abc123xyz...YOUR_LONG_TOKEN_HERE
DEVICE_ID=DEVICE-001
```

---

## 🧪 الخطوة 4: اختبار الاتصال

**من الجهاز المحلي:**

```bash
php artisan tinker

use App\Services\CentralServerService;
$service = app(CentralServerService::class);
$test = $service->test();
print_r($test);
```

**يجب أن ترى:**
```php
Array
(
    [status] => success
    [message] => Connection successful
    [server_time] => 2025-12-17 10:30:00
    [latency] => 245
)
```

---

## ❌ حل المشاكل

### المشكلة: "Could not open input file: artisan"
**الحل:** أنت لست في مجلد Laravel. استخدم `find ~ -name "artisan"` لإيجاده.

### المشكلة: "Class 'App\Models\User' not found"
**الحل:** قاعدة البيانات فارغة أو Models غير موجودة. نفّذ:
```bash
php artisan migrate
php artisan db:seed
```

### المشكلة: جدول personal_access_tokens غير موجود
**الحل:** نفّذ migration لـ Sanctum:
```bash
php artisan migrate
```

---

## 💡 نصيحة

**استخدم phpMyAdmin** - إنها الطريقة الأسهل والأسرع على cPanel!

1. افتح phpMyAdmin
2. انسخ الكود SQL أعلاه
3. نفّذه
4. انسخ التوكن
5. انتهى! ✅
