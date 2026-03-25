-- EduSkill Marketplace (Auth + Profiles) Migration Script
-- Import this file in phpMyAdmin (SQL tab).
--
-- IMPORTANT for ERROR 1813 (Tablespace exists):
-- If phpMyAdmin shows: "Tablespace for table ... users exists", do this once:
-- 1) Stop MySQL in XAMPP
-- 2) Delete orphan files in: C:\xampp\mysql\data\eduskill_marketplace\
--    (usually users.ibd, and if present learner_profiles.ibd/provider_profiles.ibd)
-- 3) Start MySQL again
-- 4) Re-run this SQL

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS eduskill_marketplace
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE eduskill_marketplace;

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
