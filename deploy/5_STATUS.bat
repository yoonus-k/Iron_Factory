@echo off
chcp 65001 >nul
title حالة النظام - مصنع الحديد
color 0E

:menu
cls
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              📊 حالة نظام مصنع الحديد                       ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo ══════════════════════════════════════════════════════════════
echo   حالة الخدمات:
echo ══════════════════════════════════════════════════════════════

:: MySQL Status
sc query IronFactoryMySQL 2>nul | find "RUNNING" >nul
if %errorlevel% equ 0 (
    echo   [✓] MySQL: يعمل
    set "MYSQL_STATUS=OK"
) else (
    echo   [✗] MySQL: متوقف
    set "MYSQL_STATUS=STOPPED"
)

:: Apache Status
sc query IronFactoryApache 2>nul | find "RUNNING" >nul
if %errorlevel% equ 0 (
    echo   [✓] Apache: يعمل
    set "APACHE_STATUS=OK"
) else (
    echo   [✗] Apache: متوقف
    set "APACHE_STATUS=STOPPED"
)

echo.
echo ══════════════════════════════════════════════════════════════
echo   معلومات الشبكة:
echo ══════════════════════════════════════════════════════════════
echo.

for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    for /f "tokens=1" %%b in ("%%a") do (
        echo   IP السيرفر: %%b
        echo   رابط النظام: http://%%b/iron-factory/public
    )
    goto :show_backup
)

:show_backup
echo.
echo ══════════════════════════════════════════════════════════════
echo   النسخ الاحتياطية:
echo ══════════════════════════════════════════════════════════════
echo.

set "BACKUP_DIR=%~dp0backups"
set "BACKUP_COUNT=0"
for %%F in ("%BACKUP_DIR%\*.sql") do set /a BACKUP_COUNT+=1

echo   عدد النسخ: %BACKUP_COUNT%
echo.
echo   آخر 5 نسخ:
for /f "tokens=*" %%F in ('dir /b /o-d "%BACKUP_DIR%\*.sql" 2^>nul ^| findstr /n "^" ^| findstr "^[1-5]:"') do (
    for /f "tokens=2 delims=:" %%G in ("%%F") do echo     %%G
)

echo.
echo ══════════════════════════════════════════════════════════════
echo   مساحة القرص:
echo ══════════════════════════════════════════════════════════════
echo.

for /f "tokens=3" %%a in ('dir /-c "%~dp0" ^| findstr /c:"bytes free"') do (
    set /a FREE_GB=%%a/1073741824
    echo   المساحة الفارغة: حوالي %FREE_GB% GB
)

echo.
echo ══════════════════════════════════════════════════════════════
echo   الخيارات:
echo ══════════════════════════════════════════════════════════════
echo.
echo   [1] تحديث الحالة
echo   [2] فتح النظام في المتصفح
echo   [3] فتح مجلد النسخ الاحتياطية
echo   [4] عرض سجلات Apache
echo   [5] عرض سجلات MySQL
echo   [0] خروج
echo.

choice /c 123450 /n /m "اختر رقم: "

if %errorlevel% equ 1 goto menu
if %errorlevel% equ 2 (
    start http://localhost/iron-factory/public
    goto menu
)
if %errorlevel% equ 3 (
    explorer "%BACKUP_DIR%"
    goto menu
)
if %errorlevel% equ 4 (
    set "LARAGON_DIR=%~dp0laragon"
    for /d %%G in ("%LARAGON_DIR%\bin\apache\*") do (
        if exist "%%G\logs\error.log" notepad "%%G\logs\error.log"
    )
    goto menu
)
if %errorlevel% equ 5 (
    set "LARAGON_DIR=%~dp0laragon"
    for /d %%G in ("%LARAGON_DIR%\bin\mysql\*") do (
        if exist "%%G\data\*.err" notepad "%%G\data\*.err"
    )
    goto menu
)
if %errorlevel% equ 6 exit /b 0

goto menu
