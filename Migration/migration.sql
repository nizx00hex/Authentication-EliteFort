-- Adminer 4.8.1 MySQL 8.0.46-0ubuntu0.24.04.3 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `Auth`;
CREATE TABLE `Auth` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `register_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `register_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `register_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `otp_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `otp_attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `otp_last_sent` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_unique` (`username`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `Auth` (`id`, `fullname`, `username`, `email`, `password`, `register_ip`, `register_agent`, `register_date`, `otp_hash`, `otp_expiry`, `otp_attempts`, `otp_last_sent`, `is_verified`) VALUES
(5,	'Muhammed Nisath',	'nisath',	'nisath.hex@gmail.com',	'$2y$10$1/2LjolSc3w8uWqvGZYDxucTpqvzDlxpTGvgHwDryy1kpST5aaqmO',	'0.0.0.0',	'Mozila',	'2026-08-04 23:40:38',	NULL,	NULL,	0,	NULL,	0);

DROP TABLE IF EXISTS `Session`;
CREATE TABLE `Session` (
  `id` int NOT NULL AUTO_INCREMENT,
  `u_id` int NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `u_id_index` (`u_id`),
  CONSTRAINT `session_auth_fk` FOREIGN KEY (`u_id`) REFERENCES `Auth` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    event VARCHAR(100) NOT NULL,
    severity ENUM('INFO', 'WARNING', 'HIGH', 'CRITICAL') NOT NULL DEFAULT 'INFO',
    status ENUM('SUCCESS', 'FAILED', 'BLOCKED') NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) NULL,
    request_method VARCHAR(10) NULL,
    request_uri VARCHAR(1000) NULL,
    session_id VARCHAR(128) NULL,
    attempted_value VARCHAR(255) NULL,
    failure_reason VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_id (user_id),
    INDEX idx_ip_address (ip_address),
    INDEX idx_event (event),
    INDEX idx_created_at (created_at)
);
-- 2026-08-04 19:48:31
