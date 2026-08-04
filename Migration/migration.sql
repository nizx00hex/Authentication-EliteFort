ALTER TABLE `Auth`
ADD `is_verified` tinyint(1) NOT NULL DEFAULT '0' AFTER `password`,
CHANGE `register_date` `register_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP AFTER `is_verified`;


ALTER TABLE `Auth`
CHANGE `register_ip` `register_ip` varchar(32) COLLATE 'utf8mb4_uca1400_ai_ci' NOT NULL AFTER `is_verified`,
CHANGE `register_agent` `register_agent` varchar(128) COLLATE 'utf8mb4_uca1400_ai_ci' NOT NULL AFTER `register_ip`,
CHANGE `register_date` `register_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP AFTER `register_agent`;