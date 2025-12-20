#!/bin/bash

# ============================================
# دليل سريع لتوليد API Token على السيرفر الأونلاين
# ============================================

echo "🔍 البحث عن مجلد Laravel..."
echo ""

# طريقة 1: إيجاد المجلد الصحيح
echo "📂 الخطوة 1: اذهب لمجلد Laravel"
echo "cd ~/public_html"
echo "# أو"
echo "cd ~/laravel"
echo "# أو"
echo "cd ~/domains/hitstest.sehoool.com"
echo ""

# طريقة 2: التحقق من وجود artisan
echo "✅ الخطوة 2: تحقق من وجود ملف artisan"
echo "ls -la artisan"
echo ""

# طريقة 3: توليد التوكن
echo "🔑 الخطوة 3: توليد التوكن (اختر طريقة واحدة)"
echo ""
echo "═══════════════════════════════════════════════════════"
echo "الطريقة 1: باستخدام Tinker (سهلة)"
echo "═══════════════════════════════════════════════════════"
echo "php artisan tinker"
echo ""
echo "# ثم اكتب كل سطر على حدة:"
echo "\$user = App\\Models\\User::first();"
echo "\$token = \$user->createToken('Local-Device-1', ['sync:*'])->plainTextToken;"
echo "echo \$token;"
echo ""
echo ""
echo "═══════════════════════════════════════════════════════"
echo "الطريقة 2: أمر واحد مباشر (أسرع)"
echo "═══════════════════════════════════════════════════════"
echo "php artisan tinker --execute=\"\\\$user = App\\\Models\\\User::first(); \\\$token = \\\$user->createToken('Local-Device-1', ['sync:*'])->plainTextToken; echo \\\$token;\""
echo ""
echo ""
echo "═══════════════════════════════════════════════════════"
echo "الطريقة 3: باستخدام الأمر المخصص (الأسهل)"
echo "═══════════════════════════════════════════════════════"
echo "php artisan sync:generate-token"
echo ""
echo ""
echo "═══════════════════════════════════════════════════════"
echo "الطريقة 4: باستخدام SQL مباشرة في phpMyAdmin"
echo "═══════════════════════════════════════════════════════"
echo "-- افتح phpMyAdmin من cPanel"
echo "-- اختر قاعدة البيانات sehohoqm_fatwora"
echo "-- انتقل لتبويب SQL"
echo "-- نفّذ هذا الاستعلام:"
echo ""
cat << 'EOF'
-- إنشاء التوكن
INSERT INTO personal_access_tokens 
(tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
SELECT 
    'App\\Models\\User',
    id,
    'Local-Device-1',
    SHA2(CONCAT('sync-token-', id, '-', NOW(), '-', RAND()), 256),
    '["sync:*"]',
    NOW(),
    NOW()
FROM users 
WHERE email = 'admin@system.com' 
LIMIT 1;

-- عرض التوكن الكامل
SELECT 
    CONCAT(id, '|', token) as full_token,
    name,
    created_at
FROM personal_access_tokens 
WHERE name = 'Local-Device-1'
ORDER BY id DESC 
LIMIT 1;
EOF
