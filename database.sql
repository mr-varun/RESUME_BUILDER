-- ResumeForge Database Schema
-- Run this once to set up the database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `resume_builder`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `resume_builder`;

-- --------------------------------------------------------
-- Users table
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `phone`      VARCHAR(20)  DEFAULT NULL,
  `avatar`     VARCHAR(255) DEFAULT NULL,
  `is_active`  TINYINT(1)  NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Documents table (stores full document data as JSON)
-- --------------------------------------------------------
CREATE TABLE `documents` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT UNSIGNED DEFAULT NULL,
  `doc_type`       ENUM('resume','cv','biodata') NOT NULL DEFAULT 'resume',
  `title`          VARCHAR(200) NOT NULL DEFAULT 'Untitled Document',
  `status`         ENUM('draft','final') NOT NULL DEFAULT 'draft',
  `data`           LONGTEXT NOT NULL DEFAULT (JSON_OBJECT()),
  `public_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `public_slug`    VARCHAR(32)  DEFAULT NULL,
  `link_expiry`    DATE         DEFAULT NULL,
  `view_count`     INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_slug` (`public_slug`),
  CONSTRAINT `fk_doc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Quick-mode customers (for biodata print shops)
-- --------------------------------------------------------
CREATE TABLE `customers` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(100) NOT NULL,
  `phone`       VARCHAR(20)  DEFAULT NULL,
  `email`       VARCHAR(150) DEFAULT NULL,
  `last_doc_id` INT UNSIGNED DEFAULT NULL,
  `doc_count`   INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_cust_doc` FOREIGN KEY (`last_doc_id`) REFERENCES `documents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Default demo user (password: demo1234)
-- --------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`) VALUES
('Demo User', 'demo@example.com', '$2y$12$9i7O5j.cGFnUHnkBsAPYnO5y7vNdX3tPTpKjJb2vN8bI3J6xAk6Ci');
