@echo off
chcp 65001 >nul
title نسخ احتياطي - مصنع الحديد
color 0B

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║              💾 نسخ احتياطي لمصنع الحديد                    ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

set "INSTALL_DIR=%~dp0"
set "LARAGON_DIR=%INSTALL_DIR%laragon"
set "BACKUP_DIR=%INSTALL_DIR%backups"

:: إنشاء مجلد النسخ الاحتياطي
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

:: إنشاء اسم الملف بالتاريخ والوقت
set "TIMESTAMP=%date:~-4,4%-%date:~-7,2%-%date:~-10,2%_%time:~0,2%-%time:~3,2%"
set "TIMESTAMP=%TIMESTAMP: =0%"
set "BACKUP_FILE=%BACKUP_DIR%\iron_factory_%TIMESTAMP%.sql"

echo [*] جاري إنشاء النسخة الاحتياطية...
echo     الملف: %BACKUP_FILE%
echo.

:: البحث عن MySQL وتنفيذ النسخ
for /d %%G in ("%LARAGON_DIR%\bin\mysql\*") do (
    "%%G\bin\mysqldump.exe" -u root --single-transaction --quick iron_factory > "%BACKUP_FILE%" 2>nul
    if exist "%BACKUP_FILE%" (
        goto :backup_done
    )
)

:backup_done

:: التحقق من نجاح النسخ
if exist "%BACKUP_FILE%" (
    for %%A in ("%BACKUP_FILE%") do set "FILE_SIZE=%%~zA"
    
    if %FILE_SIZE% GTR 1000 (
        echo [✓] تم النسخ الاحتياطي بنجاح!
        echo.
        echo     الحجم: %FILE_SIZE% bytes
        echo     الموقع: %BACKUP_FILE%
    ) else (
        echo [!] النسخة قد تكون فارغة - تحقق من قاعدة البيانات
    )
) else (
    echo [✗] فشل النسخ الاحتياطي!
    echo     تأكد أن MySQL يعمل
)

echo.
echo ══════════════════════════════════════════════════════════════
echo   النسخ الاحتياطية المتوفرة:
echo ══════════════════════════════════════════════════════════════
echo.

dir /b /o-d "%BACKUP_DIR%\*.sql" 2>nul | head -10
if %errorlevel% neq 0 (
    for /f "tokens=*" %%F in ('dir /b /o-d "%BACKUP_DIR%\*.sql" 2^>nul') do (
        echo   %%F
    )
)

echo.
pause
