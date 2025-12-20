-- ===============================================
-- جداول المزامنة الكاملة للسيرفر الأون لاين
-- sehohoqm_fatwora
-- نفذ هذا الملف كاملاً في phpMyAdmin
-- ===============================================

-- 1. جدول sync_logs - سجل المزامنة
CREATE TABLE IF NOT EXISTS `sync_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(50) NOT NULL COMMENT 'نوع الكيان',
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'معرف الكيان',
  `status` enum('pending','synced','failed') NOT NULL DEFAULT 'pending' COMMENT 'حالة المزامنة',
  `error_message` text DEFAULT NULL COMMENT 'رسالة الخطأ',
  `data_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'البيانات',
  `synced_at` timestamp NULL DEFAULT NULL COMMENT 'وقت المزامنة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sync_logs_user_id_foreign` (`user_id`),
  KEY `sync_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  KEY `sync_logs_status_index` (`status`),
  CONSTRAINT `sync_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. جدول sync_histories - تاريخ المزامنة
CREATE TABLE IF NOT EXISTS `sync_histories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `device_id` varchar(255) DEFAULT NULL COMMENT 'معرف الجهاز',
  `entity_type` varchar(50) NOT NULL COMMENT 'نوع الكيان',
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `local_id` varchar(255) DEFAULT NULL COMMENT 'UUID المحلي',
  `action` enum('create','update','delete','pull','push') NOT NULL COMMENT 'نوع العملية',
  `direction` enum('to_central','from_central') NOT NULL DEFAULT 'to_central' COMMENT 'اتجاه المزامنة',
  `status` enum('success','failed','pending') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `data_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `duration_ms` int(11) DEFAULT NULL COMMENT 'مدة العملية بالميلي ثانية',
  `synced_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sync_histories_user_id_foreign` (`user_id`),
  KEY `sync_histories_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  KEY `sync_histories_local_id_index` (`local_id`),
  KEY `sync_histories_action_index` (`action`),
  KEY `sync_histories_status_index` (`status`),
  KEY `sync_histories_device_id_index` (`device_id`),
  KEY `sync_histories_synced_at_index` (`synced_at`),
  CONSTRAINT `sync_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. جدول pending_syncs - قائمة انتظار المزامنة
CREATE TABLE IF NOT EXISTS `pending_syncs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `local_id` varchar(255) DEFAULT NULL,
  `action` enum('create','update','delete') NOT NULL,
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `related_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'الأولوية',
  `status` enum('pending','processing','failed','synced') NOT NULL DEFAULT 'pending',
  `retry_count` int(11) NOT NULL DEFAULT 0,
  `max_retries` int(11) NOT NULL DEFAULT 3,
  `last_error` text DEFAULT NULL,
  `created_at_local` timestamp NULL DEFAULT NULL,
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pending_syncs_user_id_foreign` (`user_id`),
  KEY `pending_syncs_user_id_status_index` (`user_id`,`status`),
  KEY `pending_syncs_entity_type_status_index` (`entity_type`,`status`),
  KEY `pending_syncs_local_id_index` (`local_id`),
  KEY `pending_syncs_status_priority_index` (`status`,`priority`),
  KEY `pending_syncs_created_at_index` (`created_at`),
  CONSTRAINT `pending_syncs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. جدول user_last_syncs - آخر مزامنة لكل مستخدم
CREATE TABLE IF NOT EXISTS `user_last_syncs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `last_pull_at` timestamp NULL DEFAULT NULL,
  `last_push_at` timestamp NULL DEFAULT NULL,
  `pending_count` int(11) NOT NULL DEFAULT 0,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `sync_stats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_last_syncs_user_id_unique` (`user_id`),
  KEY `user_last_syncs_last_pull_at_index` (`last_pull_at`),
  KEY `user_last_syncs_last_push_at_index` (`last_push_at`),
  CONSTRAINT `user_last_syncs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- تم! الآن جداول المزامنة جاهزة
-- ===============================================
