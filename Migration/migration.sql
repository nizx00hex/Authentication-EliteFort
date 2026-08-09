-- Adminer 5.4.4 MariaDB 11.8.6-MariaDB-0+deb13u1 from Debian dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `elite-fort` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `elite-fort`;

CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event` varchar(100) NOT NULL,
  `severity` enum('INFO','WARNING','HIGH','CRITICAL') NOT NULL DEFAULT 'INFO',
  `status` enum('SUCCESS','FAILED','BLOCKED') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_uri` varchar(1000) DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `attempted_value` varchar(255) DEFAULT NULL,
  `failure_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_event` (`event`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5661 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


CREATE TABLE `Auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `register_ip` varchar(45) NOT NULL,
  `register_agent` varchar(255) NOT NULL,
  `register_date` datetime NOT NULL DEFAULT current_timestamp(),
  `otp_hash` varchar(255) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `otp_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `otp_last_sent` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_unique` (`username`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `Session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fingerprint` varchar(255) NOT NULL,
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(45) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `u_id_index` (`u_id`),
  CONSTRAINT `session_auth_fk` FOREIGN KEY (`u_id`) REFERENCES `Auth` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2026-08-09 10:38:02 UTC