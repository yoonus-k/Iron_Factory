@echo off
chcp 65001 >nul
title إلغاء تثبيت النظام - مصنع الحديد
color 0C

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              ⚠️ إلغاء تثبيت نظام مصنع الحديد               ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

:: التحقق من صلاحيات المدير
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo [خطأ] يجب تشغيل هذا الملف كمسؤول ^(Administrator^)
    pause
    exit /b 1
)

echo ⚠️  تحذير: سيتم إزالة خدمات النظام من Windows
echo     (لن يتم حذف الملفات أو قاعدة البيانات)
echo.
choice /c YN /m "هل تريد المتابعة؟ (Y=نعم / N=لا)"
if %errorlevel% neq 1 (
    echo تم الإلغاء.
    pause
    exit /b 0
)

echo.
echo [*] إيقاف Apache...
sc stop IronFactoryApache >nul 2>&1
timeout /t 2 /nobreak >nul

echo [*] إيقاف MySQL...
sc stop IronFactoryMySQL >nul 2>&1
timeout /t 2 /nobreak >nul

echo [*] إزالة خدمة Apache...
sc delete IronFactoryApache >nul 2>&1

echo [*] إزالة خدمة MySQL...
sc delete IronFactoryMySQL >nul 2>&1

echo [*] إزالة المهام المجدولة...
schtasks /delete /tn "IronFactoryBackup" /f >nul 2>&1
schtasks /delete /tn "IronFactoryCleanup" /f >nul 2>&1

echo [*] إزالة قاعدة جدار الحماية...
netsh advfirewall firewall delete rule name="IronFactory HTTP" >nul 2>&1

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                                                              ║
echo ║              ✅ تم إلغاء التثبيت!                           ║
echo ║                                                              ║
echo ║   ملاحظة: الملفات وقاعدة البيانات لا تزال موجودة           ║
echo ║   يمكنك حذفها يدوياً إذا أردت                               ║
echo ║                                                              ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

pause
