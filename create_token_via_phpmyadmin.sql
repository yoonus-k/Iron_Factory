-- ===============================================
-- إنشاء جدول personal_access_tokens وToken جديد
-- للسيرفر الأون لاين (sehohoqm_fatwora)
-- نفذ هذه الأوامر في phpMyAdmin
-- ===============================================

-- 1. أولاً: إنشاء جدول personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. ثانياً: حذف أي tokens قديمة (إن وجدت)
DELETE FROM personal_access_tokens;

-- 3. ثالثاً: إنشاء token جديد
-- ملاحظة: غيّر tokenable_id إلى ID المستخدم المناسب (عادة 1)
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
    SHA2('new_secure_token_2025_12_17', 256),
    '["sync:*"]',
    NOW(),
    NOW()
);

-- 4. رابعاً: احصل على الـ Token الكامل
-- انسخ الناتج واستخدمه في ملف .env على السيرفر المحلي
SELECT CONCAT(id, '|', 'new_secure_token_2025_12_17') AS full_token
FROM personal_access_tokens
WHERE name = 'local-server-sync'
ORDER BY id DESC
LIMIT 1;

-- ===============================================
-- الناتج سيكون بهذا الشكل: 1|new_secure_token_2025_12_17
-- انسخه وضعه في ملف .env المحلي في:
-- CENTRAL_SERVER_TOKEN=1|new_secure_token_2025_12_17
-- ===============================================
