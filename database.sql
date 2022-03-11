-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.33 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table sms.cg_app_config
CREATE TABLE IF NOT EXISTS `cg_app_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_app_config: ~67 rows (approximately)
/*!40000 ALTER TABLE `cg_app_config` DISABLE KEYS */;
INSERT INTO `cg_app_config` (`id`, `setting`, `value`, `created_at`, `updated_at`) VALUES
	(1, 'app_name', 'arabcodesms', '2022-03-07 04:26:53', '2022-03-07 02:40:48'),
	(2, 'app_title', 'التسويق بواسطة sms', '2022-03-07 04:26:53', '2022-03-07 02:40:48'),
	(3, 'app_keyword', 'تسويق', '2022-03-07 04:26:53', '2022-03-07 02:40:48'),
	(4, 'license', '98798797889', '2022-03-07 04:26:53', '2022-03-07 04:29:13'),
	(5, 'license_type', 'Extended license', '2022-03-07 04:26:53', '2022-03-07 04:29:13'),
	(6, 'valid_domain', 'yes', '2022-03-07 04:26:53', '2022-03-07 04:29:13'),
	(7, 'from_email', 'info@arabcode.online', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(8, 'from_name', 'Ultimate SMS', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(9, 'company_address', 'House#, <br><br><br>مأرب<br><br>اليمن', '2022-03-07 04:26:53', '2022-03-07 02:40:48'),
	(10, 'software_version', '3.0.1', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(11, 'footer_text', 'Copyright © Arabcode - 2022', '2022-03-07 04:26:53', '2022-03-07 02:40:48'),
	(12, 'app_logo', 'images/logo/e2b0daca79363c491f378bea714cb8f0.png', '2022-03-07 04:26:53', '2022-03-07 04:39:53'),
	(13, 'app_favicon', 'images/logo/4cdcdd8e2f25ae941305986c13d444c1.png', '2022-03-07 04:26:53', '2022-03-07 04:39:53'),
	(14, 'country', 'Saudi Arabia', '2022-03-07 04:26:53', '2022-03-07 02:40:48'),
	(15, 'timezone', 'Asia/Riyadh', '2022-03-07 04:26:53', '2022-03-07 02:40:48'),
	(16, 'app_stage', 'live', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(17, 'maintenance_mode', '1', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(18, 'maintenance_mode_message', 'We\'re undergoing a bit of scheduled maintenance.', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(19, 'maintenance_mode_end', 'Jan 5, 2021 15:37:25', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(20, 'php_bin_path', '/usr/bin/php', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(21, 'driver', 'default', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(22, 'host', 'smtp.gmail.com', '2022-03-07 04:26:53', '2022-03-07 04:26:53'),
	(23, 'username', 'user@example.com', '2022-03-07 04:26:54', '2022-03-07 04:26:54'),
	(24, 'password', 'testpassword', '2022-03-07 04:26:54', '2022-03-07 04:26:54'),
	(25, 'port', '587', '2022-03-07 04:26:54', '2022-03-07 04:26:54'),
	(26, 'encryption', 'tls', '2022-03-07 04:26:54', '2022-03-07 04:26:54'),
	(27, 'date_format', 'Y/m/d', '2022-03-07 04:26:54', '2022-03-07 02:40:48'),
	(28, 'language', 'ar', '2022-03-07 04:26:54', '2022-03-07 02:40:48'),
	(29, 'client_registration', '1', '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(30, 'registration_verification', '1', '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(31, 'two_factor', '0', '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(32, 'two_factor_send_by', 'email', '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(33, 'captcha_in_login', '0', '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(34, 'captcha_in_client_registration', '0', '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(35, 'captcha_site_key', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(36, 'captcha_secret_key', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(37, 'login_with_facebook', '0', '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(38, 'facebook_client_id', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(39, 'facebook_client_secret', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(40, 'login_with_twitter', '0', '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(41, 'twitter_client_id', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(42, 'twitter_client_secret', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:18'),
	(43, 'login_with_google', '0', '2022-03-07 04:26:54', '2022-03-07 02:19:19'),
	(44, 'google_client_id', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:19'),
	(45, 'google_client_secret', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:19'),
	(46, 'login_with_github', '0', '2022-03-07 04:26:54', '2022-03-07 02:19:19'),
	(47, 'github_client_id', NULL, '2022-03-07 04:26:54', '2022-03-07 02:19:19'),
	(48, 'github_client_secret', NULL, '2022-03-07 04:26:55', '2022-03-07 02:19:19'),
	(49, 'notification_sms_gateway', '622541c73b657', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(50, 'notification_sender_id', 'arabcodesms', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(51, 'notification_phone', '+967714311582', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(52, 'notification_from_name', 'arab6ode@gmail.com', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(53, 'notification_email', 'arab6ode@gmail.com', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(54, 'sender_id_notification_email', 'true', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(55, 'sender_id_notification_sms', 'true', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(56, 'user_registration_notification_email', 'true', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(57, 'user_registration_notification_sms', '0', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(58, 'subscription_notification_email', 'true', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(59, 'subscription_notification_sms', '0', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(60, 'keyword_notification_email', 'true', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(61, 'keyword_notification_sms', '0', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(62, 'phone_number_notification_email', 'true', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(63, 'phone_number_notification_sms', '0', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(64, 'block_message_notification_email', '0', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(65, 'block_message_notification_sms', '0', '2022-03-07 04:26:55', '2022-03-07 02:25:38'),
	(66, 'unsubscribe_message', 'Reply Stop to unsubscribe', '2022-03-07 04:26:55', '2022-03-07 04:26:55'),
	(67, 'custom_script', NULL, '2022-03-07 04:26:55', '2022-03-07 02:40:48');
/*!40000 ALTER TABLE `cg_app_config` ENABLE KEYS */;

-- Dumping structure for table sms.cg_blacklists
CREATE TABLE IF NOT EXISTS `cg_blacklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_blacklists_user_id_foreign` (`user_id`),
  CONSTRAINT `cg_blacklists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_blacklists: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_blacklists` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_blacklists` ENABLE KEYS */;

-- Dumping structure for table sms.cg_campaigns
CREATE TABLE IF NOT EXISTS `cg_campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `campaign_name` text COLLATE utf8mb4_unicode_ci,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `media_url` longtext COLLATE utf8mb4_unicode_ci,
  `language` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `api_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cache` text COLLATE utf8mb4_unicode_ci,
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `schedule_time` timestamp NULL DEFAULT NULL,
  `schedule_type` enum('onetime','recurring') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency_cycle` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency_amount` int(11) DEFAULT NULL,
  `frequency_unit` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurring_end` timestamp NULL DEFAULT NULL,
  `run_at` timestamp NULL DEFAULT NULL,
  `delivery_at` timestamp NULL DEFAULT NULL,
  `batch_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_campaigns_user_id_foreign` (`user_id`),
  CONSTRAINT `cg_campaigns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_campaigns: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_campaigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_campaigns` ENABLE KEYS */;

-- Dumping structure for table sms.cg_campaigns_lists
CREATE TABLE IF NOT EXISTS `cg_campaigns_lists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `contact_list_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_campaigns_lists_campaign_id_foreign` (`campaign_id`),
  KEY `cg_campaigns_lists_contact_list_id_foreign` (`contact_list_id`),
  CONSTRAINT `cg_campaigns_lists_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `cg_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_campaigns_lists_contact_list_id_foreign` FOREIGN KEY (`contact_list_id`) REFERENCES `cg_contact_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_campaigns_lists: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_campaigns_lists` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_campaigns_lists` ENABLE KEYS */;

-- Dumping structure for table sms.cg_campaigns_recipients
CREATE TABLE IF NOT EXISTS `cg_campaigns_recipients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `recipient` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_campaigns_recipients_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `cg_campaigns_recipients_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `cg_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_campaigns_recipients: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_campaigns_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_campaigns_recipients` ENABLE KEYS */;

-- Dumping structure for table sms.cg_campaigns_senderids
CREATE TABLE IF NOT EXISTS `cg_campaigns_senderids` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `sender_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `originator` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_campaigns_senderids_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `cg_campaigns_senderids_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `cg_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_campaigns_senderids: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_campaigns_senderids` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_campaigns_senderids` ENABLE KEYS */;

