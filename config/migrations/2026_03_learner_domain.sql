-- Learner domain backend schema
-- Safe to run multiple times (idempotent CREATE TABLE IF NOT EXISTS)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

USE eduskill_marketplace;

CREATE TABLE IF NOT EXISTS learner_wishlist (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_wishlist_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_wishlist_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_learner_wishlist_course (learner_user_id, course_id),
    INDEX idx_learner_wishlist_learner (learner_user_id),
    INDEX idx_learner_wishlist_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency_code CHAR(3) NOT NULL DEFAULT 'USD',
    coupon_code VARCHAR(40) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,

    CONSTRAINT fk_learner_cart_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_cart_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_learner_cart_course (learner_user_id, course_id),
    INDEX idx_learner_cart_learner (learner_user_id),
    INDEX idx_learner_cart_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    order_ref VARCHAR(40) NOT NULL,
    order_status ENUM('pending', 'paid', 'failed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
    subtotal_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency_code CHAR(3) NOT NULL DEFAULT 'USD',
    payment_method ENUM('card', 'paypal', 'manual', 'wallet') NOT NULL DEFAULT 'card',
    metadata_json JSON NULL,
    placed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_orders_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_learner_orders_ref (order_ref),
    INDEX idx_learner_orders_learner (learner_user_id),
    INDEX idx_learner_orders_status (order_status),
    INDEX idx_learner_orders_placed (placed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NULL,
    provider_user_id INT UNSIGNED NULL,
    course_title_snapshot VARCHAR(180) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_order_items_order
        FOREIGN KEY (order_id) REFERENCES learner_orders(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_order_items_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_learner_order_items_provider
        FOREIGN KEY (provider_user_id) REFERENCES users(id)
        ON DELETE SET NULL,

    INDEX idx_learner_order_items_order (order_id),
    INDEX idx_learner_order_items_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    learner_user_id INT UNSIGNED NOT NULL,
    transaction_ref VARCHAR(64) NOT NULL,
    payment_gateway VARCHAR(60) NULL,
    payment_method ENUM('card', 'paypal', 'manual', 'wallet') NOT NULL DEFAULT 'card',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency_code CHAR(3) NOT NULL DEFAULT 'USD',
    paid_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_payments_order
        FOREIGN KEY (order_id) REFERENCES learner_orders(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_payments_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_learner_payments_ref (transaction_ref),
    INDEX idx_learner_payments_learner (learner_user_id),
    INDEX idx_learner_payments_status (payment_status),
    INDEX idx_learner_payments_paid_at (paid_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_lesson_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    is_completed TINYINT(1) NOT NULL DEFAULT 0,
    minutes_spent INT UNSIGNED NOT NULL DEFAULT 0,
    last_position_seconds INT UNSIGNED NOT NULL DEFAULT 0,
    last_activity_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_lesson_progress_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_lesson_progress_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_lesson_progress_lesson
        FOREIGN KEY (lesson_id) REFERENCES course_lessons(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_learner_lesson_progress (learner_user_id, course_id, lesson_id),
    INDEX idx_learner_lesson_progress_course (course_id),
    INDEX idx_learner_lesson_progress_activity (last_activity_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_quiz_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    attempt_no SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    score_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    answers_json JSON NULL,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_quiz_attempts_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_quiz_attempts_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_quiz_attempts_lesson
        FOREIGN KEY (lesson_id) REFERENCES course_lessons(id)
        ON DELETE CASCADE,

    INDEX idx_learner_quiz_attempts_learner (learner_user_id),
    INDEX idx_learner_quiz_attempts_course_lesson (course_id, lesson_id),
    INDEX idx_learner_quiz_attempts_attempted (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_certificates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    enrollment_id BIGINT UNSIGNED NULL,
    certificate_code VARCHAR(40) NOT NULL,
    grade_label VARCHAR(20) NOT NULL DEFAULT 'A',
    file_path VARCHAR(255) NULL,
    status ENUM('ready', 'revoked') NOT NULL DEFAULT 'ready',
    issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_certificates_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_certificates_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_certificates_enrollment
        FOREIGN KEY (enrollment_id) REFERENCES enrollments(id)
        ON DELETE SET NULL,

    UNIQUE KEY uq_learner_certificates_code (certificate_code),
    UNIQUE KEY uq_learner_certificates_course (learner_user_id, course_id),
    INDEX idx_learner_certificates_issued (issued_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    provider_user_id INT UNSIGNED NULL,
    course_id BIGINT UNSIGNED NULL,
    direction ENUM('inbound', 'outbound') NOT NULL DEFAULT 'inbound',
    subject VARCHAR(180) NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_messages_learner
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_learner_messages_provider
        FOREIGN KEY (provider_user_id) REFERENCES users(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_learner_messages_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE SET NULL,

    INDEX idx_learner_messages_learner_read (learner_user_id, is_read),
    INDEX idx_learner_messages_sent (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    notification_type VARCHAR(60) NOT NULL,
    title VARCHAR(180) NOT NULL,
    message_text TEXT NOT NULL,
    related_url VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME NULL,

    CONSTRAINT fk_learner_notifications_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_learner_notifications_learner_read (learner_user_id, is_read),
    INDEX idx_learner_notifications_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_settings (
    learner_user_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(10) NOT NULL DEFAULT 'en',
    timezone VARCHAR(80) NOT NULL DEFAULT 'Asia/Kolkata',
    notification_email_enabled TINYINT(1) NOT NULL DEFAULT 1,
    theme_preference ENUM('light', 'dark', 'system') NOT NULL DEFAULT 'light',
    two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (learner_user_id),
    CONSTRAINT fk_learner_settings_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_security_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    event_type ENUM('login', 'password_change', '2fa_change', 'trusted_device') NOT NULL DEFAULT 'login',
    event_label VARCHAR(120) NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    device_name VARCHAR(120) NULL,
    occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_security_logs_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_learner_security_logs_learner (learner_user_id),
    INDEX idx_learner_security_logs_occurred (occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_trusted_devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learner_user_id INT UNSIGNED NOT NULL,
    device_hash VARCHAR(120) NOT NULL,
    device_name VARCHAR(120) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    first_seen_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_learner_trusted_devices_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_learner_trusted_devices_hash (learner_user_id, device_hash),
    INDEX idx_learner_trusted_devices_active (learner_user_id, is_active),
    INDEX idx_learner_trusted_devices_last_used (last_used_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

