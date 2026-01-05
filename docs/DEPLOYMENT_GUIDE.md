# 🏭 دليل توزيع وتثبيت نظام مصنع الحديد

## 📋 نظرة عامة

هذا الدليل يشرح كيفية تجهيز وتوزيع النظام للعملاء.

---

## 🏗️ بنية النظام

```
┌─────────────────────────────────────────────────────────┐
│                    السيرفر المركزي                      │
│                  (جهاز واحد فقط)                        │
│                                                         │
│   ┌─────────┐  ┌─────────┐  ┌─────────┐               │
│   │ Laravel │  │  MySQL  │  │ Apache  │               │
│   └─────────┘  └─────────┘  └─────────┘               │
│                     ▲                                   │
└─────────────────────│───────────────────────────────────┘
                      │ شبكة محلية (LAN)
        ┌─────────────┼─────────────┐
        ▼             ▼             ▼
   ┌─────────┐   ┌─────────┐   ┌─────────┐
   │ جهاز 1  │   │ جهاز 2  │   │ جهاز 3  │
   │ متصفح  │   │ متصفح  │   │ متصفح  │
   └─────────┘   └─────────┘   └─────────┘
```

---

## 📦 محتويات حزمة التثبيت

```
📦 IronFactory_Server_v1.0.zip
│
├── 📁 laragon-portable/          # Laragon Portable (PHP + MySQL + Apache)
│   ├── bin/
│   ├── etc/
│   └── www/
│
├── 📁 iron-factory/              # مشروع Laravel
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── ...
│
├── 📁 database/                  # ملفات قاعدة البيانات
│   ├── iron_factory_structure.sql    # هيكل الجداول
│   ├── iron_factory_data.sql         # البيانات الأساسية
│   └── iron_factory_full.sql         # نسخة كاملة
│
├── 📄 install.bat                # سكربت التثبيت
├── 📄 start.bat                  # تشغيل السيرفر
├── 📄 stop.bat                   # إيقاف السيرفر
├── 📄 backup.bat                 # نسخ احتياطي
├── 📄 config.ini                 # إعدادات النظام
└── 📄 README.txt                 # تعليمات سريعة
```

---

## 🔧 ملفات التثبيت

### 1. install.bat

```batch
@echo off
chcp 65001 >nul
title تثبيت نظام مصنع الحديد

echo ╔══════════════════════════════════════════════════════════╗
echo ║          تثبيت نظام إدارة مصنع الحديد v1.0              ║
echo ╚══════════════════════════════════════════════════════════╝
echo.

:: التحقق من وجود المجلدات
if not exist "laragon-portable" (
    echo [خطأ] مجلد laragon-portable غير موجود!
    pause
    exit /b 1
)

if not exist "iron-factory" (
    echo [خطأ] مجلد iron-factory غير موجود!
    pause
    exit /b 1
)

echo [1/5] نسخ ملفات المشروع...
xcopy /E /I /Y "iron-factory" "laragon-portable\www\iron-factory" >nul
if errorlevel 1 (
    echo [خطأ] فشل نسخ الملفات!
    pause
    exit /b 1
)
echo       ✓ تم نسخ الملفات

echo [2/5] تشغيل Laragon...
cd laragon-portable
start /B laragon.exe
timeout /t 10 /nobreak >nul
echo       ✓ تم تشغيل Laragon

echo [3/5] انتظار بدء MySQL...
:wait_mysql
netstat -an | find "3306" >nul
if errorlevel 1 (
    timeout /t 2 /nobreak >nul
    goto wait_mysql
)
echo       ✓ MySQL جاهز

echo [4/5] إنشاء قاعدة البيانات...
bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS iron_factory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if errorlevel 1 (
    echo [خطأ] فشل إنشاء قاعدة البيانات!
    pause
    exit /b 1
)
echo       ✓ تم إنشاء قاعدة البيانات

echo [5/5] استيراد البيانات...
bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root iron_factory < ..\database\iron_factory_full.sql
if errorlevel 1 (
    echo [تحذير] فشل استيراد البيانات، قد تحتاج لاستيرادها يدوياً
) else (
    echo       ✓ تم استيراد البيانات
)

cd ..

echo.
echo ╔══════════════════════════════════════════════════════════╗
echo ║                   ✓ تم التثبيت بنجاح!                   ║
echo ╠══════════════════════════════════════════════════════════╣
echo ║                                                          ║
echo ║  للوصول للنظام:                                         ║
echo ║  http://localhost/iron-factory/public                   ║
echo ║                                                          ║
echo ║  من الأجهزة الأخرى:                                     ║
echo ║  http://[IP-السيرفر]/iron-factory/public                ║
echo ║                                                          ║
echo ╚══════════════════════════════════════════════════════════╝
echo.

pause
```

---

### 2. start.bat

