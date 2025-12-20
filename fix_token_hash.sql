-- تحقق من Token hash
SELECT 
    id,
    name,
    token AS current_hash,
    SHA2('new_secure_token_2025_12_17', 256) AS correct_hash,
    LENGTH(token) AS hash_length,
    (token = SHA2('new_secure_token_2025_12_17', 256)) AS is_matching
FROM personal_access_tokens
WHERE id = 3;

-- إذا كان is_matching = 0 أو hash_length != 64، نفذ هذا:
UPDATE personal_access_tokens 
SET token = SHA2('new_secure_token_2025_12_17', 256)
WHERE id = 3;

-- ثم تحقق مرة أخرى:
SELECT 
    id,
    token,
    LENGTH(token) AS length,
    'يجب أن يكون length = 64' AS note
FROM personal_access_tokens
WHERE id = 3;
