@echo off
chcp 65001 >nul
title تثبيت نظام مصنع الحديد
color 0A

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                                                              ║
echo ║          🏭 تثبيت نظام إدارة مصنع الحديد                    ║
echo ║                    الإصدار 1.0                               ║
echo ║                                                              ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

:: التحقق من صلاحيات المدير
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo [خطأ] يجب تشغيل هذا الملف كمسؤول ^(Administrator^)
    echo.
    echo اضغط كليك يمين على الملف واختر "Run as administrator"
    echo.
    pause
    exit /b 1
)

:: تحديد المسارات
set "INSTALL_DIR=%~dp0"
set "LARAGON_DIR=%INSTALL_DIR%laragon"
set "PROJECT_DIR=%LARAGON_DIR%\www\iron-factory"
set "NSSM_PATH=%INSTALL_DIR%tools\nssm.exe"

echo [معلومات] مسار التثبيت: %INSTALL_DIR%
echo.

:: التحقق من وجود Laragon
if not exist "%LARAGON_DIR%" (
    echo [خطأ] مجلد laragon غير موجود!
    echo        تأكد من وجود مجلد laragon في نفس مكان هذا الملف
    pause
    exit /b 1
)

echo ══════════════════════════════════════════════════════════════
echo   الخطوة 1/6: تحضير الملفات
echo ══════════════════════════════════════════════════════════════

:: نسخ المشروع إذا لم يكن موجوداً
if not exist "%PROJECT_DIR%" (
    echo [*] نسخ ملفات المشروع...
    xcopy /E /I /Y "%INSTALL_DIR%iron-factory" "%PROJECT_DIR%" >nul
    if errorlevel 1 (
        echo [خطأ] فشل نسخ ملفات المشروع!
        pause
        exit /b 1
    )
)
echo [✓] ملفات المشروع جاهزة

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 2/6: تثبيت Apache كخدمة Windows
echo ══════════════════════════════════════════════════════════════

:: إيقاف الخدمة إذا كانت موجودة
sc query IronFactoryApache >nul 2>&1
if %errorlevel% equ 0 (
    echo [*] إيقاف خدمة Apache القديمة...
    sc stop IronFactoryApache >nul 2>&1
    sc delete IronFactoryApache >nul 2>&1
    timeout /t 2 /nobreak >nul
)

:: تثبيت Apache كخدمة
echo [*] تثبيت Apache كخدمة Windows...
"%LARAGON_DIR%\bin\apache\httpd-2.4.54-win64-VS16\bin\httpd.exe" -k install -n "IronFactoryApache" >nul 2>&1
if %errorlevel% neq 0 (
    :: جرب مسار آخر
    for /d %%G in ("%LARAGON_DIR%\bin\apache\*") do (
        "%%G\bin\httpd.exe" -k install -n "IronFactoryApache" >nul 2>&1
    )
)

:: ضبط الخدمة للتشغيل التلقائي
sc config IronFactoryApache start= auto >nul 2>&1
sc failure IronFactoryApache reset= 60 actions= restart/5000/restart/10000/restart/30000 >nul 2>&1
echo [✓] تم تثبيت Apache

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 3/6: تثبيت MySQL كخدمة Windows
echo ══════════════════════════════════════════════════════════════

:: إيقاف الخدمة إذا كانت موجودة
sc query IronFactoryMySQL >nul 2>&1
if %errorlevel% equ 0 (
    echo [*] إيقاف خدمة MySQL القديمة...
    sc stop IronFactoryMySQL >nul 2>&1
    sc delete IronFactoryMySQL >nul 2>&1
    timeout /t 2 /nobreak >nul
)

:: البحث عن MySQL وتثبيته
echo [*] تثبيت MySQL كخدمة Windows...
for /d %%G in ("%LARAGON_DIR%\bin\mysql\*") do (
    "%%G\bin\mysqld.exe" --install "IronFactoryMySQL" --defaults-file="%%G\my.ini" >nul 2>&1
    set "MYSQL_DIR=%%G"
)

:: ضبط الخدمة للتشغيل التلقائي
sc config IronFactoryMySQL start= auto >nul 2>&1
sc failure IronFactoryMySQL reset= 60 actions= restart/5000/restart/10000/restart/30000 >nul 2>&1
echo [✓] تم تثبيت MySQL

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 4/6: تشغيل الخدمات
echo ══════════════════════════════════════════════════════════════

echo [*] تشغيل MySQL...
sc start IronFactoryMySQL >nul 2>&1
timeout /t 5 /nobreak >nul

echo [*] تشغيل Apache...
sc start IronFactoryApache >nul 2>&1
timeout /t 3 /nobreak >nul

:: التحقق من التشغيل
sc query IronFactoryMySQL | find "RUNNING" >nul
if %errorlevel% equ 0 (
    echo [✓] MySQL يعمل
) else (
    echo [!] MySQL قد لا يعمل - تحقق يدوياً
)

sc query IronFactoryApache | find "RUNNING" >nul
if %errorlevel% equ 0 (
    echo [✓] Apache يعمل
) else (
    echo [!] Apache قد لا يعمل - تحقق يدوياً
)

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 5/6: إنشاء قاعدة البيانات
echo ══════════════════════════════════════════════════════════════

:: انتظار MySQL
timeout /t 3 /nobreak >nul

:: إنشاء قاعدة البيانات
echo [*] إنشاء قاعدة البيانات...
for /d %%G in ("%LARAGON_DIR%\bin\mysql\*") do (
    "%%G\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS iron_factory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
)