```batch
@echo off
chcp 65001 >nul
title تشغيل سيرفر مصنع الحديد

echo ╔══════════════════════════════════════════════════════════╗
echo ║              تشغيل سيرفر مصنع الحديد                    ║
echo ╚══════════════════════════════════════════════════════════╝
echo.

cd laragon-portable
start laragon.exe

echo.
echo ✓ تم تشغيل السيرفر
echo.
echo للوصول للنظام: http://localhost/iron-factory/public
echo.

:: الحصول على IP الجهاز
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| find "IPv4"') do (
    for /f "tokens=1" %%b in ("%%a") do (
        echo من الأجهزة الأخرى: http://%%b/iron-factory/public
    )
)
echo.
pause
```

---

### 3. stop.bat

```batch
@echo off
chcp 65001 >nul
title إيقاف سيرفر مصنع الحديد

echo ╔══════════════════════════════════════════════════════════╗
echo ║              إيقاف سيرفر مصنع الحديد                    ║
echo ╚══════════════════════════════════════════════════════════╝
echo.

taskkill /F /IM httpd.exe 2>nul
taskkill /F /IM mysqld.exe 2>nul
taskkill /F /IM laragon.exe 2>nul

echo.
echo ✓ تم إيقاف السيرفر
echo.
pause
```

---

### 4. backup.bat

```batch
@echo off
chcp 65001 >nul
title نسخ احتياطي - مصنع الحديد

echo ╔══════════════════════════════════════════════════════════╗
echo ║                    نسخ احتياطي                          ║
echo ╚══════════════════════════════════════════════════════════╝
echo.

:: إنشاء مجلد النسخ الاحتياطي
set BACKUP_DIR=backups
set TIMESTAMP=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%
set TIMESTAMP=%TIMESTAMP: =0%

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

echo [1/2] نسخ قاعدة البيانات...
cd laragon-portable
bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe -u root iron_factory > "..\%BACKUP_DIR%\iron_factory_%TIMESTAMP%.sql"
cd ..

if errorlevel 1 (
    echo [خطأ] فشل نسخ قاعدة البيانات!
) else (
    echo       ✓ تم نسخ قاعدة البيانات
)

echo [2/2] نسخ ملفات التحميل...
if exist "laragon-portable\www\iron-factory\storage\app\public" (
    xcopy /E /I /Y "laragon-portable\www\iron-factory\storage\app\public" "%BACKUP_DIR%\uploads_%TIMESTAMP%" >nul
    echo       ✓ تم نسخ الملفات
)

echo.
echo ╔══════════════════════════════════════════════════════════╗
echo ║            ✓ تم النسخ الاحتياطي بنجاح!                  ║
echo ║                                                          ║
echo ║  الموقع: %BACKUP_DIR%\                                  ║
echo ╚══════════════════════════════════════════════════════════╝
echo.
pause
```

---

### 5. config.ini

```ini
[Database]
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=iron_factory
DB_USERNAME=root
DB_PASSWORD=

[Server]
; IP السيرفر المركزي (للمزامنة)
CENTRAL_SERVER_IP=
CENTRAL_SERVER_URL=

[Backup]
; مسار النسخ الاحتياطي
BACKUP_PATH=backups
; الاحتفاظ بالنسخ لمدة (أيام)
BACKUP_RETENTION_DAYS=30

[System]
; اسم المصنع
FACTORY_NAME=مصنع الحديد
; المنطقة الزمنية
TIMEZONE=Asia/Riyadh
```

---

### 6. README.txt

```
╔══════════════════════════════════════════════════════════════════╗
║              نظام إدارة مصنع الحديد - الإصدار 1.0               ║
╚══════════════════════════════════════════════════════════════════╝

📋 متطلبات النظام:
─────────────────
• Windows 10/11 (64-bit)
• 4GB RAM على الأقل (8GB مستحسن)
• 2GB مساحة فارغة
• شبكة محلية (LAN) للأجهزة الأخرى

📥 خطوات التثبيت:
─────────────────
1. فك ضغط الملف في أي مكان (مثلاً C:\IronFactory)
2. شغّل install.bat كـ Administrator
3. انتظر حتى يكتمل التثبيت
4. شغّل start.bat لبدء السيرفر

🌐 الوصول للنظام:
─────────────────
• من نفس الجهاز: http://localhost/iron-factory/public
• من أجهزة أخرى: http://[IP]/iron-factory/public
  (استبدل [IP] بعنوان IP السيرفر)

🔧 الملفات المهمة:
─────────────────
• install.bat  - تثبيت النظام (مرة واحدة فقط)
• start.bat    - تشغيل السيرفر
• stop.bat     - إيقاف السيرفر
• backup.bat   - نسخ احتياطي

👤 بيانات الدخول الافتراضية:
────────────────────────────
• اسم المستخدم: admin
• كلمة المرور: 123456

⚠️ هام:
───────
• قم بتغيير كلمة المرور بعد أول دخول
• شغّل backup.bat يومياً للنسخ الاحتياطي
• اجعل IP السيرفر ثابت (Static IP)
• افتح Port 80 في جدار الحماية

📞 الدعم الفني:
──────────────
• البريد: support@example.com
• الهاتف: 0500000000
```

