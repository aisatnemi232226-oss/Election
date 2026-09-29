-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_electionsystem
CREATE DATABASE IF NOT EXISTS `db_electionsystem` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_electionsystem`;

-- Dumping structure for table db_electionsystem.academic_years
CREATE TABLE IF NOT EXISTS `academic_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_years_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.academic_years: ~2 rows (approximately)
INSERT INTO `academic_years` (`id`, `name`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
	(1users, '2024-2025', '2024-06-01', '2025-05-31', 1, '2026-02-09 03:48:53', '2026-02-09 03:48:53'),
	(2, '2025-2026', '2025-06-01', '2026-05-31', 0, '2026-02-09 03:48:53', '2026-02-09 03:48:53');

-- Dumping structure for table db_electionsystem.acadyr
CREATE TABLE IF NOT EXISTS `acadyr` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.acadyr: ~1 rows (approximately)
INSERT INTO `acadyr` (`id`, `name`, `status`, `created_at`, `delete_at`) VALUES
	(1, '2024-2025', NULL, '2025-03-17 11:14:16', '2025-03-17 11:14:16');

-- Dumping structure for table db_electionsystem.allowacad
CREATE TABLE IF NOT EXISTS `allowacad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `election_id` bigint DEFAULT NULL,
  `acadyr_id` int DEFAULT NULL,
  `sem_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `acadyr_id` (`acadyr_id`),
  KEY `sem_id` (`sem_id`),
  CONSTRAINT `allowacad_ibfk_1` FOREIGN KEY (`acadyr_id`) REFERENCES `acadyr` (`id`),
  CONSTRAINT `allowacad_ibfk_2` FOREIGN KEY (`sem_id`) REFERENCES `semesters` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.allowacad: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.articles
CREATE TABLE IF NOT EXISTS `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Contacts','Guides','Announcements','News','Updates','Policies') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`),
  CONSTRAINT `articles_chk_1` CHECK (json_valid(`tags`))
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.articles: ~14 rows (approximately)
INSERT INTO `articles` (`id`, `title`, `type`, `content`, `author`, `featured_image`, `created_at`, `updated_at`, `deleted_at`, `tags`) VALUES
	(1, 'asd', 'Contacts', 'sdsad', 'sad', NULL, '2026-01-21 03:07:14', NULL, NULL, '"[]"'),
	(2, 'asd', 'Contacts', 'sdsad', 'sad', NULL, '2026-01-21 03:07:40', NULL, NULL, '"[]"'),
	(3, 'mikoo', 'Contacts', 'sd', 'd', NULL, '2026-01-21 03:10:46', NULL, NULL, '[]'),
	(4, 'as', 'Contacts', 'dsa', 'asd', NULL, '2026-01-21 03:12:59', NULL, NULL, '[]'),
	(5, 'asd', 'Contacts', 'sda', 'asd', NULL, '2026-01-21 03:14:34', NULL, '2026-01-21 07:58:41', '[]'),
	(6, 'My First Article', '', '<p>This is the article content...</p>', 'Admin User', '/storage/article-images/1745903545_Screenshot (3).png', '2026-01-21 05:01:07', NULL, NULL, '["3","5"]'),
	(7, 'asd', 'Contacts', 'sadasd', 'sd', '/storage/article-images/1745903545_Screenshot (3).png', '2026-01-21 05:02:59', NULL, '2026-01-21 07:58:36', '[]'),
	(8, 'adadad', 'Contacts', 'dadsaa', 'add', '/storage/article-images/1745903545_Screenshot (3).png', '2026-01-21 05:51:50', NULL, '2026-01-21 07:58:31', '[]'),
	(9, 'wdewad', 'Contacts', 'adasd', 'adasd', '/storage/article-images/1745903545_Screenshot (3).png', '2026-01-21 05:54:43', NULL, '2026-01-21 07:54:58', '[]'),
	(10, 'tyuytu', 'Contacts', 'tyutdfsdfsf', 'tyutyut', '/storage/article-images/1745903545_Screenshot (3).png', '2026-01-21 05:57:29', NULL, '2026-01-21 06:20:57', '[]'),
	(11, 'Article 2: Empowering Student Leaders Through Elections', 'Contacts', 'The Election of School Officers is an important event that empowers students to become future leaders. Through this activity, students learn the value of teamwork, honesty, and accountability.\r\n\r\nEach candidate demonstrated confidence and dedication as they shared their vision for the school. The voting process allowed students to express their opinions and choose leaders who will represent their voices.\r\n\r\nThe school believes that student elections help shape responsible individuals who can contribute positively to the community.', 'The PCU', '/storage/featured-images/1769157600_featured_artic_2.jpg', '2026-01-21 06:26:08', NULL, NULL, '[]'),
	(12, 'School Election of Officers 2026', 'Contacts', 'The school successfully conducted its Election of Officers for School Year 2026–2027, giving students the opportunity to practice their right to vote and participate in democratic processes.\r\n\r\nThe activity aimed to develop leadership skills, responsibility, and unity among students. Candidates presented their platforms, goals, and plans to serve the student body, encouraging informed and responsible voting.\r\n\r\nThe election was held in an orderly and fair manner under the supervision of teachers and the election committee. This event highlights the school’s commitment to promoting student leadership and good governance.', 'The PCU', '/storage/featured-images/1769157515_featured_artic_1`.webp', '2026-01-21 06:51:09', NULL, NULL, '[]'),
	(13, 'qeqeqw', 'Contacts', 'eqeq', 'qweqe', '/storage/featured-images/1768980210_featured_Blue_Modern_Simple_Grand_Opening_Laundry_Services_Instagram_Story.jpg', '2026-01-21 07:02:37', NULL, '2026-01-21 07:52:34', '[]'),
	(14, 'Article 3: Successful Conduct of School Officer Elections', 'Contacts', 'The school community proudly celebrates the successful conduct of the School Officer Elections. The event showcased active student participation and strong cooperation among teachers, staff, and learners.\r\n\r\nFrom campaign period to election day, students showed discipline and respect for school rules. The newly elected officers are expected to lead with integrity, commitment, and service to their fellow students.\r\n\r\nThis activity strengthens leadership values and promotes a positive school environment.', 'The PCU', '/storage/featured-images/1769157634_featured_artic_3.jpg', '2026-01-23 08:40:34', NULL, NULL, '[]'),
	(15, 'Test Article Nemz', 'Contacts', '<p>content</p>', 'Nemz', '/storage/featured-images/1790040952_featured_pc.jpg', '2026-09-22 01:35:52', NULL, NULL, '[]');

-- Dumping structure for table db_electionsystem.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.cache: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.cache_locks: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.candidate
CREATE TABLE IF NOT EXISTS `candidate` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `users_id` bigint DEFAULT NULL,
  `course_id` int NOT NULL,
  `position_id` bigint unsigned DEFAULT NULL,
  `electionparty_id` bigint DEFAULT NULL,
  `platforms` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_events` text COLLATE utf8mb4_unicode_ci,
  `joined_elections` longtext COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_img` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_img` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `candidate_id_number_unique` (`id_number`),
  KEY `candidate_course_id_foreign` (`course_id`),
  KEY `candidate_electionparty_id_foreign` (`electionparty_id`),
  KEY `candidate_position_id_foreign` (`position_id`),
  KEY `candidate_users_id_foreign` (`users_id`),
  CONSTRAINT `candidate_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `candidate_electionparty_id_foreign` FOREIGN KEY (`electionparty_id`) REFERENCES `electionparty` (`id`),
  CONSTRAINT `candidate_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `candidate_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.candidate: ~9 rows (approximately)
