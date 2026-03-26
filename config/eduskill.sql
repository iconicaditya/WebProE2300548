-- EduSkill Marketplace (Auth + Profiles) Migration Script
-- Import this file in phpMyAdmin (SQL tab).
--
-- IMPORTANT for ERROR 1813 (Tablespace exists):
-- If you see #1813 for `users`, do this once:
-- 1) Stop MySQL in XAMPP
-- 2) Delete orphan files/folder in: C:\xampp\mysql\data\eduskill_marketplace\
-- 3) Start MySQL again
-- 4) Re-run this SQL

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS eduskill_marketplace
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE eduskill_marketplace;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS course_resources;
DROP TABLE IF EXISTS course_lessons;
DROP TABLE IF EXISTS course_sections;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS provider_profiles;
DROP TABLE IF EXISTS learner_profiles;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('learner', 'provider', 'officer') NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS learner_profiles (
    user_id INT UNSIGNED NOT NULL,
    `current_role` VARCHAR(120) NOT NULL,
    mobile_number VARCHAR(30) NOT NULL,
    learning_interest VARCHAR(100) NOT NULL,
    experience_level VARCHAR(50) NOT NULL,
    learning_goal TEXT NOT NULL,
    profile_photo_url VARCHAR(255) DEFAULT NULL,
    accepted_terms TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_learner_profiles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS provider_profiles (
    user_id INT UNSIGNED NOT NULL,
    professional_title VARCHAR(150) NOT NULL,
    mobile_number VARCHAR(30) NOT NULL,
    skill_category VARCHAR(100) NOT NULL,
    teaching_experience VARCHAR(50) NOT NULL,
    short_bio TEXT NOT NULL,
    profile_photo_url VARCHAR(255) DEFAULT NULL,
    accepted_terms TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_provider_profiles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_user_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    short_description VARCHAR(500) NOT NULL,
    description TEXT NULL,
    level ENUM('all_levels','beginner','intermediate','advanced') NOT NULL DEFAULT 'all_levels',
    language VARCHAR(80) NOT NULL DEFAULT 'English',
    duration_label VARCHAR(80) NULL,
    lesson_count_estimate INT UNSIGNED NOT NULL DEFAULT 0,
    student_count_estimate INT UNSIGNED NOT NULL DEFAULT 0,
    certification_enabled TINYINT(1) NOT NULL DEFAULT 1,
    includes_json JSON NULL,
    outcomes_json JSON NULL,
    requirements_json JSON NULL,
    thumbnail_path VARCHAR(255) NULL,
    promo_video_url VARCHAR(500) NULL,
    trailer_path VARCHAR(255) NULL,
    gallery_json JSON NULL,
    access_type ENUM('free','paid') NOT NULL DEFAULT 'free',
    price_amount DECIMAL(10,2) NULL,
    currency_code CHAR(3) NOT NULL DEFAULT 'USD',
    coupon_code VARCHAR(40) NULL,
    visibility ENUM('public','private') NOT NULL DEFAULT 'public',
    status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    published_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_courses_provider_user
        FOREIGN KEY (provider_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_courses_provider_status (provider_user_id, status),
    INDEX idx_courses_published (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_sections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    section_order SMALLINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_course_sections_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_course_sections_order (course_id, section_order),
    INDEX idx_sections_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_lessons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    section_id BIGINT UNSIGNED NULL,
    lesson_order SMALLINT UNSIGNED NOT NULL,
    lesson_type ENUM('video','pdf','quiz') NOT NULL,
    title VARCHAR(180) NOT NULL,
    video_path VARCHAR(255) NULL,
    pdf_path VARCHAR(255) NULL,
    quiz_json JSON NULL,
    duration_seconds INT UNSIGNED NULL,
    is_preview TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_course_lessons_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_course_lessons_section
        FOREIGN KEY (section_id) REFERENCES course_sections(id)
        ON DELETE SET NULL,

    INDEX idx_lessons_course (course_id, lesson_order),
    INDEX idx_lessons_section (section_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_resources (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    subtitle VARCHAR(220) NULL,
    file_path VARCHAR(255) NOT NULL,
    mime_type VARCHAR(120) NOT NULL,
    file_size_bytes BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_course_resources_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,

    INDEX idx_resources_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enrollments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    learner_user_id INT UNSIGNED NOT NULL,
    enrollment_status ENUM('active','completed','cancelled','refunded') NOT NULL DEFAULT 'active',
    enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,

    CONSTRAINT fk_enrollments_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_enrollments_learner_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_enrollment_course_learner (course_id, learner_user_id),
    INDEX idx_enrollment_learner (learner_user_id),
    INDEX idx_enrollment_status (enrollment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    enrollment_id BIGINT UNSIGNED NULL,
    learner_user_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    review_text TEXT NULL,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,

    CONSTRAINT fk_reviews_course
        FOREIGN KEY (course_id) REFERENCES courses(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_reviews_enrollment
        FOREIGN KEY (enrollment_id) REFERENCES enrollments(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_reviews_learner_user
        FOREIGN KEY (learner_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_review_course_learner (course_id, learner_user_id),
    INDEX idx_reviews_course_visible (course_id, is_visible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Manual admin/officer seed template
-- Password below is for: Admin@123
-- Change both email and password hash before production use.
INSERT INTO users (full_name, email, password_hash, role, status)
VALUES (
    'Admin Officer',
    'admin@eduskill.com',
    '$2y$10$1OoTj5VfO6/YcxqVsMcTye9P5FRP1kK80wcR8Nbel8wKgFWNnIxwK',
    'officer',
    'active'
)
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    password_hash = VALUES(password_hash),
    role = VALUES(role),
    status = VALUES(status);

SET FOREIGN_KEY_CHECKS = 1;
