-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 11:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_electionsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `acadyr`
--

CREATE TABLE `acadyr` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `acadyr`
--

INSERT INTO `acadyr` (`id`, `name`, `status`, `created_at`, `delete_at`) VALUES
(1, '2024-2025', NULL, '2025-03-17 11:14:16', '2025-03-17 11:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `allowacad`
--

CREATE TABLE `allowacad` (
  `id` int(11) NOT NULL,
  `election_id` bigint(20) DEFAULT NULL,
  `acadyr_id` int(11) DEFAULT NULL,
  `sem_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('Contacts','Guides','Announcements','News','Updates','Policies') NOT NULL,
  `content` longtext NOT NULL,
  `author` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `id_number` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `platforms` text NOT NULL,
  `description` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `profile_img` varchar(255) NOT NULL,
  `banner_img` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `coursecode` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `coursecode`, `name`, `created_at`, `delete_at`) VALUES
(1, 'BSIT2', 'BS Information Technology', '2025-03-17 11:03:05', '2025-03-17 11:03:05'),
(2, 'BSIT1', 'BS Information Technology', '2025-03-17 11:19:12', '2025-03-17 11:19:12'),
(3, 'BSCS1', 'BS Computer Science', '2025-03-17 11:19:52', '2025-03-17 11:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `election`
--

CREATE TABLE `election` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reg_start` date NOT NULL,
  `reg_end` date NOT NULL,
  `campaign_start` date DEFAULT NULL,
  `campaign_end` date DEFAULT NULL,
  `result_date` date DEFAULT NULL,
  `election_img` blob DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `electionparty`
--

CREATE TABLE `electionparty` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `election_articles`
--

CREATE TABLE `election_articles` (
  `id` int(11) NOT NULL,
  `candidate_id` bigint(20) DEFAULT NULL,
  `election_id` bigint(20) DEFAULT NULL,
  `articles_id` bigint(20) DEFAULT NULL,
  `show_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

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
(62, '2025_03_15_031300_add_foreign_keys_to_systemlogs_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `election_id` bigint(20) NOT NULL,
  `type` enum('candidate','election_party') NOT NULL,
  `candidate_id` bigint(20) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `electionparty_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `election_id` bigint(20) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `datalisted` text NOT NULL,
  `type` enum('Voting statistics','Candidate performance','Election summary','User participation','System logs') NOT NULL,
  `admin_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` bigint(20) NOT NULL,
  `election_id` bigint(20) DEFAULT NULL,
  `option_id` bigint(20) DEFAULT NULL,
  `totalVotes` int(11) DEFAULT NULL,
  `totalVoters` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` bigint(20) NOT NULL,
  `users_id` bigint(20) DEFAULT NULL,
  `roles_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `permissions` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `name`, `created_at`, `delete_at`) VALUES
(1, '1st Semester', '2025-03-25 02:47:40', '2025-03-17 11:05:21'),
(2, '2nd Semester', '2025-03-25 02:47:49', '2025-03-17 11:20:24');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ebMvwROYHVJleQ6tqvSOr3vtuvhsTu1XDuhj7Mrj', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiOXl0b2lUNzh5cjk5WVlLNkpVM3UzRVRIbG5zaXBTaEFLbXhYZWQ3VSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2MToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3ZlcmlmaWVkLXZvdGVycz9wYWdlPTEmcGVyUGFnZT0xMCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjUwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vdmVyaWZpZWQtdm90ZXJzL2V4cG9ydCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1744108780);

-- --------------------------------------------------------

--
-- Table structure for table `systemlogs`
--

CREATE TABLE `systemlogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `log_type` enum('AUDIT','SECURITY','USER_ACTIVITY','TRANSACTION') NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `operation` enum('INSERT','UPDATE','DELETE','READ','LOGIN','LOGOUT') NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `idnumber` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `idnumber`, `name`, `email`, `password`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '741258', 'Lanz Azriel Saul', 'lacsaul@gmail.com', '$2y$12$AjH/P6Sa42Wn7MJDyEoZWOJww882.FKKizWhuIme6o9p5JeaU2jSi', '2025-03-08 03:42:55', '2025-03-18 06:21:58', NULL),
(2, '202103766', 'Jane', 'mjtlricafort@pcu.edu.ph', '$2y$12$Zzll2CAEsNE8keWoagQW5u1Yi3EvhLePMMn/0o2a3nCr4pX38nHSO', '2025-03-17 02:46:43', '2025-03-17 11:27:54', NULL),
(3, '1235456', 'Steffi Cheon', 'stefficheon@pcu.edu.ph', 'stefficheon1', '2025-03-17 11:23:30', '2025-03-17 11:25:02', NULL),
(4, '456789', 'Irene Tiu', 'irenetiu@pcu.edu.ph', 'irenetiu2', '2025-03-17 11:24:37', '2025-03-17 11:25:13', NULL),
(5, '987456', 'Gab Rivera', 'gabrivera@pcu.edu.ph', 'gabrivera3', '2025-03-17 11:26:12', '2025-03-17 11:26:12', NULL),
(10, '123456', 'John Doe', 'johndoe@gmail.com', 'johndoe', '2025-03-28 05:08:04', '2025-03-28 05:08:04', NULL),
(11, '654321', 'Jane Smith', 'janesmith@gmail.com', 'janesmith', '2025-03-28 05:10:19', '2025-03-28 05:10:19', NULL),
(12, '789012', 'Alice Johnson', 'alicejohnson@gmail.com', 'alicejohnson', '2025-03-28 05:11:29', '2025-03-28 05:11:29', NULL),
(13, '147852', 'Miguel Santiago', 'miguelsantiago@example.com', '$2y$12$78mUqPuRckBw8uHWfsVsIO1qx6dZhKR5Fk1pUHSiroGq6BYTBXOOu', '2025-04-02 21:19:01', '2025-04-02 22:44:47', NULL),
(14, '209630', 'Elijah Ramirez', 'elijahramirez@example.com', '$2y$12$EJzz4MwklAh52725HM.waeiZZZfJ6GUNeoqLPA5Vw2PD35abcPwqy', '2025-04-02 21:19:01', '2025-04-02 22:44:47', NULL),
(15, '369025', 'Joaquin Dela Cruz', 'joaquindelacruz@example.com', '$2y$12$gvERHr50C9ES3pbVtiZ/SuB97mwppyH8Qcb1c5OLfOkTtSSue92dK', '2025-04-02 21:19:02', '2025-04-02 22:44:47', NULL),
(16, '587410', 'Sophia Castillo', 'sophiacastillo@example.com', '$2y$12$Iexv4Rni48xdOLrcuOW/5e7z5W2mhBiS4mxyIbWSiTWpBsM//JpdS', '2025-04-02 21:19:02', '2025-04-02 22:44:47', NULL),
(17, '753215', 'Isabella Mercado', 'isabellamercado@example.com', '$2y$12$YLMLjdGoOQ20tN7bO89ei.VrJFV0IQUrSZ1e/fpaLEBp7673scAai', '2025-04-02 21:19:03', '2025-04-02 22:44:47', NULL),
(18, NULL, 'Miguel Santiago', 'miguel@example.com', '$2y$12$/4ht53uB3VFBCHHfOLNEy.E73Ly6Sc0rJj3zUaKmFHnwdjgr9x/N6', '2025-04-02 21:24:07', '2025-04-02 21:24:07', NULL),
(19, NULL, 'Miguel Santiago', 'miguelsantiago@example.com', '$2y$12$MeWvBDEyl9DsC96t.XmwF.8wT.VWXAvF6RW/qv1O5ZJLgzpua9gAO', '2025-04-02 22:36:02', '2025-04-02 22:36:02', NULL),
(20, NULL, 'Elijah Ramirez', 'elijahramirez@example.com', '$2y$12$V86m3TtyKQ5C7844bqUZS.zE8NKblKcFGJ7.P5bSImSSg9GX6cjx.', '2025-04-02 22:36:03', '2025-04-02 22:36:03', NULL),
(21, NULL, 'Joaquin Dela Cruz', 'joaquindelacruz@example.com', '$2y$12$mRLSnUmWd7xY5tWDZjzPW.woDC2ZG/mpPP8ltFTBJmBw/goI0lytu', '2025-04-02 22:36:03', '2025-04-02 22:36:03', NULL),
(22, NULL, 'Sophia Castillo', 'sophiacastillo@example.com', '$2y$12$JHmuWhl6ZpwY3E1FKi7kNOGrZq9X6ZOAjZ26lwScCwxubC8rCn2qu', '2025-04-02 22:36:04', '2025-04-02 22:36:04', NULL),
(23, NULL, 'Isabella Mercado', 'isabellamercado@example.com', '$2y$12$pWPULdb3PHd5HnjAOXvgOelXTRfiCwxBjB/4DWYbhYth7CwMiUtr2', '2025-04-02 22:36:04', '2025-04-02 22:36:04', NULL),
(24, '111111', 'Lucas Fernandez', 'lucasfernandez@example.com', '$2y$12$0vLkQn4ls/9KQNCxRZCCtOEI4y6RwI3LGtbxzCQjOLUV824J.Yb4K', '2025-04-04 00:48:14', '2025-04-04 00:56:50', NULL),
(25, '222222', 'Daniel Morales', 'danielmorales@example.com', '$2y$12$Hm/jcczUuADLueJAu.ht5ejyi1fOsqa.mowT.mIwvlLaGmQ8ZJxKy', '2025-04-04 00:48:14', '2025-04-04 00:56:50', NULL),
(26, '333333', 'Gabrielle Santos', 'gabriellesantos@example.com', '$2y$12$6uTEeBjBUrnIiHi9gj6hFu6xIBoJ4BBLR3CmCpx3UA3Jp3UG8hA7O', '2025-04-04 00:48:14', '2025-04-04 00:56:50', NULL),
(27, '235612', 'Renzo Aguilar', 'renzoaguilar@example.com', '$2y$12$Gj//uvHrEIrqfIJQ48UjFO91DMGq0QDBCVfheM23kBDXQYAjbt7GS', '2025-04-04 01:27:55', '2025-04-06 23:25:03', NULL),
(28, '265522', 'Caleb Dominguez', 'calebdominguez@example.com', '$2y$12$O6xOBjXt3zOMUh0g7gxt8eE7Dp2BBFujeTF4af/Gmg2Q6fzmoDc2q', '2025-04-04 01:27:56', '2025-04-06 23:25:03', NULL),
(29, '563221', 'Jared Evangelista', 'jaredevangelista@example.com', '$2y$12$XKEABxQXwjmIVpJ14yAyZO8JGspge47K2jB54Gse.Q2OEhsrj127C', '2025-04-04 01:27:57', '2025-04-06 23:25:03', NULL),
(30, '532622', 'Zeke Herrera', 'zekeherrera@example.com', '$2y$12$jquXRcug00bROB8uAUqJBu92nlS.O7CJvSzUrQcdFyMqsVanJKZ7C', '2025-04-04 01:27:57', '2025-04-06 23:25:03', NULL),
(31, '125474', 'Trisha Buenaventura', 'trishabuenaventura@example.com', '$2y$12$UT/RkFr68PuHkCoyVsnYDuB8fqxv4dFbLsVEUw0gn/yF5r3AfBRXS', '2025-04-04 01:27:58', '2025-04-06 23:25:03', NULL),
(32, '458852', 'Cheska Rivera', 'cheskarivera@example.com', '$2y$12$lWPwAMKISEnVjzmoCH5Aj./640D7CLBBC43tTtuiMPtj.E7GWD88K', '2025-04-04 01:27:59', '2025-04-06 23:25:03', NULL),
(33, '785545', 'Daniella Soriano', 'daniellasoriano@example.com', '$2y$12$zAwIU2Tb63NtNaAS.g0XWO22qZ.SEz41ZmfvRg2PeGRfHIYlocnQC', '2025-04-04 01:27:59', '2025-04-06 23:25:03', NULL),
(34, '588451', 'Mika Angeles', 'mikaangeles@example.com', '$2y$12$F/oLxlx4/SB9zCanUvFp4OxsMOA06eoPV5kKm.G/vVs8CAxbB/ukW', '2025-04-04 01:28:00', '2025-04-06 23:25:03', NULL),
(35, '895655', 'Nicole Arriola', 'nicolearriola@example.com', '$2y$12$W5upNvHmP2SZEfjQPxw7QuNFAkKqHD/5krAKzwLfgzaFiF3UcNQhO', '2025-04-04 01:28:00', '2025-04-06 23:25:03', NULL),
(36, '652301', 'Noel Cruzado', 'noelcruzado@example.com', '$2y$12$upzsMHLmez/m.7T4YPBm7elejSxyvdROSIL4p9QsShS6YwMnTUI92', '2025-04-06 23:38:51', '2025-04-06 23:48:04', NULL),
(37, '537484', 'Kyle Manalo', 'kylemanalo@example.com', '$2y$12$ihg8BUrNaH2vNY3h63mYGunhwIWBNm4hM9F.U9qV1fpj3Vj9XJ8Cm', '2025-04-06 23:38:51', '2025-04-06 23:48:04', NULL),
(38, '798484', 'Leo Vergara', 'leovergara@example.com', '$2y$12$l9tGpETUt3WDQdls9FUlP.jv4dBGv/n/iEuhxlIUuSFKa87r0klsu', '2025-04-06 23:49:45', '2025-04-06 23:49:45', NULL),
(39, '785725', 'Bryce Salcedo', 'brycesalcedo@example.com', '$2y$12$Wjcpt6lF7T6lWbocQ8CCj.D2nKC43KiruLgxaj9DkpWHmMQGomvAa', '2025-04-06 23:49:45', '2025-04-06 23:49:45', NULL),
(40, '956468', 'Janelle Pascual', 'janellepascual@example.com', '$2y$12$IxqclD/CpjEDP7sRAyYVs.c3bEvyqERtbrCuqQS/arIFfVg.fU0eG', '2025-04-07 20:11:30', '2025-04-07 20:11:30', NULL),
(41, '845455', 'Patricia Lozano', 'patricialozano@example.com', '$2y$12$lY/3JNbL.3EPL9vzdU9/fuYgbiTtcpCoTd964hTaMhFRgLSOe7Jwq', '2025-04-07 20:11:31', '2025-04-07 20:11:31', NULL),
(42, '345267', 'Andre Salazar', 'andresalazar@example.com', '$2y$12$w.iU3L0Rr/A5ZQmxM/SN3O2z8JOrU9tcf55Bk6HKje0iVLgHLJvJa', '2025-04-08 01:30:26', '2025-04-08 01:30:26', NULL),
(43, '675626', 'Carlo Manrique', 'carlomanrique@example.com', '$2y$12$ZHBj0VTCDiezfFFjzSC6EON3TBMx.TYHz43yxmK9P7z6ihUGwbvF6', '2025-04-08 01:31:51', '2025-04-08 01:31:51', NULL),
(44, '325652', 'Bianca Lerma', 'biancalerma@example.com', '$2y$12$G9SPZnSixVNmDJMEzo5uKOLfd4..kZU3B4oXpbBHfrXVDvlhch0yy', '2025-04-08 02:12:38', '2025-04-08 02:12:38', NULL),
(45, '54245', 'Shaira De Leon', 'shairadeleon@example.com', '$2y$12$XADu5nYKsvF1Ea9lRFauk.s35yL7gsn3cc9WRer00JYukZ07XoxtG', '2025-04-08 02:12:39', '2025-04-08 02:12:39', NULL),
(46, '845454', 'Enzo Villafuerte', 'enzovillafuerte@example.com', '$2y$12$s2B.ubnzb.1ByR9rZFZd9uN3dUv8ywyFdL27NfL/4RtWVvzfDUIUy', '2025-04-08 02:38:50', '2025-04-08 02:38:50', NULL),
(47, '743465', 'Lance Quijano', 'lancequijano@example.com', '$2y$12$6Dr82EGWXFSv0Vqcr/x4zu3u6eXh5C6YjQydq72eO0hX3qkTiDvf6', '2025-04-08 02:38:50', '2025-04-08 02:38:50', NULL),
(48, '845162', 'Xavier Tolentino', 'xaviertolentino@example.com', '$2y$12$MT8IXcHxfG8GEe3posZ79uSfrXEuZb4wTiKNf2L1z0cMTNZjvtkwG', '2025-04-08 02:38:51', '2025-04-08 02:38:51', NULL),
(49, '215623', 'Rafael De Vera', 'rafaeldevera@example.com', '$2y$12$o3tToMXMILce68Gskm03iuPYfCy5kdzFJMM9CDlCQ.zlMP1VFIuH2', '2025-04-08 02:38:51', '2025-04-08 02:38:51', NULL),
(50, '643616', 'Jerome Alonte', 'jeromealonte@example.com', '$2y$12$1Y4eKNT9tobqfBV9oZhDROD2IQx1ki.DPJSlIfE3SFnvrQ.DnAl62', '2025-04-08 02:38:52', '2025-04-08 02:38:52', NULL),
(51, '324794', 'Mariel Sandoval', 'marielsandoval@example.com', '$2y$12$9eBOj2D1/zq5KJPJR/QrHukiHVYGjjfBL38LNI3XG0nmcreiW3UQa', '2025-04-08 02:38:52', '2025-04-08 02:38:52', NULL),
(52, '947562', 'Krisha Jimenez', 'krishajimenez@example.com', '$2y$12$j35UaSE7QTFlOMywxaLhauYJianpVDvotUK6kJtLf3Cn9BKJbgOdm', '2025-04-08 02:38:53', '2025-04-08 02:38:53', NULL),
(53, '356865', 'Ellaine Ferrer', 'ellaineferrer@example.com', '$2y$12$kOAt2UX7l.QC8V6KCXsGs.MpGc3huoialQkExKuSp3LkWKBHJFXO.', '2025-04-08 02:38:53', '2025-04-08 02:38:53', NULL),
(54, '314564', 'Denise Galvez', 'denisegalvez@example.com', '$2y$12$mkUCvNEOyppHtvC.NpT8D.grKFP9RkTmpbOs6ecWOfGTSYRaNGtt6', '2025-04-08 02:38:53', '2025-04-08 02:38:53', NULL),
(55, '642762', 'Lourdes Ignacio', 'lourdesignacio@example.com', '$2y$12$Wj.xQTdADevtIoygkvtTA.egCRmKQ/cV0UXlNOu1XqeJfetEKcPBW', '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `verified_voters`
--

CREATE TABLE `verified_voters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `importgroup` bigint(20) DEFAULT NULL,
  `users_id` bigint(20) DEFAULT NULL,
  `acadyr_id` int(11) DEFAULT NULL,
  `sem_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `yrlvl_id` int(11) DEFAULT NULL,
  `admin_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verified_voters`
--

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
(99, NULL, 46, 1, 2, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(100, NULL, 47, 1, 2, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(101, NULL, 48, 1, 1, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(102, NULL, 49, 1, 1, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(103, NULL, 50, 1, 2, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(104, NULL, 51, 1, 1, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(105, NULL, 52, 1, 2, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(106, NULL, 53, 1, 1, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(107, NULL, 54, 1, 2, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:38:54', NULL),
(108, NULL, 55, 1, 2, NULL, NULL, NULL, '2025-04-08 02:38:54', '2025-04-08 02:39:34', '2025-04-08 02:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

CREATE TABLE `vote` (
  `id` bigint(20) NOT NULL,
  `register_id` bigint(20) DEFAULT NULL,
  `options_id` bigint(20) DEFAULT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `votedays`
--

CREATE TABLE `votedays` (
  `id` bigint(20) NOT NULL,
  `election_id` bigint(20) DEFAULT NULL,
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `yearlevel`
--

CREATE TABLE `yearlevel` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `yearlevel`
--

INSERT INTO `yearlevel` (`id`, `name`, `created_at`, `delete_at`) VALUES
(1, '1st Year', '2025-03-17 11:21:28', '2025-03-17 11:04:09'),
(3, '2nd Year', '2025-03-17 11:21:18', '2025-03-17 11:21:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acadyr`
--
ALTER TABLE `acadyr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `allowacad`
--
ALTER TABLE `allowacad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acadyr_id` (`acadyr_id`),
  ADD KEY `sem_id` (`sem_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `candidate_id_number_unique` (`id_number`),
  ADD KEY `candidate_course_id_foreign` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `election`
--
ALTER TABLE `election`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `electionparty`
--
ALTER TABLE `electionparty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `election_articles`
--
ALTER TABLE `election_articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `roles_id` (`roles_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `systemlogs`
--
ALTER TABLE `systemlogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `systemlogs_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idnumber` (`idnumber`);

--
-- Indexes for table `verified_voters`
--
ALTER TABLE `verified_voters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `acadyr_id` (`acadyr_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `yrlvl_id` (`yrlvl_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `vote`
--
ALTER TABLE `vote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `register_id` (`register_id`);

--
-- Indexes for table `votedays`
--
ALTER TABLE `votedays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `yearlevel`
--
ALTER TABLE `yearlevel`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acadyr`
--
ALTER TABLE `acadyr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `allowacad`
--
ALTER TABLE `allowacad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `election`
--
ALTER TABLE `election`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `electionparty`
--
ALTER TABLE `electionparty`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `systemlogs`
--
ALTER TABLE `systemlogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `verified_voters`
--
ALTER TABLE `verified_voters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `allowacad`
--
ALTER TABLE `allowacad`
  ADD CONSTRAINT `allowacad_ibfk_1` FOREIGN KEY (`acadyr_id`) REFERENCES `acadyr` (`id`),
  ADD CONSTRAINT `allowacad_ibfk_2` FOREIGN KEY (`sem_id`) REFERENCES `semesters` (`id`);

--
-- Constraints for table `candidate`
--
ALTER TABLE `candidate`
  ADD CONSTRAINT `candidate_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `register`
--
ALTER TABLE `register`
  ADD CONSTRAINT `register_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `role_ibfk_1` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `role_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `systemlogs`
--
ALTER TABLE `systemlogs`
  ADD CONSTRAINT `systemlogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `verified_voters`
--
ALTER TABLE `verified_voters`
  ADD CONSTRAINT `verified_voters_acadyr_id_foreign` FOREIGN KEY (`acadyr_id`) REFERENCES `acadyr` (`id`),
  ADD CONSTRAINT `verified_voters_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `verified_voters_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `verified_voters_sem_id_foreign` FOREIGN KEY (`sem_id`) REFERENCES `semesters` (`id`),
  ADD CONSTRAINT `verified_voters_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `verified_voters_yrlvl_id_foreign` FOREIGN KEY (`yrlvl_id`) REFERENCES `yearlevel` (`id`);

--
-- Constraints for table `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`register_id`) REFERENCES `register` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