:: استيراد البيانات إذا وجد ملف SQL
if exist "%INSTALL_DIR%database\iron_factory.sql" (
    echo [*] استيراد البيانات...
    for /d %%G in ("%LARAGON_DIR%\bin\mysql\*") do (
        "%%G\bin\mysql.exe" -u root iron_factory < "%INSTALL_DIR%database\iron_factory.sql" 2>nul
    )
    echo [✓] تم استيراد البيانات
) else (
    echo [!] لا يوجد ملف بيانات - قد تحتاج لاستيراده يدوياً
)

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 6/6: إعداد النسخ الاحتياطي التلقائي
echo ══════════════════════════════════════════════════════════════

:: إنشاء مجلد النسخ الاحتياطي
if not exist "%INSTALL_DIR%backups" mkdir "%INSTALL_DIR%backups"

:: إنشاء مهمة مجدولة للنسخ الاحتياطي
echo [*] إعداد النسخ الاحتياطي التلقائي كل 6 ساعات...
schtasks /delete /tn "IronFactoryBackup" /f >nul 2>&1
schtasks /create /tn "IronFactoryBackup" /tr "\"%INSTALL_DIR%4_BACKUP_NOW.bat\"" /sc hourly /mo 6 /ru SYSTEM /f >nul 2>&1
echo [✓] تم إعداد النسخ الاحتياطي التلقائي

:: إنشاء مهمة لحذف النسخ القديمة
schtasks /delete /tn "IronFactoryCleanup" /f >nul 2>&1
schtasks /create /tn "IronFactoryCleanup" /tr "forfiles /p \"%INSTALL_DIR%backups\" /s /m *.sql /d -30 /c \"cmd /c del @path\"" /sc daily /st 03:00 /ru SYSTEM /f >nul 2>&1
echo [✓] تم إعداد حذف النسخ القديمة تلقائياً

echo.
echo ══════════════════════════════════════════════════════════════
echo   إعداد جدار الحماية
echo ══════════════════════════════════════════════════════════════

echo [*] فتح Port 80...
netsh advfirewall firewall delete rule name="IronFactory HTTP" >nul 2>&1
netsh advfirewall firewall add rule name="IronFactory HTTP" dir=in action=allow protocol=TCP localport=80 >nul 2>&1
echo [✓] تم فتح Port 80

echo.
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                                                              ║
echo ║              ✅ تم التثبيت بنجاح!                           ║
echo ║                                                              ║
echo ╠══════════════════════════════════════════════════════════════╣
echo ║                                                              ║
echo ║   🌐 للوصول للنظام:                                         ║
echo ║                                                              ║
echo ║      من هذا الجهاز:                                         ║
echo ║      http://localhost/iron-factory/public                   ║
echo ║                                                              ║

:: عرض IP الجهاز
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    for /f "tokens=1" %%b in ("%%a") do (
        echo ║      من الأجهزة الأخرى:                                     ║
        echo ║      http://%%b/iron-factory/public             ║
    )
    goto :break_ip
)
:break_ip

echo ║                                                              ║
echo ╠══════════════════════════════════════════════════════════════╣
echo ║                                                              ║
echo ║   📝 ملاحظات مهمة:                                          ║
echo ║   • النظام سيعمل تلقائياً عند تشغيل الجهاز                  ║
echo ║   • النسخ الاحتياطي يعمل كل 6 ساعات تلقائياً                ║
echo ║   • النسخ الاحتياطية في مجلد backups                        ║
echo ║                                                              ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

:: إضافة Laragon للتشغيل التلقائي عند بدء Windows
echo [*] إضافة Laragon للتشغيل التلقائي...
set "STARTUP_FOLDER=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup"
echo Set WshShell = CreateObject("WScript.Shell") > "%STARTUP_FOLDER%\IronFactoryServer.vbs"
echo WshShell.Run chr(34) ^& "%INSTALL_DIR%laragon\laragon.exe" ^& chr(34), 0 >> "%STARTUP_FOLDER%\IronFactoryServer.vbs"
echo Set WshShell = Nothing >> "%STARTUP_FOLDER%\IronFactoryServer.vbs"
echo [✓] تم إضافة التشغيل التلقائي

:: إنشاء اختصار على سطح المكتب
echo [*] إنشاء اختصار على سطح المكتب...
set "DESKTOP=%USERPROFILE%\Desktop"
echo Set WshShell = CreateObject("WScript.Shell") > "%TEMP%\CreateShortcut.vbs"
echo Set Shortcut = WshShell.CreateShortcut("%DESKTOP%\مصنع الحديد.lnk") >> "%TEMP%\CreateShortcut.vbs"
echo Shortcut.TargetPath = "http://localhost/iron-factory/public/dashboard" >> "%TEMP%\CreateShortcut.vbs"
echo Shortcut.IconLocation = "%INSTALL_DIR%laragon\laragon.exe,0" >> "%TEMP%\CreateShortcut.vbs"
echo Shortcut.Description = "نظام إدارة مصنع الحديد" >> "%TEMP%\CreateShortcut.vbs"
echo Shortcut.Save >> "%TEMP%\CreateShortcut.vbs"
cscript //nologo "%TEMP%\CreateShortcut.vbs"
del "%TEMP%\CreateShortcut.vbs"
echo [✓] تم إنشاء الاختصار على سطح المكتب

:: تشغيل Laragon الآن
echo [*] تشغيل Laragon...
start "" "%INSTALL_DIR%laragon\laragon.exe"
timeout /t 10 /nobreak >nul

:: فتح المتصفح
echo [*] فتح النظام في المتصفح...
start http://localhost/iron-factory/public/dashboard

echo.
pause
