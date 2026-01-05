@echo off
chcp 65001 >nul
title تحديث النظام - مصنع الحديد
color 0E

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              🔄 تحديث نظام مصنع الحديد                      ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

set "INSTALL_DIR=%~dp0"
set "LARAGON_DIR=%INSTALL_DIR%laragon"
set "PROJECT_DIR=%LARAGON_DIR%\www\iron-factory"
set "UPDATE_DIR=%INSTALL_DIR%update_files"
set "BACKUP_DIR=%INSTALL_DIR%backups"
set "ROLLBACK_DIR=%INSTALL_DIR%rollback"

:: التحقق من وجود ملفات التحديث
if not exist "%UPDATE_DIR%" (
    echo [خطأ] مجلد update_files غير موجود!
    echo.
    echo ضع ملفات التحديث في مجلد: update_files
    echo ثم شغّل هذا الملف مرة أخرى
    echo.
    pause
    exit /b 1
)

echo ⚠️  تحذير: سيتم إيقاف النظام مؤقتاً أثناء التحديث
echo.
choice /c YN /m "هل تريد المتابعة؟ (Y=نعم / N=لا)"
if %errorlevel% neq 1 (
    echo تم الإلغاء.
    pause
    exit /b 0
)

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 1/5: نسخ احتياطي
echo ══════════════════════════════════════════════════════════════

:: إنشاء نسخة احتياطية
call "%INSTALL_DIR%4_BACKUP_NOW.bat" >nul 2>&1
echo [✓] تم النسخ الاحتياطي

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 2/5: حفظ النسخة الحالية للرجوع
echo ══════════════════════════════════════════════════════════════

:: إنشاء مجلد rollback
if exist "%ROLLBACK_DIR%" rmdir /s /q "%ROLLBACK_DIR%"
mkdir "%ROLLBACK_DIR%"

:: نسخ الملفات الحالية
echo [*] حفظ النسخة الحالية...
xcopy /E /I /Y "%PROJECT_DIR%\app" "%ROLLBACK_DIR%\app" >nul 2>&1
xcopy /E /I /Y "%PROJECT_DIR%\config" "%ROLLBACK_DIR%\config" >nul 2>&1
xcopy /E /I /Y "%PROJECT_DIR%\resources" "%ROLLBACK_DIR%\resources" >nul 2>&1
xcopy /E /I /Y "%PROJECT_DIR%\routes" "%ROLLBACK_DIR%\routes" >nul 2>&1
xcopy /E /I /Y "%PROJECT_DIR%\public" "%ROLLBACK_DIR%\public" >nul 2>&1
copy /Y "%PROJECT_DIR%\composer.json" "%ROLLBACK_DIR%\" >nul 2>&1
echo [✓] تم الحفظ

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 3/5: إيقاف السيرفر
echo ══════════════════════════════════════════════════════════════

echo [*] إيقاف السيرفر...
sc stop IronFactoryApache >nul 2>&1
timeout /t 2 /nobreak >nul
echo [✓] تم الإيقاف

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 4/5: تطبيق التحديث
echo ══════════════════════════════════════════════════════════════

echo [*] نسخ الملفات الجديدة...
xcopy /E /I /Y "%UPDATE_DIR%\*" "%PROJECT_DIR%\" >nul 2>&1

:: تنفيذ أوامر Laravel
cd /d "%PROJECT_DIR%"

:: البحث عن PHP
for /d %%G in ("%LARAGON_DIR%\bin\php\*") do set "PHP_PATH=%%G\php.exe"

echo [*] مسح الـ Cache...
"%PHP_PATH%" artisan config:clear >nul 2>&1
"%PHP_PATH%" artisan cache:clear >nul 2>&1
"%PHP_PATH%" artisan view:clear >nul 2>&1

echo [*] تشغيل migrations...
"%PHP_PATH%" artisan migrate --force >nul 2>&1

echo [*] إعادة بناء الـ Cache...
"%PHP_PATH%" artisan config:cache >nul 2>&1
"%PHP_PATH%" artisan route:cache >nul 2>&1
"%PHP_PATH%" artisan view:cache >nul 2>&1

echo [✓] تم تطبيق التحديث

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخطوة 5/5: إعادة تشغيل السيرفر
echo ══════════════════════════════════════════════════════════════

echo [*] تشغيل السيرفر...
sc start IronFactoryApache >nul 2>&1
timeout /t 3 /nobreak >nul

sc query IronFactoryApache | find "RUNNING" >nul
if %errorlevel% equ 0 (
    echo [✓] السيرفر يعمل
) else (
    echo [!] قد يكون هناك مشكلة - تحقق يدوياً
)

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                                                              ║
echo ║              ✅ تم التحديث بنجاح!                           ║
echo ║                                                              ║
echo ║   إذا واجهت مشاكل، شغّل: 7_ROLLBACK.bat                     ║
echo ║                                                              ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

:: تنظيف ملفات التحديث
choice /c YN /m "هل تريد حذف ملفات التحديث؟ (Y=نعم / N=لا)"
if %errorlevel% equ 1 (
    rmdir /s /q "%UPDATE_DIR%" 2>nul
    echo [✓] تم حذف ملفات التحديث
)

echo.
pause