INSERT INTO `candidate` (`id`, `name`, `id_number`, `users_id`, `course_id`, `position_id`, `electionparty_id`, `platforms`, `related_events`, `joined_elections`, `description`, `status`, `profile_img`, `banner_img`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(2, 'Lourence Lim', 'ST84490', NULL, 3, 2, NULL, 'sa', NULL, NULL, 'sa', 'Active', '1769045473_tQzpwiMzFV_profile.jpg', '1769045473_HQHxZv5c59_banner.png', '2026-01-22 01:31:13', NULL, NULL),
	(3, 'MARY LOI YVES', 'ST64281', NULL, 2, 6, 2, 'asdasdasd', NULL, NULL, 'asdasdasd', 'Active', '1769067970_8bXpHDsCMg_profile.jpg', '1769067970_krCa4BwH2R_banner.jpg', '2026-01-22 07:46:10', NULL, NULL),
	(4, 'JErome M Saddi', 'ST96261', NULL, 3, 3, 2, 'running for president d', NULL, NULL, 'running for president', 'Active', '1769128207_hJPgoXiW2Q_profile.jpg', '1769128208_IMQLxdTcTE_banner.jpg', '2026-01-23 00:30:08', NULL, NULL),
	(5, 'Erryl Jose Diokno', 'ST14453', NULL, 1, 3, NULL, '<p data-start="272" data-end="675">A strong student government begins with genuine concern for the welfare of its students. The platform “Students First, Always” is built on the belief that every student deserves to be heard, supported, and represented fairly. As president, my primary goal is to create an environment where students feel safe expressing their concerns and confident that their leaders are working in their best interest.</p>\r\n<p data-start="677" data-end="1144">This platform prioritizes student welfare by establishing a Student Help Desk that will serve as a one-stop channel for academic, personal, and organizational concerns. Through both online and on-campus access, students will be able to raise issues more conveniently and receive timely responses. Transparency will also be a major focus, with regular updates and reports on student government projects, activities, and budget usage to ensure accountability and trust.</p>', NULL, NULL, 'running for pres&nbsp;', 'Active', '1769164856_q3Wu90uXpl_profile.jpg', '1769164856_5ko5uiXijj_banner.jpg', '2026-01-23 10:40:56', NULL, NULL),
	(6, 'Dan Marvic Juan', 'ST48914', NULL, 2, 3, NULL, '<p data-start="1146" data-end="1625">Academic support is another core component of this platform. Peer tutoring programs, review sessions before major examinations, and extended library hours during exam weeks will be promoted to help students succeed academically. Recognizing the growing pressure faced by college students, this platform also advocates for mental health and wellness initiatives, including stress-free activities and partnerships with guidance counselors to support students’ emotional well-being.</p>\r\n<p data-start="1627" data-end="1998">Lastly, inclusivity will be at the heart of all student government initiatives. Student organizations will be given equal opportunities to participate in activities and access funding, ensuring that every student feels represented regardless of background or interest. Through this platform, student government will truly reflect the voices and needs of the student body.</p>', NULL, NULL, '<p data-start="1146" data-end="1625">Academic support is another core component of this platform. Peer tutoring programs, review sessions before major examinations, and extended library hours during exam weeks will be promoted to help students succeed academically. Recognizing the growing pressure faced by college students, this platform also advocates for mental health and wellness initiatives, including stress-free activities and partnerships with guidance counselors to support students’ emotional well-being.</p>\r\n<p data-start="1627" data-end="1998">Lastly, inclusivity will be at the heart of all student government initiatives. Student organizations will be given equal opportunities to participate in activities and access funding, ensuring that every student feels represented regardless of background or interest. Through this platform, student government will truly reflect the voices and needs of the student body.</p>', 'Active', '1769164898_RRR3UPWvSp_profile.jpg', '1769164898_Kxw7X9bbZn_banner.jpg', '2026-01-23 10:41:38', NULL, NULL),
	(7, 'Loremia, John Carlo C.', '2025400894', 62, 3, 2, 2, 'dasdasdadasd', NULL, NULL, 'asdasdasda', 'Active', '1769415137_kYmiEHsxon_profile.jpg', '1769415137_oblm4CU8xl_banner.png', '2026-01-26 08:12:17', NULL, NULL),
	(8, 'Chavez, Beverly Ann B.', '2025404538', 61, 2, 8, 6, 'asdasdasd', NULL, NULL, 'asdasdasdsad', 'Active', '1769416925_BGcIIujRwW_profile.jpg', '1769416925_ZQSBLJKzm8_banner.jpg', '2026-01-26 08:21:25', NULL, NULL),
	(11, 'Joyce Saddi', '0200003458', 63, 2, 2, 6, 'dasdasdasdasd', NULL, NULL, 'asdsadasda', 'Active', '1769578097_za6SZmW6ed_profile.jpg', '1769578097_gwLoOiP9RR_banner.png', '2026-01-28 05:28:17', NULL, NULL),
	(12, 'Hilario, Charm Mhine C.', '2025302565', 60, 3, 3, 6, 'asdasdasdasd', NULL, NULL, 'asdasdasd', 'Pending', '1769932636_GmuaXGNIwv_profile.jpg', '1769932636_I3E74UBIue_banner.jpg', '2026-02-01 07:57:16', NULL, NULL);

-- Dumping structure for table db_electionsystem.chatbot_intents
CREATE TABLE IF NOT EXISTS `chatbot_intents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `triggers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quick_replies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `priority` int unsigned NOT NULL DEFAULT '100',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `chatbot_intents_chk_1` CHECK (json_valid(`triggers`)),
  CONSTRAINT `chatbot_intents_chk_2` CHECK (json_valid(`quick_replies`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.chatbot_intents: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.chatbot_logs
CREATE TABLE IF NOT EXISTS `chatbot_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply` text COLLATE utf8mb4_unicode_ci,
  `matched_intent_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_logs_matched_intent_id_foreign` (`matched_intent_id`),
  CONSTRAINT `chatbot_logs_matched_intent_id_foreign` FOREIGN KEY (`matched_intent_id`) REFERENCES `chatbot_intents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.chatbot_logs: ~4 rows (approximately)
INSERT INTO `chatbot_logs` (`id`, `user_id`, `session_id`, `message`, `reply`, `matched_intent_id`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'WZrBZZ5MRLLwDXSEOPRkr5kvqO6pUSCcx6SUmRL4', 'hi', 'Hello! You can ask me how to log in, how to vote, or where to see the election results.', NULL, '2026-02-09 08:01:40', '2026-02-09 08:01:40'),
	(2, NULL, 'Fa1cAqoU1YVJu7xB99Z1PUApuntqQCj9ZxZL8Ofo', 'hi', 'Hello! You can ask me how to log in, how to vote, or where to see the election results.', NULL, '2026-09-22 01:26:19', '2026-09-22 01:26:19'),
	(3, NULL, 'ks5obcslqhl2L2MXa9B0HuNtGyWYPLgHDNTnewGr', 'hi', 'Hello! You can ask me how to log in, how to vote, or where to see the election results.', NULL, '2026-09-22 08:14:42', '2026-09-22 08:14:42'),
	(4, NULL, 'ks5obcslqhl2L2MXa9B0HuNtGyWYPLgHDNTnewGr', 'nigga', 'I couldn\'t find an exact match for that. Try asking about login, voting, candidate registration, or results.', NULL, '2026-09-22 08:14:46', '2026-09-22 08:14:46'),
	(5, NULL, 'ks5obcslqhl2L2MXa9B0HuNtGyWYPLgHDNTnewGr', 'pak u', 'I couldn\'t find an exact match for that. Try asking about login, voting, candidate registration, or results.', NULL, '2026-09-22 08:14:50', '2026-09-22 08:14:50');

-- Dumping structure for table db_electionsystem.chatbot_settings
CREATE TABLE IF NOT EXISTS `chatbot_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chatbot_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.chatbot_settings: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.courses
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coursecode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.courses: ~3 rows (approximately)
INSERT INTO `courses` (`id`, `coursecode`, `name`, `created_at`, `delete_at`, `deleted_at`) VALUES
	(1, 'BSIT2', 'BS Information Technology', '2025-03-17 11:03:05', '2025-03-17 11:03:05', NULL),
	(2, 'BSIT1', 'BS Information Technology', '2025-03-17 11:19:12', '2025-03-17 11:19:12', NULL),
	(3, 'BSCS1', 'BS Computer Science', '2025-03-17 11:19:52', '2025-03-17 11:19:52', NULL);

-- Dumping structure for table db_electionsystem.election
CREATE TABLE IF NOT EXISTS `election` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reg_start` date NOT NULL,
  `reg_end` date NOT NULL,
  `campaign_start` date DEFAULT NULL,
  `campaign_end` date DEFAULT NULL,
  `result_date` date DEFAULT NULL,
  `election_img` blob,
  `admin_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `candidates_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `candidates_result_announcements` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `election_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `election_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.election: ~27 rows (approximately)
INSERT INTO `election` (`id`, `name`, `description`, `start_date`, `end_date`, `reg_start`, `reg_end`, `campaign_start`, `campaign_end`, `result_date`, `election_img`, `admin_id`, `created_at`, `deleted_at`, `academic_year_id`, `candidates_group`, `candidates_result_announcements`) VALUES
	(1, 'sad', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 03:07:14', '2026-01-21 03:07:14', NULL, NULL, NULL),
	(2, 'sad', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 03:07:40', '2026-01-21 03:07:40', NULL, NULL, NULL),
	(3, 'd', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 03:10:46', '2026-01-21 03:10:46', NULL, NULL, NULL),
	(4, 'sda', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 03:12:59', '2026-01-21 03:12:59', NULL, NULL, NULL),
	(5, 'sd', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 03:14:34', '2026-01-21 03:14:34', NULL, NULL, NULL),
	(6, 'ds', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 05:02:59', '2026-01-21 05:02:59', NULL, NULL, NULL),
	(7, 'adsad', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 05:51:50', '2026-01-21 05:51:50', NULL, NULL, NULL),
	(8, 'adasdad', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 05:54:43', '2026-01-21 05:54:43', NULL, NULL, NULL),
	(9, 'tyutyu', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 05:57:29', '2026-01-21 05:57:29', NULL, NULL, NULL),
	(10, 'Miks', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 06:26:08', '2026-01-21 06:26:08', NULL, NULL, NULL),
	(11, 'asdasda', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 06:51:09', '2026-01-21 06:51:09', NULL, NULL, NULL),
	(12, 'asdasda', 'Election created from article update', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 06:53:59', '2026-01-21 06:53:59', NULL, NULL, NULL),
	(13, 'qeqe', 'Election created from article', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 07:02:37', '2026-01-21 07:02:37', NULL, NULL, NULL),
	(14, 'qeqe', 'Election created from article update', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 07:02:51', '2026-01-21 07:02:51', NULL, NULL, NULL),
	(15, 'qeqe', 'Election created from article update', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 07:04:46', '2026-01-21 07:04:46', NULL, NULL, NULL),
	(16, 'qeqe', 'Election created from article update', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 07:14:03', '2026-01-21 07:14:03', NULL, NULL, NULL),
	(17, 'qeqe', 'Election created from article update', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 07:23:30', '2026-01-21 07:23:30', NULL, NULL, NULL),
	(18, 'asdasda', 'Election created from article update', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 07:23:44', '2026-01-21 07:23:44', NULL, NULL, NULL),
	(19, 'Miks', 'Election created from article update', '2026-01-21', '2026-02-20', '2026-02-20', '2026-02-27', '2026-03-07', '2026-04-01', '2026-05-02', NULL, 59, '2026-01-21 07:52:24', '2026-01-21 07:52:24', NULL, NULL, NULL),
	(20, 'asdasdad', NULL, '2026-12-12', '2027-12-03', '2026-12-12', '2026-12-20', NULL, NULL, NULL, NULL, 59, '2026-01-22 06:20:11', '2026-01-22 06:20:11', NULL, NULL, NULL),
	(21, 'presedential', NULL, '2026-01-23', '2026-01-31', '2026-01-23', '2026-01-25', NULL, NULL, NULL, NULL, 59, '2026-01-22 06:27:03', NULL, NULL, NULL, NULL),
	(22, 'Vice-President', NULL, '2026-01-27', '2026-02-28', '2026-01-24', '2026-01-31', NULL, NULL, NULL, NULL, 59, '2026-01-22 07:05:55', NULL, NULL, NULL, NULL),
	(23, 'asdasda', 'Election created from article update', '2026-01-23', '2026-02-22', '2026-02-22', '2026-03-01', '2026-03-09', '2026-04-03', '2026-05-04', NULL, 59, '2026-01-23 05:51:53', '2026-01-23 05:51:53', NULL, NULL, NULL),
	(24, 'Miks', 'Election created from article update', '2026-01-23', '2026-02-22', '2026-02-22', '2026-03-01', '2026-03-09', '2026-04-03', '2026-05-04', NULL, 59, '2026-01-23 05:52:11', '2026-01-23 05:52:11', NULL, NULL, NULL),
	(25, '2025 - 2026', 'Election created from article update', '2026-01-23', '2026-02-22', '2026-02-22', '2026-03-01', '2026-03-09', '2026-04-03', '2026-05-04', NULL, 59, '2026-01-23 08:38:35', '2026-01-23 08:38:35', NULL, NULL, NULL),
	(26, '2025-2026', 'Election created from article update', '2026-01-23', '2026-02-22', '2026-02-22', '2026-03-01', '2026-03-09', '2026-04-03', '2026-05-04', NULL, 59, '2026-01-23 08:40:00', '2026-01-23 08:40:00', NULL, NULL, NULL),
	(27, '2025 - 2026', 'Election created from article', '2026-01-23', '2026-02-22', '2026-02-22', '2026-03-01', '2026-03-09', '2026-04-03', '2026-05-04', NULL, 59, '2026-01-23 08:40:34', '2026-01-23 08:40:34', NULL, NULL, NULL),
	(28, 'President', 'Election created from article', '2026-09-22', '2026-10-22', '2026-10-22', '2026-10-29', '2026-11-06', '2026-12-01', '2027-01-01', NULL, 64, '2026-09-22 01:35:52', '2026-09-22 01:35:52', NULL, NULL, NULL);

-- Dumping structure for table db_electionsystem.electionparty
CREATE TABLE IF NOT EXISTS `electionparty` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.electionparty: ~3 rows (approximately)
INSERT INTO `electionparty` (`id`, `name`, `description`, `logo`, `status`, `created_at`, `delete_at`, `deleted_at`) VALUES
	(2, 'union', 'fdsgdsg', 'election-parties/nsimKrpgezRg14ybuIALmTvYLzg2Bhq51q856ikL.png', 'Active', '2026-01-24 00:42:46', '2026-01-22 02:52:36', NULL),
	(4, 'liberal', 'asdasdasd', 'election-parties/mn61geENCiMHZwpn7XvUrnDv6Uqh4FY6XA6ZZsHw.jpg', 'Active', '2026-01-24 00:42:20', '2026-01-23 10:42:17', NULL),
	(6, 'PCU dolphins', 'adasdasd', 'election-parties/Q2UAMFTsOT0xxXBEP2OtDrNYEc3zNPGBx5jqev7b.jpg', 'Active', '2026-01-24 00:42:36', '2026-01-23 10:55:36', NULL);

-- Dumping structure for table db_electionsystem.election_articles
CREATE TABLE IF NOT EXISTS `election_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint DEFAULT NULL,
  `election_id` bigint DEFAULT NULL,
  `articles_id` bigint DEFAULT NULL,
  `show_status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.election_articles: ~3 rows (approximately)
INSERT INTO `election_articles` (`id`, `candidate_id`, `election_id`, `articles_id`, `show_status`, `created_at`, `delete_at`) VALUES
	(6, NULL, 26, 11, 1, '2026-01-23 08:40:00', '2026-01-21 06:26:08'),
	(7, NULL, 25, 12, 1, '2026-01-23 08:38:35', '2026-01-21 06:51:09'),
	(9, NULL, 27, 14, 1, '2026-01-23 08:40:34', '2026-01-23 08:40:34'),
	(10, NULL, 28, 15, 1, '2026-09-22 01:35:52', '2026-09-22 01:35:52');

-- Dumping structure for table db_electionsystem.election_event_settings
CREATE TABLE IF NOT EXISTS `election_event_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_1_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_1_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_1_description` text COLLATE utf8mb4_unicode_ci,
  `event_1_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_2_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_2_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_2_description` text COLLATE utf8mb4_unicode_ci,
  `event_2_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_3_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_3_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_3_description` text COLLATE utf8mb4_unicode_ci,
  `event_3_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'upcoming',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.election_event_settings: ~1 rows (approximately)
INSERT INTO `election_event_settings` (`id`, `label`, `label_image`, `event_1_title`, `event_1_date`, `event_1_description`, `event_1_image`, `event_2_title`, `event_2_date`, `event_2_description`, `event_2_image`, `event_3_title`, `event_3_date`, `event_3_description`, `event_3_image`, `created_at`, `updated_at`, `start_date`, `end_date`, `status`) VALUES
	(1, 'Election 2026 for CSG', 'event-label/HylVy1BrWsKWH36aefhZuwuwCzHAr2HKQLRqFQp4.png', 'Election Kickoff', 'March 1, 2023', 'Join us for the official start of the election season with speeches from candidates and more.', NULL, 'Candidate Debate', 'March 15, 2023', 'Watch the candidates debate on key issues and present their platforms.', NULL, 'Voting Day', 'April 1, 2023', 'Cast your vote online and make your voice heard in the student government elections.', NULL, '2026-01-23 02:16:00', '2026-01-23 03:59:02', NULL, NULL, 'upcoming');

-- Dumping structure for table db_electionsystem.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.migrations: ~65 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(27, '2025_03_08_085603_create_cache_table', 1),
	(28, '2025_03_08_090529_create_acadyr_table', 1),
	(29, '2025_03_08_090529_create_allowacad_table', 1),
	(30, '2025_03_08_090529_create_cache_locks_table', 2),
	(31, '2025_03_08_090529_create_cache_table', 3),
	(32, '2025_03_08_090529_create_courses_table', 3),
	(33, '2025_03_08_090529_create_election_articles_table', 3),
	(34, '2025_03_08_090529_create_election_table', 3),
	(35, '2025_03_08_090529_create_electionparty_table', 3),
	(36, '2025_03_08_090529_create_positions_table', 3),
	(37, '2025_03_08_090529_create_register_table', 3),
	(38, '2025_03_08_090529_create_results_table', 3),
	(39, '2025_03_08_090529_create_role_table', 3),
	(40, '2025_03_08_090529_create_roles_table', 3),
	(41, '2025_03_08_090529_create_semesters_table', 3),
	(42, '2025_03_08_090529_create_users_table', 3),
	(43, '2025_03_08_090529_create_verified_users_table', 3),
	(44, '2025_03_08_090529_create_vote_table', 3),
	(45, '2025_03_08_090529_create_votedays_table', 3),
	(46, '2025_03_08_090529_create_yearlevel_table', 3),
	(47, '2025_03_08_090532_add_foreign_keys_to_allowacad_table', 3),
	(48, '2025_03_08_090532_add_foreign_keys_to_register_table', 3),
	(49, '2025_03_08_090532_add_foreign_keys_to_role_table', 3),
	(50, '2025_03_08_090532_add_foreign_keys_to_verified_users_table', 3),
	(51, '2025_03_08_090532_add_foreign_keys_to_vote_table', 3),
	(52, '2025_03_08_091134_create_sessions_table', 3),
	(53, '2025_03_08_090529_create_verified_voters_table', 4),
	(54, '2025_03_08_090532_add_foreign_keys_to_verified_voters_table', 4),
	(55, '2025_03_14_153434_create_options_table', 4),
	(56, '2025_03_15_014522_create_candidate_table', 4),
	(57, '2025_03_15_023102_add_foreign_keys_to_candidate_table', 4),
	(58, '2025_03_15_025802_create_reports_table', 4),
	(59, '2025_03_15_025825_add_foreign_keys_to_reports_table', 4),
	(60, '2025_03_15_030758_create_articles_table', 4),
	(61, '2025_03_15_031152_create_systemlogs_table', 4),
	(62, '2025_03_15_031300_add_foreign_keys_to_systemlogs_table', 4),
	(63, '2025_05_08_080345_add_featured_image_to_articles_table', 5),
	(64, '2025_05_22_062705_add_tags_to_articles_table', 5),
	(65, '2025_05_29_190409_change_show_status_column_in_election_articles_table', 5),
	(66, '2025_06_03_202918_add_electionparty_id_to_candidate_table', 6),
	(67, '2026_01_21_000001_add_is_admin_to_users_table', 7),
	(68, '2026_01_21_000002_add_deleted_at_to_roles_table', 7),
	(69, '2026_01_21_000003_change_permissions_column_type_on_roles_table', 7),
	(70, '2026_01_22_021500_add_description_to_electionparty_table', 7),
	(71, '2026_01_22_023000_add_description_to_positions_table', 8),
	(72, '2026_01_22_023200_fix_positions_id_auto_increment', 9),
	(73, '2026_01_22_023600_fix_positions_deleted_at_column', 10),
	(74, '2026_01_22_065300_add_position_id_to_candidate_table', 11),
	(75, '2026_01_23_094100_create_election_event_settings_table', 11),
	(76, '2026_01_23_100200_add_label_image_to_election_event_settings_table', 11),
	(77, '2026_01_23_010500_add_tile_img_to_positions_table', 12),
	(78, '2026_01_23_174500_create_position_votes_table', 13),
	(79, '2026_01_23_190000_add_logo_to_electionparty_table', 13),
	(80, '2026_01_27_110710_add_deleted_at_to_courses_table', 14),
	(81, '2026_01_27_110700_add_deleted_at_to_electionparty_table', 15),
	(82, '2026_01_26_075200_add_users_id_to_candidate_table', 16),
	(83, '2026_01_28_151700_add_voter_fields_to_users_table', 17),
	(84, '2026_02_03_000000_add_related_events_and_joined_elections_to_candidate_table', 17),
	(88, '2026_02_05_000001_create_chatbot_intents_table', 18),
	(89, '2026_02_05_000002_create_chatbot_settings_table', 18),
	(90, '2026_02_05_000003_create_chatbot_logs_table', 19),
	(91, '2026_02_05_000004_create_tickets_table', 20),
	(92, '2026_02_09_000000_add_dates_and_status_to_election_event_settings_table', 21),
	(93, '2026_02_09_000001_create_academic_years_table', 22),
	(94, '2026_02_09_000002_add_fields_to_election_table', 23),
	(95, '2026_09_22_000001_add_first_name_and_last_name_to_users_table', 24),
	(96, '2026_09_22_000002_add_middle_initial_to_users_table', 25);

-- Dumping structure for table db_electionsystem.options
CREATE TABLE IF NOT EXISTS `options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `election_id` bigint NOT NULL,
  `type` enum('candidate','election_party') COLLATE utf8mb4_unicode_ci NOT NULL,
  `candidate_id` bigint DEFAULT NULL,
  `position_id` int DEFAULT NULL,
  `electionparty_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.options: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.positions
CREATE TABLE IF NOT EXISTS `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `tile_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.positions: ~7 rows (approximately)
INSERT INTO `positions` (`id`, `name`, `description`, `tile_img`, `status`, `created_at`, `deleted_at`) VALUES
	(2, 'Vice', 'e', 'position-tiles/d1971792-9117-48cb-a08c-a39110adab0e.jpg', 'active', '2026-01-23 05:22:49', NULL),
	(3, 'Presidential', 'PCU student running for CSG', 'position-tiles/9963dfd8-cca1-4e70-99c1-ad097ea7e08f.jpg', 'active', '2026-01-23 05:21:43', NULL),
	(4, 'Secretary', 'e', 'position-tiles/fd4359cd-60d2-43d1-ba9b-7cdf6127d752.png', 'active', '2026-01-23 05:22:29', NULL),
	(5, 'B-O-D', 'e', 'position-tiles/fba31903-dc6b-4945-ad08-32491a930581.jpg', 'active', '2026-01-23 05:26:43', NULL),
	(6, 'Sgt. Arms', 'e', 'position-tiles/87f83adc-87da-4757-a6ca-83d7f0245c74.jpg', 'active', '2026-01-23 05:25:27', NULL),
	(7, 'Treasurer', 'e', 'position-tiles/fc332181-39b4-4b96-92c6-48d438b3d69d.jpg', 'active', '2026-01-23 05:23:42', NULL),
	(8, 'Auditor', 'e', 'position-tiles/f6016018-2ee7-4d1f-ae23-7d46857b162d.jpg', 'active', '2026-01-23 05:24:40', NULL);

-- Dumping structure for table db_electionsystem.position_votes
CREATE TABLE IF NOT EXISTS `position_votes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `position_id` bigint unsigned NOT NULL,
  `candidate_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `position_votes_user_position_unique` (`user_id`,`position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.position_votes: ~7 rows (approximately)
INSERT INTO `position_votes` (`id`, `user_id`, `position_id`, `candidate_id`, `created_at`, `updated_at`) VALUES
	(2, 60, 3, 4, '2026-01-26 00:07:27', '2026-01-26 00:07:27'),
	(3, 61, 3, 6, '2026-01-29 03:38:13', '2026-01-29 03:38:13'),
	(8, 67, 3, 6, '2026-09-22 06:53:02', '2026-09-22 06:53:02'),
	(9, 67, 2, 11, '2026-09-22 06:55:50', '2026-09-22 06:55:50'),
	(10, 67, 8, 8, '2026-09-22 06:55:58', '2026-09-22 06:55:58'),
	(11, 67, 6, 3, '2026-09-22 06:56:07', '2026-09-22 06:56:07'),
	(12, 66, 3, 6, '2026-09-24 04:41:54', '2026-09-24 04:41:54');

-- Dumping structure for table db_electionsystem.register
CREATE TABLE IF NOT EXISTS `register` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `election_id` bigint DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `register_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.register: ~1 rows (approximately)
INSERT INTO `register` (`id`, `user_id`, `election_id`, `status`, `created_at`, `delete_at`) VALUES
	(1, 60, 22, 'Registered', '2026-01-26 07:26:08', '2026-01-26 07:26:08');

-- Dumping structure for table db_electionsystem.reports
CREATE TABLE IF NOT EXISTS `reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `datalisted` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Voting statistics','Candidate performance','Election summary','User participation','System logs') COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_admin_id_foreign` (`admin_id`),
  CONSTRAINT `reports_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.reports: ~2 rows (approximately)
INSERT INTO `reports` (`id`, `name`, `description`, `datalisted`, `type`, `admin_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Lourence Lim', 'Hello', 'pending', 'System logs', 59, '2026-01-22 02:45:22', '2026-01-22 02:45:58', '2026-01-22 02:45:58'),
	(2, 'Lourence Lim', 'Hi', 'pending', 'System logs', 59, '2026-01-22 02:46:07', '2026-01-22 02:46:07', NULL);

-- Dumping structure for table db_electionsystem.results
CREATE TABLE IF NOT EXISTS `results` (
  `id` bigint NOT NULL,
  `election_id` bigint DEFAULT NULL,
  `option_id` bigint DEFAULT NULL,
  `totalVotes` int DEFAULT NULL,
  `totalVoters` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.results: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.role
CREATE TABLE IF NOT EXISTS `role` (
  `id` bigint NOT NULL,
  `users_id` bigint DEFAULT NULL,
  `roles_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `roles_id` (`roles_id`),
  CONSTRAINT `role_ibfk_1` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.role: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.roles: ~2 rows (approximately)
INSERT INTO `roles` (`id`, `name`, `description`, `permissions`, `status`, `created_at`, `deleted_at`, `delete_at`) VALUES
	(1, 'sda', 'asd', 'manage-elections-edit, manage-candidates-add', 'active', '2026-01-21 02:35:47', NULL, '2026-01-21 02:35:47'),
	(2, 'liberal', 'wsa', 'manage-elections-delete, manage-candidates-view, manage-candidates-add, manage-articles-view, view-results', 'active', '2026-01-26 04:32:00', NULL, '2026-01-26 04:32:00');

-- Dumping structure for table db_electionsystem.semesters
CREATE TABLE IF NOT EXISTS `semesters` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.semesters: ~2 rows (approximately)
INSERT INTO `semesters` (`id`, `name`, `created_at`, `delete_at`) VALUES
	(1, '1st Semester', '2025-03-25 02:47:40', '2025-03-17 11:05:21'),
	(2, '2nd Semester', '2025-03-25 02:47:49', '2025-03-17 11:20:24');

-- Dumping structure for table db_electionsystem.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.sessions: ~1 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('1Whw8NTtCPqu8uC4EPZ8esSUXQ36yQtihc8TbZyB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWE4ydmxvd2VHVlZ2N2VDSHpEQTQzVVkyMG9VcERma3hlWWhyckhyYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MjI6IlBIUERFQlVHQkFSX1NUQUNLX0RBVEEiO2E6MDp7fX0=', 1768982772);

-- Dumping structure for table db_electionsystem.systemlogs
CREATE TABLE IF NOT EXISTS `systemlogs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `log_type` enum('AUDIT','SECURITY','USER_ACTIVITY','TRANSACTION') COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` bigint unsigned DEFAULT NULL,
  `operation` enum('INSERT','UPDATE','DELETE','READ','LOGIN','LOGOUT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `systemlogs_user_id_foreign` (`user_id`),
  CONSTRAINT `systemlogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `systemlogs_chk_1` CHECK (json_valid(`old_data`)),
  CONSTRAINT `systemlogs_chk_2` CHECK (json_valid(`new_data`))
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.systemlogs: ~50 rows (approximately)
INSERT INTO `systemlogs` (`id`, `user_id`, `log_type`, `action`, `description`, `table_name`, `record_id`, `operation`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
	(1, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 08:24:05'),
	(2, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 08:24:17'),
	(3, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 01:36:42'),
	(4, 59, 'AUDIT', 'Candidate Updated', 'Updated candidate #11', 'candidate', 11, 'UPDATE', '{"status":"Pending"}', '{"status":"Active"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 01:36:56'),
	(5, 59, 'AUDIT', 'Candidate Updated', 'Updated candidate #8', 'candidate', 8, 'UPDATE', '{"description":"asdasd"}', '{"description":"asdasdasdsad"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 01:37:05'),
	(6, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 02:52:40'),
	(7, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:38:42'),
	(8, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:38:59'),
	(9, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 04:01:30'),
	(10, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 04:35:21'),
	(11, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 00:58:01'),
	(12, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:01:43'),
	(13, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:03:55'),
	(14, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:04:43'),
	(15, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:05:04'),
	(16, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:05:35'),
	(17, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:06:12'),
	(18, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:06:54'),
	(19, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:07:10'),
	(20, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:08:39'),
	(21, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:09:02'),
	(22, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:10:13'),
	(23, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:12:22'),
	(24, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:13:25'),
	(25, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:13:57'),
	(26, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:14:35'),
	(27, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:15:36'),
	(28, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:17:46'),
	(29, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:24:18'),
	(30, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:30:47'),
	(31, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:32:11'),
	(32, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:32:42'),
	(33, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:36:15'),
	(34, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:39:33'),
	(35, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 01:43:54'),
	(36, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 00:46:38'),
	(37, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 00:48:03'),
	(38, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 00:49:12'),
	(39, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 01:05:31'),
	(40, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 01:05:41'),
	(41, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 03:05:07'),
	(42, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 03:05:16'),
	(43, 59, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 59, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 03:28:07'),
	(44, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 03:28:23'),
	(45, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-11 00:56:42'),
	(46, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-16 00:35:57'),
	(47, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 00:57:01'),
	(48, 59, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 59, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-10 02:00:08'),
	(49, 64, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 64, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 01:27:49'),
	(50, 64, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 64, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 01:28:05'),
	(51, 64, 'AUDIT', 'Election Created', 'Created election #28', 'election', 28, 'INSERT', NULL, '{"name":"President","description":"Election created from article","start_date":"2026-09-22 00:00:00","end_date":"2026-10-22 00:00:00","reg_start":"2026-10-22 00:00:00","reg_end":"2026-10-29 00:00:00","campaign_start":"2026-11-06 00:00:00","campaign_end":"2026-12-01 00:00:00","result_date":"2027-01-01 00:00:00","admin_id":64,"id":28}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 01:35:52'),
	(52, 64, 'AUDIT', 'Article Created', 'Created article #15', 'articles', 15, 'INSERT', NULL, '{"title":"Test Article Nemz","author":"Nemz","content":"<p>content<\\/p>","featured_image":"\\/storage\\/featured-images\\/1790040952_featured_pc.jpg","tags":"[]","created_at":"2026-09-22 09:35:52","id":15}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 01:35:52'),
	(53, 64, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 64, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 01:38:21'),
	(54, 64, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 64, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 03:40:41'),
	(55, 64, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 64, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 05:01:31'),
	(56, 64, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 64, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 05:02:02'),
	(57, 64, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 64, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 05:11:59'),
	(58, 64, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 64, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 05:14:42'),
	(59, 64, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 64, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 05:21:29'),
	(60, 64, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 64, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 06:17:59'),
	(61, 64, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 64, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 06:21:31'),
	(62, 64, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 64, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 07:35:56'),
	(63, 64, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 64, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 07:53:53'),
	(64, 64, 'SECURITY', 'Admin Login', 'Admin logged in.', 'users', 64, 'LOGIN', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 04:40:06'),
	(65, 64, 'SECURITY', 'Admin Logout', 'Admin logged out.', 'users', 64, 'LOGOUT', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-24 04:40:40');

-- Dumping structure for table db_electionsystem.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Vote Concern',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Open',
  `admin_reply` longtext COLLATE utf8mb4_unicode_ci,
  `admin_id` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.tickets: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `idnumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_initial` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idnumber` (`idnumber`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.users: ~55 rows (approximately)
INSERT INTO `users` (`id`, `idnumber`, `first_name`, `middle_initial`, `last_name`, `course`, `year_level`, `status`, `admin_role`, `profile_image`, `name`, `email`, `password`, `is_admin`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, '741258', 'Lanz', NULL, 'Azriel Saul', NULL, NULL, NULL, NULL, NULL, 'Lanz Azriel Saul', 'lacsaul@gmail.com', '$2y$12$AjH/P6Sa42Wn7MJDyEoZWOJww882.FKKizWhuIme6o9p5JeaU2jSi', 0, '2025-03-08 03:42:55', '2026-09-22 03:14:21', NULL),
	(2, '202103766', 'Jane', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jane', 'mjtlricafort@pcu.edu.ph', '$2y$12$Zzll2CAEsNE8keWoagQW5u1Yi3EvhLePMMn/0o2a3nCr4pX38nHSO', 0, '2025-03-17 02:46:43', '2026-09-22 03:14:21', NULL),
	(3, '1235456', 'Steffi', NULL, 'Cheon', NULL, NULL, NULL, NULL, NULL, 'Steffi Cheon', 'stefficheon@pcu.edu.ph', 'stefficheon1', 0, '2025-03-17 11:23:30', '2026-09-22 03:14:21', NULL),
	(4, '456789', 'Irene', NULL, 'Tiu', NULL, NULL, NULL, NULL, NULL, 'Irene Tiu', 'irenetiu@pcu.edu.ph', 'irenetiu2', 0, '2025-03-17 11:24:37', '2026-09-22 03:14:21', NULL),
	(5, '987456', 'Gab', NULL, 'Rivera', NULL, NULL, NULL, NULL, NULL, 'Gab Rivera', 'gabrivera@pcu.edu.ph', 'gabrivera3', 0, '2025-03-17 11:26:12', '2026-09-22 03:14:21', NULL),
	(10, '123456', 'John', NULL, 'Doe', NULL, NULL, NULL, NULL, NULL, 'John Doe', 'johndoe@gmail.com', 'johndoe', 0, '2025-03-28 05:08:04', '2026-09-22 03:14:21', NULL),
	(11, '654321', 'Jane', NULL, 'Smith', NULL, NULL, NULL, NULL, NULL, 'Jane Smith', 'janesmith@gmail.com', 'janesmith', 0, '2025-03-28 05:10:19', '2026-09-22 03:14:21', NULL),
	(12, '789012', 'Alice', NULL, 'Johnson', NULL, NULL, NULL, NULL, NULL, 'Alice Johnson', 'alicejohnson@gmail.com', 'alicejohnson', 0, '2025-03-28 05:11:29', '2026-09-22 03:14:21', NULL),
	(13, '147852', 'Miguel', NULL, 'Santiago', NULL, NULL, NULL, NULL, NULL, 'Miguel Santiago', 'miguelsantiago@example.com', '$2y$12$78mUqPuRckBw8uHWfsVsIO1qx6dZhKR5Fk1pUHSiroGq6BYTBXOOu', 0, '2025-04-02 21:19:01', '2026-09-22 03:14:21', NULL),
	(14, '209630', 'Elijah', NULL, 'Ramirez', NULL, NULL, NULL, NULL, NULL, 'Elijah Ramirez', 'elijahramirez@example.com', '$2y$12$EJzz4MwklAh52725HM.waeiZZZfJ6GUNeoqLPA5Vw2PD35abcPwqy', 0, '2025-04-02 21:19:01', '2026-09-22 03:14:21', NULL),
	(15, '369025', 'Joaquin', NULL, 'Dela Cruz', NULL, NULL, NULL, NULL, NULL, 'Joaquin Dela Cruz', 'joaquindelacruz@example.com', '$2y$12$gvERHr50C9ES3pbVtiZ/SuB97mwppyH8Qcb1c5OLfOkTtSSue92dK', 0, '2025-04-02 21:19:02', '2026-09-22 03:14:21', NULL),
	(16, '587410', 'Sophia', NULL, 'Castillo', NULL, NULL, NULL, NULL, NULL, 'Sophia Castillo', 'sophiacastillo@example.com', '$2y$12$Iexv4Rni48xdOLrcuOW/5e7z5W2mhBiS4mxyIbWSiTWpBsM//JpdS', 0, '2025-04-02 21:19:02', '2026-09-22 03:14:21', NULL),
	(17, '753215', 'Isabella', NULL, 'Mercado', NULL, NULL, NULL, NULL, NULL, 'Isabella Mercado', 'isabellamercado@example.com', '$2y$12$YLMLjdGoOQ20tN7bO89ei.VrJFV0IQUrSZ1e/fpaLEBp7673scAai', 0, '2025-04-02 21:19:03', '2026-09-22 03:14:21', NULL),
	(18, NULL, 'Miguel', NULL, 'Santiago', NULL, NULL, NULL, NULL, NULL, 'Miguel Santiago', 'miguel@example.com', '$2y$12$/4ht53uB3VFBCHHfOLNEy.E73Ly6Sc0rJj3zUaKmFHnwdjgr9x/N6', 0, '2025-04-02 21:24:07', '2026-09-22 03:14:21', NULL),
	(19, NULL, 'Miguel', NULL, 'Santiago', NULL, NULL, NULL, NULL, NULL, 'Miguel Santiago', 'miguelsantiago@example.com', '$2y$12$MeWvBDEyl9DsC96t.XmwF.8wT.VWXAvF6RW/qv1O5ZJLgzpua9gAO', 0, '2025-04-02 22:36:02', '2026-09-22 03:14:21', NULL),
	(20, NULL, 'Elijah', NULL, 'Ramirez', NULL, NULL, NULL, NULL, NULL, 'Elijah Ramirez', 'elijahramirez@example.com', '$2y$12$V86m3TtyKQ5C7844bqUZS.zE8NKblKcFGJ7.P5bSImSSg9GX6cjx.', 0, '2025-04-02 22:36:03', '2026-09-22 03:14:21', NULL),
	(21, NULL, 'Joaquin', NULL, 'Dela Cruz', NULL, NULL, NULL, NULL, NULL, 'Joaquin Dela Cruz', 'joaquindelacruz@example.com', '$2y$12$mRLSnUmWd7xY5tWDZjzPW.woDC2ZG/mpPP8ltFTBJmBw/goI0lytu', 0, '2025-04-02 22:36:03', '2026-09-22 03:14:21', NULL),
	(22, NULL, 'Sophia', NULL, 'Castillo', NULL, NULL, NULL, NULL, NULL, 'Sophia Castillo', 'sophiacastillo@example.com', '$2y$12$JHmuWhl6ZpwY3E1FKi7kNOGrZq9X6ZOAjZ26lwScCwxubC8rCn2qu', 0, '2025-04-02 22:36:04', '2026-09-22 03:14:21', NULL),
	(23, NULL, 'Isabella', NULL, 'Mercado', NULL, NULL, NULL, NULL, NULL, 'Isabella Mercado', 'isabellamercado@example.com', '$2y$12$pWPULdb3PHd5HnjAOXvgOelXTRfiCwxBjB/4DWYbhYth7CwMiUtr2', 0, '2025-04-02 22:36:04', '2026-09-22 03:14:21', NULL),
	(24, '111111', 'Lucas', NULL, 'Fernandez', NULL, NULL, NULL, NULL, NULL, 'Lucas Fernandez', 'lucasfernandez@example.com', '$2y$12$0vLkQn4ls/9KQNCxRZCCtOEI4y6RwI3LGtbxzCQjOLUV824J.Yb4K', 0, '2025-04-04 00:48:14', '2026-09-22 03:14:21', NULL),
	(25, '222222', 'Daniel', NULL, 'Morales', NULL, NULL, NULL, NULL, NULL, 'Daniel Morales', 'danielmorales@example.com', '$2y$12$Hm/jcczUuADLueJAu.ht5ejyi1fOsqa.mowT.mIwvlLaGmQ8ZJxKy', 0, '2025-04-04 00:48:14', '2026-09-22 03:14:21', NULL),
	(26, '333333', 'Gabrielle', NULL, 'Santos', NULL, NULL, NULL, NULL, NULL, 'Gabrielle Santos', 'gabriellesantos@example.com', '$2y$12$6uTEeBjBUrnIiHi9gj6hFu6xIBoJ4BBLR3CmCpx3UA3Jp3UG8hA7O', 0, '2025-04-04 00:48:14', '2026-09-22 03:14:21', NULL),
	(27, '235612', 'Renzo', NULL, 'Aguilar', NULL, NULL, NULL, NULL, NULL, 'Renzo Aguilar', 'renzoaguilar@example.com', '$2y$12$Gj//uvHrEIrqfIJQ48UjFO91DMGq0QDBCVfheM23kBDXQYAjbt7GS', 0, '2025-04-04 01:27:55', '2026-09-22 03:14:21', NULL),
	(28, '265522', 'Caleb', NULL, 'Dominguez', NULL, NULL, NULL, NULL, NULL, 'Caleb Dominguez', 'calebdominguez@example.com', '$2y$12$O6xOBjXt3zOMUh0g7gxt8eE7Dp2BBFujeTF4af/Gmg2Q6fzmoDc2q', 0, '2025-04-04 01:27:56', '2026-09-22 03:14:21', NULL),
	(29, '563221', 'Jared', NULL, 'Evangelista', NULL, NULL, NULL, NULL, NULL, 'Jared Evangelista', 'jaredevangelista@example.com', '$2y$12$XKEABxQXwjmIVpJ14yAyZO8JGspge47K2jB54Gse.Q2OEhsrj127C', 0, '2025-04-04 01:27:57', '2026-09-22 03:14:21', NULL),
	(30, '532622', 'Zeke', NULL, 'Herrera', NULL, NULL, NULL, NULL, NULL, 'Zeke Herrera', 'zekeherrera@example.com', '$2y$12$jquXRcug00bROB8uAUqJBu92nlS.O7CJvSzUrQcdFyMqsVanJKZ7C', 0, '2025-04-04 01:27:57', '2026-09-22 03:14:21', NULL),
	(31, '125474', 'Trisha', NULL, 'Buenaventura', NULL, NULL, NULL, NULL, NULL, 'Trisha Buenaventura', 'trishabuenaventura@example.com', '$2y$12$UT/RkFr68PuHkCoyVsnYDuB8fqxv4dFbLsVEUw0gn/yF5r3AfBRXS', 0, '2025-04-04 01:27:58', '2026-09-22 03:14:21', NULL),
	(32, '458852', 'Cheska', NULL, 'Rivera', NULL, NULL, NULL, NULL, NULL, 'Cheska Rivera', 'cheskarivera@example.com', '$2y$12$lWPwAMKISEnVjzmoCH5Aj./640D7CLBBC43tTtuiMPtj.E7GWD88K', 0, '2025-04-04 01:27:59', '2026-09-22 03:14:21', NULL),
	(33, '785545', 'Daniella', NULL, 'Soriano', NULL, NULL, NULL, NULL, NULL, 'Daniella Soriano', 'daniellasoriano@example.com', '$2y$12$zAwIU2Tb63NtNaAS.g0XWO22qZ.SEz41ZmfvRg2PeGRfHIYlocnQC', 0, '2025-04-04 01:27:59', '2026-09-22 03:14:21', NULL),
	(34, '588451', 'Mika', NULL, 'Angeles', NULL, NULL, NULL, NULL, NULL, 'Mika Angeles', 'mikaangeles@example.com', '$2y$12$F/oLxlx4/SB9zCanUvFp4OxsMOA06eoPV5kKm.G/vVs8CAxbB/ukW', 0, '2025-04-04 01:28:00', '2026-09-22 03:14:21', NULL),
	(35, '895655', 'Nicole', NULL, 'Arriola', NULL, NULL, NULL, NULL, NULL, 'Nicole Arriola', 'nicolearriola@example.com', '$2y$12$W5upNvHmP2SZEfjQPxw7QuNFAkKqHD/5krAKzwLfgzaFiF3UcNQhO', 0, '2025-04-04 01:28:00', '2026-09-22 03:14:21', NULL),
	(36, '652301', 'Noel', NULL, 'Cruzado', NULL, NULL, NULL, NULL, NULL, 'Noel Cruzado', 'noelcruzado@example.com', '$2y$12$upzsMHLmez/m.7T4YPBm7elejSxyvdROSIL4p9QsShS6YwMnTUI92', 0, '2025-04-06 23:38:51', '2026-09-22 03:14:21', NULL),
	(37, '537484', 'Kyle', NULL, 'Manalo', NULL, NULL, NULL, NULL, NULL, 'Kyle Manalo', 'kylemanalo@example.com', '$2y$12$ihg8BUrNaH2vNY3h63mYGunhwIWBNm4hM9F.U9qV1fpj3Vj9XJ8Cm', 0, '2025-04-06 23:38:51', '2026-09-22 03:14:21', NULL),
	(38, '798484', 'Leo', NULL, 'Vergara', NULL, NULL, NULL, NULL, NULL, 'Leo Vergara', 'leovergara@example.com', '$2y$12$l9tGpETUt3WDQdls9FUlP.jv4dBGv/n/iEuhxlIUuSFKa87r0klsu', 0, '2025-04-06 23:49:45', '2026-09-22 03:14:21', NULL),
	(39, '785725', 'Bryce', NULL, 'Salcedo', NULL, NULL, NULL, NULL, NULL, 'Bryce Salcedo', 'brycesalcedo@example.com', '$2y$12$Wjcpt6lF7T6lWbocQ8CCj.D2nKC43KiruLgxaj9DkpWHmMQGomvAa', 0, '2025-04-06 23:49:45', '2026-09-22 03:14:21', NULL),
	(40, '956468', 'Janelle', NULL, 'Pascual', NULL, NULL, NULL, NULL, NULL, 'Janelle Pascual', 'janellepascual@example.com', '$2y$12$IxqclD/CpjEDP7sRAyYVs.c3bEvyqERtbrCuqQS/arIFfVg.fU0eG', 0, '2025-04-07 20:11:30', '2026-09-22 03:14:21', NULL),
	(41, '845455', 'Patricia', NULL, 'Lozano', NULL, NULL, NULL, NULL, NULL, 'Patricia Lozano', 'patricialozano@example.com', '$2y$12$lY/3JNbL.3EPL9vzdU9/fuYgbiTtcpCoTd964hTaMhFRgLSOe7Jwq', 0, '2025-04-07 20:11:31', '2026-09-22 03:14:21', NULL),
	(42, '345267', 'Andre', NULL, 'Salazar', NULL, NULL, NULL, NULL, NULL, 'Andre Salazar', 'andresalazar@example.com', '$2y$12$w.iU3L0Rr/A5ZQmxM/SN3O2z8JOrU9tcf55Bk6HKje0iVLgHLJvJa', 0, '2025-04-08 01:30:26', '2026-09-22 03:14:21', NULL),
	(43, '675626', 'Carlo', NULL, 'Manrique', NULL, NULL, NULL, NULL, NULL, 'Carlo Manrique', 'carlomanrique@example.com', '$2y$12$ZHBj0VTCDiezfFFjzSC6EON3TBMx.TYHz43yxmK9P7z6ihUGwbvF6', 0, '2025-04-08 01:31:51', '2026-09-22 03:14:21', NULL),
	(44, '325652', 'Bianca', NULL, 'Lerma', NULL, NULL, NULL, NULL, NULL, 'Bianca Lerma', 'biancalerma@example.com', '$2y$12$G9SPZnSixVNmDJMEzo5uKOLfd4..kZU3B4oXpbBHfrXVDvlhch0yy', 0, '2025-04-08 02:12:38', '2026-09-22 03:14:21', NULL),
	(45, '54245', 'Shaira', NULL, 'De Leon', NULL, NULL, NULL, NULL, NULL, 'Shaira De Leon', 'shairadeleon@example.com', '$2y$12$XADu5nYKsvF1Ea9lRFauk.s35yL7gsn3cc9WRer00JYukZ07XoxtG', 0, '2025-04-08 02:12:39', '2026-09-22 03:14:21', NULL),
	(48, '845162', 'Xavier', NULL, 'Tolentino', NULL, NULL, NULL, NULL, NULL, 'Xavier Tolentino', 'xaviertolentino@example.com', '$2y$12$MT8IXcHxfG8GEe3posZ79uSfrXEuZb4wTiKNf2L1z0cMTNZjvtkwG', 0, '2025-04-08 02:38:51', '2026-09-22 03:14:21', NULL),
	(49, '215623', 'Rafael', NULL, 'De Vera', NULL, NULL, NULL, NULL, NULL, 'Rafael De Vera', 'rafaeldevera@example.com', '$2y$12$o3tToMXMILce68Gskm03iuPYfCy5kdzFJMM9CDlCQ.zlMP1VFIuH2', 0, '2025-04-08 02:38:51', '2026-09-22 03:14:21', NULL),
	(50, '643616', 'Jerome', NULL, 'Alonte', NULL, NULL, NULL, NULL, NULL, 'Jerome Alonte', 'jeromealonte@example.com', '$2y$12$1Y4eKNT9tobqfBV9oZhDROD2IQx1ki.DPJSlIfE3SFnvrQ.DnAl62', 0, '2025-04-08 02:38:52', '2026-09-22 03:14:21', NULL),
	(54, '314564', 'Denise', NULL, 'Galvez', NULL, NULL, NULL, NULL, NULL, 'Denise Galvez', 'denisegalvez@example.com', '$2y$12$mkUCvNEOyppHtvC.NpT8D.grKFP9RkTmpbOs6ecWOfGTSYRaNGtt6', 0, '2025-04-08 02:38:53', '2026-09-22 03:14:21', NULL),
	(57, '1234567890', 'miko', NULL, 'roanrio', NULL, NULL, NULL, NULL, NULL, 'miko roanrio', 'christianmhicor@gmail.com', '$2y$12$UGIh.kiZexY25FvazbvVaeeGuRdwSGE93NFGR4RyC7OGXKHo7kpqK', 0, '2026-01-21 01:09:26', '2026-09-22 03:14:21', NULL),
	(59, '12345678', 'miko', NULL, 'miko', NULL, NULL, NULL, NULL, NULL, 'miko miko', 'mikronario824@gmail.com', '$2y$12$gBt2xIkTqrCmNpPBUiDXtuXpsqJpe95V9R51mNyV9yZXLDscbVFSS', 1, '2026-01-21 01:36:11', '2026-09-22 03:14:21', NULL),
	(60, '2025302565', 'Charm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Charm', 'saddijerome3@gmail.com', '$2y$12$LdvwU.XS69bnbq4fzGef3OxuROGmxVjIXmub61rHl8OSew//Be3gG', 0, '2026-01-22 05:27:34', '2026-09-22 03:14:21', NULL),
	(61, '2025404538', 'Chavez,', NULL, 'Beverly Ann B.', NULL, NULL, NULL, NULL, NULL, 'Chavez, Beverly Ann B.', 'jerome.saddi4@gmail.com', '$2y$12$BOrumqTVxoA9tHGX3n4gte9VB0AYF1dXcGaMD7heYyGMUKW.mntom', 0, '2026-01-22 05:40:05', '2026-09-22 03:14:21', NULL),
	(62, '2025400894', 'Loremia,', NULL, 'John Carlo C.', NULL, NULL, NULL, NULL, NULL, 'Loremia, John Carlo C.', 'johmcarlo@gmail.com', '$2y$12$z7X9P9pNY6Kz27lTKeCuruaZAybW2uzOSsolJEq5EHCgzunaPfi2O', 0, '2026-01-26 08:06:47', '2026-09-22 03:14:21', NULL),
	(63, '0200003458', 'Joyce', NULL, 'Saddi', NULL, NULL, NULL, NULL, NULL, 'Joyce Saddi', 'Joycesaddi@gmail.com', '$2y$12$5UJf/kvkvsUiW.1KM2.8KOuSWylS.PyiaFw0pQX8up.VvSyFWsYqK', 0, '2026-01-28 05:27:48', '2026-09-22 03:14:21', NULL),
	(64, NULL, 'Administrator', NULL, NULL, NULL, NULL, NULL, 'Administrator', NULL, 'Administrator', 'admin@example.com', '$2y$12$HGSwnSnGa7t2/vFoiNNX3e72zGvEquNcndwyEz5dELPbo/zxgAw6q', 1, '2026-09-22 01:26:26', '2026-09-22 03:14:21', NULL),
	(65, '20250001', 'Demo', NULL, 'Student', 'BS Information Technology', '4', 'Active', NULL, NULL, 'Demo Student', 'student@example.com', '$2y$12$AmBmnXOzWCYckIr.FrQfTuC7tTvB93M3bmr8sjpAnax2AgQBhbAjm', 0, '2026-09-22 01:37:38', '2026-09-22 03:14:21', NULL),
	(66, '232226', 'John', NULL, 'Cedrick L. Nemi', NULL, NULL, NULL, NULL, NULL, 'John Cedrick L. Nemi', 'jclnemi@gmail.com', '$2y$12$MMMgsq7NUBDSZD2GfMZvAuwLf6Ps7VkBqUp9ZnubhRqdOPNY8QT2m', 0, '2026-09-22 03:01:26', '2026-09-22 03:14:21', NULL),
	(67, '000001', 'Test', NULL, 'Student', NULL, NULL, NULL, NULL, NULL, 'Test Student', 'test@gmai.com', '$2y$12$QOrFQXZMM28ZwCD3hBanqu34wVTiQn041ElEFHFJHgcYs0VaWS/CW', 0, '2026-09-22 03:15:39', '2026-09-22 03:15:39', NULL);

-- Dumping structure for table db_electionsystem.verified_voters
CREATE TABLE IF NOT EXISTS `verified_voters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `importgroup` bigint DEFAULT NULL,
  `users_id` bigint DEFAULT NULL,
  `acadyr_id` int DEFAULT NULL,
  `sem_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `yrlvl_id` int DEFAULT NULL,
  `admin_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `acadyr_id` (`acadyr_id`),
  KEY `sem_id` (`sem_id`),
  KEY `course_id` (`course_id`),
  KEY `yrlvl_id` (`yrlvl_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `verified_voters_acadyr_id_foreign` FOREIGN KEY (`acadyr_id`) REFERENCES `acadyr` (`id`),
  CONSTRAINT `verified_voters_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `verified_voters_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `verified_voters_sem_id_foreign` FOREIGN KEY (`sem_id`) REFERENCES `semesters` (`id`),
  CONSTRAINT `verified_voters_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `verified_voters_yrlvl_id_foreign` FOREIGN KEY (`yrlvl_id`) REFERENCES `yearlevel` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.verified_voters: ~94 rows (approximately)
INSERT INTO `verified_voters` (`id`, `importgroup`, `users_id`, `acadyr_id`, `sem_id`, `course_id`, `yrlvl_id`, `admin_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(4, 1, 1, 1, 1, 1, 1, 1, NULL, '2025-03-28 15:43:36', '2025-03-28 15:43:36'),
	(5, 2, 1, 1, 2, 1, 1, 1, NULL, '2025-03-30 16:28:11', '2025-03-30 16:28:11'),
	(6, 1, 2, 1, 1, 1, 1, 1, NULL, NULL, NULL),
	(7, 2, 3, 1, 2, 3, 3, 3, NULL, NULL, NULL),
	(8, 1, 4, 1, 2, 2, 1, 3, NULL, NULL, NULL),
	(9, 2, 5, 1, 1, 1, 1, 5, NULL, '2025-03-27 23:28:54', '2025-03-27 23:28:54'),
	(10, NULL, 10, 1, 1, NULL, NULL, NULL, NULL, '2025-03-30 16:28:23', '2025-03-30 16:28:23'),
	(11, NULL, 11, 1, 1, NULL, NULL, NULL, '2025-03-27 23:25:54', '2025-03-27 23:29:18', '2025-03-27 23:29:18'),
	(12, NULL, 12, 1, 1, NULL, NULL, NULL, '2025-03-27 23:25:54', '2025-03-27 23:29:22', '2025-03-27 23:29:22'),
	(13, NULL, 10, 1, 1, NULL, NULL, NULL, '2025-03-27 23:29:32', '2025-03-27 23:29:45', '2025-03-27 23:29:45'),
	(14, NULL, 11, 1, 1, NULL, NULL, NULL, '2025-03-27 23:29:32', '2025-03-27 23:29:42', '2025-03-27 23:29:42'),
	(15, NULL, 12, 1, 1, NULL, NULL, NULL, '2025-03-27 23:29:32', '2025-03-27 23:29:39', '2025-03-27 23:29:39'),
	(16, NULL, 10, 1, 1, NULL, NULL, NULL, '2025-03-27 23:30:26', '2025-03-27 23:31:18', '2025-03-27 23:31:18'),
	(17, NULL, 11, 1, 1, NULL, NULL, NULL, '2025-03-27 23:30:26', '2025-03-27 23:31:22', '2025-03-27 23:31:22'),
	(18, NULL, 12, 1, 1, NULL, NULL, NULL, '2025-03-27 23:30:26', '2025-03-27 23:31:25', '2025-03-27 23:31:25'),
	(19, NULL, 10, 1, 1, NULL, NULL, NULL, '2025-03-27 23:31:33', '2025-03-27 23:41:32', '2025-03-27 23:41:32'),
	(20, NULL, 11, 1, 1, NULL, NULL, NULL, '2025-03-27 23:31:33', '2025-03-30 16:28:30', '2025-03-30 16:28:30'),
	(21, NULL, 12, 1, 1, NULL, NULL, NULL, '2025-03-27 23:31:33', '2025-03-31 01:52:35', '2025-03-31 01:52:35'),
	(22, NULL, 10, 1, 1, NULL, NULL, NULL, '2025-03-28 15:43:24', '2025-03-28 15:43:48', '2025-03-28 15:43:48'),
	(23, NULL, 11, 1, 1, NULL, NULL, NULL, '2025-03-28 15:43:24', '2025-03-28 15:44:06', '2025-03-28 15:44:06'),
	(24, NULL, 12, 1, 1, NULL, NULL, NULL, '2025-03-28 15:43:24', '2025-03-28 15:43:33', '2025-03-28 15:43:33'),
	(25, NULL, 10, 1, 1, NULL, NULL, NULL, '2025-03-30 16:28:01', '2025-03-31 01:52:43', '2025-03-31 01:52:43'),
	(26, NULL, 11, 1, 1, NULL, NULL, NULL, '2025-03-30 16:28:01', '2025-03-31 01:52:48', '2025-03-31 01:52:48'),
	(27, NULL, 12, 1, 1, NULL, NULL, NULL, '2025-03-30 16:28:01', '2025-03-30 16:28:09', '2025-03-30 16:28:09'),
	(28, NULL, 10, 1, 1, NULL, NULL, NULL, '2025-03-31 01:57:10', '2025-03-31 01:57:19', '2025-03-31 01:57:19'),
	(29, NULL, 11, 1, 1, NULL, NULL, NULL, '2025-03-31 01:57:10', '2025-03-31 01:57:10', NULL),
	(30, NULL, 12, 1, 1, NULL, NULL, NULL, '2025-03-31 01:57:10', '2025-03-31 01:57:10', NULL),
	(31, NULL, 13, 1, 2, NULL, NULL, NULL, '2025-04-02 21:19:03', '2025-04-02 22:45:26', '2025-04-02 22:45:26'),
	(32, NULL, 14, 1, 1, NULL, NULL, NULL, '2025-04-02 21:19:03', '2025-04-02 22:45:33', '2025-04-02 22:45:33'),
	(33, NULL, 15, 1, 2, NULL, NULL, NULL, '2025-04-02 21:19:03', '2025-04-02 22:45:37', '2025-04-02 22:45:37'),
	(34, NULL, 16, 1, 1, NULL, NULL, NULL, '2025-04-02 21:19:03', '2025-04-02 22:45:41', '2025-04-02 22:45:41'),
	(35, NULL, 17, 1, 2, NULL, NULL, NULL, '2025-04-02 21:19:03', '2025-04-02 22:45:45', '2025-04-02 22:45:45'),
	(36, NULL, 19, 1, 2, NULL, NULL, NULL, '2025-04-02 22:36:04', '2025-04-02 22:45:00', '2025-04-02 22:45:00'),
	(37, NULL, 20, 1, 1, NULL, NULL, NULL, '2025-04-02 22:36:04', '2025-04-02 22:45:05', '2025-04-02 22:45:05'),
	(38, NULL, 21, 1, 2, NULL, NULL, NULL, '2025-04-02 22:36:04', '2025-04-02 22:45:09', '2025-04-02 22:45:09'),
	(39, NULL, 22, 1, 1, NULL, NULL, NULL, '2025-04-02 22:36:04', '2025-04-02 22:45:14', '2025-04-02 22:45:14'),
	(40, NULL, 23, 1, 2, NULL, NULL, NULL, '2025-04-02 22:36:04', '2025-04-02 22:45:17', '2025-04-02 22:45:17'),
	(41, NULL, 13, 1, 2, NULL, NULL, NULL, '2025-04-02 22:45:56', '2025-04-02 22:45:56', NULL),
	(42, NULL, 14, 1, 1, NULL, NULL, NULL, '2025-04-02 22:45:56', '2025-04-02 22:45:56', NULL),
	(43, NULL, 15, 1, 2, NULL, NULL, NULL, '2025-04-02 22:45:56', '2025-04-02 22:45:56', NULL),
	(44, NULL, 16, 1, 1, NULL, NULL, NULL, '2025-04-02 22:45:56', '2025-04-02 22:45:56', NULL),
	(45, NULL, 17, 1, 2, NULL, NULL, NULL, '2025-04-02 22:45:56', '2025-04-02 22:45:56', NULL),
	(46, NULL, 2, 1, 2, NULL, NULL, NULL, '2025-04-02 23:16:23', '2025-04-02 23:16:23', NULL),
	(47, NULL, 10, 1, 2, NULL, NULL, NULL, '2025-04-02 23:16:23', '2025-04-02 23:16:23', NULL),
	(48, NULL, 10, 1, 1, NULL, NULL, NULL, '2025-04-02 23:16:23', '2025-04-02 23:16:23', NULL),
	(49, NULL, 24, 1, 1, NULL, NULL, NULL, '2025-04-04 00:48:14', '2025-04-04 00:56:21', '2025-04-04 00:56:21'),
	(50, NULL, 25, 1, 2, NULL, NULL, NULL, '2025-04-04 00:48:14', '2025-04-04 00:56:32', '2025-04-04 00:56:32'),
	(51, NULL, 26, 1, 1, NULL, NULL, NULL, '2025-04-04 00:48:14', '2025-04-04 00:56:40', '2025-04-04 00:56:40'),
	(52, NULL, 24, 1, 1, NULL, NULL, NULL, '2025-04-04 00:56:50', '2025-04-04 00:56:50', NULL),
	(53, NULL, 25, 1, 2, NULL, NULL, NULL, '2025-04-04 00:56:50', '2025-04-04 00:56:50', NULL),
	(54, NULL, 26, 1, 1, NULL, NULL, NULL, '2025-04-04 00:56:50', '2025-04-04 00:56:50', NULL),
	(55, NULL, 27, 1, 1, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:57:12', '2025-04-06 22:57:12'),
	(56, NULL, 28, 1, 2, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:57:20', '2025-04-06 22:57:20'),
	(57, NULL, 29, 1, 1, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:57:27', '2025-04-06 22:57:27'),
	(58, NULL, 30, 1, 1, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:57:00', '2025-04-06 22:57:00'),
	(59, NULL, 31, 1, 2, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:57:34', '2025-04-06 22:57:34'),
	(60, NULL, 32, 1, 2, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:57:42', '2025-04-06 22:57:42'),
	(61, NULL, 33, 1, 1, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:57:51', '2025-04-06 22:57:51'),
	(62, NULL, 34, 1, 1, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:57:56', '2025-04-06 22:57:56'),
	(63, NULL, 35, 1, 2, NULL, NULL, NULL, '2025-04-04 01:28:00', '2025-04-06 22:58:03', '2025-04-06 22:58:03'),
	(64, NULL, 27, 1, 1, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-06 23:25:03', NULL),
	(65, NULL, 28, 1, 2, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-06 23:25:03', NULL),
	(66, NULL, 29, 1, 1, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-06 23:25:03', NULL),
	(67, NULL, 30, 1, 1, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-07 20:24:38', '2025-04-07 20:24:38'),
	(68, NULL, 31, 1, 2, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-07 20:24:42', '2025-04-07 20:24:42'),
	(69, NULL, 32, 1, 2, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-07 20:24:45', '2025-04-07 20:24:45'),
	(70, NULL, 33, 1, 1, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-07 20:24:49', '2025-04-07 20:24:49'),
	(71, NULL, 34, 1, 1, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-07 20:24:51', '2025-04-07 20:24:51'),
	(72, NULL, 35, 1, 2, NULL, NULL, NULL, '2025-04-06 23:25:03', '2025-04-07 20:24:56', '2025-04-07 20:24:56'),
	(73, NULL, 36, 1, 1, NULL, NULL, NULL, '2025-04-06 23:38:51', '2025-04-06 23:43:11', '2025-04-06 23:43:11'),
	(74, NULL, 37, 1, 2, NULL, NULL, NULL, '2025-04-06 23:38:51', '2025-04-06 23:43:18', '2025-04-06 23:43:18'),
	(75, NULL, 36, 1, 1, NULL, NULL, NULL, '2025-04-06 23:48:04', '2025-04-07 20:24:59', '2025-04-07 20:24:59'),
	(76, NULL, 37, 1, 2, NULL, NULL, NULL, '2025-04-06 23:48:04', '2025-04-07 20:25:02', '2025-04-07 20:25:02'),
	(77, NULL, 38, 1, 1, NULL, NULL, NULL, '2025-04-06 23:49:45', '2025-04-07 20:25:05', '2025-04-07 20:25:05'),
	(78, NULL, 39, 1, 2, NULL, NULL, NULL, '2025-04-06 23:49:45', '2025-04-06 23:50:05', '2025-04-06 23:50:05'),
	(79, NULL, 40, 1, 2, NULL, NULL, NULL, '2025-04-07 20:11:31', '2025-04-07 20:25:14', '2025-04-07 20:25:14'),
	(80, NULL, 41, 1, 2, NULL, NULL, NULL, '2025-04-07 20:11:31', '2025-04-07 20:23:18', '2025-04-07 20:23:18'),
	(85, NULL, 38, 1, 1, NULL, NULL, NULL, '2025-04-08 01:08:28', '2025-04-08 01:12:03', '2025-04-08 01:12:03'),
	(86, NULL, 39, 1, 2, NULL, NULL, NULL, '2025-04-08 01:08:28', '2025-04-08 01:12:08', '2025-04-08 01:12:08'),
	(89, NULL, 42, 1, 1, NULL, NULL, NULL, '2025-04-08 01:30:26', '2025-04-08 01:31:37', '2025-04-08 01:31:37'),
	(90, NULL, 39, 1, 2, NULL, NULL, NULL, '2025-04-08 01:30:26', '2025-04-08 01:31:29', '2025-04-08 01:31:29'),
	(91, NULL, 42, 1, 1, NULL, NULL, NULL, '2025-04-08 01:31:51', '2025-04-08 01:31:51', NULL),
	(92, NULL, 43, 1, 2, NULL, NULL, NULL, '2025-04-08 01:31:51', '2025-04-08 01:31:51', NULL),
	(93, NULL, 38, 1, 1, NULL, NULL, NULL, '2025-04-08 01:32:15', '2025-04-08 01:32:15', NULL),
	(94, NULL, 39, 1, 2, NULL, NULL, NULL, '2025-04-08 01:32:15', '2025-04-08 02:12:14', '2025-04-08 02:12:14'),
	(95, NULL, 44, 1, 1, NULL, NULL, NULL, '2025-04-08 02:12:39', '2025-04-08 02:22:02', '2025-04-08 02:22:02'),
	(96, NULL, 45, 1, 1, NULL, NULL, NULL, '2025-04-08 02:12:39', '2025-04-08 02:22:05', '2025-04-08 02:22:05'),
	(97, NULL, 44, 1, 1, NULL, NULL, NULL, '2025-04-08 02:23:13', '2025-04-08 02:23:13', NULL),
	(98, NULL, 45, 1, 1, NULL, NULL, NULL, '2025-04-08 02:23:13', '2025-04-08 02:23:13', NULL),
	(101, NULL, 48, 1, 1, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
	(102, NULL, 49, 1, 1, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
	(103, NULL, 50, 1, 2, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
	(107, NULL, 54, 1, 2, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
	(109, NULL, 67, 1, 1, NULL, NULL, NULL, '2026-09-22 06:21:19', '2026-09-22 06:21:19', NULL),
	(110, NULL, 66, 1, 1, NULL, NULL, NULL, '2026-09-24 04:40:36', '2026-09-24 04:40:36', NULL);

-- Dumping structure for table db_electionsystem.vote
CREATE TABLE IF NOT EXISTS `vote` (
  `id` bigint NOT NULL,
  `register_id` bigint DEFAULT NULL,
  `options_id` bigint DEFAULT NULL,
  `voted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `register_id` (`register_id`),
  CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`register_id`) REFERENCES `register` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.vote: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.votedays
CREATE TABLE IF NOT EXISTS `votedays` (
  `id` bigint NOT NULL,
  `election_id` bigint DEFAULT NULL,
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.votedays: ~0 rows (approximately)

-- Dumping structure for table db_electionsystem.yearlevel
CREATE TABLE IF NOT EXISTS `yearlevel` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_electionsystem.yearlevel: ~2 rows (approximately)
INSERT INTO `yearlevel` (`id`, `name`, `created_at`, `delete_at`) VALUES
	(1, '1st Year', '2025-03-17 11:21:28', '2025-03-17 11:04:09'),
	(3, '2nd Year', '2025-03-17 11:21:18', '2025-03-17 11:21:18');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
