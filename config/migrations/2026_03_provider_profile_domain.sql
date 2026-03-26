-- Provider Complete Profile Domain Migration
-- Adds provider education, experience, certifications, and admin approval workflow tables.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS provider_educations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_user_id INT UNSIGNED NOT NULL,
    degree_title VARCHAR(180) NOT NULL,
    institution_name VARCHAR(180) NOT NULL,
    field_of_study VARCHAR(180) NULL,
    start_year SMALLINT UNSIGNED NULL,
    end_year SMALLINT UNSIGNED NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 0,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_provider_educations_user
        FOREIGN KEY (provider_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_provider_educations_user (provider_user_id),
    INDEX idx_provider_educations_years (start_year, end_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS provider_experiences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_user_id INT UNSIGNED NOT NULL,
    job_title VARCHAR(180) NOT NULL,
    company_name VARCHAR(180) NOT NULL,
    employment_type VARCHAR(80) NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 0,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_provider_experiences_user
        FOREIGN KEY (provider_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_provider_experiences_user (provider_user_id),
    INDEX idx_provider_experiences_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS provider_certifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_user_id INT UNSIGNED NOT NULL,
    certificate_name VARCHAR(220) NOT NULL,
    issued_by VARCHAR(220) NOT NULL,
    issue_date DATE NULL,
    expiry_date DATE NULL,
    credential_id VARCHAR(120) NULL,
    credential_url VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_provider_certifications_user
        FOREIGN KEY (provider_user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_provider_certifications_user (provider_user_id),
    INDEX idx_provider_certifications_dates (issue_date, expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS provider_approval_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_user_id INT UNSIGNED NOT NULL,
    request_status ENUM('draft','pending','approved','rejected') NOT NULL DEFAULT 'draft',
    submitted_at DATETIME NULL,
    reviewed_at DATETIME NULL,
    reviewed_by_user_id INT UNSIGNED NULL,
    completion_score DECIMAL(5,2) NOT NULL DEFAULT 0,
    review_note TEXT NULL,
    snapshot_json JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_provider_approval_requests_user
        FOREIGN KEY (provider_user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_provider_approval_requests_reviewer
        FOREIGN KEY (reviewed_by_user_id) REFERENCES users(id)
        ON DELETE SET NULL,

    UNIQUE KEY uq_provider_approval_requests_provider (provider_user_id),
    INDEX idx_provider_approval_requests_status (request_status, submitted_at),
    INDEX idx_provider_approval_requests_reviewed_at (reviewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

