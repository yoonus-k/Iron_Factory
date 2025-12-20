# 🔧 دليل إعداد ملف .env للمزامنة الأونلاين/أوفلاين

## 📋 نظرة عامة

هذا الدليل يشرح كيفية إعداد ملف `.env` للسيرفر الأونلاين والأجهزة المحلية.

---

## 🌐 السيناريو 1: السيرفر الأونلاين (Central Server)

### الموقع
استضافة NameCheap / cPanel / VPS على الإنترنت

### الإعدادات المطلوبة

```env
# التطبيق
APP_NAME="Factory System"
APP_ENV=production
APP_DEBUG=false                    # ⚠️ مهم جداً للأمان!
APP_URL=https://yourdomain.com

# قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourdatabase_name      # من cPanel
DB_USERNAME=yourdatabase_user      # من cPanel
DB_PASSWORD=your_db_password       # من cPanel

# ⭐ المزامنة - السيرفر المركزي
IS_CENTRAL_SERVER=true
CENTRAL_SERVER_URL=                # فارغ (هذا هو المركز)
CENTRAL_SERVER_TOKEN=              # فارغ (هذا هو المركز)
SYNC_ENABLED=true
DEVICE_ID=CENTRAL-SERVER
DEVICE_NAME="Central Server - Online"
```

### خطوات التطبيق

1. **رفع المشروع على الاستضافة**
   ```bash
   # ارفع الملفات عبر FTP أو File Manager
   # ضع محتويات public في public_html
   # ضع باقي الملفات في مجلد laravel
   ```

2. **إنشاء قاعدة البيانات**
   - افتح cPanel → MySQL Databases
   - أنشئ قاعدة بيانات جديدة
   - أنشئ مستخدم وأضفه للقاعدة

3. **تعديل .env**
   ```bash
   # في File Manager، عدّل .env
   IS_CENTRAL_SERVER=true
   ```

4. **تشغيل Migrations**
   ```bash
   # في Terminal من cPanel
   cd /home/username/laravel
   php artisan migrate --force
   php artisan db:seed --force
   ```

5. **إنشاء API Token للأجهزة المحلية**
   ```bash
   php artisan tinker
   
   $user = App\Models\User::first();
   $token = $user->createToken('Local-Device-1', ['sync:*'])->plainTextToken;
   echo $token;
   # انسخ التوكن الطويل
   ```

---

## 💻 السيناريو 2: الجهاز المحلي (Local Device)

### الموقع
جهاز Windows في المصنع / المستودع

### الإعدادات المطلوبة

```env
# التطبيق
APP_NAME="Factory System - Local"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# قاعدة البيانات المحلية
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=factory_local          # قاعدة محلية
DB_USERNAME=root
DB_PASSWORD=

# ⭐ المزامنة - الجهاز المحلي
IS_CENTRAL_SERVER=false
CENTRAL_SERVER_URL=https://yourdomain.com/api/sync
CENTRAL_SERVER_TOKEN=1|abc123xyz...token_from_online_server
SYNC_ENABLED=true
DEVICE_ID=DEVICE-001               # فريد لكل جهاز
DEVICE_NAME="Factory Floor - PC #1"
```

### خطوات التطبيق

1. **تثبيت Laravel محلياً**
   ```bash
   cd C:\Users\mon3em\Desktop\tesr_docker
   composer install
   php artisan key:generate
   ```

2. **إنشاء قاعدة بيانات محلية**
   ```bash
   # في phpMyAdmin المحلي
   CREATE DATABASE factory_local;
   ```

3. **تعديل .env**
   ```env
   IS_CENTRAL_SERVER=false
   CENTRAL_SERVER_URL=https://yourdomain.com/api/sync
   CENTRAL_SERVER_TOKEN=1|abc123xyz...
   DEVICE_ID=DEVICE-001
   ```

4. **تشغيل Migrations محلياً**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **اختبار الاتصال**
   ```bash
   php artisan tinker
   
   use App\Services\CentralServerService;
   $service = app(CentralServerService::class);
   $result = $service->test();
   print_r($result);
   ```

---

## 🔄 السيناريو 3: عدة أجهزة محلية

### إذا كان لديك 3 أجهزة في المصنع

#### الجهاز الأول (PC-1)
```env
DEVICE_ID=DEVICE-001
DEVICE_NAME="Factory Floor - PC #1"
DB_DATABASE=factory_local_pc1
```

#### الجهاز الثاني (PC-2)
```env
DEVICE_ID=DEVICE-002
DEVICE_NAME="Warehouse - PC #2"
DB_DATABASE=factory_local_pc2
```

