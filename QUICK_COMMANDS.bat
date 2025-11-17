@echo off
REM 🏭 Iron Factory - أوامر Laravel والمشروع السريعة (Batch/Windows CMD)

cls
color 0B
echo ==========================================
echo 📋 أوامر Laravel الأساسية للمشروع
echo ==========================================
echo.
echo 👇 اختر الأمر أو انسخ من الخيارات:
echo.

REM Setup
echo ==================== SETUP ====================
echo 1️⃣  تثبيت المشروع لأول مرة:
echo composer install && php artisan key:generate && php artisan migrate:fresh --seed
echo.
echo 2️⃣  تشغيل الخادم:
echo php artisan serve
echo.

REM Migrations
echo ==================== MIGRATIONS ====================
echo 3️⃣  تشغيل المايجريشن:
echo php artisan migrate
echo.
echo 4️⃣  إعادة تشغيل جميع المايجريشن:
echo php artisan migrate:refresh --seed
echo.
echo 5️⃣  التراجع عن آخر مايجريشن:
echo php artisan migrate:rollback
echo.

REM Seeds
echo ==================== SEEDS ====================
echo 6️⃣  تشغيل البيانات الأساسية:
echo php artisan db:seed
echo.

REM Cache
echo ==================== CACHE ====================
echo 7️⃣  مسح الـ Cache:
echo php artisan optimize:clear
echo.

REM Tests
echo ==================== TESTS ====================
echo 8️⃣  تشغيل الاختبارات:
echo php artisan test
echo.

REM Routes
echo ==================== ROUTES ====================
echo 9️⃣  عرض الـ Routes:
echo php artisan route:list
echo.

REM Tinker
echo ==================== TINKER ====================
echo 🔟  فتح Tinker (Shell):
echo php artisan tinker
echo.

REM Quick Commands
echo ==================== QUICK COMMANDS ====================
echo أسرع طريقة للبدء:
echo php artisan migrate:refresh --seed ^&^& php artisan serve
echo.

pause
