@echo off
chcp 65001 >nul
title تشغيل سيرفر مصنع الحديد
color 0A

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              🚀 تشغيل سيرفر مصنع الحديد                     ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

set "INSTALL_DIR=%~dp0"

echo [*] تشغيل Laragon...
start "" "%INSTALL_DIR%laragon\laragon.exe"

echo [*] انتظار بدء السيرفر...
timeout /t 8 /nobreak >nul

echo.
echo ══════════════════════════════════════════════════════════════
echo   روابط الوصول:
echo ══════════════════════════════════════════════════════════════
echo.
echo   من هذا الجهاز:
echo   http://localhost/iron-factory/public/dashboard
echo.

:: عرض IP الجهاز
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    for /f "tokens=1" %%b in ("%%a") do (
        echo   من الأجهزة الأخرى:
        echo   http://%%b/iron-factory/public/dashboard
    )
    goto :open_browser
)

:open_browser
echo.
start http://localhost/iron-factory/public/dashboard
pause