---

## 📋 خطوات تجهيز الحزمة

### 1. تحضير Laragon Portable

```powershell
# تحميل Laragon Portable من:
# https://laragon.org/download/

# أو استخدم Laragon Full وانسخ المجلد
```

### 2. تصدير قاعدة البيانات

```powershell
# من داخل Laragon Terminal أو CMD

# تصدير الهيكل فقط
mysqldump -u root --no-data iron_factory > database/iron_factory_structure.sql

# تصدير البيانات الأساسية (المواد، المستخدمين، الإعدادات)
mysqldump -u root iron_factory users roles permissions materials settings > database/iron_factory_data.sql

# تصدير كامل
mysqldump -u root iron_factory > database/iron_factory_full.sql
```

### 3. تحضير ملف .env

```env
APP_NAME="مصنع الحديد"
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_DEBUG=false
APP_URL=http://localhost/iron-factory/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iron_factory
DB_USERNAME=root
DB_PASSWORD=

# إعدادات المزامنة
SYNC_ENABLED=true
CENTRAL_SERVER_URL=
```

### 4. تحسين Laravel للإنتاج

```powershell
cd iron-factory

# مسح وإعادة بناء الـ Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 5. إنشاء الحزمة النهائية

```powershell
# إنشاء مجلد الحزمة
mkdir IronFactory_Server_v1.0

# نسخ الملفات
Copy-Item -Recurse laragon-portable IronFactory_Server_v1.0\
Copy-Item -Recurse iron-factory IronFactory_Server_v1.0\
Copy-Item -Recurse database IronFactory_Server_v1.0\
Copy-Item *.bat IronFactory_Server_v1.0\
Copy-Item config.ini IronFactory_Server_v1.0\
Copy-Item README.txt IronFactory_Server_v1.0\

# ضغط الحزمة
Compress-Archive -Path IronFactory_Server_v1.0 -DestinationPath IronFactory_Server_v1.0.zip
```

---

## 🔒 إعدادات الأمان

### 1. جدار الحماية (Firewall)

```powershell
# فتح Port 80 للـ HTTP
netsh advfirewall firewall add rule name="Iron Factory HTTP" dir=in action=allow protocol=TCP localport=80

# فتح Port 3306 للـ MySQL (اختياري - للوصول عن بعد)
netsh advfirewall firewall add rule name="Iron Factory MySQL" dir=in action=allow protocol=TCP localport=3306
```

### 2. IP ثابت (Static IP)

```
1. افتح Network Settings
2. اختر Ethernet أو Wi-Fi
3. Properties > IPv4 > Properties
4. Use the following IP address:
   - IP: 192.168.1.100 (مثال)
   - Subnet: 255.255.255.0
   - Gateway: 192.168.1.1
   - DNS: 8.8.8.8
```

---

## 🔄 التحديثات

### تحديث الكود فقط:

```batch
@echo off
echo تحديث النظام...

:: إيقاف السيرفر
call stop.bat

:: نسخ احتياطي
call backup.bat

:: استبدال الملفات
xcopy /E /I /Y "iron-factory-new" "laragon-portable\www\iron-factory"

:: تشغيل السيرفر
call start.bat

echo تم التحديث!
pause
```

---

## 📊 المراقبة والصيانة

### مهام يومية:
- [ ] تشغيل backup.bat
- [ ] مراجعة سجلات الأخطاء

### مهام أسبوعية:
- [ ] فحص مساحة القرص
- [ ] مراجعة أداء النظام

### مهام شهرية:
- [ ] تنظيف النسخ الاحتياطية القديمة
- [ ] تحديث النظام إن وجد

---

## ❓ الأسئلة الشائعة

### س: كيف أعرف IP السيرفر؟
```batch
ipconfig | find "IPv4"
```

### س: النظام لا يعمل من الأجهزة الأخرى؟
1. تأكد من تشغيل Laragon
2. افحص جدار الحماية
3. تأكد أن الأجهزة على نفس الشبكة

### س: نسيت كلمة المرور؟
```sql
-- من Laragon Terminal
mysql -u root
USE iron_factory;
UPDATE users SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE email = 'admin@admin.com';
-- كلمة المرور الجديدة: password
```

---

## 📞 الدعم الفني

للمساعدة أو الإبلاغ عن مشاكل:
- 📧 البريد: support@ironfactory.com
- 📱 الهاتف: 0500000000
- 💬 واتساب: 0500000000

---

*آخر تحديث: يناير 2026*
