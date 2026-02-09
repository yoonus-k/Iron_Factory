@echo off
chcp 65001 >nul
title إيقاف سيرفر مصنع الحديد
color 0C

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              ⛔ إيقاف سيرفر مصنع الحديد                     ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo [*] إيقاف Apache...
sc stop IronFactoryApache >nul 2>&1

echo [*] إيقاف MySQL...
sc stop IronFactoryMySQL >nul 2>&1

timeout /t 3 /nobreak >nul

echo.
echo ══════════════════════════════════════════════════════════════
echo   حالة الخدمات:
echo ══════════════════════════════════════════════════════════════

sc query IronFactoryMySQL | find "STOPPED" >nul
if %errorlevel% equ 0 (
    echo   [✓] MySQL: متوقف
) else (
    echo   [!] MySQL: قد يكون لا يزال يعمل
)

sc query IronFactoryApache | find "STOPPED" >nul
if %errorlevel% equ 0 (
    echo   [✓] Apache: متوقف
) else (
    echo   [!] Apache: قد يكون لا يزال يعمل
)

echo.
echo [✓] تم إيقاف السيرفر
echo.
echo لإعادة التشغيل: شغّل 2_START.bat
echo.
pause
