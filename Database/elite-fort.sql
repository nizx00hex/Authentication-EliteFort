-- Adminer 5.4.4 MariaDB 11.8.6-MariaDB-0+deb13u1 from Debian dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `elite-fort` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `elite-fort`;

CREATE TABLE `Auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `register_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `register_ip` varchar(32) NOT NULL,
  `register_agent` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `Auth` (`id`, `fullname`, `username`, `email`, `password`, `register_date`, `register_ip`, `register_agent`) VALUES
(2,	'Muhammed Nisath',	'nisath',	'nisath.hex@gmail.com',	'$2y$12$hsNOndPgGfkqG3d1i/7KaOt6sAbulse.Wrvd2Scf2dg4qEPUrMqti',	'2026-08-02 11:05:43',	'0.0.0.0',	'Mozila'),
(7,	'Hexona Teora',	'teona',	'teona@gmail.com, ',	'pasdasf',	'2026-08-03 00:12:19',	'::1',	'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0'),
(8,	'Hexona Teora',	'teonaa',	'teonda@gmail.com, ',	'pasdasf',	'2026-08-03 01:43:08',	'::1',	'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0'),
(9,	'Hexona Teora',	'teoxsnaa',	'teondax@gmail.com, ',	'pasdasf',	'2026-08-03 02:01:30',	'::1',	'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0'),
(10,	'Hexona Teora',	'hexiii',	'hexiii@gmail.com, ',	'pasddfsasf',	'2026-08-03 02:06:39',	'::1',	'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0'),
(11,	'Hexona Teora',	'hexiDii',	'hexiDii@gmail.com, ',	'$2y$12$UNLx61T7OmlPrjXr.xkKzejaFBu2Oj0wJyMutQfnPrxtAVnMhiixC',	'2026-08-03 02:10:14',	'::1',	'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0'),
(12,	'Hexona Teora',	'hexiDfii',	'hexiDii@dfsmail.com, ',	'$2y$12$bH2/pnMDHFQ0Mpk1CIOlhu9pQHPo67qxVVk81H3307tQLmTe1t1iC',	'2026-08-03 02:16:11',	'::1',	'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0'),
(13,	'Hexona Teora',	'heDfii',	'he@dfsmail.com, ',	'$2y$12$DQUnAjJFY4CpBO62PMwsdeUy6DefHwBdap5UdOUB0UNYxw9ji2uBa',	'2026-08-03 02:17:27',	'::1',	'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0'),
(14,	'Hexona Teora',	'hDfii',	'h@dfsmail.com, ',	'$2y$12$I5Ip6XPM9uJcbCimq1kD/.i33eDFfOxevUxZ8F1xEmF0rr4U2Qud6',	'2026-08-03 02:17:58',	'::1',	'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0');

CREATE TABLE `Session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fingerprint` varchar(255) NOT NULL,
  `login_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `ip` varchar(32) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `u_id` (`u_id`),
  CONSTRAINT `Session_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `Auth` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `Session` (`id`, `u_id`, `token`, `fingerprint`, `login_time`, `ip`, `user_agent`, `active`) VALUES
(8,	2,	'token',	'fingerprint',	'2026-08-02 10:25:35',	'0.0.0.0',	'user agent',	1);

-- 2026-08-03 17:01:54 UTC