-- Dumping structure for table sms.cg_campaigns_sending_servers
CREATE TABLE IF NOT EXISTS `cg_campaigns_sending_servers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `sending_server_id` bigint(20) unsigned NOT NULL,
  `fitness` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_campaigns_sending_servers_campaign_id_foreign` (`campaign_id`),
  KEY `cg_campaigns_sending_servers_sending_server_id_foreign` (`sending_server_id`),
  CONSTRAINT `cg_campaigns_sending_servers_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `cg_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_campaigns_sending_servers_sending_server_id_foreign` FOREIGN KEY (`sending_server_id`) REFERENCES `cg_sending_servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_campaigns_sending_servers: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_campaigns_sending_servers` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_campaigns_sending_servers` ENABLE KEYS */;

-- Dumping structure for table sms.cg_chat_boxes
CREATE TABLE IF NOT EXISTS `cg_chat_boxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_chat_boxes_user_id_foreign` (`user_id`),
  CONSTRAINT `cg_chat_boxes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_chat_boxes: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_chat_boxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_chat_boxes` ENABLE KEYS */;

-- Dumping structure for table sms.cg_chat_box_messages
CREATE TABLE IF NOT EXISTS `cg_chat_box_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `box_id` bigint(20) unsigned NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `media_url` longtext COLLATE utf8mb4_unicode_ci,
  `sms_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sms',
  `send_by` enum('from','to') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sending_server_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_chat_box_messages_box_id_foreign` (`box_id`),
  KEY `cg_chat_box_messages_sending_server_id_foreign` (`sending_server_id`),
  CONSTRAINT `cg_chat_box_messages_box_id_foreign` FOREIGN KEY (`box_id`) REFERENCES `cg_chat_boxes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_chat_box_messages_sending_server_id_foreign` FOREIGN KEY (`sending_server_id`) REFERENCES `cg_sending_servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_chat_box_messages: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_chat_box_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_chat_box_messages` ENABLE KEYS */;

-- Dumping structure for table sms.cg_contacts
CREATE TABLE IF NOT EXISTS `cg_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `birth_date` date DEFAULT NULL,
  `anniversary_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_contacts_customer_id_foreign` (`customer_id`),
  KEY `cg_contacts_group_id_foreign` (`group_id`),
  CONSTRAINT `cg_contacts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_contacts_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `cg_contact_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_contacts: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_contacts` ENABLE KEYS */;

-- Dumping structure for table sms.cg_contacts_custom_field
CREATE TABLE IF NOT EXISTS `cg_contacts_custom_field` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_contacts_custom_field_contact_id_foreign` (`contact_id`),
  CONSTRAINT `cg_contacts_custom_field_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `cg_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_contacts_custom_field: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_contacts_custom_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_contacts_custom_field` ENABLE KEYS */;

-- Dumping structure for table sms.cg_contact_groups
CREATE TABLE IF NOT EXISTS `cg_contact_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `send_welcome_sms` tinyint(1) NOT NULL DEFAULT '1',
  `unsubscribe_notification` tinyint(1) NOT NULL DEFAULT '1',
  `send_keyword_message` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `signup_sms` text COLLATE utf8mb4_unicode_ci,
  `welcome_sms` text COLLATE utf8mb4_unicode_ci,
  `unsubscribe_sms` text COLLATE utf8mb4_unicode_ci,
  `cache` text COLLATE utf8mb4_unicode_ci,
  `batch_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_contact_groups_customer_id_foreign` (`customer_id`),
  CONSTRAINT `cg_contact_groups_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_contact_groups: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_contact_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_contact_groups` ENABLE KEYS */;

-- Dumping structure for table sms.cg_contact_groups_optin_keywords
CREATE TABLE IF NOT EXISTS `cg_contact_groups_optin_keywords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_group` bigint(20) unsigned NOT NULL,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_contact_groups_optin_keywords_contact_group_foreign` (`contact_group`),
  CONSTRAINT `cg_contact_groups_optin_keywords_contact_group_foreign` FOREIGN KEY (`contact_group`) REFERENCES `cg_contact_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_contact_groups_optin_keywords: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_contact_groups_optin_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_contact_groups_optin_keywords` ENABLE KEYS */;

-- Dumping structure for table sms.cg_contact_groups_optout_keywords
CREATE TABLE IF NOT EXISTS `cg_contact_groups_optout_keywords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_group` bigint(20) unsigned NOT NULL,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_contact_groups_optout_keywords_contact_group_foreign` (`contact_group`),
  CONSTRAINT `cg_contact_groups_optout_keywords_contact_group_foreign` FOREIGN KEY (`contact_group`) REFERENCES `cg_contact_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_contact_groups_optout_keywords: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_contact_groups_optout_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_contact_groups_optout_keywords` ENABLE KEYS */;

