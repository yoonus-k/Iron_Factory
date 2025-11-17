# 🏭 Iron Factory - أوامر Laravel والمشروع السريعة (PowerShell/Windows)

Write-Host "📋 أوامر Laravel الأساسية للمشروع" -ForegroundColor Cyan
Write-Host ""
Write-Host "👇 انسخ الأمر اللي تحتاجه:" -ForegroundColor Yellow
Write-Host ""

# Setup
Write-Host "==================== SETUP ====================" -ForegroundColor Green
Write-Host "1️⃣  تثبيت المشروع لأول مرة:" -ForegroundColor White
Write-Host "composer install ; php artisan key:generate ; php artisan migrate:fresh --seed" -ForegroundColor Cyan
Write-Host ""

Write-Host "2️⃣  تشغيل الخادم:" -ForegroundColor White
Write-Host "php artisan serve" -ForegroundColor Cyan
Write-Host ""

# Migrations
Write-Host "==================== MIGRATIONS ====================" -ForegroundColor Green
Write-Host "3️⃣  تشغيل المايجريشن:" -ForegroundColor White
Write-Host "php artisan migrate" -ForegroundColor Cyan
Write-Host ""

Write-Host "4️⃣  إعادة تشغيل جميع المايجريشن:" -ForegroundColor White
Write-Host "php artisan migrate:refresh --seed" -ForegroundColor Cyan
Write-Host ""

Write-Host "5️⃣  التراجع عن آخر مايجريشن:" -ForegroundColor White
Write-Host "php artisan migrate:rollback" -ForegroundColor Cyan
Write-Host ""

Write-Host "6️⃣  التراجع عن آخر 3 مايجريشن:" -ForegroundColor White
Write-Host "php artisan migrate:rollback --step=3" -ForegroundColor Cyan
Write-Host ""

# Seeds
Write-Host "==================== SEEDS ====================" -ForegroundColor Green
Write-Host "7️⃣  تشغيل البيانات الأساسية:" -ForegroundColor White
Write-Host "php artisan db:seed" -ForegroundColor Cyan
Write-Host ""

Write-Host "8️⃣  تشغيل سيدر معين:" -ForegroundColor White
Write-Host "php artisan db:seed --class=SupplierSeeder" -ForegroundColor Cyan
Write-Host ""

# Cache
Write-Host "==================== CACHE ====================" -ForegroundColor Green
Write-Host "9️⃣  مسح الـ Cache:" -ForegroundColor White
Write-Host "php artisan optimize:clear" -ForegroundColor Cyan
Write-Host ""

Write-Host "🔟 مسح Cache فقط:" -ForegroundColor White
Write-Host "php artisan cache:clear" -ForegroundColor Cyan
Write-Host ""

Write-Host "1️⃣1️⃣  مسح View Cache:" -ForegroundColor White
Write-Host "php artisan view:clear" -ForegroundColor Cyan
Write-Host ""

# Tests
Write-Host "==================== TESTS ====================" -ForegroundColor Green
Write-Host "1️⃣2️⃣  تشغيل الاختبارات:" -ForegroundColor White
Write-Host "php artisan test" -ForegroundColor Cyan
Write-Host ""

Write-Host "1️⃣3️⃣  تشغيل اختبار معين:" -ForegroundColor White
Write-Host "php artisan test tests/Unit/Services/DuplicatePreventionServiceTest.php" -ForegroundColor Cyan
Write-Host ""

# Routes
Write-Host "==================== ROUTES ====================" -ForegroundColor Green
Write-Host "1️⃣4️⃣  عرض جميع الـ Routes:" -ForegroundColor White
Write-Host "php artisan route:list" -ForegroundColor Cyan
Write-Host ""

Write-Host "1️⃣5️⃣  عرض الـ Routes بتفاصيل:" -ForegroundColor White
Write-Host "php artisan route:list --verbose" -ForegroundColor Cyan
Write-Host ""

