@echo off
chcp 65001 >nul
title الرجوع للنسخة السابقة - مصنع الحديد
color 0C

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              ⏪ الرجوع للنسخة السابقة                       ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

set "INSTALL_DIR=%~dp0"
set "LARAGON_DIR=%INSTALL_DIR%laragon"
set "PROJECT_DIR=%LARAGON_DIR%\www\iron-factory"
set "ROLLBACK_DIR=%INSTALL_DIR%rollback"

:: التحقق من وجود نسخة للرجوع
if not exist "%ROLLBACK_DIR%" (
    echo [خطأ] لا توجد نسخة سابقة للرجوع إليها!
    echo.
    pause
    exit /b 1
)

echo ⚠️  تحذير: سيتم استعادة النسخة السابقة من النظام
echo.
choice /c YN /m "هل تريد المتابعة؟ (Y=نعم / N=لا)"
if %errorlevel% neq 1 (
    echo تم الإلغاء.
    pause
    exit /b 0
)

echo.
echo [*] إيقاف السيرفر...
sc stop IronFactoryApache >nul 2>&1
timeout /t 2 /nobreak >nul

echo [*] استعادة الملفات السابقة...

:: استعادة الملفات
if exist "%ROLLBACK_DIR%\app" (
    rmdir /s /q "%PROJECT_DIR%\app" 2>nul
    xcopy /E /I /Y "%ROLLBACK_DIR%\app" "%PROJECT_DIR%\app" >nul 2>&1
)

if exist "%ROLLBACK_DIR%\config" (
    rmdir /s /q "%PROJECT_DIR%\config" 2>nul
    xcopy /E /I /Y "%ROLLBACK_DIR%\config" "%PROJECT_DIR%\config" >nul 2>&1
)

if exist "%ROLLBACK_DIR%\resources" (
    rmdir /s /q "%PROJECT_DIR%\resources" 2>nul
    xcopy /E /I /Y "%ROLLBACK_DIR%\resources" "%PROJECT_DIR%\resources" >nul 2>&1
)

if exist "%ROLLBACK_DIR%\routes" (
    rmdir /s /q "%PROJECT_DIR%\routes" 2>nul
    xcopy /E /I /Y "%ROLLBACK_DIR%\routes" "%PROJECT_DIR%\routes" >nul 2>&1
)

if exist "%ROLLBACK_DIR%\public" (
    rmdir /s /q "%PROJECT_DIR%\public" 2>nul
    xcopy /E /I /Y "%ROLLBACK_DIR%\public" "%PROJECT_DIR%\public" >nul 2>&1
)

:: مسح الـ Cache
cd /d "%PROJECT_DIR%"
for /d %%G in ("%LARAGON_DIR%\bin\php\*") do set "PHP_PATH=%%G\php.exe"

echo [*] مسح الـ Cache...
"%PHP_PATH%" artisan config:clear >nul 2>&1
"%PHP_PATH%" artisan cache:clear >nul 2>&1
"%PHP_PATH%" artisan view:clear >nul 2>&1

echo [*] إعادة بناء الـ Cache...
"%PHP_PATH%" artisan config:cache >nul 2>&1
"%PHP_PATH%" artisan route:cache >nul 2>&1

echo [*] تشغيل السيرفر...
sc start IronFactoryApache >nul 2>&1
timeout /t 3 /nobreak >nul

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                                                              ║
echo ║              ✅ تم الرجوع للنسخة السابقة!                   ║
echo ║                                                              ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

pause
