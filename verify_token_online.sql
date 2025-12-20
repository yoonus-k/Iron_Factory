-- تحقق من الـ Token في السيرفر الأون لاين
-- نفذ هذا في phpMyAdmin على قاعدة sehohoqm_fatwora

-- 1. عرض جميع الـ tokens
SELECT * FROM personal_access_tokens;

-- 2. التحقق من أن الـ hash صحيح
SELECT 
    id,
    tokenable_id,
    name,
    token,
    SHA2('new_secure_token_2025_12_17', 256) AS expected_hash,
    (token = SHA2('new_secure_token_2025_12_17', 256)) AS is_correct
FROM personal_access_tokens
WHERE name = 'local-server-sync';

-- إذا كان is_correct = 0، يعني الـ hash خاطئ
-- إذا كان is_correct = 1، يعني الـ hash صحيح والمشكلة في مكان آخر
