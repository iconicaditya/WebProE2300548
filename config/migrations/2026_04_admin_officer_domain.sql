-- Admin Officer domain backend schema
-- Safe to run multiple times (idempotent CREATE TABLE IF NOT EXISTS)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

USE eduskill_marketplace;

CREATE TABLE IF NOT EXISTS admin_officer_profiles (
    user_id INT UNSIGNED NOT NULL,
    designation VARCHAR(120) NOT NULL DEFAULT 'System Administrator',
    phone_number VARCHAR(30) NULL,
    employee_code VARCHAR(40) NULL,
    department VARCHAR(120) NULL,
    office_location VARCHAR(220) NULL,
    timezone VARCHAR(80) NOT NULL DEFAULT 'Asia/Kathmandu (UTC+05:45)',
    language_code VARCHAR(30) NOT NULL DEFAULT 'English',
    responsibilities TEXT NULL,
    last_login_at DATETIME NULL,
    password_updated_at DATETIME NULL,
    active_sessions INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id),
    CONSTRAINT fk_admin_officer_profiles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_admin_officer_profiles_employee_code (employee_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_officer_preferences (
    user_id INT UNSIGNED NOT NULL,
    pref_email_alerts TINYINT(1) NOT NULL DEFAULT 1,
    pref_daily_digest TINYINT(1) NOT NULL DEFAULT 1,
    pref_auto_archive TINYINT(1) NOT NULL DEFAULT 0,
    two_factor_enabled TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id),
    CONSTRAINT fk_admin_officer_preferences_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    officer_user_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME NULL,

    CONSTRAINT fk_admin_notifications_officer
        FOREIGN KEY (officer_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_admin_notifications_officer_read (officer_user_id, is_read),
    INDEX idx_admin_notifications_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    officer_user_id INT UNSIGNED NOT NULL,
    action_type VARCHAR(80) NOT NULL,
    entity_type VARCHAR(80) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    details_json JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_admin_activity_logs_officer
        FOREIGN KEY (officer_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_admin_activity_logs_officer (officer_user_id),
    INDEX idx_admin_activity_logs_action (action_type),
    INDEX idx_admin_activity_logs_entity (entity_type, entity_id),
    INDEX idx_admin_activity_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS platform_settings (
    setting_key VARCHAR(120) NOT NULL,
    setting_value TEXT NULL,
    updated_by_user_id INT UNSIGNED NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (setting_key),
    CONSTRAINT fk_platform_settings_updated_by
        FOREIGN KEY (updated_by_user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS report_exports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_type VARCHAR(80) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NULL,
    status ENUM('pending', 'ready', 'failed') NOT NULL DEFAULT 'pending',
    error_message TEXT NULL,
    requested_by_user_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,

    CONSTRAINT fk_report_exports_requested_by
        FOREIGN KEY (requested_by_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_report_exports_requested_by (requested_by_user_id),
    INDEX idx_report_exports_status (status),
    INDEX idx_report_exports_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @courses_has_moderated_by := (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'courses'
      AND column_name = 'moderated_by_user_id'
);

SET @courses_has_moderated_at := (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'courses'
      AND column_name = 'moderated_at'
);

SET @courses_has_moderation_note := (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'courses'
      AND column_name = 'moderation_note'
);

SET @sql_add_moderated_by := IF(
    @courses_has_moderated_by = 0,
    'ALTER TABLE courses ADD COLUMN moderated_by_user_id INT UNSIGNED NULL AFTER published_at',
    'SELECT 1'
);
PREPARE stmt_add_moderated_by FROM @sql_add_moderated_by;
EXECUTE stmt_add_moderated_by;
DEALLOCATE PREPARE stmt_add_moderated_by;

SET @sql_add_moderated_at := IF(
    @courses_has_moderated_at = 0,
    'ALTER TABLE courses ADD COLUMN moderated_at DATETIME NULL AFTER moderated_by_user_id',
    'SELECT 1'
);
PREPARE stmt_add_moderated_at FROM @sql_add_moderated_at;
EXECUTE stmt_add_moderated_at;
DEALLOCATE PREPARE stmt_add_moderated_at;

SET @sql_add_moderation_note := IF(
    @courses_has_moderation_note = 0,
    'ALTER TABLE courses ADD COLUMN moderation_note TEXT NULL AFTER moderated_at',
    'SELECT 1'
);
PREPARE stmt_add_moderation_note FROM @sql_add_moderation_note;
EXECUTE stmt_add_moderation_note;
DEALLOCATE PREPARE stmt_add_moderation_note;

SET @fk_exists := (
    SELECT COUNT(*)
    FROM information_schema.table_constraints
    WHERE table_schema = DATABASE()
      AND table_name = 'courses'
      AND constraint_name = 'fk_courses_moderated_by'
);

SET @sql_add_fk := IF(
    @fk_exists = 0,
    'ALTER TABLE courses ADD CONSTRAINT fk_courses_moderated_by FOREIGN KEY (moderated_by_user_id) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE stmt_add_fk FROM @sql_add_fk;
EXECUTE stmt_add_fk;
DEALLOCATE PREPARE stmt_add_fk;

SET @idx_exists := (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'courses'
      AND index_name = 'idx_courses_moderation'
);

SET @sql_add_idx := IF(
    @idx_exists = 0,
    'ALTER TABLE courses ADD INDEX idx_courses_moderation (status, moderated_at)',
    'SELECT 1'
);
PREPARE stmt_add_idx FROM @sql_add_idx;
EXECUTE stmt_add_idx;
DEALLOCATE PREPARE stmt_add_idx;

INSERT INTO platform_settings (setting_key, setting_value, updated_by_user_id, updated_at)
VALUES
    ('platform_name', 'EduSkill Marketplace', NULL, NOW()),
    ('platform_email', 'support@eduskill.com', NULL, NOW()),
    ('support_phone', '+977-01-0000000', NULL, NOW()),
    ('platform_commission', '20', NULL, NOW()),
    ('minimum_payout_amount', '100', NULL, NOW()),
    ('auto_approve_verified_instructors', '1', NULL, NOW()),
    ('require_content_review', '0', NULL, NOW())
ON DUPLICATE KEY UPDATE
    setting_value = VALUES(setting_value),
    updated_at = NOW();

SET FOREIGN_KEY_CHECKS = 1;