# Tinker
Write-Host "==================== TINKER ====================" -ForegroundColor Green
Write-Host "1️⃣6️⃣  فتح Tinker (Interactive Shell):" -ForegroundColor White
Write-Host "php artisan tinker" -ForegroundColor Cyan
Write-Host ""
Write-Host "   بعدها في Tinker:" -ForegroundColor Gray
Write-Host "   > User::all()" -ForegroundColor Cyan
Write-Host "   > Material::find(1)" -ForegroundColor Cyan
Write-Host "   > DB::table('delivery_notes')->count()" -ForegroundColor Cyan
Write-Host "   > exit()" -ForegroundColor Cyan
Write-Host ""

# Database
Write-Host "==================== DATABASE ====================" -ForegroundColor Green
Write-Host "1️⃣7️⃣  البدء من الصفر (حذف كل شيء):" -ForegroundColor White
Write-Host "php artisan migrate:fresh --seed" -ForegroundColor Cyan
Write-Host ""

Write-Host "1️⃣8️⃣  حذف جميع الجداول:" -ForegroundColor White
Write-Host "php artisan migrate:reset" -ForegroundColor Cyan
Write-Host ""

# Generate
Write-Host "==================== GENERATE FILES ====================" -ForegroundColor Green
Write-Host "1️⃣9️⃣  إنشاء Model مع Migration و Controller:" -ForegroundColor White
Write-Host "php artisan make:model Material -mcr" -ForegroundColor Cyan
Write-Host ""

Write-Host "2️⃣0️⃣  إنشاء Controller:" -ForegroundColor White
Write-Host "php artisan make:controller WarehouseController -r" -ForegroundColor Cyan
Write-Host ""

# Composer
Write-Host "==================== COMPOSER ====================" -ForegroundColor Green
Write-Host "2️⃣1️⃣  تثبيت المكتبات:" -ForegroundColor White
Write-Host "composer install" -ForegroundColor Cyan
Write-Host ""

Write-Host "2️⃣2️⃣  تحديث المكتبات:" -ForegroundColor White
Write-Host "composer update" -ForegroundColor Cyan
Write-Host ""

Write-Host "2️⃣3️⃣  إعادة بناء Autoload:" -ForegroundColor White
Write-Host "composer dump-autoload" -ForegroundColor Cyan
Write-Host ""

# Storage
Write-Host "==================== STORAGE ====================" -ForegroundColor Green
Write-Host "2️⃣4️⃣  ربط مجلد التخزين:" -ForegroundColor White
Write-Host "php artisan storage:link" -ForegroundColor Cyan
Write-Host ""

# One-liners
Write-Host "==================== ONE-LINERS (أوامر مركبة) ====================" -ForegroundColor Green
Write-Host "أسرع طريقة للتثبيت + البدء:" -ForegroundColor White
Write-Host "composer install ; php artisan key:generate ; php artisan migrate:fresh --seed ; php artisan serve" -ForegroundColor Cyan
Write-Host ""

Write-Host "مسح Cache + مايجريشن + بيانات في أمر واحد:" -ForegroundColor White
Write-Host "php artisan migrate:refresh --seed" -ForegroundColor Cyan
Write-Host ""

Write-Host "مسح كل شيء وإعادة تشغيل:" -ForegroundColor White
Write-Host "php artisan optimize:clear ; php artisan migrate:fresh --seed" -ForegroundColor Cyan
Write-Host ""

# Tips
Write-Host "==================== 💡 نصائح ====================" -ForegroundColor Yellow
Write-Host "• اضغط Ctrl+C لإيقاف الخادم" -ForegroundColor Gray
Write-Host "• استخدم --help لمزيد من الخيارات: php artisan migrate --help" -ForegroundColor Gray
Write-Host "• اقرأ ملف LARAVEL_COMMANDS.md لمزيد من الأوامر" -ForegroundColor Gray
Write-Host "• استخدم README.md للتوثيق الشامل" -ForegroundColor Gray
Write-Host ""
