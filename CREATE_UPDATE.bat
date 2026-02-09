@echo off
chcp 65001 >nul
title إنشاء ملف تحديث - مصنع الحديد
color 0B

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              📦 إنشاء ملف تحديث                             ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

set "PROJECT_DIR=C:\Users\mon3em\Desktop\Iron_Factory"
set "OUTPUT_DIR=C:\Users\mon3em\Desktop"
set "VERSION=1.1"

:: طلب رقم الإصدار
set /p VERSION="أدخل رقم الإصدار (مثال: 1.1): "

set "UPDATE_DIR=%OUTPUT_DIR%\IronFactory_Update_v%VERSION%"

:: إنشاء مجلد التحديث
if exist "%UPDATE_DIR%" rmdir /s /q "%UPDATE_DIR%"
mkdir "%UPDATE_DIR%"
mkdir "%UPDATE_DIR%\update_files"

echo.
echo [*] نسخ الملفات المُحدّثة...

:: نسخ الملفات الرئيسية (عدّل هذه القائمة حسب ما غيّرت)
xcopy /E /I /Y "%PROJECT_DIR%\app" "%UPDATE_DIR%\update_files\app" >nul
xcopy /E /I /Y "%PROJECT_DIR%\resources" "%UPDATE_DIR%\update_files\resources" >nul
xcopy /E /I /Y "%PROJECT_DIR%\routes" "%UPDATE_DIR%\update_files\routes" >nul
xcopy /E /I /Y "%PROJECT_DIR%\config" "%UPDATE_DIR%\update_files\config" >nul
xcopy /E /I /Y "%PROJECT_DIR%\public" "%UPDATE_DIR%\update_files\public" >nul
xcopy /E /I /Y "%PROJECT_DIR%\Modules" "%UPDATE_DIR%\update_files\Modules" >nul

:: نسخ سكربت التحديث
copy /Y "%PROJECT_DIR%\deploy\6_UPDATE.bat" "%UPDATE_DIR%\" >nul
copy /Y "%PROJECT_DIR%\deploy\7_ROLLBACK.bat" "%UPDATE_DIR%\" >nul

:: إنشاء ملف التغييرات
echo ═══════════════════════════════════════════════════ > "%UPDATE_DIR%\CHANGELOG.txt"
echo    تحديث نظام مصنع الحديد - الإصدار %VERSION% >> "%UPDATE_DIR%\CHANGELOG.txt"
echo ═══════════════════════════════════════════════════ >> "%UPDATE_DIR%\CHANGELOG.txt"
echo. >> "%UPDATE_DIR%\CHANGELOG.txt"
echo التاريخ: %date% >> "%UPDATE_DIR%\CHANGELOG.txt"
echo. >> "%UPDATE_DIR%\CHANGELOG.txt"
echo التغييرات: >> "%UPDATE_DIR%\CHANGELOG.txt"
echo - [أضف التغييرات هنا] >> "%UPDATE_DIR%\CHANGELOG.txt"
echo. >> "%UPDATE_DIR%\CHANGELOG.txt"
echo ═══════════════════════════════════════════════════ >> "%UPDATE_DIR%\CHANGELOG.txt"

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              ✅ تم إنشاء ملف التحديث!                       ║
echo ╠══════════════════════════════════════════════════════════════╣
echo ║                                                              ║
echo ║   الموقع: %UPDATE_DIR%
echo ║                                                              ║
echo ║   للعميل:                                                   ║
echo ║   1. انسخ المجلد للعميل                                     ║
echo ║   2. العميل يشغّل 6_UPDATE.bat                              ║
echo ║                                                              ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

:: فتح المجلد
explorer "%UPDATE_DIR%"

pause
