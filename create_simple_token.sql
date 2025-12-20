-- ===============================================
-- إنشاء Token جديد مبسط
-- للسيرفر الأون لاين (sehohoqm_fatwora)
-- ===============================================

-- 1. حذف جميع الـ tokens القديمة
DELETE FROM personal_access_tokens;

-- 2. إنشاء token جديد
INSERT INTO personal_access_tokens (
    tokenable_type,
    tokenable_id,
    name,
    token,
    abilities,
    created_at,
    updated_at
) VALUES (
    'App\\Models\\User',
    1,
    'local-server-sync',
    '1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef',
    '["sync:*"]',
    NOW(),
    NOW()
);

-- 3. عرض الـ Token الكامل
SELECT 
    CONCAT(id, '|simple_token_123') AS 'انسخ هذا Token',
    id,
    token
FROM personal_access_tokens
WHERE name = 'local-server-sync'
ORDER BY id DESC
LIMIT 1;

-- ===============================================
-- بعد تنفيذ الأوامر:
-- 1. انسخ الناتج من العمود الأول (مثل: 3|simple_token_123)
-- 2. ضعه في .env المحلي في CENTRAL_SERVER_TOKEN
-- 3. شغّل: UPDATE personal_access_tokens SET token = SHA2('simple_token_123', 256) WHERE name = 'local-server-sync'
-- ===============================================
