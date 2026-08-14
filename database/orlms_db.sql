-- ============================================================================
-- ORLMS - Ordinance and Resolution Lifecycle Management System
-- Database Schema for MariaDB / MySQL (XAMPP)
--
-- Import this file via phpMyAdmin:
--   1. Open phpMyAdmin → http://localhost/phpmyadmin
--   2. Click "Import" tab (top menu)
--   3. Choose this file (orlms_db.sql)
--   4. Click "Go"
-- ============================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `orlms_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `orlms_db`;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1. USERS
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150)    NOT NULL,
    `email`      VARCHAR(255)    NOT NULL,
    `password`   VARCHAR(255)    NOT NULL,
    `role`       ENUM('super_admin','legislative_staff','committee_member','sp_member')
                                 NOT NULL DEFAULT 'legislative_staff',
    `is_active`  TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 2. COMMITTEES
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `committees` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(255) NOT NULL,
    `jurisdiction`    TEXT         NOT NULL,
    `chairperson_id`  INT UNSIGNED DEFAULT NULL,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_committees_chairperson` (`chairperson_id`),
    CONSTRAINT `fk_committees_chairperson`
        FOREIGN KEY (`chairperson_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 3. ORDINANCES
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `ordinances` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `ordinance_no`  VARCHAR(50)   NOT NULL,
    `title`         VARCHAR(500)  NOT NULL,
    `subject`       VARCHAR(500)  DEFAULT NULL,
    `content`       LONGTEXT      DEFAULT NULL,
    `author_id`     INT UNSIGNED  DEFAULT NULL,
    `committee_id`  INT UNSIGNED  DEFAULT NULL,
    `status`        ENUM('draft','submitted','under_review','endorsed','approved','enacted','rejected','archived','published')
                                  NOT NULL DEFAULT 'draft',
    `ai_summary`    TEXT          DEFAULT NULL,
    `file_path`     VARCHAR(500)  DEFAULT NULL,
    `date_filed`    DATE          DEFAULT NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_ordinances_no` (`ordinance_no`),
    KEY `fk_ordinances_author` (`author_id`),
    KEY `fk_ordinances_committee` (`committee_id`),
    KEY `idx_ordinances_status` (`status`),
    CONSTRAINT `fk_ordinances_author`
        FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_ordinances_committee`
        FOREIGN KEY (`committee_id`) REFERENCES `committees` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 4. RESOLUTIONS
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `resolutions` (
    `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `resolution_no`  VARCHAR(50)   NOT NULL,
    `title`          VARCHAR(500)  NOT NULL,
    `subject`        VARCHAR(500)  DEFAULT NULL,
    `content`        LONGTEXT      DEFAULT NULL,
    `author_id`      INT UNSIGNED  DEFAULT NULL,
    `committee_id`   INT UNSIGNED  DEFAULT NULL,
    `status`         ENUM('draft','submitted','under_review','endorsed','approved','enacted','rejected','archived','published')
                                   NOT NULL DEFAULT 'draft',
    `ai_summary`     TEXT          DEFAULT NULL,
    `file_path`      VARCHAR(500)  DEFAULT NULL,
    `date_filed`     DATE          DEFAULT NULL,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_resolutions_no` (`resolution_no`),
    KEY `fk_resolutions_author` (`author_id`),
    KEY `fk_resolutions_committee` (`committee_id`),
    KEY `idx_resolutions_status` (`status`),
    CONSTRAINT `fk_resolutions_author`
        FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_resolutions_committee`
        FOREIGN KEY (`committee_id`) REFERENCES `committees` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 5. AUDIT LOGS
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED  DEFAULT NULL,
    `action`      VARCHAR(100)  NOT NULL,
    `table_name`  VARCHAR(100)  NOT NULL,
    `record_id`   INT UNSIGNED  DEFAULT NULL,
    `old_value`   TEXT          DEFAULT NULL,
    `new_value`   TEXT          DEFAULT NULL,
    `ip_address`  VARCHAR(45)   DEFAULT NULL,
    `location`    VARCHAR(150)  DEFAULT NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_audit_user` (`user_id`),
    KEY `idx_audit_action` (`action`),
    KEY `idx_audit_table` (`table_name`),
    CONSTRAINT `fk_audit_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 6. AI VALIDATION REPORTS
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `ai_validation_reports` (
    `id`                     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `document_type`          ENUM('ordinance','resolution') NOT NULL,
    `document_id`            INT UNSIGNED  NOT NULL,
    `validation_status`      ENUM('passed','flagged','failed') NOT NULL DEFAULT 'flagged',
    `completeness_score`     INT           NOT NULL DEFAULT 0,
    `similarity_score`       FLOAT         NOT NULL DEFAULT 0,
    `similar_document_type`  ENUM('ordinance','resolution') DEFAULT NULL,
    `similar_document_id`    INT UNSIGNED  DEFAULT NULL,
    `similar_document_no`    VARCHAR(50)   DEFAULT NULL,
    `completeness_details`   JSON          DEFAULT NULL,
    `similarity_details`     JSON          DEFAULT NULL,
    `ai_summary`             TEXT          DEFAULT NULL,
    `recommendation`         TEXT          DEFAULT NULL,
    `raw_response`           LONGTEXT      DEFAULT NULL,
    `validated_by`           INT UNSIGNED  DEFAULT NULL,
    `created_at`             DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_aivr_document` (`document_type`, `document_id`),
    KEY `fk_aivr_validator` (`validated_by`),
    CONSTRAINT `fk_aivr_validator`
        FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 7. REVIEW LOGS
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `review_logs` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `document_type` ENUM('ordinance','resolution') NOT NULL,
    `document_id`   INT UNSIGNED  NOT NULL,
    `action`        VARCHAR(100)  NOT NULL,
    `reason`        TEXT          DEFAULT NULL,
    `reviewed_by`   INT UNSIGNED  DEFAULT NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_rl_document` (`document_type`, `document_id`),
    KEY `fk_rl_reviewer` (`reviewed_by`),
    CONSTRAINT `fk_rl_reviewer`
        FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 8. AMENDMENTS
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `amendments` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `document_type` ENUM('ordinance','resolution') NOT NULL,
    `document_id`   INT UNSIGNED  NOT NULL,
    `amendment_no`  VARCHAR(100)  NOT NULL,
    `description`   TEXT          NOT NULL,
    `changes`       LONGTEXT      NOT NULL,
    `status`        ENUM('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
    `amended_by`    INT UNSIGNED  DEFAULT NULL,
    `amended_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_amend_document` (`document_type`, `document_id`),
    KEY `fk_amend_user` (`amended_by`),
    CONSTRAINT `fk_amend_user`
        FOREIGN KEY (`amended_by`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 9. MONITORING LOGS
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `monitoring_logs` (
    `id`                     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `document_type`          ENUM('ordinance','resolution') NOT NULL,
    `document_id`            INT UNSIGNED  NOT NULL,
    `implementation_status`  ENUM('pending','ongoing','completed','delayed') NOT NULL,
    `implementation_notes`   TEXT          NOT NULL,
    `logged_by`              INT UNSIGNED  DEFAULT NULL,
    `logged_at`              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ml_document` (`document_type`, `document_id`),
    KEY `fk_ml_user` (`logged_by`),
    CONSTRAINT `fk_ml_user`
        FOREIGN KEY (`logged_by`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- 10. PUBLICATIONS
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `publications` (
    `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `document_type`   ENUM('ordinance','resolution') NOT NULL,
    `document_id`     INT UNSIGNED  NOT NULL,
    `publication_ref` VARCHAR(255)  NOT NULL,
    `plain_summary`   TEXT          NOT NULL,
    `file_path`       VARCHAR(500)  DEFAULT NULL,
    `published_by`    INT UNSIGNED  DEFAULT NULL,
    `published_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_pub_document` (`document_type`, `document_id`),
    KEY `fk_pub_user` (`published_by`),
    CONSTRAINT `fk_pub_user`
        FOREIGN KEY (`published_by`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- DEFAULT ADMIN ACCOUNT
-- Email:    admin@orlms.ph
-- Password: Admin@123 (bcrypt hashed)
-- ─────────────────────────────────────────────────────────────────────────────
INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_active`)
VALUES (
    'Administrator',
    'admin@orlms.ph',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'super_admin',
    1
);

-- ============================================================================
-- DONE! 🎉
-- 10 tables created + 1 default admin account
--
-- Default login:
--   Email:    admin@orlms.ph
--   Password: password
-- 
-- (Change the password after first login!)
-- ============================================================================
