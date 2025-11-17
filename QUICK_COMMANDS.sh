#!/bin/bash
# 🏭 Iron Factory - أوامر Laravel والمشروع السريعة

# البدء السريع
echo "📋 أوامر Laravel الأساسية للمشروع"
echo ""
echo "👇 انسخ الأمر اللي تحتاجه:"
echo ""

# Setup
echo "==================== SETUP ===================="
echo "1️⃣  تثبيت المشروع لأول مرة:"
echo "composer install && php artisan key:generate && php artisan migrate:fresh --seed"
echo ""
echo "2️⃣  تشغيل الخادم:"
echo "php artisan serve"
echo ""

# Migrations
echo "==================== MIGRATIONS ===================="
echo "3️⃣  تشغيل المايجريشن:"
echo "php artisan migrate"
echo ""
echo "4️⃣  إعادة تشغيل جميع المايجريشن:"
echo "php artisan migrate:refresh --seed"
echo ""
echo "5️⃣  التراجع عن آخر مايجريشن:"
echo "php artisan migrate:rollback"
echo ""

# Seeds
echo "==================== SEEDS ===================="
echo "6️⃣  تشغيل البيانات الأساسية:"
echo "php artisan db:seed"
echo ""

# Cache
echo "==================== CACHE ===================="
echo "7️⃣  مسح الـ Cache:"
echo "php artisan optimize:clear"
echo ""

# Tests
echo "==================== TESTS ===================="
echo "8️⃣  تشغيل الاختبارات:"
echo "php artisan test"
echo ""

# Routes
echo "==================== ROUTES ===================="
echo "9️⃣  عرض الـ Routes:"
echo "php artisan route:list"
echo ""

# Tinker
echo "==================== TINKER ===================="
echo "🔟  فتح Tinker (Shell):"
echo "php artisan tinker"
echo ""
echo "   بعدها في Tinker:"
echo "   > User::all()"
echo "   > Material::find(1)"
echo "   > exit()"
echo ""

# Quick Commands
echo "==================== QUICK COMMANDS ===================="
echo "مسح Cache + مايجريشن + بيانات:"
echo "php artisan migrate:refresh --seed && php artisan optimize:clear"
echo ""
echo "أو استخدم الأمر المركب:"
echo "composer install && php artisan migrate:fresh --seed && php artisan serve"
echo ""