-- Dumping structure for table sms.cg_countries
CREATE TABLE IF NOT EXISTS `cg_countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_countries: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_countries` DISABLE KEYS */;
INSERT INTO `cg_countries` (`id`, `name`, `iso_code`, `country_code`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Zimbabwe', 'ZW', '263', 1, '2022-03-07 04:26:56', '2022-03-07 04:27:05');
/*!40000 ALTER TABLE `cg_countries` ENABLE KEYS */;

-- Dumping structure for table sms.cg_csv_data
CREATE TABLE IF NOT EXISTS `cg_csv_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ref_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `csv_filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `csv_header` tinyint(1) NOT NULL DEFAULT '0',
  `csv_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_csv_data: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_csv_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_csv_data` ENABLE KEYS */;

-- Dumping structure for table sms.cg_currencies
CREATE TABLE IF NOT EXISTS `cg_currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_currencies_user_id_foreign` (`user_id`),
  CONSTRAINT `cg_currencies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_currencies: ~11 rows (approximately)
/*!40000 ALTER TABLE `cg_currencies` DISABLE KEYS */;
INSERT INTO `cg_currencies` (`id`, `uid`, `user_id`, `name`, `code`, `format`, `status`, `created_at`, `updated_at`) VALUES
	(1, '6225353f7ef49', 1, 'US Dollar', 'USD', '${PRICE}', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(2, '6225353f88762', 1, 'EURO', 'EUR', '€{PRICE}', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(3, '6225353f911e4', 1, 'British Pound', 'GBP', '£{PRICE}', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(4, '6225353fabc63', 1, 'Japanese Yen', 'JPY', '¥{PRICE}', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(5, '6225353fbf06e', 1, 'Russian Ruble', 'RUB', '‎₽{PRICE}', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(6, '6225353fcc6c1', 1, 'Vietnam Dong', 'VND', '{PRICE}₫', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(7, '6225353fe2044', 1, 'Brazilian Real', 'BRL', '‎R${PRICE}', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(8, '6225353fea4ed', 1, 'Bangladeshi Taka', 'BDT', '‎৳{PRICE}', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(9, '6225353ff24fc', 1, 'Canadian Dollar', 'CAD', '‎C${PRICE}', 1, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(10, '622535400673d', 1, 'Indian rupee', 'INR', '‎₹{PRICE}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(11, '622535400e830', 1, 'Nigerian Naira', 'CBN', '‎₦{PRICE}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(12, '6225395a13537', 1, 'ر.س', 'SAR', 'sar(price)', 1, '2022-03-07 01:44:42', '2022-03-07 01:44:42');
/*!40000 ALTER TABLE `cg_currencies` ENABLE KEYS */;

-- Dumping structure for table sms.cg_customers
CREATE TABLE IF NOT EXISTS `cg_customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `contact_id` bigint(20) unsigned DEFAULT NULL,
  `parent` bigint(20) unsigned DEFAULT NULL,
  `company` text COLLATE utf8mb4_unicode_ci,
  `website` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `financial_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `financial_city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `financial_postcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notifications` text COLLATE utf8mb4_unicode_ci,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_customers_user_id_foreign` (`user_id`),
  KEY `cg_customers_contact_id_foreign` (`contact_id`),
  CONSTRAINT `cg_customers_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `cg_contacts` (`id`),
  CONSTRAINT `cg_customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_customers: ~2 rows (approximately)
/*!40000 ALTER TABLE `cg_customers` DISABLE KEYS */;
INSERT INTO `cg_customers` (`id`, `uid`, `user_id`, `contact_id`, `parent`, `company`, `website`, `address`, `city`, `postcode`, `financial_address`, `financial_city`, `financial_postcode`, `tax_number`, `state`, `country`, `phone`, `notifications`, `permissions`, `created_at`, `updated_at`) VALUES
	(1, '62253542c206a', 1, NULL, NULL, NULL, 'https://localhost/sms', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{"login":"no","sender_id":"yes","keyword":"yes","subscription":"yes","promotion":"yes","profile":"yes"}', '["access_backend","view_reports","view_contact_group","create_contact_group","update_contact_group","delete_contact_group","view_contact","create_contact","update_contact","delete_contact","view_sender_id","create_sender_id","view_blacklist","create_blacklist","delete_blacklist","sms_campaign_builder","sms_quick_send","sms_bulk_messages","sms_template","developers"]', '2022-03-07 04:27:14', '2022-03-07 04:27:14'),
	(2, '62253b35d2e20', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{"login":"no","tickets":"yes","sender_id":"yes","keyword":"yes","subscription":"yes","promotion":"yes","profile":"yes"}', '["view_reports","create_sending_servers","view_contact_group","create_contact_group","update_contact_group","delete_contact_group","view_contact","create_contact","update_contact","delete_contact","view_numbers","buy_numbers","release_numbers","view_keywords","buy_keywords","update_keywords","release_keywords","view_sender_id","create_sender_id","delete_sender_id","view_blacklist","create_blacklist","delete_blacklist","sms_campaign_builder","sms_quick_send","sms_bulk_messages","voice_campaign_builder","voice_quick_send","voice_bulk_messages","mms_campaign_builder","mms_quick_send","whatsapp_campaign_builder","whatsapp_quick_send","whatsapp_bulk_messages","sms_template","chat_box","developers","access_backend"]', '2022-03-07 01:52:37', '2022-03-07 02:18:15');
/*!40000 ALTER TABLE `cg_customers` ENABLE KEYS */;

-- Dumping structure for table sms.cg_custom_sending_servers
CREATE TABLE IF NOT EXISTS `cg_custom_sending_servers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `server_id` bigint(20) unsigned NOT NULL,
  `http_request_method` enum('get','post') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'get',
  `json_encoded_post` tinyint(1) NOT NULL DEFAULT '0',
  `content_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_type_accept` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `character_encoding` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ssl_certificate_verification` tinyint(1) NOT NULL DEFAULT '0',
  `authorization` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `multi_sms_delimiter` enum(',',';','array') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username_param` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_value` text COLLATE utf8mb4_unicode_ci,
  `password_status` tinyint(1) NOT NULL DEFAULT '0',
  `action_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_value` text COLLATE utf8mb4_unicode_ci,
  `action_status` tinyint(1) NOT NULL DEFAULT '0',
  `source_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_value` text COLLATE utf8mb4_unicode_ci,
  `source_status` tinyint(1) NOT NULL DEFAULT '0',
  `destination_param` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_param` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unicode_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unicode_value` text COLLATE utf8mb4_unicode_ci,
  `unicode_status` tinyint(1) NOT NULL DEFAULT '0',
  `route_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_value` text COLLATE utf8mb4_unicode_ci,
  `route_status` tinyint(1) NOT NULL DEFAULT '0',
  `language_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_value` text COLLATE utf8mb4_unicode_ci,
  `language_status` tinyint(1) NOT NULL DEFAULT '0',
  `custom_one_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_one_value` text COLLATE utf8mb4_unicode_ci,
  `custom_one_status` tinyint(1) NOT NULL DEFAULT '0',
  `custom_two_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_two_value` text COLLATE utf8mb4_unicode_ci,
  `custom_two_status` tinyint(1) NOT NULL DEFAULT '0',
  `custom_three_param` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_three_value` text COLLATE utf8mb4_unicode_ci,
  `custom_three_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_custom_sending_servers_server_id_foreign` (`server_id`),
  CONSTRAINT `cg_custom_sending_servers_server_id_foreign` FOREIGN KEY (`server_id`) REFERENCES `cg_sending_servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_custom_sending_servers: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_custom_sending_servers` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_custom_sending_servers` ENABLE KEYS */;

-- Dumping structure for table sms.cg_email_templates
CREATE TABLE IF NOT EXISTS `cg_email_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_email_templates: ~10 rows (approximately)
/*!40000 ALTER TABLE `cg_email_templates` DISABLE KEYS */;
INSERT INTO `cg_email_templates` (`id`, `uid`, `name`, `slug`, `subject`, `content`, `status`, `created_at`, `updated_at`) VALUES
	(1, '62253540885c4', 'Customer Registration', 'customer_registration', 'Welcome to {app_name}', 'Hi {first_name} {last_name},\n                                      Welcome to {app_name}! This message is an automated reply to your User Access request. Login to your User panel by using the details below:\n                                      {login_url}\n                                      Email: {email_address}\n                                      Password: {password}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(2, '6225354093695', 'Customer Registration Verification', 'registration_verification', 'Registration Verification From {app_name}', 'Hi {first_name} {last_name},\n                                      Welcome to {app_name}! This message is an automated reply to your account verification request. Click the following url to verify your account:\n                                      {verification_url}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(3, '622535409b57c', 'Password Reset', 'password_reset', '{app_name} New Password', 'Hi {first_name} {last_name},\n                                      Password Reset Successfully! This message is an automated reply to your password reset request. Login to your account to set up your all details by using the details below:\n                                      {login_url}\n                                      Email: {email_address}\n                                      Password: {password}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(4, '62253540a3c7e', 'Forgot Password', 'forgot_password', '{app_name} password change request', 'Hi {first_name} {last_name},\n                                      Password Reset Successfully! This message is an automated reply to your password reset request. Click this link to reset your password:\n                                      {forgot_password_link}\n                                      Notes: Until your password has been changed, your current password will remain valid. The Forgot Password Link will be available for a limited time only.', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(5, '62253540abc67', 'Login Notification', 'login_notification', 'Your {app_name} Login Information', 'Hi,\n                                      You successfully logged in to {app_name} on {time} from ip {ip_address}.  If you did not login, please contact our support immediately.', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(6, '62253540b4124', 'Customer Registration Notification', 'registration_notification', 'New customer registered to {app_name}', 'Hi,\n                                      New customer named {first_name} {last_name} registered. Login to your portal to show details.\n                                      {customer_profile_url}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(7, '62253540cc683', 'Sender ID Notification', 'sender_id_notification', 'New sender id requested to {app_name}', 'Hi,\n                                      New sender id {sender_id} requested. Login to your portal to show details.\n                                      {sender_id_url}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(8, '62253540e2318', 'Subscription Notification', 'subscription_notification', 'New subscription to {app_name}', 'Hi,\n                                      New subscription made on {app_name}. Login to your portal to show details.\n                                      {invoice_url}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(9, '62253540ea350', 'Keyword purchase Notification', 'keyword_purchase_notification', 'New keyword sale on {app_name}', 'Hi,\n                                      New keyword sale made on {app_name}. Login to your portal to show details.\n                                      {keyword_url}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12'),
	(10, '62253540f27d3', 'Phone number purchase Notification', 'number_purchase_notification', 'New phone number sale on {app_name}', 'Hi,\n                                      New phone number sale made on {app_name}. Login to your portal to show details.\n                                      {number_url}', 1, '2022-03-07 04:27:12', '2022-03-07 04:27:12');
/*!40000 ALTER TABLE `cg_email_templates` ENABLE KEYS */;

-- Dumping structure for table sms.cg_failed_jobs
CREATE TABLE IF NOT EXISTS `cg_failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_failed_jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_failed_jobs` ENABLE KEYS */;

-- Dumping structure for table sms.cg_import_job_histories
CREATE TABLE IF NOT EXISTS `cg_import_job_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing',
  `options` text COLLATE utf8mb4_unicode_ci,
  `batch_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_import_job_histories: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_import_job_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_import_job_histories` ENABLE KEYS */;

-- Dumping structure for table sms.cg_invoices
CREATE TABLE IF NOT EXISTS `cg_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `payment_method` bigint(20) unsigned NOT NULL,
  `amount` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_invoices_user_id_foreign` (`user_id`),
  KEY `cg_invoices_currency_id_foreign` (`currency_id`),
  KEY `cg_invoices_payment_method_foreign` (`payment_method`),
  CONSTRAINT `cg_invoices_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `cg_currencies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_invoices_payment_method_foreign` FOREIGN KEY (`payment_method`) REFERENCES `cg_payment_methods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_invoices: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_invoices` DISABLE KEYS */;
INSERT INTO `cg_invoices` (`id`, `uid`, `user_id`, `currency_id`, `payment_method`, `amount`, `type`, `description`, `transaction_id`, `status`, `created_at`, `updated_at`) VALUES
	(1, '62253b62e2870', 2, 12, 16, '0.00', 'subscription', 'Payment for plan مجاني', '622539cde530c', 'paid', '2022-03-07 01:53:22', '2022-03-07 01:53:22');
/*!40000 ALTER TABLE `cg_invoices` ENABLE KEYS */;

-- Dumping structure for table sms.cg_jobs
CREATE TABLE IF NOT EXISTS `cg_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_jobs` ENABLE KEYS */;

-- Dumping structure for table sms.cg_job_batches
CREATE TABLE IF NOT EXISTS `cg_job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_job_batches: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_job_batches` ENABLE KEYS */;

-- Dumping structure for table sms.cg_keywords
CREATE TABLE IF NOT EXISTS `cg_keywords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` text COLLATE utf8mb4_unicode_ci,
  `reply_text` text COLLATE utf8mb4_unicode_ci,
  `reply_voice` text COLLATE utf8mb4_unicode_ci,
  `reply_mms` text COLLATE utf8mb4_unicode_ci,
  `status` enum('available','assigned','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `price` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `billing_cycle` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency_amount` int(11) DEFAULT NULL,
  `frequency_unit` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validity_date` date DEFAULT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_keywords_user_id_foreign` (`user_id`),
  KEY `cg_keywords_currency_id_foreign` (`currency_id`),
  CONSTRAINT `cg_keywords_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `cg_currencies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_keywords_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_keywords: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_keywords` ENABLE KEYS */;

-- Dumping structure for table sms.cg_languages
CREATE TABLE IF NOT EXISTS `cg_languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_languages: ~4 rows (approximately)
/*!40000 ALTER TABLE `cg_languages` DISABLE KEYS */;
INSERT INTO `cg_languages` (`id`, `name`, `code`, `iso_code`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'English', 'en', 'us', 1, '2022-03-07 04:27:05', '2022-03-07 04:27:05'),
	(2, 'German', 'de', 'de', 1, '2022-03-07 04:27:05', '2022-03-07 04:27:05'),
	(3, 'French', 'fr', 'fr', 1, '2022-03-07 04:27:05', '2022-03-07 04:27:05'),
	(4, 'Portuguese', 'pt', 'pt', 1, '2022-03-07 04:27:06', '2022-03-07 04:27:06'),
	(5, 'Arabic', 'ar', 'sa', 1, '2022-03-07 04:30:30', '2022-03-07 04:30:30');
/*!40000 ALTER TABLE `cg_languages` ENABLE KEYS */;

-- Dumping structure for table sms.cg_migrations
CREATE TABLE IF NOT EXISTS `cg_migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_migrations: ~47 rows (approximately)
/*!40000 ALTER TABLE `cg_migrations` DISABLE KEYS */;
INSERT INTO `cg_migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2018_07_26_134739_create_app_config_table', 1),
	(4, '2018_07_27_112022_create_payment_methods_table', 1),
	(5, '2018_10_18_201850_create_countries_table', 1),
	(6, '2018_12_01_122106_create_languages_table', 1),
	(7, '2018_12_01_130207_create_contact_groups_table', 1),
	(8, '2018_12_01_130424_create_contacts_table', 1),
	(9, '2018_12_01_191808_create_currencies_table', 1),
	(10, '2018_12_01_192942_create_customers_table', 1),
	(11, '2018_12_01_193935_create_plan_table', 1),
	(12, '2018_12_02_190238_setup_permission_table', 1),
	(13, '2019_03_09_065029_create_subscriptions_table', 1),
	(14, '2019_08_19_000000_create_failed_jobs_table', 1),
	(15, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(16, '2020_02_19_092836_create_sending_servers_table', 1),
	(17, '2020_02_25_121119_create_jobs_table', 1),
	(18, '2020_04_27_174308_create_plans_sending_servers_table', 1),
	(19, '2020_04_27_174552_create_plans_coverage_countries_table', 1),
	(20, '2020_05_01_184946_create_custom_sending_servers_table', 1),
	(21, '2020_05_27_084347_create_keywords_table', 1),
	(22, '2020_05_30_123429_create_senderid_table', 1),
	(23, '2020_05_31_175740_create_subscription_transactions_table', 1),
	(24, '2020_05_31_175813_create_subscription_logs_table', 1),
	(25, '2020_06_29_124959_create_email_templates_table', 1),
	(26, '2020_11_14_105312_create_phone_numbers_table', 1),
	(27, '2020_11_15_122755_create_blacklists_table', 1),
	(28, '2020_11_17_134741_create_spam_word_table', 1),
	(29, '2020_12_17_120510_create_contact_groups_optin_keywords_table', 1),
	(30, '2020_12_17_120525_create_contact_groups_optout_keywords_table', 1),
	(31, '2020_12_17_132523_create_contacts_custom_field_table', 1),
	(32, '2021_02_07_101007_create_template_tags_table', 1),
	(33, '2021_02_10_192026_create_job_batches_table', 1),
	(34, '2021_02_12_135406_create_import_job_histories_table', 1),
	(35, '2021_02_24_095516_create_senderid_plans_table', 1),
	(36, '2021_02_27_094439_create_templates_table', 1),
	(37, '2021_03_01_062609_create_campaigns_table', 1),
	(38, '2021_03_02_060310_create_reports_table', 1),
	(39, '2021_03_07_033941_create_campaigns_lists_table', 1),
	(40, '2021_03_07_034338_create_campaigns_senderids_table', 1),
	(41, '2021_03_07_035250_create_campaigns_recipients_table', 1),
	(42, '2021_03_08_054924_create_campaigns_sending_servers_table', 1),
	(43, '2021_03_25_135511_create_invoices_table', 1),
	(44, '2021_03_31_081200_create_csv_data_table', 1),
	(45, '2021_03_31_125855_create_chat_boxes_table', 1),
	(46, '2021_03_31_130224_create_chat_box_messages_table', 1),
	(47, '2021_04_08_140645_create_notifications_table', 1);
/*!40000 ALTER TABLE `cg_migrations` ENABLE KEYS */;

-- Dumping structure for table sms.cg_notifications
CREATE TABLE IF NOT EXISTS `cg_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '1',
  `notification_for` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `notification_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `mark_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `cg_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_notifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_notifications` DISABLE KEYS */;
INSERT INTO `cg_notifications` (`id`, `uid`, `user_id`, `notification_for`, `notification_type`, `message`, `mark_read`, `created_at`, `updated_at`) VALUES
	(1, '62253b36cb653', 1, 'admin', 'user', 'mohammed hudair Registered', 1, '2022-03-07 01:52:38', '2022-03-07 02:16:27');
/*!40000 ALTER TABLE `cg_notifications` ENABLE KEYS */;

-- Dumping structure for table sms.cg_password_resets
CREATE TABLE IF NOT EXISTS `cg_password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `cg_password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_password_resets: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_password_resets` ENABLE KEYS */;

-- Dumping structure for table sms.cg_payment_methods
CREATE TABLE IF NOT EXISTS `cg_payment_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_payment_methods: ~16 rows (approximately)
/*!40000 ALTER TABLE `cg_payment_methods` DISABLE KEYS */;
INSERT INTO `cg_payment_methods` (`id`, `uid`, `name`, `type`, `options`, `status`, `created_at`, `updated_at`) VALUES
	(1, '622535416c414', 'PayPal', 'paypal', '{"environment":"production","client_id":"gjgjg","secret":"-jgghjgjhg"}', 1, '2022-03-07 04:27:13', '2022-03-07 03:03:54'),
	(2, '62253541756ca', 'Braintree', 'braintree', '{"merchant_id":"s999zxpkvhh6dpm2","public_key":"hw4v45rty67jdxc9","private_key":"cac14c4d9d950e32c947b400b10a7596","environment":"sandbox"}', 1, '2022-03-07 04:27:13', '2022-03-07 04:27:13'),
	(3, '622535417ddfa', 'Stripe', 'stripe', '{"publishable_key":"pk_test_AnS4Ov8GS92XmHeVCDRPIZF4","secret_key":"sk_test_iS0xwfgzBF6cmPBBkgO13sjd","environment":"sandbox"}', 1, '2022-03-07 04:27:13', '2022-03-07 04:27:13'),
	(4, '622535419bcbf', 'Authorize.net', 'authorize_net', '{"login_id":"login_id","transaction_key":"transaction_key","environment":"sandbox"}', 1, '2022-03-07 04:27:13', '2022-03-07 04:27:13'),
	(5, '62253541c3e7c', '2checkout', '2checkout', '{"merchant_code":"merchant_code","private_key":"private_key","environment":"sandbox"}', 1, '2022-03-07 04:27:13', '2022-03-07 04:27:13'),
	(6, '62253541df69f', 'Paystack', 'paystack', '{"public_key":"public_key","secret_key":"secret_key","merchant_email":"merchant_email"}', 1, '2022-03-07 04:27:13', '2022-03-07 04:27:13'),
	(7, '62253541e7b05', 'PayU', 'payu', '{"client_id":"client_id","client_secret":"client_secret"}', 1, '2022-03-07 04:27:13', '2022-03-07 04:27:13'),
	(8, '62253541efae1', 'Paynow', 'paynow', '{"integration_id":"integration_id","integration_key":"integration_key"}', 1, '2022-03-07 04:27:13', '2022-03-07 04:27:13'),
	(9, '6225354203cfe', 'CoinPayments', 'coinpayments', '{"merchant_id":"merchant_id"}', 1, '2022-03-07 04:27:14', '2022-03-07 04:27:14'),
	(10, '622535420bced', 'Instamojo', 'instamojo', '{"api_key":"api_key","auth_token":"auth_token"}', 1, '2022-03-07 04:27:14', '2022-03-07 04:27:14'),
	(11, '622535421442e', 'PayUmoney', 'payumoney', '{"merchant_key":"merchant_key","merchant_salt":"merchant_salt","environment":"sandbox"}', 1, '2022-03-07 04:27:14', '2022-03-07 04:27:14'),
	(12, '622535423742b', 'Razorpay', 'razorpay', '{"key_id":"key_id","key_secret":"key_secret","environment":"sandbox"}', 1, '2022-03-07 04:27:14', '2022-03-07 04:27:14'),
	(13, '62253542420f5', 'SSLcommerz', 'sslcommerz', '{"store_id":"store_id","store_passwd":"store_id@ssl","environment":"sandbox"}', 1, '2022-03-07 04:27:14', '2022-03-07 04:27:14'),
	(14, '622535424a0f8', 'aamarPay', 'aamarpay', '{"store_id":"store_id","signature_key":"signature_key","environment":"sandbox"}', 1, '2022-03-07 04:27:14', '2022-03-07 04:27:14'),
	(15, '6225354252560', 'Flutterwave', 'flutterwave', '{"public_key":"public_key","secret_key":"secret_key","environment":"sandbox"}', 1, '2022-03-07 04:27:14', '2022-03-07 04:27:14'),
	(16, '622535425a537', 'Offline Payment', 'offline_payment', '{"payment_details":"<p>Please make a deposit to our bank account at:<\\/p>\\n<h6>US BANK USA<\\/h6>\\n<p>Routing (ABA): 045134400<\\/p>\\n<p>Account number: 6216587467378<\\/p>\\n<p>Beneficiary name: Ultimate sms<\\/p>","payment_confirmation":"After payment please contact with following email address codeglen@gmail.com with your transaction id. Normally it may take 1 - 2 business days to process. Should you have any question, feel free contact with us."}', 1, '2022-03-07 04:27:14', '2022-03-07 04:27:14');
/*!40000 ALTER TABLE `cg_payment_methods` ENABLE KEYS */;

-- Dumping structure for table sms.cg_permissions
CREATE TABLE IF NOT EXISTS `cg_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `cg_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `cg_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_permissions: ~70 rows (approximately)
/*!40000 ALTER TABLE `cg_permissions` DISABLE KEYS */;
INSERT INTO `cg_permissions` (`id`, `uid`, `role_id`, `name`, `created_at`, `updated_at`) VALUES
	(1, '6225353b98f7c', 1, 'access backend', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(2, '6225353ba0af9', 1, 'view customer', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(3, '6225353ba95d3', 1, 'create customer', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(4, '6225353bb0f9a', 1, 'edit customer', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(5, '6225353bb9831', 1, 'delete customer', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(6, '6225353bc1722', 1, 'view subscription', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(7, '6225353bc9f98', 1, 'new subscription', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(8, '6225353bd19fe', 1, 'manage subscription', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(9, '6225353bd9d2f', 1, 'delete subscription', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(10, '6225353be1cfc', 1, 'manage plans', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(11, '6225353bea418', 1, 'create plans', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(12, '6225353bf2394', 1, 'edit plans', '2022-03-07 04:27:07', '2022-03-07 04:27:07'),
	(13, '6225353c066ce', 1, 'delete plans', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(14, '6225353c1690c', 1, 'manage currencies', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(15, '6225353c21950', 1, 'create currencies', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(16, '6225353c298ff', 1, 'edit currencies', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(17, '6225353c31dfa', 1, 'delete currencies', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(18, '6225353c39d81', 1, 'view sending_servers', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(19, '6225353c44d5d', 1, 'create sending_servers', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(20, '6225353c4cd1f', 1, 'edit sending_servers', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(21, '6225353c55180', 1, 'delete sending_servers', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(22, '6225353c5d16f', 1, 'view keywords', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(23, '6225353c655ff', 1, 'create keywords', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(24, '6225353c6d65a', 1, 'edit keywords', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(25, '6225353c75bf9', 1, 'delete keywords', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(26, '6225353c7db10', 1, 'view sender_id', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(27, '6225353c85ec1', 1, 'create sender_id', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(28, '6225353c8df1e', 1, 'edit sender_id', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(29, '6225353c964c7', 1, 'delete sender_id', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(30, '6225353c9e1ee', 1, 'view blacklist', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(31, '6225353ca667a', 1, 'create blacklist', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(32, '6225353cae62c', 1, 'edit blacklist', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(33, '6225353cb6a91', 1, 'delete blacklist', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(34, '6225353ccc394', 1, 'view spam_word', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(35, '6225353cd4824', 1, 'create spam_word', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(36, '6225353ce20e6', 1, 'edit spam_word', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(37, '6225353ced135', 1, 'delete spam_word', '2022-03-07 04:27:08', '2022-03-07 04:27:08'),
	(38, '6225353d00e8d', 1, 'view administrator', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(39, '6225353d09335', 1, 'create administrator', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(40, '6225353d24532', 1, 'edit administrator', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(41, '6225353d2f476', 1, 'delete administrator', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(42, '6225353d39f0b', 1, 'view roles', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(43, '6225353d4228d', 1, 'create roles', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(44, '6225353d5fddc', 1, 'edit roles', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(45, '6225353d6d967', 1, 'delete roles', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(46, '6225353d7a234', 1, 'general settings', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(47, '6225353d85f95', 1, 'system_email settings', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(48, '6225353d92a27', 1, 'authentication settings', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(49, '6225353da127c', 1, 'notifications settings', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(50, '6225353dbed61', 1, 'localization settings', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(51, '6225353dcc989', 1, 'pusher settings', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(52, '6225353dd9f19', 1, 'view languages', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(53, '6225353de6987', 1, 'new languages', '2022-03-07 04:27:09', '2022-03-07 04:27:09'),
	(54, '6225353e0025d', 1, 'manage languages', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(55, '6225353e0eae1', 1, 'delete languages', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(56, '6225353e1c352', 1, 'view payment_gateways', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(57, '6225353e270e5', 1, 'update payment_gateways', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(58, '6225353e2f0f3', 1, 'view email_templates', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(59, '6225353e375ff', 1, 'update email_templates', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(60, '6225353e3f5d9', 1, 'view background_jobs', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(61, '6225353e47a59', 1, 'view purchase_code', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(62, '6225353e4fa70', 1, 'manage update_application', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(63, '6225353e57e68', 1, 'manage maintenance_mode', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(64, '6225353e5fea9', 1, 'view invoices', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(65, '6225353e68390', 1, 'create invoices', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(66, '6225353e703e0', 1, 'edit invoices', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(67, '6225353e784d6', 1, 'delete invoices', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(68, '6225353e807c6', 1, 'view sms_history', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(69, '6225353e88942', 1, 'view block_message', '2022-03-07 04:27:10', '2022-03-07 04:27:10'),
	(70, '6225353e9091f', 1, 'manage coverage_rates', '2022-03-07 04:27:10', '2022-03-07 04:27:10');
/*!40000 ALTER TABLE `cg_permissions` ENABLE KEYS */;

-- Dumping structure for table sms.cg_personal_access_tokens
CREATE TABLE IF NOT EXISTS `cg_personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cg_personal_access_tokens_token_unique` (`token`),
  KEY `cg_personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_personal_access_tokens: ~2 rows (approximately)
/*!40000 ALTER TABLE `cg_personal_access_tokens` DISABLE KEYS */;
INSERT INTO `cg_personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
	(1, 'user', 1, 'info@arabcode.online', '3b12741f7d54fc32593fd8db92758ef3bbf0571987f2775b7f309c82c374732f', '["*"]', NULL, '2022-03-07 04:27:11', '2022-03-07 04:27:11'),
	(2, 'user', 2, 'mohammed.hudair@gmail.com', 'd6ff2bea7861595706a9e0cb5d93c5a794b6d42bcce528eeba2b824f5b381091', '["access_backend","view_reports","view_contact_group","create_contact_group","update_contact_group","delete_contact_group","view_contact","create_contact","update_contact","delete_contact","view_sender_id","create_sender_id","view_blacklist","create_blacklist","delete_blacklist","sms_campaign_builder","sms_quick_send","sms_bulk_messages","sms_template","developers"]', NULL, '2022-03-07 01:52:38', '2022-03-07 01:52:38');
/*!40000 ALTER TABLE `cg_personal_access_tokens` ENABLE KEYS */;

-- Dumping structure for table sms.cg_phone_numbers
CREATE TABLE IF NOT EXISTS `cg_phone_numbers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','assigned','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `capabilities` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `billing_cycle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency_amount` int(11) NOT NULL,
  `frequency_unit` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validity_date` date DEFAULT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_phone_numbers_user_id_foreign` (`user_id`),
  KEY `cg_phone_numbers_currency_id_foreign` (`currency_id`),
  CONSTRAINT `cg_phone_numbers_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `cg_currencies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_phone_numbers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_phone_numbers: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_phone_numbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_phone_numbers` ENABLE KEYS */;

-- Dumping structure for table sms.cg_plans
CREATE TABLE IF NOT EXISTS `cg_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(16,2) NOT NULL,
  `billing_cycle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency_amount` int(11) NOT NULL,
  `frequency_unit` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `custom_order` int(11) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `tax_billing_required` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_plans_user_id_foreign` (`user_id`),
  KEY `cg_plans_currency_id_foreign` (`currency_id`),
  CONSTRAINT `cg_plans_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `cg_currencies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_plans: ~4 rows (approximately)
/*!40000 ALTER TABLE `cg_plans` DISABLE KEYS */;
INSERT INTO `cg_plans` (`id`, `uid`, `user_id`, `currency_id`, `name`, `description`, `price`, `billing_cycle`, `frequency_amount`, `frequency_unit`, `options`, `status`, `custom_order`, `is_default`, `is_popular`, `tax_billing_required`, `created_at`, `updated_at`) VALUES
	(1, '622539cde530c', 1, 12, 'مجاني', NULL, 0.00, 'monthly', 1, 'month', '{"sms_max":"5","whatsapp_max":"100","list_max":"-1","subscriber_max":"-1","subscriber_per_list_max":"-1","segment_per_list_max":"3","billing_cycle":"monthly","sending_limit":"1000_per_hour","sending_quota":"1000","sending_quota_time":"1","sending_quota_time_unit":"hour","max_process":"1","unsubscribe_url_required":"no","create_sending_server":"no","sending_servers_max":"5","list_import":"yes","list_export":"yes","api_access":"no","create_sub_account":"yes","delete_sms_history":"yes","add_previous_balance":"no","sender_id_verification":"yes","send_spam_message":"no","cutting_system":"no","cutting_value":"0","cutting_unit":"percentage","cutting_logic":"random","plain_sms":"1","receive_plain_sms":"0","voice_sms":"2","receive_voice_sms":"0","mms_sms":"3","receive_mms_sms":"0","whatsapp_sms":"1","receive_whatsapp_sms":"0","per_unit_price":".3"}', 1, 3, 0, 1, 0, '2022-03-07 01:46:37', '2022-03-07 02:56:04'),
	(2, '62254702c175e', 1, 1, 'بيسك', 'باقة اشتراك مجانية', 0.00, 'yearly', 1, 'year', '{"sms_max":"100","whatsapp_max":"100","list_max":"-1","subscriber_max":"-1","subscriber_per_list_max":"-1","segment_per_list_max":"3","billing_cycle":"monthly","sending_limit":"1000_per_hour","sending_quota":"1000","sending_quota_time":"1","sending_quota_time_unit":"hour","max_process":"1","unsubscribe_url_required":"no","create_sending_server":"no","sending_servers_max":"5","list_import":"yes","list_export":"yes","api_access":"no","create_sub_account":"yes","delete_sms_history":"yes","add_previous_balance":"no","sender_id_verification":"yes","send_spam_message":"no","cutting_system":"no","cutting_value":"5","cutting_unit":"digit","cutting_logic":"start","plain_sms":"1","receive_plain_sms":"0","voice_sms":"2","receive_voice_sms":"0","mms_sms":"3","receive_mms_sms":"0","whatsapp_sms":"1","receive_whatsapp_sms":"0","per_unit_price":".3"}', 1, 2, 0, 0, 0, '2022-03-07 02:42:58', '2022-03-07 02:56:04'),
	(3, '622548bc5f3a7', 1, 1, 'بلس', NULL, 100.00, 'monthly', 1, 'month', '{"sms_max":"100","whatsapp_max":"100","list_max":"-1","subscriber_max":"-1","subscriber_per_list_max":"-1","segment_per_list_max":"3","billing_cycle":"monthly","sending_limit":"1000_per_hour","sending_quota":"1000","sending_quota_time":"1","sending_quota_time_unit":"hour","max_process":"1","unsubscribe_url_required":"no","create_sending_server":"no","sending_servers_max":"5","list_import":"yes","list_export":"yes","api_access":"no","create_sub_account":"yes","delete_sms_history":"yes","add_previous_balance":"no","sender_id_verification":"yes","send_spam_message":"no","cutting_system":"no","cutting_value":"50","cutting_unit":"digit","cutting_logic":"start","plain_sms":"1","receive_plain_sms":"0","voice_sms":"2","receive_voice_sms":"0","mms_sms":"3","receive_mms_sms":"0","whatsapp_sms":"1","receive_whatsapp_sms":"0","per_unit_price":".3"}', 1, 1, 0, 0, 0, '2022-03-07 02:50:20', '2022-03-07 02:56:04'),
	(4, '62254a14c74c6', 1, 1, 'برو', 'باقة ذهبية', 300.00, 'monthly', 1, 'month', '{"sms_max":"-1","whatsapp_max":"100","list_max":"-1","subscriber_max":"-1","subscriber_per_list_max":"-1","segment_per_list_max":"-1","billing_cycle":"monthly","sending_limit":"100000_per_day","sending_quota":"1000","sending_quota_time":"1","sending_quota_time_unit":"hour","max_process":"3","unsubscribe_url_required":"no","create_sending_server":"yes","sending_servers_max":"5","list_import":"yes","list_export":"yes","api_access":"yes","create_sub_account":"yes","delete_sms_history":"yes","add_previous_balance":"yes","sender_id_verification":"yes","send_spam_message":"yes","cutting_system":"yes","cutting_value":"300","cutting_unit":"digit","cutting_logic":"start","plain_sms":"1","receive_plain_sms":"0","voice_sms":"2","receive_voice_sms":"0","mms_sms":"3","receive_mms_sms":"0","whatsapp_sms":"1","receive_whatsapp_sms":"0","per_unit_price":".3","quota_value":100000,"quota_base":1,"quota_unit":"day"}', 1, 0, 0, 0, 0, '2022-03-07 02:56:04', '2022-03-07 02:58:36');
/*!40000 ALTER TABLE `cg_plans` ENABLE KEYS */;

-- Dumping structure for table sms.cg_plans_coverage_countries
CREATE TABLE IF NOT EXISTS `cg_plans_coverage_countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_plans_coverage_countries_country_id_foreign` (`country_id`),
  KEY `cg_plans_coverage_countries_plan_id_foreign` (`plan_id`),
  CONSTRAINT `cg_plans_coverage_countries_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `cg_countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_plans_coverage_countries_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `cg_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_plans_coverage_countries: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_plans_coverage_countries` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_plans_coverage_countries` ENABLE KEYS */;

-- Dumping structure for table sms.cg_plans_sending_servers
CREATE TABLE IF NOT EXISTS `cg_plans_sending_servers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sending_server_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `fitness` int(11) NOT NULL,
  `is_primary` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_plans_sending_servers_sending_server_id_foreign` (`sending_server_id`),
  KEY `cg_plans_sending_servers_plan_id_foreign` (`plan_id`),
  CONSTRAINT `cg_plans_sending_servers_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `cg_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_plans_sending_servers_sending_server_id_foreign` FOREIGN KEY (`sending_server_id`) REFERENCES `cg_sending_servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_plans_sending_servers: ~4 rows (approximately)
/*!40000 ALTER TABLE `cg_plans_sending_servers` DISABLE KEYS */;
INSERT INTO `cg_plans_sending_servers` (`id`, `sending_server_id`, `plan_id`, `fitness`, `is_primary`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, 50, 1, '2022-03-07 02:45:59', '2022-03-07 02:46:10'),
	(2, 2, 2, 50, 0, '2022-03-07 02:46:10', '2022-03-07 02:46:10'),
	(3, 1, 3, 100, 1, '2022-03-07 02:53:09', '2022-03-07 02:53:09'),
	(4, 1, 4, 100, 1, '2022-03-07 02:58:16', '2022-03-07 02:58:16');
/*!40000 ALTER TABLE `cg_plans_sending_servers` ENABLE KEYS */;

-- Dumping structure for table sms.cg_reports
CREATE TABLE IF NOT EXISTS `cg_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `media_url` longtext COLLATE utf8mb4_unicode_ci,
  `sms_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` longtext COLLATE utf8mb4_unicode_ci,
  `send_by` enum('from','to','api') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `api_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sending_server_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_reports_user_id_foreign` (`user_id`),
  KEY `cg_reports_campaign_id_foreign` (`campaign_id`),
  KEY `cg_reports_sending_server_id_foreign` (`sending_server_id`),
  CONSTRAINT `cg_reports_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `cg_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_reports_sending_server_id_foreign` FOREIGN KEY (`sending_server_id`) REFERENCES `cg_sending_servers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_reports: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_reports` ENABLE KEYS */;

-- Dumping structure for table sms.cg_roles
CREATE TABLE IF NOT EXISTS `cg_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cg_roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_roles: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_roles` DISABLE KEYS */;
INSERT INTO `cg_roles` (`id`, `uid`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, '6225353b5cf3b', 'administrator', 1, '2022-03-07 04:27:07', '2022-03-07 04:27:07');
/*!40000 ALTER TABLE `cg_roles` ENABLE KEYS */;

-- Dumping structure for table sms.cg_role_user
CREATE TABLE IF NOT EXISTS `cg_role_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `cg_role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `cg_role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `cg_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_role_user: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_role_user` DISABLE KEYS */;
INSERT INTO `cg_role_user` (`user_id`, `role_id`) VALUES
	(1, 1);
/*!40000 ALTER TABLE `cg_role_user` ENABLE KEYS */;

-- Dumping structure for table sms.cg_senderid
CREATE TABLE IF NOT EXISTS `cg_senderid` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `sender_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','active','block','payment_required','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `price` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `billing_cycle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency_amount` int(11) NOT NULL,
  `frequency_unit` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validity_date` date DEFAULT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_claimed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_senderid_user_id_foreign` (`user_id`),
  KEY `cg_senderid_currency_id_foreign` (`currency_id`),
  CONSTRAINT `cg_senderid_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `cg_currencies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_senderid_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_senderid: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_senderid` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_senderid` ENABLE KEYS */;

-- Dumping structure for table sms.cg_senderid_plans
CREATE TABLE IF NOT EXISTS `cg_senderid_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_id` bigint(20) unsigned DEFAULT NULL,
  `price` decimal(16,2) NOT NULL,
  `billing_cycle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency_amount` int(11) NOT NULL,
  `frequency_unit` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_senderid_plans_currency_id_foreign` (`currency_id`),
  CONSTRAINT `cg_senderid_plans_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `cg_currencies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_senderid_plans: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_senderid_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_senderid_plans` ENABLE KEYS */;

-- Dumping structure for table sms.cg_sending_servers
CREATE TABLE IF NOT EXISTS `cg_sending_servers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_link` longtext COLLATE utf8mb4_unicode_ci,
  `port` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` longtext COLLATE utf8mb4_unicode_ci,
  `password` longtext COLLATE utf8mb4_unicode_ci,
  `route` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secret_access` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_secret` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `application_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_addr_ton` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '5',
  `source_addr_npi` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `dest_addr_ton` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `dest_addr_npi` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `c1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c3` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c4` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c5` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c6` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c7` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('http','smpp','whatsapp') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'http',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `plain` tinyint(1) NOT NULL DEFAULT '0',
  `schedule` tinyint(1) NOT NULL DEFAULT '0',
  `two_way` tinyint(1) NOT NULL DEFAULT '0',
  `voice` tinyint(1) NOT NULL DEFAULT '0',
  `mms` tinyint(1) NOT NULL DEFAULT '0',
  `whatsapp` tinyint(1) NOT NULL DEFAULT '0',
  `sms_per_request` int(11) NOT NULL DEFAULT '1',
  `quota_value` int(11) NOT NULL DEFAULT '0',
  `quota_base` int(11) NOT NULL DEFAULT '0',
  `quota_unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'minute',
  `custom` tinyint(1) NOT NULL DEFAULT '0',
  `custom_order` int(11) NOT NULL DEFAULT '0',
  `success_keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_sending_servers_user_id_foreign` (`user_id`),
  CONSTRAINT `cg_sending_servers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_sending_servers: ~2 rows (approximately)
/*!40000 ALTER TABLE `cg_sending_servers` DISABLE KEYS */;
INSERT INTO `cg_sending_servers` (`id`, `uid`, `user_id`, `name`, `settings`, `api_link`, `port`, `username`, `password`, `route`, `sms_type`, `account_sid`, `auth_id`, `auth_token`, `access_key`, `secret_access`, `access_token`, `api_key`, `api_secret`, `user_token`, `project_id`, `api_token`, `auth_key`, `device_id`, `region`, `application_id`, `source_addr_ton`, `source_addr_npi`, `dest_addr_ton`, `dest_addr_npi`, `c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `type`, `status`, `plain`, `schedule`, `two_way`, `voice`, `mms`, `whatsapp`, `sms_per_request`, `quota_value`, `quota_base`, `quota_unit`, `custom`, `custom_order`, `success_keyword`, `created_at`, `updated_at`) VALUES
	(1, '622541c73b657', 1, 'Twilio', 'Twilio', NULL, NULL, NULL, NULL, NULL, NULL, 'account_sid', NULL, 'auth_token', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5', '0', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http', 1, 1, 1, 1, 1, 1, 1, 1, 60, 1, 'minute', 0, 0, NULL, '2022-03-07 02:20:39', '2022-03-07 02:20:39'),
	(2, '6225420969fdc', 1, 'JohnsonConnect', 'JohnsonConnect', 'http://161.117.182.177:8080/api/sms/mtsend', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'app_key', 'secret_key', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5', '0', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http', 1, 1, 1, 0, 0, 0, 0, 1, 100, 1, 'minute', 0, 0, NULL, '2022-03-07 02:21:45', '2022-03-07 02:21:45');
/*!40000 ALTER TABLE `cg_sending_servers` ENABLE KEYS */;

-- Dumping structure for table sms.cg_spam_word
CREATE TABLE IF NOT EXISTS `cg_spam_word` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `word` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_spam_word: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_spam_word` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_spam_word` ENABLE KEYS */;

-- Dumping structure for table sms.cg_subscriptions
CREATE TABLE IF NOT EXISTS `cg_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `payment_method_id` bigint(20) unsigned DEFAULT NULL,
  `options` text COLLATE utf8mb4_unicode_ci,
  `status` enum('new','pending','active','ended','renew') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_claimed` tinyint(1) NOT NULL DEFAULT '0',
  `current_period_ends_at` timestamp NULL DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `end_by` bigint(20) unsigned DEFAULT NULL,
  `end_period_last_days` int(11) NOT NULL DEFAULT '10',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_subscriptions_user_id_foreign` (`user_id`),
  KEY `cg_subscriptions_end_by_foreign` (`end_by`),
  KEY `cg_subscriptions_plan_id_foreign` (`plan_id`),
  KEY `cg_subscriptions_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `cg_subscriptions_end_by_foreign` FOREIGN KEY (`end_by`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_subscriptions_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `cg_payment_methods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `cg_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_subscriptions: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_subscriptions` DISABLE KEYS */;
INSERT INTO `cg_subscriptions` (`id`, `uid`, `user_id`, `plan_id`, `payment_method_id`, `options`, `status`, `paid`, `payment_claimed`, `current_period_ends_at`, `start_at`, `end_at`, `end_by`, `end_period_last_days`, `created_at`, `updated_at`) VALUES
	(1, '62253b631736d', 2, 1, 16, '{"credit_warning":true,"credit":"100","credit_notify":"both","subscription_warning":true,"subscription_notify":"both"}', 'active', 0, 0, '2022-04-07 01:53:23', '2022-03-07 01:53:23', NULL, NULL, 10, '2022-03-07 01:53:23', '2022-03-07 01:53:23');
/*!40000 ALTER TABLE `cg_subscriptions` ENABLE KEYS */;

-- Dumping structure for table sms.cg_subscription_logs
CREATE TABLE IF NOT EXISTS `cg_subscription_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `transaction_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_subscription_logs_subscription_id_foreign` (`subscription_id`),
  KEY `cg_subscription_logs_transaction_id_foreign` (`transaction_id`),
  CONSTRAINT `cg_subscription_logs_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `cg_subscriptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cg_subscription_logs_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `cg_subscription_transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_subscription_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_subscription_logs` DISABLE KEYS */;
INSERT INTO `cg_subscription_logs` (`id`, `uid`, `subscription_id`, `transaction_id`, `type`, `data`, `created_at`, `updated_at`) VALUES
	(1, '62253b63792a0', 1, NULL, 'admin_plan_assigned', '{"plan":"\\u0645\\u062c\\u0627\\u0646\\u064a","price":"sar(price)"}', '2022-03-07 01:53:23', '2022-03-07 01:53:23');
/*!40000 ALTER TABLE `cg_subscription_logs` ENABLE KEYS */;

-- Dumping structure for table sms.cg_subscription_transactions
CREATE TABLE IF NOT EXISTS `cg_subscription_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_subscription_transactions_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `cg_subscription_transactions_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `cg_subscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_subscription_transactions: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_subscription_transactions` DISABLE KEYS */;
INSERT INTO `cg_subscription_transactions` (`id`, `uid`, `subscription_id`, `title`, `type`, `status`, `amount`, `created_at`, `updated_at`) VALUES
	(1, '62253b6352d72', 1, 'Subscribed to مجاني plan', 'subscribe', 'success', 'sar(price)', '2022-03-07 01:53:23', '2022-03-07 01:53:23');
/*!40000 ALTER TABLE `cg_subscription_transactions` ENABLE KEYS */;

-- Dumping structure for table sms.cg_templates
CREATE TABLE IF NOT EXISTS `cg_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cg_templates_user_id_foreign` (`user_id`),
  CONSTRAINT `cg_templates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `cg_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_templates: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_templates` ENABLE KEYS */;

-- Dumping structure for table sms.cg_template_tags
CREATE TABLE IF NOT EXISTS `cg_template_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_template_tags: ~0 rows (approximately)
/*!40000 ALTER TABLE `cg_template_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_template_tags` ENABLE KEYS */;

-- Dumping structure for table sms.cg_users
CREATE TABLE IF NOT EXISTS `cg_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `image` text COLLATE utf8mb4_unicode_ci,
  `sms_unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_customer` tinyint(1) NOT NULL DEFAULT '0',
  `active_portal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_code` int(11) DEFAULT NULL,
  `two_factor_expires_at` datetime DEFAULT NULL,
  `two_factor_backup_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `last_access_at` timestamp NULL DEFAULT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cg_users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table sms.cg_users: ~1 rows (approximately)
/*!40000 ALTER TABLE `cg_users` DISABLE KEYS */;
INSERT INTO `cg_users` (`id`, `uid`, `api_token`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `status`, `image`, `sms_unit`, `is_admin`, `is_customer`, `active_portal`, `two_factor`, `two_factor_code`, `two_factor_expires_at`, `two_factor_backup_code`, `locale`, `timezone`, `last_access_at`, `provider`, `provider_id`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, '6225353ed821e', '1|dWRVTGsdrGg54lg2PhAPFsGUOw1VKUmXDEQwYgOT', 'arab', 'code', 'admin@gmail.com', '2022-03-07 04:27:10', '$2y$10$RvxQuT9wsOuc3Tv1n4sq0O4jhy00s0c7mLa/utqAaJZeXriubcqrC', 1, NULL, NULL, 1, 1, 'admin', 0, NULL, NULL, NULL, 'ar', 'Asia/Dhaka', '2022-03-07 03:04:10', NULL, NULL, NULL, '2022-03-07 04:27:10', '2022-03-07 03:04:10'),
	(2, '62253b359e5bb', '2|T1K18U0a5Xb7WsjpsLI6GXcRIIovFEPDG86CHEOL', 'mohammed', 'hudair', 'mohammed.hudair@gmail.com', '2022-03-07 01:52:37', '$2y$10$tNazDAsVhTX/vC35skrUNOxZWrbFyj58LhsSUQs03RWeYI0kk.XbS', 1, NULL, NULL, 0, 1, 'customer', 0, NULL, NULL, NULL, 'ar', 'Asia/Riyadh', NULL, NULL, NULL, NULL, '2022-03-07 01:52:37', '2022-03-07 02:23:08');
/*!40000 ALTER TABLE `cg_users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