#### الجهاز الثالث (PC-3)
```env
DEVICE_ID=DEVICE-003
DEVICE_NAME="Quality Control - PC #3"
DB_DATABASE=factory_local_pc3
```

### ملاحظات مهمة
- كل جهاز يحتاج قاعدة بيانات محلية منفصلة
- كل جهاز يحتاج DEVICE_ID فريد
- جميع الأجهزة تستخدم نفس CENTRAL_SERVER_TOKEN

---

## 🔐 الحصول على API Token

### من السيرفر الأونلاين

#### الطريقة 1: Tinker
```bash
php artisan tinker

$user = App\Models\User::where('email', 'admin@system.com')->first();
$token = $user->createToken('Local-Device-1', ['sync:*'])->plainTextToken;
echo $token;
```

#### الطريقة 2: phpMyAdmin
```sql
-- أنشئ التوكن يدوياً
INSERT INTO personal_access_tokens 
(tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
VALUES 
('App\\Models\\User', 1, 'Local-Device-1', 
SHA2(CONCAT('random_', NOW(), RAND()), 256), 
'["sync:*"]', NOW(), NOW());

-- اعرض التوكن
SELECT CONCAT(id, '|', token) as full_token 
FROM personal_access_tokens 
WHERE name = 'Local-Device-1' 
ORDER BY id DESC LIMIT 1;
```

---

## 🧪 اختبار الإعدادات

### 1. اختبار السيرفر الأونلاين
```bash
# على السيرفر الأونلاين
curl https://yourdomain.com/api/sync/heartbeat \
  -H "Authorization: Bearer 1|your_token" \
  -H "Content-Type: application/json"
```

### 2. اختبار الجهاز المحلي
```bash
# على الجهاز المحلي
php artisan tinker

use App\Services\CentralServerService;
$service = app(CentralServerService::class);

// اختبار الاتصال
$test = $service->test();
print_r($test);

// يجب أن يعرض:
// ['status' => 'success', 'message' => 'Connected']
```

---

## ⚙️ جدولة المزامنة التلقائية

### Windows - Task Scheduler

1. أنشئ ملف batch:
```batch
@echo off
cd C:\Users\mon3em\Desktop\tesr_docker
php artisan sync:process
```

2. جدوله في Task Scheduler:
   - افتح Task Scheduler
   - Create Basic Task
   - Trigger: كل 5 دقائق
   - Action: تشغيل الملف

### Linux/cPanel - Cron Job
```bash
# في cPanel → Cron Jobs
# أضف:
*/5 * * * * cd /home/username/laravel && php artisan sync:process
```

---

## 📊 مراقبة المزامنة

### Dashboard
```
http://localhost/sync-dashboard           # محلي
https://yourdomain.com/sync-dashboard     # أونلاين
```

### Logs
```bash
# محلي
tail -f storage/logs/laravel.log

# أونلاين (cPanel File Manager)
# storage/logs/laravel.log
```

---

## ❌ حل المشاكل الشائعة

### خطأ: Connection refused
```bash
# تحقق من:
1. CENTRAL_SERVER_URL صحيح
2. السيرفر الأونلاين يعمل
3. لا يوجد Firewall يحجب الاتصال
```

### خطأ: Unauthenticated
```bash
# تحقق من:
1. CENTRAL_SERVER_TOKEN صحيح
2. التوكن موجود في personal_access_tokens
3. التوكن لم ينتهي
```

### خطأ: Database connection
```bash
# تحقق من:
1. قاعدة البيانات موجودة
2. المستخدم لديه صلاحيات
3. كلمة المرور صحيحة
```

---

## 📋 ملخص سريع

| الإعداد | السيرفر الأونلاين | الجهاز المحلي |
|--------|-------------------|---------------|
| **IS_CENTRAL_SERVER** | true | false |
| **CENTRAL_SERVER_URL** | (فارغ) | https://yourdomain.com/api/sync |
| **CENTRAL_SERVER_TOKEN** | (فارغ) | 1\|abc123xyz... |
| **DEVICE_ID** | CENTRAL-SERVER | DEVICE-001, 002, etc |
| **DB_HOST** | localhost | 127.0.0.1 |
| **APP_DEBUG** | false | true |

---

## 🎯 الخلاصة

1. ✅ السيرفر الأونلاين: `IS_CENTRAL_SERVER=true`
2. ✅ الأجهزة المحلية: `IS_CENTRAL_SERVER=false` + توكن
3. ✅ كل جهاز له DEVICE_ID فريد
4. ✅ جدولة المزامنة كل 5 دقائق
5. ✅ مراقبة من Dashboard
