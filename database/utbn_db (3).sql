-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 09:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `utbn_db`
--
CREATE DATABASE IF NOT EXISTS `utbn_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `utbn_db`;

-- --------------------------------------------------------

--
-- Table structure for table `api_rate_limits`
--

DROP TABLE IF EXISTS `api_rate_limits`;
CREATE TABLE `api_rate_limits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `window_start` int(10) UNSIGNED NOT NULL,
  `count` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_emails`
--

DROP TABLE IF EXISTS `auth_emails`;
CREATE TABLE `auth_emails` (
  `email` varchar(150) NOT NULL,
  `account_type` enum('student','partner') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_emails`
--

INSERT INTO `auth_emails` (`email`, `account_type`, `created_at`) VALUES
('aaaa@gmail.com', 'partner', '2026-02-18 20:02:10'),
('aii@gmail.com', 'student', '2026-02-19 19:22:35'),
('cs@gmail.com', 'student', '2026-02-20 16:47:42'),
('eeee@gmail.com', 'student', '2026-02-12 17:29:34'),
('fgfdg@gmaul.com', 'student', '2026-02-11 21:48:33'),
('hala@gmail.com', 'student', '2026-02-19 18:39:09'),
('qqq@gmail.com', 'student', '2026-02-12 17:16:51'),
('ruaayousef11@gmail.com', 'student', '2026-02-11 20:40:18'),
('ruaayousef2005@gmail.com', 'student', '2026-02-08 15:22:25'),
('test1770411120405@test.com', 'partner', '2026-02-06 20:52:00'),
('yousefhedaya00@gmail.com', 'student', '2026-02-07 12:44:33'),
('yousefruaa2025@gmail.com', 'partner', '2026-02-06 21:19:20'),
('yousefruaa2026@gmail.com', 'student', '2026-02-06 21:16:30'),
('yousefruaa555@gmail.com', 'partner', '2026-02-11 20:59:53'),
('yousefruaa55@gmail.com', 'student', '2026-02-11 20:58:11'),
('yousefsamer55@gmail.com', 'partner', '2026-02-06 21:17:38'),
('yousefyasin694@gmail.com', 'partner', '2026-02-06 21:14:52');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(190) NOT NULL,
  `issued_at` datetime NOT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `major_name` varchar(120) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `user_id`, `title`, `issued_at`, `student_name`, `major_name`, `token`, `pdf_path`, `created_at`) VALUES
(1, 2, 'UTBN - شهادة إتمام المرحلة الأولى', '2026-02-11 18:40:03', 'yousef samer yasin', 'ai', '38ea73f170e25a699957540625e11e02', 'uploads/certificates/certificate-1.pdf', '2026-02-11 17:40:03');

-- --------------------------------------------------------

--
-- Table structure for table `code_rewards`
--

DROP TABLE IF EXISTS `code_rewards`;
CREATE TABLE `code_rewards` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `problem_id` int(10) UNSIGNED NOT NULL,
  `score` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `coin_awarded` int(11) NOT NULL DEFAULT 0,
  `feedback_json` mediumtext DEFAULT NULL,
  `rewarded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `code_rewards`
--

INSERT INTO `code_rewards` (`user_id`, `problem_id`, `score`, `coin_awarded`, `feedback_json`, `rewarded_at`) VALUES
(2, 0, 0.0000, 990, NULL, '2026-02-11 17:21:53'),
(2, 6, 1.0000, 200, '{\"reason\":\"حل الطالب مطابق تمامًا للحل المرجعي. ينتج نفس المخرجات تمامًا لجميع المدخلات الممكنة.\",\"highlights\":[\"نفس المنطق لطباعة المسافات والنجوم.\",\"نفس حدود الحلقات التكرارية.\",\"نفس تحسينات الإدخال\\/الإخراج القياسية (ios::sync_with_stdio(false), cin.tie(nullptr)).\",\"سلوك متطابق.\"],\"issues\":[]}', '2026-02-15 19:49:05'),
(4, 2, 1.0000, 50, '{\"reason\":\"الحل متطابق تمامًا مع الحل المرجعي وينتج نفس السلوك الصحيح.\",\"highlights\":[\"استخدام صحيح للحلقات المتداخلة لرسم المثلث.\",\"معالجة صحيحة للمدخلات والمخرجات.\",\"طباعة النجوم والأسطر الجديدة بشكل صحيح.\"],\"issues\":[]}', '2026-02-11 16:23:13'),
(4, 3, 1.0000, 50, '{\"reason\":\"الحل متطابق سلوكيًا مع الحل المرجعي وينتج نفس المخرجات تمامًا.\",\"highlights\":[\"بنية الحلقات المتداخلة صحيحة لرسم المثلث.\",\"معالجة إدخال المستخدم سليمة.\",\"الإخراج يطابق المطلوب تمامًا.\"],\"issues\":[]}', '2026-02-11 14:22:22');

-- --------------------------------------------------------

--
-- Table structure for table `coins_ledger`
--

DROP TABLE IF EXISTS `coins_ledger`;
CREATE TABLE `coins_ledger` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `reason` varchar(50) NOT NULL,
  `ref_type` varchar(30) NOT NULL,
  `ref_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'Data Structures', 'Test course', '2026-02-06 13:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `course_submissions`
--

DROP TABLE IF EXISTS `course_submissions`;
CREATE TABLE `course_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_mime` varchar(120) DEFAULT NULL,
  `file_size` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_submissions`
--

INSERT INTO `course_submissions` (`id`, `user_id`, `course_title`, `description`, `file_path`, `file_mime`, `file_size`, `created_at`) VALUES
(1, 2, 'data structure', 'how to learn data structure', 'uploads/course_submissions/u2_20260220_212729_75c3b694883d_AI_Water_System_Edited_-_Zaid_Al-Shobaki__1_.pdf', 'application/pdf', 365590, '2026-02-20 23:27:29'),
(2, 2, 'deep learning', 'how to learn deep using ai', 'uploads/course_submissions/u2_20260220_213146_342e4708a283_Draft1_pdf.pdf', 'application/pdf', 116099, '2026-02-20 23:31:46');

-- --------------------------------------------------------

--
-- Table structure for table `major_courses`
--

DROP TABLE IF EXISTS `major_courses`;
CREATE TABLE `major_courses` (
  `id` int(11) NOT NULL,
  `major_text` varchar(200) NOT NULL,
  `course_name` varchar(200) NOT NULL,
  `training_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

DROP TABLE IF EXISTS `partners`;
CREATE TABLE `partners` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `partner_type` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `company_name`, `email`, `partner_type`, `password_hash`, `phone`, `created_at`) VALUES
(1, 'Test Company', 'test1770411120405@test.com', 'company', '$2y$10$bcfEiQhe3wXKBLFVSkGwVuN0Rji4BVVeiZOsSYEuzNMH4uBL3uqMa', '', '2026-02-06 20:52:00'),
(2, 'qoyn', 'yousefyasin694@gmail.com', 'company', '$2y$10$6ZwvxfVeihAuksCsgpQWGOKDLGMyDg92b78jxln.UD4qJ8aEZiBKu', '', '2026-02-06 21:14:52'),
(3, 'qoyn', 'yousefsamer55@gmail.com', 'company', '$2y$10$w3PUk9htvRQi0.hWd03o2OENI9lxZ0IIygTiua9QhRqYInJIESXg2', '', '2026-02-06 21:17:38'),
(4, 'qoyn', 'yousefruaa2025@gmail.com', 'company', '$2y$10$.tpWVKfIaOFeeLh8AlA5Je70rH/prVfaF1kf3G.17iEC4JKzcMQqS', '', '2026-02-06 21:19:20'),
(5, 'yousefruaa(qoyn)', 'yousefruaa555@gmail.com', 'company', '$2y$10$eCvJFRd3x/eznVwdOS6dsuk.AJrksY3UhOGCvR7QO76dLErCyDDUG', '', '2026-02-11 20:59:53'),
(6, 'aaa', 'aaaa@gmail.com', 'company', '$2y$10$OS8DqD1NbaqwWr/WR.62Aey5KOKWiLVv0tCWfxGx5xAGDYQGnR/NS', '', '2026-02-18 20:02:10');

-- --------------------------------------------------------

--
-- Table structure for table `partner_phase2_projects`
--

DROP TABLE IF EXISTS `partner_phase2_projects`;
CREATE TABLE `partner_phase2_projects` (
  `id` int(11) NOT NULL,
  `partner_user_id` int(11) NOT NULL,
  `course_code` varchar(50) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `project_title` varchar(255) NOT NULL,
  `project_description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_phase2_projects`
--

INSERT INTO `partner_phase2_projects` (`id`, `partner_user_id`, `course_code`, `course_name`, `project_title`, `project_description`, `created_at`) VALUES
(1, 58, NULL, 'data structure', 'dfdf', 'dfdf', '2026-02-08 20:50:09'),
(2, 58, NULL, 'بيبي', 'يبيب', 'يبيبق', '2026-02-09 15:58:54');

-- --------------------------------------------------------

--
-- Table structure for table `partner_phase3_projects`
--

DROP TABLE IF EXISTS `partner_phase3_projects`;
CREATE TABLE `partner_phase3_projects` (
  `id` int(11) NOT NULL,
  `partner_user_id` int(11) NOT NULL,
  `capstone_title` varchar(255) NOT NULL,
  `capstone_description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_phase3_projects`
--

INSERT INTO `partner_phase3_projects` (`id`, `partner_user_id`, `capstone_title`, `capstone_description`, `created_at`) VALUES
(1, 58, 'سيسيس', 'يسيسي', '2026-02-08 21:33:37'),
(2, 58, 'يبيبيب', 'يبيبيب', '2026-02-09 15:59:11');

-- --------------------------------------------------------

--
-- Table structure for table `partner_playlists`
--

DROP TABLE IF EXISTS `partner_playlists`;
CREATE TABLE `partner_playlists` (
  `id` int(11) NOT NULL,
  `partner_user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` mediumtext DEFAULT NULL,
  `expected_lectures` int(11) NOT NULL DEFAULT 0,
  `difficulty` int(11) NOT NULL DEFAULT 0,
  `coin_pool` int(11) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `major_text` varchar(220) DEFAULT NULL,
  `course_name` varchar(220) DEFAULT NULL,
  `cover_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_playlists`
--

INSERT INTO `partner_playlists` (`id`, `partner_user_id`, `name`, `slug`, `created_at`, `description`, `expected_lectures`, `difficulty`, `coin_pool`, `is_published`, `published_at`, `major_text`, `course_name`, `cover_path`) VALUES
(16, 58, 'deep', 'deep', '2026-02-18 17:20:24', 'deep', 1, 95, 380, 1, '2026-02-18 22:53:29', 'ai', 'deep', NULL),
(18, 67, 'machine', 'machine', '2026-02-18 20:04:35', 'machone', 1, 60, 240, 1, '2026-02-18 23:04:55', 'ai', 'macine', NULL),
(19, 58, 'NLP', 'nlp', '2026-02-18 22:10:38', 'learn NLP', 3, 70, 1400, 1, '2026-02-19 01:11:43', 'ai', 'NLP', NULL),
(21, 58, 'java', 'java', '2026-02-18 22:46:12', 'leARN', 1, 90, 1800, 1, '2026-02-19 03:27:16', 'ai', 'java', 'uploads/playlist_covers/download_20260219_181318_a6843467.png'),
(22, 58, 'data', 'data', '2026-02-18 23:45:25', 'data', 1, 20, 400, 1, '2026-02-19 20:01:43', 'ai', 'data', 'uploads/playlist_covers/Screenshot_2026-02-17_193142_20260219_180140_9d4a6f7b.png');

-- --------------------------------------------------------

--
-- Table structure for table `partner_playlist_majors`
--

DROP TABLE IF EXISTS `partner_playlist_majors`;
CREATE TABLE `partner_playlist_majors` (
  `playlist_id` int(11) NOT NULL,
  `major_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_videos`
--

DROP TABLE IF EXISTS `partner_videos`;
CREATE TABLE `partner_videos` (
  `id` int(11) NOT NULL,
  `partner_user_id` int(11) NOT NULL,
  `playlist_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `original_name` varchar(255) NOT NULL,
  `stored_path` varchar(500) NOT NULL,
  `mime` varchar(100) DEFAULT NULL,
  `size_bytes` bigint(20) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `duration_seconds` int(11) NOT NULL DEFAULT 0,
  `cover_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_videos`
--

INSERT INTO `partner_videos` (`id`, `partner_user_id`, `playlist_id`, `title`, `original_name`, `stored_path`, `mime`, `size_bytes`, `created_at`, `duration_seconds`, `cover_path`) VALUES
(1, 58, 0, '', 'login.mp4', 'uploads/partner_videos/u58/v_20260208_214613_d5dac302c9bf.mp4', 'video/mp4', 14649973, '2026-02-08 20:46:13', 0, NULL),
(2, 58, 0, 'شششششششش', 'login.mp4', 'uploads/partner_videos/u58/v_1770652769_05cd5249.mp4', NULL, 0, '2026-02-09 15:59:29', 0, NULL),
(35, 58, 16, 'شششش', '', 'uploads/videos/TEDX_FINAL_20260218_182344_3a534810.mp4', NULL, 0, '2026-02-18 17:23:44', 0, NULL),
(38, 67, 18, 'dddd', '', 'uploads/videos/TEDX_FINAL_20260218_210446_b3ace23b.mp4', NULL, 0, '2026-02-18 20:04:46', 0, NULL),
(39, 58, 19, 'sssssss', '', 'uploads/videos/TEDX_FINAL_20260218_231047_24c6e3da.mp4', NULL, 0, '2026-02-18 22:10:47', 0, NULL),
(40, 58, 19, 'aaaaa', '', 'uploads/videos/TEDX_FINAL_20260218_231110_ed469869.mp4', NULL, 0, '2026-02-18 22:11:10', 0, NULL),
(41, 58, 19, 'ssss', '', 'uploads/videos/TEDX_FINAL_20260218_231133_c0be9b99.mp4', NULL, 0, '2026-02-18 22:11:33', 0, NULL),
(47, 58, 21, 'aaaaa', '', 'uploads/videos/TEDX_FINAL_20260218_234619_36716583.mp4', NULL, 0, '2026-02-18 22:46:19', 0, NULL),
(48, 58, 22, 'aaaaaaaaa', '', 'uploads/videos/TEDX_FINAL_20260219_004534_a52390d9.mp4', NULL, 0, '2026-02-18 23:45:34', 0, 'uploads/video_covers/video_48_20260219_193711_d096f14cddea.png'),
(49, 58, 22, 'test', '', 'uploads/videos/TEDX_FINAL_20260219_011230_5f0f05ae.mp4', NULL, 0, '2026-02-19 00:12:30', 0, NULL),
(50, 58, 22, 'test1', '', 'uploads/videos/TEDX_FINAL_20260219_012506_a72ca82a.mp4', NULL, 0, '2026-02-19 00:25:06', 0, 'uploads/covers/Screenshot_2026-02-17_192553_50_20260219_012506_7a006d87.png'),
(51, 58, 22, 'سسسس', '', 'uploads/videos/TEDX_FINAL_20260219_012742_a42daa1a.mp4', NULL, 0, '2026-02-19 00:27:42', 0, 'uploads/covers/Screenshot_2026-02-17_193142_51_20260219_012742_22a7c55f.png'),
(52, 58, 22, 'sdssd', '', 'uploads/videos/TEDX_FINAL_20260219_185931_cd3e5483.mp4', NULL, 0, '2026-02-19 17:59:31', 0, 'uploads/covers/______________________________________________________________________________________52_20260219_185931_60f07dda.png');

-- --------------------------------------------------------

--
-- Table structure for table `partner_video_code_problems`
--

DROP TABLE IF EXISTS `partner_video_code_problems`;
CREATE TABLE `partner_video_code_problems` (
  `id` int(10) UNSIGNED NOT NULL,
  `partner_user_id` int(10) UNSIGNED NOT NULL,
  `partner_video_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `prompt` mediumtext NOT NULL,
  `language` varchar(40) NOT NULL DEFAULT 'python',
  `starter_code` mediumtext DEFAULT NULL,
  `solution_code` mediumtext NOT NULL,
  `max_coin` int(11) NOT NULL DEFAULT 50,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_video_quizzes`
--

DROP TABLE IF EXISTS `partner_video_quizzes`;
CREATE TABLE `partner_video_quizzes` (
  `id` int(11) NOT NULL,
  `partner_user_id` int(11) NOT NULL,
  `partner_video_id` int(11) NOT NULL,
  `quiz_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`quiz_json`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_video_quizzes`
--

INSERT INTO `partner_video_quizzes` (`id`, `partner_user_id`, `partner_video_id`, `quiz_json`, `created_at`) VALUES
(1, 58, 1, '[{\"question\":\"رقم\",\"options\":[\"1\",\"2\",\"3\",\"4\"],\"correct\":\"B\",\"explanation\":\"ارقام\"}]', '2026-02-08 20:47:05'),
(2, 58, 2, '[{\"question\":\"شششش\",\"options\":[\"ش\",\"س\",\"ي\",\"ب\"],\"correct\":\"A\",\"explanation\":\"لللل\"}]', '2026-02-09 15:59:43'),
(24, 58, 35, '[{\"question\":\"شششش\",\"options\":[\"ش\",\"س\",\"ب\",\"ي\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-18 17:23:53'),
(26, 67, 38, '[{\"question\":\"eee\",\"options\":[\"ff\",\"gg\",\"hh\",\"jj\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-18 20:04:51'),
(27, 58, 39, '[{\"question\":\"aaa\",\"options\":[\"aaa\",\"sss\",\"ddd\",\"fff\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-18 22:10:54'),
(28, 58, 40, '[{\"question\":\"aaa\",\"options\":[\"aaa\",\"sss\",\"ddd\",\"fff\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-18 22:11:17'),
(29, 58, 41, '[{\"question\":\"aaa\",\"options\":[\"aaa\",\"sss\",\"ddd\",\"fff\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-18 22:11:39'),
(35, 58, 47, '[{\"question\":\"aaa\",\"options\":[\"aa\",\"sss\",\"ddd\",\"fff\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-18 22:46:24'),
(36, 58, 48, '[{\"question\":\"aaaa\",\"options\":[\"aaqa\",\"sss\",\"ddd\",\"fff\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-18 23:45:39'),
(37, 58, 50, '[{\"question\":\"شش\",\"options\":[\"شش\",\"شش\",\"شش\",\"شش\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-19 00:26:28'),
(38, 58, 50, '[{\"question\":\"شش\",\"options\":[\"شش\",\"شش\",\"شش\",\"شش\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-19 00:26:39'),
(39, 58, 51, '[{\"question\":\"ضضض\",\"options\":[\"ضض\",\"شش\",\"ئئ\",\"ءء\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-19 00:27:49'),
(40, 58, 52, '[{\"question\":\"sdsd\",\"options\":[\"sdsd\",\"gdf\",\"hghjg\",\"jhjhj\"],\"correct\":\"A\",\"explanation\":\"\"}]', '2026-02-19 17:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `partner_video_submissions`
--

DROP TABLE IF EXISTS `partner_video_submissions`;
CREATE TABLE `partner_video_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `partner_video_id` int(10) UNSIGNED NOT NULL,
  `student_user_id` int(10) UNSIGNED NOT NULL,
  `answers_json` mediumtext NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_video_submissions`
--

INSERT INTO `partner_video_submissions` (`id`, `partner_video_id`, `student_user_id`, `answers_json`, `score`, `total`, `submitted_at`) VALUES
(7, 35, 2, '{\"answers\":[0],\"detail\":[{\"q\":\"شششش\",\"chosen\":0,\"correct\":0}]}', 1, 1, '2026-02-18 17:45:29');

-- --------------------------------------------------------

--
-- Table structure for table `plan_analysis`
--

DROP TABLE IF EXISTS `plan_analysis`;
CREATE TABLE `plan_analysis` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `source_attachment_id` int(11) DEFAULT NULL,
  `analysis_json` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
CREATE TABLE `rate_limits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(64) NOT NULL,
  `endpoint` varchar(64) NOT NULL,
  `window_start` int(10) UNSIGNED NOT NULL,
  `hits` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_attachments`
--

DROP TABLE IF EXISTS `student_attachments`;
CREATE TABLE `student_attachments` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('plan','experience') NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_attachments`
--

INSERT INTO `student_attachments` (`id`, `user_id`, `type`, `title`, `file_path`, `original_name`, `mime_type`, `file_size`, `created_at`) VALUES
(4, 2, 'plan', 'خطه', 'uploads/plan_2_1770056175_881417d8.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 18:16:15'),
(5, 2, 'experience', '', 'uploads/experience_2_1770056798_ab8679cd.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 18:26:38'),
(6, 2, 'plan', '', 'uploads/plan_2_1770058046_60edbaf7.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 18:47:26'),
(7, 2, 'plan', '', 'uploads/plan_2_1770058067_4cd6bcec.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 18:47:47'),
(8, 2, 'plan', '', 'uploads/plan_2_1770058085_e5934bb7.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 18:48:05'),
(9, 2, 'plan', '', 'uploads/plan_2_1770058776_0528bcfe.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 18:59:36'),
(10, 2, 'plan', '', 'uploads/plan_2_1770058964_886240dc.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 19:02:44'),
(11, 2, 'plan', '', 'uploads/plan_2_1770059653_bacdaa78.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 19:14:13'),
(12, 2, 'plan', '', 'uploads/plan_2_1770059727_2105a1af.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 19:15:27'),
(13, 2, 'plan', '', 'uploads/plan_2_1770059747_33f2082b.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 19:15:47'),
(14, 2, 'plan', '', 'uploads/plan_2_1770059778_4b0b9d4d.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 19:16:18'),
(15, 2, 'plan', '', 'uploads/plan_2_1770059937_82d75126.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 19:18:57'),
(16, 2, 'plan', '', 'uploads/plan_2_1770060761_5f783b07.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 19:32:41'),
(17, 2, 'plan', '', 'uploads/plan_2_1770060972_526fdd07.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 19:36:12'),
(18, 2, 'plan', '', 'uploads/plan_2_1770064648_4231761e.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-02 20:37:28'),
(19, 2, 'plan', '', 'uploads/plan_2_1770122553_4fb7ec29.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-03 12:42:33'),
(20, 2, 'plan', '', 'uploads/plan_2_1770124642_0a7b9c61.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-03 13:17:22'),
(21, 2, 'plan', '', 'uploads/plan_2_1770124712_3b7d70de.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-03 13:18:32'),
(22, 2, 'plan', '', 'uploads/plan_2_1770126707_9ee54419.pdf', 'خطهه.pdf', 'application/pdf', 102716, '2026-02-03 13:51:47'),
(23, 2, 'plan', '', 'uploads/plan_2_1770127060_a29ae5ec.pdf', 'خطهه.pdf', 'application/pdf', 102716, '2026-02-03 13:57:40'),
(24, 2, 'plan', '', 'uploads/plan_2_1770127669_33f67a97.pdf', 'خطهه.pdf', 'application/pdf', 102716, '2026-02-03 14:07:49'),
(25, 2, 'plan', '', 'uploads/plan_2_1770127996_441cf70f.pdf', 'خطهه.pdf', 'application/pdf', 102716, '2026-02-03 14:13:16'),
(26, 2, 'plan', '', 'uploads/plan_2_1770128227_fee6d213.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-03 14:17:07'),
(27, 2, 'plan', '', 'uploads/plan_2_1770129337_04cef1f8.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-03 14:35:37'),
(28, 2, 'plan', '', 'uploads/plan_2_1770130071_87c6cea1.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-03 14:47:51'),
(29, 2, 'plan', '', 'uploads/plan_2_1770130631_68073a93.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-03 14:57:11'),
(30, 2, 'plan', '', 'uploads/plan_2_1770131261_27665306.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-03 15:07:41'),
(31, 4, 'plan', '', 'uploads/plan_4_1770297068_9c7cce1f.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-05 13:11:08'),
(32, 4, 'plan', '', 'uploads/plan_4_1770302250_6bb8729d.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-05 14:37:30'),
(33, 4, 'plan', '', 'uploads/plan_4_1770302975_44adb942.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-05 14:49:35'),
(34, 4, 'plan', '', 'uploads/plan_4_1770303384_a1cabc27.pdf', 'خطه.pdf', 'application/pdf', 103232, '2026-02-05 14:56:24'),
(35, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770304493_5fcde3bd.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:14:53'),
(36, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770304518_915fa5c7.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:15:18'),
(37, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770304541_70e7c21c.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:15:41'),
(38, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770304561_d59bd461.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:16:01'),
(39, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770304707_6428ae80.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:18:27'),
(40, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770304928_55a2db39.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:22:08'),
(41, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770304944_490b0743.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:22:24'),
(42, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770305227_153d6177.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:27:07'),
(43, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770305658_3b647a5c.png', 'خطه 3.png', 'image/png', 23517, '2026-02-05 15:34:18'),
(44, 4, 'plan', 'خطة (صفحة 2)', 'uploads/plan_4_1770305658_f329fcab.png', 'خطه2.png', 'image/png', 88764, '2026-02-05 15:34:18'),
(45, 4, 'plan', 'خطة (صفحة 3)', 'uploads/plan_4_1770305658_13166b40.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 15:34:18'),
(46, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770307616_d63af4df.png', 'خطه 3.png', 'image/png', 23517, '2026-02-05 16:06:56'),
(47, 4, 'plan', 'خطة (صفحة 2)', 'uploads/plan_4_1770307616_da38a51e.png', 'خطه2.png', 'image/png', 88764, '2026-02-05 16:06:56'),
(48, 4, 'plan', 'خطة (صفحة 3)', 'uploads/plan_4_1770307616_5e302d05.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 16:06:56'),
(49, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770308100_c0dfa7e5.png', 'خطه 3.png', 'image/png', 23517, '2026-02-05 16:15:00'),
(50, 4, 'plan', 'خطة (صفحة 2)', 'uploads/plan_4_1770308100_d12fe6dd.png', 'خطه2.png', 'image/png', 88764, '2026-02-05 16:15:00'),
(51, 4, 'plan', 'خطة (صفحة 3)', 'uploads/plan_4_1770308100_0c146225.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-05 16:15:00'),
(52, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770389977_ed60d4fb.png', 'خطه 3.png', 'image/png', 23517, '2026-02-06 14:59:37'),
(53, 4, 'plan', 'خطة (صفحة 2)', 'uploads/plan_4_1770389977_ec64d849.png', 'خطه2.png', 'image/png', 88764, '2026-02-06 14:59:37'),
(54, 4, 'plan', 'خطة (صفحة 3)', 'uploads/plan_4_1770389977_9377eeb6.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-06 14:59:37'),
(55, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770390033_99230697.png', 'خطه 3.png', 'image/png', 23517, '2026-02-06 15:00:33'),
(56, 4, 'plan', 'خطة (صفحة 2)', 'uploads/plan_4_1770390033_de3ae374.png', 'خطه2.png', 'image/png', 88764, '2026-02-06 15:00:33'),
(57, 4, 'plan', 'خطة (صفحة 3)', 'uploads/plan_4_1770390033_8ae13d5b.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-06 15:00:33'),
(58, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770392337_55dd3584.png', 'خطه 3.png', 'image/png', 23517, '2026-02-06 15:38:57'),
(59, 4, 'plan', 'خطة (صفحة 2)', 'uploads/plan_4_1770392337_64e3fc31.png', 'خطه2.png', 'image/png', 88764, '2026-02-06 15:38:57'),
(60, 4, 'plan', 'خطة (صفحة 3)', 'uploads/plan_4_1770392337_725e9141.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-06 15:38:57'),
(61, 4, 'plan', 'خطة (صفحة 1)', 'uploads/plan_4_1770392713_2ec655dd.png', 'ورقه 1.png', 'image/png', 110991, '2026-02-06 15:45:13');

-- --------------------------------------------------------

--
-- Table structure for table `student_performance`
--

DROP TABLE IF EXISTS `student_performance`;
CREATE TABLE `student_performance` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `major_id` int(10) UNSIGNED DEFAULT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `training_id` int(10) UNSIGNED DEFAULT NULL,
  `video_id` varchar(32) NOT NULL,
  `quiz_type` enum('quick','deep') NOT NULL DEFAULT 'quick',
  `attempt_no` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `score` int(11) NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL DEFAULT 0,
  `quiz_id` int(10) UNSIGNED DEFAULT NULL,
  `watched_seconds` int(11) NOT NULL DEFAULT 0,
  `duration_seconds` int(11) NOT NULL DEFAULT 0,
  `watched_percent` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `time_spent_seconds` int(11) NOT NULL DEFAULT 0,
  `quiz_correct` int(11) NOT NULL DEFAULT 0,
  `quiz_total` int(11) NOT NULL DEFAULT 0,
  `score_percent` int(11) NOT NULL DEFAULT 0,
  `difficulty` tinyint(4) NOT NULL DEFAULT 3,
  `meta_json` mediumtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_performance`
--

INSERT INTO `student_performance` (`id`, `user_id`, `major_id`, `course_id`, `training_id`, `video_id`, `quiz_type`, `attempt_no`, `score`, `total`, `quiz_id`, `watched_seconds`, `duration_seconds`, `watched_percent`, `time_spent_seconds`, `quiz_correct`, `quiz_total`, `score_percent`, `difficulty`, `meta_json`, `created_at`) VALUES
(1, 2, NULL, NULL, NULL, '0', 'quick', 1, 0, 0, NULL, 0, 0, 0.0000, 0, 3, 10, 30, 3, NULL, '2026-02-12 22:48:09'),
(2, 2, NULL, NULL, NULL, '61', 'quick', 1, 0, 0, NULL, 0, 0, 0.0000, 0, 3, 10, 30, 3, NULL, '2026-02-12 22:55:01'),
(3, 2, NULL, NULL, NULL, '0', 'quick', 1, 0, 0, NULL, 0, 0, 0.0000, 0, 2, 10, 20, 3, NULL, '2026-02-12 22:55:19'),
(4, 2, NULL, NULL, NULL, '0', 'quick', 1, 0, 0, NULL, 0, 0, 0.0000, 0, 4, 10, 40, 3, NULL, '2026-02-12 22:56:15'),
(5, 2, NULL, NULL, NULL, '0', 'quick', 1, 0, 0, NULL, 0, 0, 0.0000, 0, 2, 10, 20, 3, NULL, '2026-02-12 22:56:38'),
(6, 2, NULL, NULL, NULL, 'CxGTRLkXUYg', 'deep', 1, 3, 10, NULL, 0, 0, 0.9449, 0, 0, 0, 30, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-13 07:35:58'),
(7, 2, NULL, NULL, NULL, 'B1z0FPFo24A', 'deep', 1, 2, 10, NULL, 0, 0, 0.0035, 0, 0, 0, 20, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-13 21:26:59'),
(8, 2, NULL, NULL, NULL, 'qn5fHGXt1Gw', 'deep', 1, 2, 10, NULL, 0, 0, 0.6953, 0, 0, 0, 20, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-15 22:19:42'),
(9, 2, NULL, NULL, NULL, 'KT6ASzh_JXs', 'deep', 1, 10, 10, NULL, 0, 0, 0.0000, 0, 0, 0, 100, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-15 22:21:25'),
(10, 2, NULL, NULL, NULL, 'HMoH6b7Qu8A', 'deep', 1, 3, 10, NULL, 0, 0, 0.0000, 0, 0, 0, 30, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-15 22:29:23'),
(11, 2, NULL, NULL, NULL, 'SaqjDiZJosk', 'deep', 1, 6, 10, NULL, 0, 0, 0.9892, 0, 0, 0, 60, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-15 22:31:06'),
(12, 2, NULL, NULL, NULL, '16', 'quick', 0, 0, 0, NULL, 0, 0, 0.0000, 0, 0, 0, 0, 1, '{\"source\":\"student_video_progress_save\",\"kind\":\"watch_only\"}', '2026-02-15 22:35:32'),
(13, 2, NULL, NULL, NULL, 'WWn8tOHjZbw', 'deep', 1, 9, 10, NULL, 0, 0, 0.9774, 0, 0, 0, 90, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-15 22:42:10'),
(14, 2, NULL, NULL, NULL, 'H5WUwwivEaI', 'deep', 1, 10, 10, NULL, 0, 0, 0.9278, 0, 0, 0, 100, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-15 22:42:54'),
(15, 2, NULL, NULL, NULL, '98LuqEZlZis', 'deep', 1, 10, 10, NULL, 0, 0, 0.7640, 0, 0, 0, 100, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-15 22:43:59'),
(16, 2, NULL, NULL, NULL, '19', 'quick', 0, 0, 0, NULL, 0, 0, 0.0000, 0, 0, 0, 0, 1, '{\"source\":\"student_video_progress_save\",\"kind\":\"watch_only\"}', '2026-02-15 22:48:55'),
(17, 2, NULL, NULL, NULL, '18', 'quick', 0, 0, 0, NULL, 0, 0, 0.0000, 0, 0, 0, 0, 1, '{\"source\":\"student_video_progress_save\",\"kind\":\"watch_only\"}', '2026-02-15 23:13:01'),
(18, 2, NULL, NULL, NULL, 'h3VCQjyaLws', 'deep', 1, 3, 10, NULL, 0, 0, 0.9384, 0, 0, 0, 30, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-16 01:19:11'),
(19, 2, NULL, NULL, NULL, 'McifeJjrvpI', 'deep', 1, 6, 10, NULL, 0, 0, 0.0000, 0, 0, 0, 60, 4, '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', '2026-02-16 01:54:43'),
(20, 2, NULL, NULL, NULL, '35', 'quick', 0, 0, 0, NULL, 0, 0, 0.8171, 0, 0, 0, 0, 1, '{\"source\":\"student_video_progress_save\",\"kind\":\"watch_only\"}', '2026-02-18 20:45:24'),
(21, 2, NULL, NULL, NULL, '35', 'quick', 2, 1, 1, NULL, 0, 0, 0.0000, 10, 0, 0, 100, 3, '{\"source\":\"partner_video_submit\",\"answers_count\":1,\"detail\":[{\"q\":\"شششش\",\"chosen\":0,\"correct\":0}],\"coin_pool\":380,\"coins_per_video\":380,\"coins_awarded\":380}', '2026-02-18 20:45:29');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

DROP TABLE IF EXISTS `student_profiles`;
CREATE TABLE `student_profiles` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `major_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `level` int(11) NOT NULL DEFAULT 1,
  `coins_total` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`user_id`, `major_id`, `level`, `coins_total`) VALUES
(2, 1, 1, 13980),
(4, 1, 1, 120),
(60, 1, 1, 0),
(61, 1, 1, 0),
(62, 1, 1, 0),
(64, 1, 1, 0),
(65, 1, 1, 0),
(66, 1, 1, 0),
(68, 1, 1, 0),
(69, 1, 1, 320),
(70, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_video_progress`
--

DROP TABLE IF EXISTS `student_video_progress`;
CREATE TABLE `student_video_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `watched_seconds` int(11) NOT NULL DEFAULT 0,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_video_progress`
--

INSERT INTO `student_video_progress` (`id`, `user_id`, `video_id`, `watched_seconds`, `completed`, `updated_at`) VALUES
(30, 2, 35, 67, 1, '2026-02-18 17:45:24');

-- --------------------------------------------------------

--
-- Table structure for table `study_plans`
--

DROP TABLE IF EXISTS `study_plans`;
CREATE TABLE `study_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_key` varchar(50) NOT NULL,
  `plan_name` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_plans`
--

INSERT INTO `study_plans` (`id`, `plan_key`, `plan_name`, `created_at`) VALUES
(1, 'AI_DS', 'علم البيانات والذكاء الاصطناعي', '2026-02-06 15:59:03'),
(2, 'CS', 'علم الحاسوب', '2026-02-11 20:34:43'),
(3, 'SE', 'Software Engineering', '2026-02-12 17:13:11'),
(5, 'CYBER', 'Cyber Security', '2026-02-12 17:27:38');

-- --------------------------------------------------------

--
-- Table structure for table `study_plan_courses`
--

DROP TABLE IF EXISTS `study_plan_courses`;
CREATE TABLE `study_plan_courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `is_required` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_plan_courses`
--

INSERT INTO `study_plan_courses` (`id`, `plan_id`, `course_name`, `is_required`) VALUES
(140, 2, 'تحليل عددي (1)', 1),
(141, 2, 'فيزياء عامة (1)', 1),
(142, 2, 'مختبر فيزياء عامة (1)', 1),
(143, 2, 'تحليل وتصميم النظم', 1),
(144, 2, 'أساسيات هندسة البرمجيات', 1),
(145, 2, 'برمجة تطبيقات الانترنت', 1),
(146, 2, 'تصميم المنطق الرقمي', 1),
(147, 2, 'مبادئ الاحصاء والاحتمالات', 1),
(148, 2, 'برمجة متقدمة', 1),
(149, 2, 'الذكاء الاصطناعي', 1),
(150, 2, 'رياضيات متقطعة', 1),
(151, 2, 'مهارات الاتصال والكتابة', 1),
(152, 2, 'اساسيات تكنولوجيا المعلومات', 1),
(153, 2, 'البرمجة الكينونية', 1),
(154, 2, 'مختبر البرمجة الكينونية', 1),
(155, 2, 'تحليل وتصميم الخوارزميات', 1),
(156, 2, 'نظم التشغيل', 1),
(157, 2, 'أمن المعلومات', 1),
(158, 2, 'تنظيم الحاسوب ومعماريته', 1),
(159, 2, 'برمجة مرئية', 1),
(160, 2, 'شبكات الحاسوب', 1),
(161, 2, 'نظرية الحسابات', 1),
(162, 2, 'تصميم المترجمات', 1),
(163, 2, 'اخلاقيات الحاسوب', 1),
(164, 2, 'النظم الحاسوبية المتوازية و الموزعة', 1),
(165, 2, 'التدريب الميداني', 1),
(166, 2, 'مشروع تخرج تطبيقي (1)', 1),
(167, 2, 'مشروع تخرج تطبيقي (2)', 1),
(168, 2, 'تصميم مواقع الكترونية', 1),
(169, 2, 'جبر خطي (1)', 1),
(170, 2, 'تراكيب البيانات', 1),
(171, 2, 'مختبر تراكيب البيانات', 1),
(172, 2, 'قواعد البيانات', 1),
(173, 2, 'مختبر قواعد البيانات', 1),
(174, 2, 'مقدمة في البرمجة', 1),
(175, 2, 'مختبر مقدمة في البرمجة', 1),
(176, 2, 'تفاضل وتكامل (1)', 1),
(213, 1, 'تصميم مواقع إلكترونية', 1),
(214, 1, 'جبر خطي (1)', 1),
(215, 1, 'تراكيب البيانات', 1),
(216, 1, 'مختبر تراكيب البيانات', 1),
(217, 1, 'قواعد البيانات', 1),
(218, 1, 'مختبر قواعد البيانات', 1),
(219, 1, 'مقدمة في البرمجة', 1),
(220, 1, 'مختبر مقدمة في البرمجة', 1),
(221, 1, 'مبادئ الإحصاء والاحتمالات', 1),
(222, 1, 'أساسيات علم البيانات', 1),
(223, 1, 'أمن البيانات', 1),
(224, 1, 'برمجة متقدمة', 1),
(225, 1, 'برمجة علم البيانات والذكاء الاصطناعي', 1),
(226, 1, 'الذكاء الاصطناعي', 1),
(227, 1, 'التعلم الآلي', 1),
(228, 1, 'التعلم العميق', 1),
(229, 1, 'الروبوتات المتنقلة الذكية', 1),
(230, 1, 'هندسة البيانات وتحليلها', 1),
(231, 1, 'تمثيل واستدلال المعرفة', 1),
(232, 1, 'معالجة اللغات الطبيعية وتطبيقاتها', 1),
(233, 1, 'البيانات الكبيرة والمفتوحة', 1),
(234, 1, 'البيانات المرئية', 1),
(235, 1, 'التنقيب عن البيانات', 1),
(236, 1, 'التدريب الميداني', 1),
(237, 1, 'مشروع تطبيقي (1)', 1),
(238, 1, 'مشروع تخرج تطبيقي (2)', 1),
(239, 1, 'رياضيات متقطعة', 1),
(240, 1, 'مهارات الاتصال والكتابة', 1),
(241, 1, 'أساسيات تكنولوجيا المعلومات', 1),
(242, 1, 'البرمجة الكينونية', 1),
(243, 1, 'مختبر البرمجة الكينونية', 1),
(244, 1, 'تحليل وتصميم الخوارزميات', 1),
(245, 1, 'نظم التشغيل', 1),
(246, 1, 'أخلاقيات علم البيانات والذكاء الاصطناعي', 1),
(247, 1, 'الطرق الإحصائية', 1),
(248, 1, 'تفاضل وتكامل (1)', 1),
(283, 3, 'تراكيب البيانات', 1),
(284, 3, 'أساسيات تكنولوجيا المعلومات', 1),
(285, 3, 'قواعد بيانات', 1),
(286, 3, 'مختبر قواعد البيانات', 1),
(287, 3, 'مهارات الاتصال والكتابة', 1),
(288, 3, 'رياضيات متقطعة', 1),
(289, 3, 'تصميم مواقع إلكترونية', 1),
(290, 3, 'مقدمة في البرمجة', 1),
(291, 3, 'مختبر مقدمة في البرمجة', 1),
(292, 3, 'نظم التشغيل', 1),
(293, 3, 'تحليل وتصميم الخوارزميات', 1),
(294, 3, 'برمجة قواعد البيانات', 1),
(295, 3, 'نظم إدارة قواعد البيانات', 1),
(296, 3, 'برمجة تطبيقات الإنترنت', 1),
(297, 3, 'برمجة مرئية', 1),
(298, 3, 'تصميم المنطق الرقمي', 1),
(299, 3, 'تنظيم الحاسوب ومعماريته', 1),
(300, 3, 'تحليل وتصميم النظم', 1),
(301, 3, 'أساسيات هندسة البرمجيات', 1),
(302, 3, 'هندسة متطلبات البرمجيات', 1),
(303, 3, 'إدارة المشروع البرمجي', 1),
(304, 3, 'مواصفات البرمجيات وتصميمها', 1),
(305, 3, 'معمارية البرمجيات', 1),
(306, 3, 'تفاعل الإنسان مع الحاسوب', 1),
(307, 3, 'أدوات هندسة البرمجيات', 1),
(308, 3, 'تطوير البرمجيات وتوثيقها', 1),
(309, 3, 'فحص البرمجيات وجودتها', 1),
(310, 3, 'التدريب العملي في مجال هندسة البرمجيات', 1),
(311, 3, 'مشروع تخرج تطبيقي (1)', 1),
(312, 3, 'مشروع تخرج تطبيقي (2)', 1),
(313, 3, 'البرمجة الكينونية', 1),
(314, 3, 'مختبر البرمجة الكينونية', 1),
(315, 3, 'شبكات الحاسوب', 1),
(316, 3, 'تحليل عددي (1)', 1),
(317, 5, 'أساسيات تكنولوجيا المعلومات', 1),
(318, 5, 'تصميم مواقع إلكترونية', 1),
(319, 5, 'تفاضل وتكامل (1)', 1),
(320, 5, 'رياضيات متقطعة', 1),
(321, 5, 'نظم التشغيل', 1),
(322, 5, 'تحليل وتصميم الخوارزميات', 1),
(323, 5, 'اللغة الإنجليزية لتكنولوجيا المعلومات', 1),
(324, 5, 'مقدمة في البرمجة', 1),
(325, 5, 'مختبر مقدمة في البرمجة', 1),
(326, 5, 'البرمجة الكينونية', 1),
(327, 5, 'مختبر البرمجة الكينونية', 1),
(328, 5, 'تراكيب البيانات', 1),
(329, 5, 'مختبر تراكيب البيانات', 1),
(330, 5, 'قواعد البيانات', 1),
(331, 5, 'مختبر قواعد البيانات', 1),
(332, 5, 'أمن البيانات والبرمجيات', 1),
(333, 5, 'برمجة تطبيقات الإنترنت', 1),
(334, 5, 'شبكات الحاسوب', 1),
(335, 5, 'مقدمة في الأمن السيبراني', 1),
(336, 5, 'أساسيات التشفير', 1),
(337, 5, 'أمن النظم والبنية التحتية', 1),
(338, 5, 'بروتوكولات أمن المعلومات', 1),
(339, 5, 'إدارة وأمن الشبكات', 1),
(340, 5, 'مراقبة الشبكات وتوثيقها', 1),
(341, 5, 'القرصنة الأخلاقية', 1),
(342, 5, 'اختبار الاختراق', 1),
(343, 5, 'برمجة أمن المعلومات والشبكات', 1),
(344, 5, 'علوم جنائية رقمية', 1),
(345, 5, 'سلامة ومصادقة البيانات', 1),
(346, 5, 'تطوير وتصميم الأنظمة الآمنة', 1),
(347, 5, 'أخلاقيات ومخاطر وسياسات الأمن السيبراني', 1),
(348, 5, 'التدريب الميداني', 1),
(349, 5, 'مشروع تخرج تطبيقي (1)', 1),
(350, 5, 'مشروع تخرج تطبيقي (2)', 1),
(351, 5, 'مبادئ الإحصاء والاحتمالات', 1),
(352, 5, 'جبر خطي (1)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `trainings`
--

DROP TABLE IF EXISTS `trainings`;
CREATE TABLE `trainings` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `coin_reward` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainings`
--

INSERT INTO `trainings` (`id`, `course_id`, `title`, `description`, `coin_reward`, `sort_order`, `created_at`) VALUES
(1, 1, 'Full DS Playlist', 'Complete playlist', 200, 1, '2026-02-06 13:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `training_rewards`
--

DROP TABLE IF EXISTS `training_rewards`;
CREATE TABLE `training_rewards` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `training_id` int(10) UNSIGNED NOT NULL,
  `coins_awarded` int(11) NOT NULL,
  `awarded_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `major_text` varchar(200) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `major_text`, `password_hash`, `role`, `phone`, `created_at`) VALUES
(1, 'Test User', 'test@test.com', NULL, 'TEMP_HASH\r\n', 'student', '0790000000', '2026-01-29 13:45:33'),
(2, 'yousef samer yasin', 'yousefyasin694@gmail.com', 'ai', '$2y$10$dQuKXolbnUTWUdpL6ofikOwIBT/jtBTsp7tlv/6rtcZi1DwTuuAXa', 'student', '0796047615', '2026-01-29 15:20:24'),
(3, 'leyo team', 'leyoteamyole@gmail.com', NULL, '$2y$10$6zHVgy6fOXXC/4Pt4btiBO0l.d/W0AmVNHp5G90MmxCNaSv3SrNse', 'student', '0796047615', '2026-01-29 15:26:17'),
(4, 'Ruaa RAbah Hussin', 'roaarabah27@gmail.com', 'علم بيانات', '$2y$10$yY/I5chIco2XwRTlPSlN0e9hTwNX94mDCGpKxmC/DwO.BqmvsbjJW', 'student', '0786013783', '2026-01-29 15:44:46'),
(5, 'yousef', 'yousefsamer08@gmail.com', NULL, '$2y$10$bbTSqPiiM7794CaFxerrqe5OaaEVm5aBA841xYYcy.CUHJ.Vr0tr2', 'student', 'yousefsamer08@gmail.com', '2026-02-02 15:40:06'),
(8, 'yousef', 'qoun@gmail.com', NULL, '$2y$10$763SJ9UCS8pis.FSzpayK.frZXdkEyb/NfWqERcMchFOvu/6kOPEO', 'student', '', '2026-02-06 17:21:13'),
(9, 'ruaa', 'ruaa2005@gmail.com', NULL, '$2y$10$ihvcenfspgukGg0cOq8ZdeFZKIFQ60/OMEztfFKIF95I6LuLt6qEK', 'student', '', '2026-02-06 17:21:58'),
(55, 'yousefruaa', 'yousefruaa2026@gmail.com', NULL, '$2y$10$g7blMlxsphLknW45JADrx.jC5V8zPCi5cVkwa1cK.clf/F.7YBck.', 'student', '', '2026-02-06 21:16:30'),
(56, 'Test Company', 'test1770411120405@test.com', NULL, '$2y$10$bcfEiQhe3wXKBLFVSkGwVuN0Rji4BVVeiZOsSYEuzNMH4uBL3uqMa', 'partner', '', '2026-02-06 21:23:15'),
(57, 'qoyn', 'yousefsamer55@gmail.com', NULL, '$2y$10$w3PUk9htvRQi0.hWd03o2OENI9lxZ0IIygTiua9QhRqYInJIESXg2', 'partner', '', '2026-02-06 21:23:15'),
(58, 'qoyn', 'yousefruaa2025@gmail.com', NULL, '$2y$10$.tpWVKfIaOFeeLh8AlA5Je70rH/prVfaF1kf3G.17iEC4JKzcMQqS', 'partner', '', '2026-02-06 21:23:15'),
(59, 'hedaya samer mohammad yasin', 'yousefhedaya00@gmail.com', 'علم بيانات', '$2y$10$G9DMSuWWjWspxCZ8RYF05.L9bFythL2/0al5sOdvRe3flig9VzM9a', 'student', '', '2026-02-07 12:44:33'),
(60, 'ruaa', 'ruaayousef2005@gmail.com', NULL, '$2y$10$VojRyLYY5MZ/vvWwr80WIOwfxIFP5DMJgdiB0vqaQ1Emr1plkiw6W', 'student', '', '2026-02-08 15:22:25'),
(61, 'ruaayousef11', 'ruaayousef11@gmail.com', 'cs', '$2y$10$n./FS9GdOa9APIBaofZZaeAlh0JTIFSdC8SVQHQf3VJe6tiN9CId.', 'student', '', '2026-02-11 20:40:18'),
(63, 'yousefruaa(qoyn)', 'yousefruaa555@gmail.com', NULL, '$2y$10$eCvJFRd3x/eznVwdOS6dsuk.AJrksY3UhOGCvR7QO76dLErCyDDUG', 'admin', '', '2026-02-11 20:59:53'),
(64, 'gggggg', 'fgfdg@gmaul.com', 'cs', '$2y$10$mCa9vjWKo87X1dK0zpGfbuhFsdOUw1kc3Wj.yFwmtY5Lu0QgdtL8S', 'student', '', '2026-02-11 21:48:33'),
(65, 'qqq', 'qqq@gmail.com', 'software', '$2y$10$1zUh4yh//uBMWcDu4Bh79OEUjXM9homtqGJ52DJn2V.Dxat4Wgl92', 'student', '', '2026-02-12 17:16:51'),
(66, 'eeee', 'eeee@gmail.com', 'سايبر', '$2y$10$ePSP9yZRFAhFarHnqHqfa.7.DNYwjeXK7bUiYUygEmBGDHYLzZ2le', 'student', '', '2026-02-12 17:29:34'),
(67, 'aaa', 'aaaa@gmail.com', NULL, '$2y$10$OS8DqD1NbaqwWr/WR.62Aey5KOKWiLVv0tCWfxGx5xAGDYQGnR/NS', 'partner', '', '2026-02-18 20:02:10'),
(68, 'hala', 'hala@gmail.com', 'software', '$2y$10$vRnhXZjulxcYCwvenZdSL.TqPTkjRguCyz3W00gETkVG9gpTihRAG', 'student', '', '2026-02-19 18:39:09'),
(69, 'aii', 'aii@gmail.com', 'ai', '$2y$10$Kd8sDwP1IerH54VzQM/fEOqpCpeHO4BKh1mfyF2FTWXKy1wwPgtGG', 'student', '', '2026-02-19 19:22:35'),
(70, 'cs', 'cs@gmail.com', 'cs', '$2y$10$1m951Sdg4VX.4vL2Q.MO3OrVBeKVMiXMx26TuZTv7GfO3hdfeZDHi', 'student', '', '2026-02-20 16:47:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_behavior`
--

DROP TABLE IF EXISTS `user_behavior`;
CREATE TABLE `user_behavior` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `session_id` varchar(64) DEFAULT NULL,
  `event_type` varchar(64) NOT NULL,
  `page` varchar(128) DEFAULT NULL,
  `video_id` varchar(32) DEFAULT NULL,
  `quiz_id` int(10) UNSIGNED DEFAULT NULL,
  `ts` datetime NOT NULL DEFAULT current_timestamp(),
  `meta_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_json`)),
  `user_agent` varchar(255) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `value_float` double DEFAULT NULL,
  `value_int` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_behavior`
--

INSERT INTO `user_behavior` (`id`, `user_id`, `session_id`, `event_type`, `page`, `video_id`, `quiz_id`, `ts`, `meta_json`, `user_agent`, `ip`, `value_float`, `value_int`, `created_at`) VALUES
(1, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:24:00', '{\"watched_ratio\":0.006119951040391677,\"watched_seconds\":5,\"duration_seconds\":817}', NULL, NULL, 0.006119951040391677, NULL, '2026-02-13 04:24:00'),
(2, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:25:00', '{\"watched_ratio\":0.01591187270501836,\"watched_seconds\":13,\"duration_seconds\":817}', NULL, NULL, 0.01591187270501836, NULL, '2026-02-13 04:25:00'),
(3, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:28:14', '{\"watched_ratio\":0.006119951040391677,\"watched_seconds\":5,\"duration_seconds\":817}', NULL, NULL, 0.006119951040391677, NULL, '2026-02-13 04:28:14'),
(4, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:28:19', '{\"watched_ratio\":0.012239902080783354,\"watched_seconds\":10,\"duration_seconds\":817}', NULL, NULL, 0.012239902080783354, NULL, '2026-02-13 04:28:19'),
(5, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:28:24', '{\"watched_ratio\":0.01835985312117503,\"watched_seconds\":15,\"duration_seconds\":817}', NULL, NULL, 0.01835985312117503, NULL, '2026-02-13 04:28:24'),
(6, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:28:29', '{\"watched_ratio\":0.02447980416156671,\"watched_seconds\":20,\"duration_seconds\":817}', NULL, NULL, 0.02447980416156671, NULL, '2026-02-13 04:28:29'),
(7, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:28:34', '{\"watched_ratio\":0.030599755201958383,\"watched_seconds\":25,\"duration_seconds\":817}', NULL, NULL, 0.030599755201958383, NULL, '2026-02-13 04:28:34'),
(8, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:28:39', '{\"watched_ratio\":0.03671970624235006,\"watched_seconds\":30,\"duration_seconds\":817}', NULL, NULL, 0.03671970624235006, NULL, '2026-02-13 04:28:39'),
(9, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:28:44', '{\"watched_ratio\":0.042839657282741736,\"watched_seconds\":35,\"duration_seconds\":817}', NULL, NULL, 0.042839657282741736, NULL, '2026-02-13 04:28:44'),
(10, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:28:49', '{\"watched_ratio\":0.04895960832313342,\"watched_seconds\":40,\"duration_seconds\":817}', NULL, NULL, 0.04895960832313342, NULL, '2026-02-13 04:28:49'),
(11, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:29:54', '{\"watched_ratio\":0.06487148102815178,\"watched_seconds\":53,\"duration_seconds\":817}', NULL, NULL, 0.06487148102815178, NULL, '2026-02-13 04:29:54'),
(12, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:29:59', '{\"watched_ratio\":0.07099143206854346,\"watched_seconds\":58,\"duration_seconds\":817}', NULL, NULL, 0.07099143206854346, NULL, '2026-02-13 04:29:59'),
(13, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:35:58', '{\"watched_ratio\":0.006112469437652812,\"watched_seconds\":5,\"duration_seconds\":818}', NULL, NULL, 0.006112469437652812, NULL, '2026-02-13 04:35:58'),
(14, 2, NULL, 'quiz_submit', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:35:58', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 30, 30, '2026-02-13 04:35:58'),
(15, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:36:03', '{\"watched_ratio\":0.012224938875305624,\"watched_seconds\":10,\"duration_seconds\":818}', NULL, NULL, 0.012224938875305624, NULL, '2026-02-13 04:36:03'),
(16, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:38:19', '{\"watched_ratio\":0.019583843329253364,\"watched_seconds\":16,\"duration_seconds\":817}', NULL, NULL, 0.019583843329253364, NULL, '2026-02-13 04:38:19'),
(17, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:38:24', '{\"watched_ratio\":0.025703794369645042,\"watched_seconds\":21,\"duration_seconds\":817}', NULL, NULL, 0.025703794369645042, NULL, '2026-02-13 04:38:24'),
(18, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:38:29', '{\"watched_ratio\":0.03182374541003672,\"watched_seconds\":26,\"duration_seconds\":817}', NULL, NULL, 0.03182374541003672, NULL, '2026-02-13 04:38:29'),
(19, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:38:34', '{\"watched_ratio\":0.037943696450428395,\"watched_seconds\":31,\"duration_seconds\":817}', NULL, NULL, 0.037943696450428395, NULL, '2026-02-13 04:38:34'),
(20, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:38:39', '{\"watched_ratio\":0.044063647490820076,\"watched_seconds\":36,\"duration_seconds\":817}', NULL, NULL, 0.044063647490820076, NULL, '2026-02-13 04:38:39'),
(21, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:38:44', '{\"watched_ratio\":0.05018359853121175,\"watched_seconds\":41,\"duration_seconds\":817}', NULL, NULL, 0.05018359853121175, NULL, '2026-02-13 04:38:44'),
(22, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:38:53', '{\"watched_ratio\":0.2607099143206854,\"watched_seconds\":213,\"duration_seconds\":817}', NULL, NULL, 0.2607099143206854, NULL, '2026-02-13 04:38:53'),
(23, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:38:58', '{\"watched_ratio\":0.2668298653610771,\"watched_seconds\":218,\"duration_seconds\":817}', NULL, NULL, 0.2668298653610771, NULL, '2026-02-13 04:38:58'),
(24, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:39:06', '{\"watched_ratio\":0.9388004895960832,\"watched_seconds\":767,\"duration_seconds\":817}', NULL, NULL, 0.9388004895960832, NULL, '2026-02-13 04:39:06'),
(25, 2, NULL, 'watch_progress', NULL, 'CxGTRLkXUYg', NULL, '2026-02-13 07:39:11', '{\"watched_ratio\":0.944920440636475,\"watched_seconds\":772,\"duration_seconds\":817}', NULL, NULL, 0.944920440636475, NULL, '2026-02-13 04:39:11'),
(26, 2, NULL, 'watch_progress', NULL, 'B1z0FPFo24A', NULL, '2026-02-13 21:26:56', '{\"watched_ratio\":0.0008801267382503081,\"watched_seconds\":5,\"duration_seconds\":5681}', NULL, NULL, 0.0008801267382503081, NULL, '2026-02-13 18:26:56'),
(27, 2, NULL, 'quiz_submit', NULL, 'B1z0FPFo24A', NULL, '2026-02-13 21:26:59', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 20, 20, '2026-02-13 18:26:59'),
(28, 2, NULL, 'watch_progress', NULL, 'B1z0FPFo24A', NULL, '2026-02-13 21:27:01', '{\"watched_ratio\":0.0017602534765006161,\"watched_seconds\":10,\"duration_seconds\":5681}', NULL, NULL, 0.0017602534765006161, NULL, '2026-02-13 18:27:01'),
(29, 2, NULL, 'watch_progress', NULL, 'B1z0FPFo24A', NULL, '2026-02-13 21:27:06', '{\"watched_ratio\":0.002640380214750924,\"watched_seconds\":15,\"duration_seconds\":5681}', NULL, NULL, 0.002640380214750924, NULL, '2026-02-13 18:27:06'),
(30, 2, NULL, 'watch_progress', NULL, 'B1z0FPFo24A', NULL, '2026-02-13 21:27:11', '{\"watched_ratio\":0.0035205069530012323,\"watched_seconds\":20,\"duration_seconds\":5681}', NULL, NULL, 0.0035205069530012323, NULL, '2026-02-13 18:27:11'),
(31, 2, NULL, 'watch_progress', NULL, 'Au7aD0fSHl4', NULL, '2026-02-13 21:27:44', '{\"watched_ratio\":0.0034674063800277394,\"watched_seconds\":5,\"duration_seconds\":1442}', NULL, NULL, 0.0034674063800277394, NULL, '2026-02-13 18:27:44'),
(32, 2, NULL, 'watch_progress', NULL, 'Au7aD0fSHl4', NULL, '2026-02-13 21:27:49', '{\"watched_ratio\":0.006934812760055479,\"watched_seconds\":10,\"duration_seconds\":1442}', NULL, NULL, 0.006934812760055479, NULL, '2026-02-13 18:27:49'),
(33, 2, NULL, 'watch_progress', NULL, 'Au7aD0fSHl4', NULL, '2026-02-13 21:27:54', '{\"watched_ratio\":0.010402219140083218,\"watched_seconds\":15,\"duration_seconds\":1442}', NULL, NULL, 0.010402219140083218, NULL, '2026-02-13 18:27:54'),
(34, 2, NULL, 'watch_progress', NULL, 'Au7aD0fSHl4', NULL, '2026-02-13 21:27:59', '{\"watched_ratio\":0.013869625520110958,\"watched_seconds\":20,\"duration_seconds\":1442}', NULL, NULL, 0.013869625520110958, NULL, '2026-02-13 18:27:59'),
(35, 2, NULL, 'watch_progress', NULL, 'Au7aD0fSHl4', NULL, '2026-02-13 21:28:09', '{\"watched_ratio\":0.9507628294036061,\"watched_seconds\":1371,\"duration_seconds\":1442}', NULL, NULL, 0.9507628294036061, NULL, '2026-02-13 18:28:09'),
(36, 2, NULL, 'quiz_submit', NULL, 'qn5fHGXt1Gw', NULL, '2026-02-15 22:19:42', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 20, 20, '2026-02-15 19:19:42'),
(37, 2, NULL, 'watch_progress', NULL, 'qn5fHGXt1Gw', NULL, '2026-02-15 22:19:42', '{\"watched_ratio\":0.6953286482017362,\"watched_seconds\":1682,\"duration_seconds\":2419}', NULL, NULL, 0.6953286482017362, NULL, '2026-02-15 19:19:42'),
(38, 2, NULL, 'quiz_submit', NULL, 'KT6ASzh_JXs', NULL, '2026-02-15 22:21:25', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 100, 100, '2026-02-15 19:21:25'),
(39, 2, NULL, 'quiz_submit', NULL, 'HMoH6b7Qu8A', NULL, '2026-02-15 22:29:23', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 30, 30, '2026-02-15 19:29:23'),
(40, 2, NULL, 'watch_progress', NULL, 'SaqjDiZJosk', NULL, '2026-02-15 22:31:04', '{\"watched_ratio\":0.9847122302158273,\"watched_seconds\":1095,\"duration_seconds\":1112}', NULL, NULL, 0.9847122302158273, NULL, '2026-02-15 19:31:04'),
(41, 2, NULL, 'quiz_submit', NULL, 'SaqjDiZJosk', NULL, '2026-02-15 22:31:06', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 60, 60, '2026-02-15 19:31:06'),
(42, 2, NULL, 'watch_progress', NULL, 'SaqjDiZJosk', NULL, '2026-02-15 22:31:09', '{\"watched_ratio\":0.9892086330935251,\"watched_seconds\":1100,\"duration_seconds\":1112}', NULL, NULL, 0.9892086330935251, NULL, '2026-02-15 19:31:09'),
(43, 2, NULL, 'watch_progress', NULL, '16', NULL, '2026-02-15 22:35:32', '{\"watched_seconds\":12,\"duration_seconds\":0,\"completed\":1}', NULL, NULL, 0, 12, '2026-02-15 19:35:32'),
(44, 2, NULL, 'watch_completed', NULL, '16', NULL, '2026-02-15 22:35:32', '{\"watched_seconds\":12,\"duration_seconds\":0,\"completed\":1}', NULL, NULL, 0, 12, '2026-02-15 19:35:32'),
(45, 2, NULL, 'watch_progress', NULL, '16', NULL, '2026-02-15 22:41:16', '{\"watched_seconds\":11,\"duration_seconds\":0,\"completed\":1}', NULL, NULL, 0, 11, '2026-02-15 19:41:16'),
(46, 2, NULL, 'watch_completed', NULL, '16', NULL, '2026-02-15 22:41:16', '{\"watched_seconds\":11,\"duration_seconds\":0,\"completed\":1}', NULL, NULL, 0, 11, '2026-02-15 19:41:16'),
(47, 2, NULL, 'watch_progress', NULL, 'WWn8tOHjZbw', NULL, '2026-02-15 22:42:01', '{\"watched_ratio\":0.9586466165413534,\"watched_seconds\":255,\"duration_seconds\":266}', NULL, NULL, 0.9586466165413534, NULL, '2026-02-15 19:42:01'),
(48, 2, NULL, 'watch_progress', NULL, 'WWn8tOHjZbw', NULL, '2026-02-15 22:42:06', '{\"watched_ratio\":0.9774436090225563,\"watched_seconds\":260,\"duration_seconds\":266}', NULL, NULL, 0.9774436090225563, NULL, '2026-02-15 19:42:06'),
(49, 2, NULL, 'quiz_submit', NULL, 'WWn8tOHjZbw', NULL, '2026-02-15 22:42:10', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 90, 90, '2026-02-15 19:42:10'),
(50, 2, NULL, 'watch_progress', NULL, 'H5WUwwivEaI', NULL, '2026-02-15 22:42:44', '{\"watched_ratio\":0.9252975436819448,\"watched_seconds\":3654,\"duration_seconds\":3949}', NULL, NULL, 0.9252975436819448, NULL, '2026-02-15 19:42:44'),
(51, 2, NULL, 'watch_progress', NULL, 'H5WUwwivEaI', NULL, '2026-02-15 22:42:49', '{\"watched_ratio\":0.9265636870093694,\"watched_seconds\":3659,\"duration_seconds\":3949}', NULL, NULL, 0.9265636870093694, NULL, '2026-02-15 19:42:49'),
(52, 2, NULL, 'quiz_submit', NULL, 'H5WUwwivEaI', NULL, '2026-02-15 22:42:54', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 100, 100, '2026-02-15 19:42:54'),
(53, 2, NULL, 'watch_progress', NULL, 'H5WUwwivEaI', NULL, '2026-02-15 22:42:54', '{\"watched_ratio\":0.9278298303367941,\"watched_seconds\":3664,\"duration_seconds\":3949}', NULL, NULL, 0.9278298303367941, NULL, '2026-02-15 19:42:54'),
(54, 2, NULL, 'watch_progress', NULL, '98LuqEZlZis', NULL, '2026-02-15 22:43:50', '{\"watched_ratio\":0.7329192546583851,\"watched_seconds\":236,\"duration_seconds\":322}', NULL, NULL, 0.7329192546583851, NULL, '2026-02-15 19:43:50'),
(55, 2, NULL, 'watch_progress', NULL, '98LuqEZlZis', NULL, '2026-02-15 22:43:55', '{\"watched_ratio\":0.7484472049689441,\"watched_seconds\":241,\"duration_seconds\":322}', NULL, NULL, 0.7484472049689441, NULL, '2026-02-15 19:43:55'),
(56, 2, NULL, 'quiz_submit', NULL, '98LuqEZlZis', NULL, '2026-02-15 22:43:59', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 100, 100, '2026-02-15 19:43:59'),
(57, 2, NULL, 'watch_progress', NULL, '98LuqEZlZis', NULL, '2026-02-15 22:44:00', '{\"watched_ratio\":0.7639751552795031,\"watched_seconds\":246,\"duration_seconds\":322}', NULL, NULL, 0.7639751552795031, NULL, '2026-02-15 19:44:00'),
(58, 2, NULL, 'watch_progress', NULL, '19', NULL, '2026-02-15 22:48:55', '{\"watched_seconds\":11,\"duration_seconds\":0,\"completed\":1}', NULL, NULL, 0, 11, '2026-02-15 19:48:55'),
(59, 2, NULL, 'watch_completed', NULL, '19', NULL, '2026-02-15 22:48:55', '{\"watched_seconds\":11,\"duration_seconds\":0,\"completed\":1}', NULL, NULL, 0, 11, '2026-02-15 19:48:55'),
(60, 2, NULL, 'watch_progress', NULL, '18', NULL, '2026-02-15 23:13:01', '{\"watched_seconds\":11,\"duration_seconds\":0,\"completed\":1}', NULL, NULL, 0, 11, '2026-02-15 20:13:01'),
(61, 2, NULL, 'watch_completed', NULL, '18', NULL, '2026-02-15 23:13:01', '{\"watched_seconds\":11,\"duration_seconds\":0,\"completed\":1}', NULL, NULL, 0, 11, '2026-02-15 20:13:01'),
(62, 2, NULL, 'watch_progress', NULL, 'h3VCQjyaLws', NULL, '2026-02-16 01:19:10', '{\"watched_ratio\":0.9383886255924171,\"watched_seconds\":396,\"duration_seconds\":422}', NULL, NULL, 0.9383886255924171, NULL, '2026-02-15 22:19:10'),
(63, 2, NULL, 'quiz_submit', NULL, 'h3VCQjyaLws', NULL, '2026-02-16 01:19:11', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 30, 30, '2026-02-15 22:19:11'),
(64, 2, NULL, 'quiz_submit', NULL, 'McifeJjrvpI', NULL, '2026-02-16 01:54:43', '{\"source\":\"video_quiz_submit\",\"quiz_type\":\"deep\"}', NULL, NULL, 60, 60, '2026-02-15 22:54:43'),
(65, 2, NULL, 'watch_progress', NULL, '35', NULL, '2026-02-18 20:45:24', '{\"watched_seconds\":67,\"duration_seconds\":82,\"completed\":1}', NULL, NULL, 0.8170731707317073, 67, '2026-02-18 17:45:24'),
(66, 2, NULL, 'watch_completed', NULL, '35', NULL, '2026-02-18 20:45:24', '{\"watched_seconds\":67,\"duration_seconds\":82,\"completed\":1}', NULL, NULL, 0.8170731707317073, 67, '2026-02-18 17:45:24'),
(67, 2, NULL, 'quiz_submit', NULL, '35', NULL, '2026-02-18 20:45:29', '{\"source\":\"partner_video_submit\",\"answers_count\":1,\"detail\":[{\"q\":\"شششش\",\"chosen\":0,\"correct\":0}],\"coin_pool\":380,\"coins_per_video\":380,\"coins_awarded\":380}', NULL, NULL, 100, 100, '2026-02-18 17:45:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_level_predictions`
--

DROP TABLE IF EXISTS `user_level_predictions`;
CREATE TABLE `user_level_predictions` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `level_label` varchar(32) NOT NULL,
  `phase_ready` tinyint(1) NOT NULL DEFAULT 0,
  `model_version` varchar(32) NOT NULL DEFAULT 'v1',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `phase2_ready` tinyint(1) NOT NULL DEFAULT 0,
  `phase3_ready` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_level_predictions`
--

INSERT INTO `user_level_predictions` (`user_id`, `level_label`, `phase_ready`, `model_version`, `updated_at`, `phase2_ready`, `phase3_ready`) VALUES
(2, 'beginner', 0, 'ml-v1', '2026-02-16 01:54:59', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_plan_courses`
--

DROP TABLE IF EXISTS `user_plan_courses`;
CREATE TABLE `user_plan_courses` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `course_code` varchar(40) NOT NULL,
  `course_name` varchar(220) NOT NULL,
  `group_title` varchar(220) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `credits` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_plan_courses`
--

INSERT INTO `user_plan_courses` (`user_id`, `course_code`, `course_name`, `group_title`, `is_required`, `credits`) VALUES
(2, 'أخلاقيات علم البيانات والذكاء الاصطناعي', 'أخلاقيات علم البيانات والذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(2, 'أساسيات تكنولوجيا المعلومات', 'أساسيات تكنولوجيا المعلومات', 'خطة AI/DS', 1, 0),
(2, 'أساسيات علم البيانات', 'أساسيات علم البيانات', 'خطة AI/DS', 1, 0),
(2, 'أمن البيانات', 'أمن البيانات', 'خطة AI/DS', 1, 0),
(2, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة AI/DS', 1, 0),
(2, 'البيانات الكبيرة والمفتوحة', 'البيانات الكبيرة والمفتوحة', 'خطة AI/DS', 1, 0),
(2, 'البيانات المرئية', 'البيانات المرئية', 'خطة AI/DS', 1, 0),
(2, 'التدريب الميداني', 'التدريب الميداني', 'خطة AI/DS', 1, 0),
(2, 'التعلم الآلي', 'التعلم الآلي', 'خطة AI/DS', 1, 0),
(2, 'التعلم العميق', 'التعلم العميق', 'خطة AI/DS', 1, 0),
(2, 'التنقيب عن البيانات', 'التنقيب عن البيانات', 'خطة AI/DS', 1, 0),
(2, 'الذكاء الاصطناعي', 'الذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(2, 'الروبوتات المتنقلة الذكية', 'الروبوتات المتنقلة الذكية', 'خطة AI/DS', 1, 0),
(2, 'الطرق الإحصائية', 'الطرق الإحصائية', 'خطة AI/DS', 1, 0),
(2, 'برمجة علم البيانات والذكاء الاصطناعي', 'برمجة علم البيانات والذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(2, 'برمجة متقدمة', 'برمجة متقدمة', 'خطة AI/DS', 1, 0),
(2, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة AI/DS', 1, 0),
(2, 'تفاضل وتكامل (1)', 'تفاضل وتكامل (1)', 'خطة AI/DS', 1, 0),
(2, 'تمثيل واستدلال المعرفة', 'تمثيل واستدلال المعرفة', 'خطة AI/DS', 1, 0),
(2, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة AI/DS', 1, 0),
(2, 'مبادئ الإحصاء والاحتمالات', 'مبادئ الإحصاء والاحتمالات', 'خطة AI/DS', 1, 0),
(2, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة AI/DS', 1, 0),
(2, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة AI/DS', 1, 0),
(2, 'مشروع تطبيقي (1)', 'مشروع تطبيقي (1)', 'خطة AI/DS', 1, 0),
(2, 'معالجة اللغات الطبيعية وتطبيقاتها', 'معالجة اللغات الطبيعية وتطبيقاتها', 'خطة AI/DS', 1, 0),
(2, 'مهارات الاتصال والكتابة', 'مهارات الاتصال والكتابة', 'خطة AI/DS', 1, 0),
(2, 'نظم التشغيل', 'نظم التشغيل', 'خطة AI/DS', 1, 0),
(2, 'هندسة البيانات وتحليلها', 'هندسة البيانات وتحليلها', 'خطة AI/DS', 1, 0),
(4, 'أخلاقيات علم البيانات والذكاء الاصطناعي', 'أخلاقيات علم البيانات والذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(4, 'أساسيات تكنولوجيا المعلومات', 'أساسيات تكنولوجيا المعلومات', 'خطة AI/DS', 1, 0),
(4, 'أساسيات علم البيانات', 'أساسيات علم البيانات', 'خطة AI/DS', 1, 0),
(4, 'أمن البيانات', 'أمن البيانات', 'خطة AI/DS', 1, 0),
(4, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة AI/DS', 1, 0),
(4, 'البيانات الكبيرة والمفتوحة', 'البيانات الكبيرة والمفتوحة', 'خطة AI/DS', 1, 0),
(4, 'البيانات المرئية', 'البيانات المرئية', 'خطة AI/DS', 1, 0),
(4, 'التدريب الميداني', 'التدريب الميداني', 'خطة AI/DS', 1, 0),
(4, 'التعلم الآلي', 'التعلم الآلي', 'خطة AI/DS', 1, 0),
(4, 'التعلم العميق', 'التعلم العميق', 'خطة AI/DS', 1, 0),
(4, 'التنقيب عن البيانات', 'التنقيب عن البيانات', 'خطة AI/DS', 1, 0),
(4, 'الذكاء الاصطناعي', 'الذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(4, 'الروبوتات المتنقلة الذكية', 'الروبوتات المتنقلة الذكية', 'خطة AI/DS', 1, 0),
(4, 'الطرق الإحصائية', 'الطرق الإحصائية', 'خطة AI/DS', 1, 0),
(4, 'برمجة علم البيانات والذكاء الاصطناعي', 'برمجة علم البيانات والذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(4, 'برمجة متقدمة', 'برمجة متقدمة', 'خطة AI/DS', 1, 0),
(4, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة AI/DS', 1, 0),
(4, 'تفاضل وتكامل (1)', 'تفاضل وتكامل (1)', 'خطة AI/DS', 1, 0),
(4, 'تمثيل واستدلال المعرفة', 'تمثيل واستدلال المعرفة', 'خطة AI/DS', 1, 0),
(4, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة AI/DS', 1, 0),
(4, 'مبادئ الإحصاء والاحتمالات', 'مبادئ الإحصاء والاحتمالات', 'خطة AI/DS', 1, 0),
(4, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة AI/DS', 1, 0),
(4, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة AI/DS', 1, 0),
(4, 'مشروع تطبيقي (1)', 'مشروع تطبيقي (1)', 'خطة AI/DS', 1, 0),
(4, 'معالجة اللغات الطبيعية وتطبيقاتها', 'معالجة اللغات الطبيعية وتطبيقاتها', 'خطة AI/DS', 1, 0),
(4, 'مهارات الاتصال والكتابة', 'مهارات الاتصال والكتابة', 'خطة AI/DS', 1, 0),
(4, 'نظم التشغيل', 'نظم التشغيل', 'خطة AI/DS', 1, 0),
(4, 'هندسة البيانات وتحليلها', 'هندسة البيانات وتحليلها', 'خطة AI/DS', 1, 0),
(61, 'أساسيات هندسة البرمجيات', 'أساسيات هندسة البرمجيات', 'خطة CS', 1, 0),
(61, 'أمن المعلومات', 'أمن المعلومات', 'خطة CS', 1, 0),
(61, 'اخلاقيات الحاسوب', 'اخلاقيات الحاسوب', 'خطة CS', 1, 0),
(61, 'اساسيات تكنولوجيا المعلومات', 'اساسيات تكنولوجيا المعلومات', 'خطة CS', 1, 0),
(61, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة CS', 1, 0),
(61, 'التدريب الميداني', 'التدريب الميداني', 'خطة CS', 1, 0),
(61, 'الذكاء الاصطناعي', 'الذكاء الاصطناعي', 'خطة CS', 1, 0),
(61, 'النظم الحاسوبية المتوازية و الموزعة', 'النظم الحاسوبية المتوازية و الموزعة', 'خطة CS', 1, 0),
(61, 'برمجة تطبيقات الانترنت', 'برمجة تطبيقات الانترنت', 'خطة CS', 1, 0),
(61, 'برمجة متقدمة', 'برمجة متقدمة', 'خطة CS', 1, 0),
(61, 'برمجة مرئية', 'برمجة مرئية', 'خطة CS', 1, 0),
(61, 'تحليل عددي (1)', 'تحليل عددي (1)', 'خطة CS', 1, 0),
(61, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة CS', 1, 0),
(61, 'تحليل وتصميم النظم', 'تحليل وتصميم النظم', 'خطة CS', 1, 0),
(61, 'تراكيب البيانات', 'تراكيب البيانات', 'خطة CS', 1, 0),
(61, 'تصميم المترجمات', 'تصميم المترجمات', 'خطة CS', 1, 0),
(61, 'تصميم المنطق الرقمي', 'تصميم المنطق الرقمي', 'خطة CS', 1, 0),
(61, 'تصميم مواقع الكترونية', 'تصميم مواقع الكترونية', 'خطة CS', 1, 0),
(61, 'تفاضل وتكامل (1)', 'تفاضل وتكامل (1)', 'خطة CS', 1, 0),
(61, 'تنظيم الحاسوب ومعماريته', 'تنظيم الحاسوب ومعماريته', 'خطة CS', 1, 0),
(61, 'جبر خطي (1)', 'جبر خطي (1)', 'خطة CS', 1, 0),
(61, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة CS', 1, 0),
(61, 'شبكات الحاسوب', 'شبكات الحاسوب', 'خطة CS', 1, 0),
(61, 'فيزياء عامة (1)', 'فيزياء عامة (1)', 'خطة CS', 1, 0),
(61, 'قواعد البيانات', 'قواعد البيانات', 'خطة CS', 1, 0),
(61, 'مبادئ الاحصاء والاحتمالات', 'مبادئ الاحصاء والاحتمالات', 'خطة CS', 1, 0),
(61, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة CS', 1, 0),
(61, 'مختبر تراكيب البيانات', 'مختبر تراكيب البيانات', 'خطة CS', 1, 0),
(61, 'مختبر فيزياء عامة (1)', 'مختبر فيزياء عامة (1)', 'خطة CS', 1, 0),
(61, 'مختبر قواعد البيانات', 'مختبر قواعد البيانات', 'خطة CS', 1, 0),
(61, 'مختبر مقدمة في البرمجة', 'مختبر مقدمة في البرمجة', 'خطة CS', 1, 0),
(61, 'مشروع تخرج تطبيقي (1)', 'مشروع تخرج تطبيقي (1)', 'خطة CS', 1, 0),
(61, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة CS', 1, 0),
(61, 'مقدمة في البرمجة', 'مقدمة في البرمجة', 'خطة CS', 1, 0),
(61, 'مهارات الاتصال والكتابة', 'مهارات الاتصال والكتابة', 'خطة CS', 1, 0),
(61, 'نظرية الحسابات', 'نظرية الحسابات', 'خطة CS', 1, 0),
(61, 'نظم التشغيل', 'نظم التشغيل', 'خطة CS', 1, 0),
(64, 'أساسيات هندسة البرمجيات', 'أساسيات هندسة البرمجيات', 'خطة CS', 1, 0),
(64, 'أمن المعلومات', 'أمن المعلومات', 'خطة CS', 1, 0),
(64, 'اخلاقيات الحاسوب', 'اخلاقيات الحاسوب', 'خطة CS', 1, 0),
(64, 'اساسيات تكنولوجيا المعلومات', 'اساسيات تكنولوجيا المعلومات', 'خطة CS', 1, 0),
(64, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة CS', 1, 0),
(64, 'التدريب الميداني', 'التدريب الميداني', 'خطة CS', 1, 0),
(64, 'الذكاء الاصطناعي', 'الذكاء الاصطناعي', 'خطة CS', 1, 0),
(64, 'النظم الحاسوبية المتوازية و الموزعة', 'النظم الحاسوبية المتوازية و الموزعة', 'خطة CS', 1, 0),
(64, 'برمجة تطبيقات الانترنت', 'برمجة تطبيقات الانترنت', 'خطة CS', 1, 0),
(64, 'برمجة متقدمة', 'برمجة متقدمة', 'خطة CS', 1, 0),
(64, 'برمجة مرئية', 'برمجة مرئية', 'خطة CS', 1, 0),
(64, 'تحليل عددي (1)', 'تحليل عددي (1)', 'خطة CS', 1, 0),
(64, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة CS', 1, 0),
(64, 'تحليل وتصميم النظم', 'تحليل وتصميم النظم', 'خطة CS', 1, 0),
(64, 'تراكيب البيانات', 'تراكيب البيانات', 'خطة CS', 1, 0),
(64, 'تصميم المترجمات', 'تصميم المترجمات', 'خطة CS', 1, 0),
(64, 'تصميم المنطق الرقمي', 'تصميم المنطق الرقمي', 'خطة CS', 1, 0),
(64, 'تصميم مواقع الكترونية', 'تصميم مواقع الكترونية', 'خطة CS', 1, 0),
(64, 'تفاضل وتكامل (1)', 'تفاضل وتكامل (1)', 'خطة CS', 1, 0),
(64, 'تنظيم الحاسوب ومعماريته', 'تنظيم الحاسوب ومعماريته', 'خطة CS', 1, 0),
(64, 'جبر خطي (1)', 'جبر خطي (1)', 'خطة CS', 1, 0),
(64, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة CS', 1, 0),
(64, 'شبكات الحاسوب', 'شبكات الحاسوب', 'خطة CS', 1, 0),
(64, 'فيزياء عامة (1)', 'فيزياء عامة (1)', 'خطة CS', 1, 0),
(64, 'قواعد البيانات', 'قواعد البيانات', 'خطة CS', 1, 0),
(64, 'مبادئ الاحصاء والاحتمالات', 'مبادئ الاحصاء والاحتمالات', 'خطة CS', 1, 0),
(64, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة CS', 1, 0),
(64, 'مختبر تراكيب البيانات', 'مختبر تراكيب البيانات', 'خطة CS', 1, 0),
(64, 'مختبر فيزياء عامة (1)', 'مختبر فيزياء عامة (1)', 'خطة CS', 1, 0),
(64, 'مختبر قواعد البيانات', 'مختبر قواعد البيانات', 'خطة CS', 1, 0),
(64, 'مختبر مقدمة في البرمجة', 'مختبر مقدمة في البرمجة', 'خطة CS', 1, 0),
(64, 'مشروع تخرج تطبيقي (1)', 'مشروع تخرج تطبيقي (1)', 'خطة CS', 1, 0),
(64, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة CS', 1, 0),
(64, 'مقدمة في البرمجة', 'مقدمة في البرمجة', 'خطة CS', 1, 0),
(64, 'مهارات الاتصال والكتابة', 'مهارات الاتصال والكتابة', 'خطة CS', 1, 0),
(64, 'نظرية الحسابات', 'نظرية الحسابات', 'خطة CS', 1, 0),
(64, 'نظم التشغيل', 'نظم التشغيل', 'خطة CS', 1, 0),
(65, 'أدوات هندسة البرمجيات', 'أدوات هندسة البرمجيات', 'خطة SE', 1, 0),
(65, 'أساسيات تكنولوجيا المعلومات', 'أساسيات تكنولوجيا المعلومات', 'خطة SE', 1, 0),
(65, 'أساسيات هندسة البرمجيات', 'أساسيات هندسة البرمجيات', 'خطة SE', 1, 0),
(65, 'إدارة المشروع البرمجي', 'إدارة المشروع البرمجي', 'خطة SE', 1, 0),
(65, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة SE', 1, 0),
(65, 'التدريب العملي في مجال هندسة البرمجيات', 'التدريب العملي في مجال هندسة البرمجيات', 'خطة SE', 1, 0),
(65, 'برمجة تطبيقات الإنترنت', 'برمجة تطبيقات الإنترنت', 'خطة SE', 1, 0),
(65, 'برمجة قواعد البيانات', 'برمجة قواعد البيانات', 'خطة SE', 1, 0),
(65, 'برمجة مرئية', 'برمجة مرئية', 'خطة SE', 1, 0),
(65, 'تحليل عددي (1)', 'تحليل عددي (1)', 'خطة SE', 1, 0),
(65, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة SE', 1, 0),
(65, 'تحليل وتصميم النظم', 'تحليل وتصميم النظم', 'خطة SE', 1, 0),
(65, 'تراكيب البيانات', 'تراكيب البيانات', 'خطة SE', 1, 0),
(65, 'تصميم المنطق الرقمي', 'تصميم المنطق الرقمي', 'خطة SE', 1, 0),
(65, 'تصميم مواقع إلكترونية', 'تصميم مواقع إلكترونية', 'خطة SE', 1, 0),
(65, 'تطوير البرمجيات وتوثيقها', 'تطوير البرمجيات وتوثيقها', 'خطة SE', 1, 0),
(65, 'تفاعل الإنسان مع الحاسوب', 'تفاعل الإنسان مع الحاسوب', 'خطة SE', 1, 0),
(65, 'تنظيم الحاسوب ومعماريته', 'تنظيم الحاسوب ومعماريته', 'خطة SE', 1, 0),
(65, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة SE', 1, 0),
(65, 'شبكات الحاسوب', 'شبكات الحاسوب', 'خطة SE', 1, 0),
(65, 'فحص البرمجيات وجودتها', 'فحص البرمجيات وجودتها', 'خطة SE', 1, 0),
(65, 'قواعد بيانات', 'قواعد بيانات', 'خطة SE', 1, 0),
(65, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة SE', 1, 0),
(65, 'مختبر قواعد البيانات', 'مختبر قواعد البيانات', 'خطة SE', 1, 0),
(65, 'مختبر مقدمة في البرمجة', 'مختبر مقدمة في البرمجة', 'خطة SE', 1, 0),
(65, 'مشروع تخرج تطبيقي (1)', 'مشروع تخرج تطبيقي (1)', 'خطة SE', 1, 0),
(65, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة SE', 1, 0),
(65, 'معمارية البرمجيات', 'معمارية البرمجيات', 'خطة SE', 1, 0),
(65, 'مقدمة في البرمجة', 'مقدمة في البرمجة', 'خطة SE', 1, 0),
(65, 'مهارات الاتصال والكتابة', 'مهارات الاتصال والكتابة', 'خطة SE', 1, 0),
(65, 'مواصفات البرمجيات وتصميمها', 'مواصفات البرمجيات وتصميمها', 'خطة SE', 1, 0),
(65, 'نظم إدارة قواعد البيانات', 'نظم إدارة قواعد البيانات', 'خطة SE', 1, 0),
(65, 'نظم التشغيل', 'نظم التشغيل', 'خطة SE', 1, 0),
(65, 'هندسة متطلبات البرمجيات', 'هندسة متطلبات البرمجيات', 'خطة SE', 1, 0),
(66, 'أخلاقيات ومخاطر وسياسات الأمن السيبراني', 'أخلاقيات ومخاطر وسياسات الأمن السيبراني', 'خطة Cyber', 1, 0),
(66, 'أساسيات التشفير', 'أساسيات التشفير', 'خطة Cyber', 1, 0),
(66, 'أساسيات تكنولوجيا المعلومات', 'أساسيات تكنولوجيا المعلومات', 'خطة Cyber', 1, 0),
(66, 'أمن البيانات والبرمجيات', 'أمن البيانات والبرمجيات', 'خطة Cyber', 1, 0),
(66, 'أمن النظم والبنية التحتية', 'أمن النظم والبنية التحتية', 'خطة Cyber', 1, 0),
(66, 'إدارة وأمن الشبكات', 'إدارة وأمن الشبكات', 'خطة Cyber', 1, 0),
(66, 'اختبار الاختراق', 'اختبار الاختراق', 'خطة Cyber', 1, 0),
(66, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة Cyber', 1, 0),
(66, 'التدريب الميداني', 'التدريب الميداني', 'خطة Cyber', 1, 0),
(66, 'القرصنة الأخلاقية', 'القرصنة الأخلاقية', 'خطة Cyber', 1, 0),
(66, 'اللغة الإنجليزية لتكنولوجيا المعلومات', 'اللغة الإنجليزية لتكنولوجيا المعلومات', 'خطة Cyber', 1, 0),
(66, 'برمجة أمن المعلومات والشبكات', 'برمجة أمن المعلومات والشبكات', 'خطة Cyber', 1, 0),
(66, 'برمجة تطبيقات الإنترنت', 'برمجة تطبيقات الإنترنت', 'خطة Cyber', 1, 0),
(66, 'بروتوكولات أمن المعلومات', 'بروتوكولات أمن المعلومات', 'خطة Cyber', 1, 0),
(66, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة Cyber', 1, 0),
(66, 'تراكيب البيانات', 'تراكيب البيانات', 'خطة Cyber', 1, 0),
(66, 'تصميم مواقع إلكترونية', 'تصميم مواقع إلكترونية', 'خطة Cyber', 1, 0),
(66, 'تطوير وتصميم الأنظمة الآمنة', 'تطوير وتصميم الأنظمة الآمنة', 'خطة Cyber', 1, 0),
(66, 'تفاضل وتكامل (1)', 'تفاضل وتكامل (1)', 'خطة Cyber', 1, 0),
(66, 'جبر خطي (1)', 'جبر خطي (1)', 'خطة Cyber', 1, 0),
(66, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة Cyber', 1, 0),
(66, 'سلامة ومصادقة البيانات', 'سلامة ومصادقة البيانات', 'خطة Cyber', 1, 0),
(66, 'شبكات الحاسوب', 'شبكات الحاسوب', 'خطة Cyber', 1, 0),
(66, 'علوم جنائية رقمية', 'علوم جنائية رقمية', 'خطة Cyber', 1, 0),
(66, 'قواعد البيانات', 'قواعد البيانات', 'خطة Cyber', 1, 0),
(66, 'مبادئ الإحصاء والاحتمالات', 'مبادئ الإحصاء والاحتمالات', 'خطة Cyber', 1, 0),
(66, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة Cyber', 1, 0),
(66, 'مختبر تراكيب البيانات', 'مختبر تراكيب البيانات', 'خطة Cyber', 1, 0),
(66, 'مختبر قواعد البيانات', 'مختبر قواعد البيانات', 'خطة Cyber', 1, 0),
(66, 'مختبر مقدمة في البرمجة', 'مختبر مقدمة في البرمجة', 'خطة Cyber', 1, 0),
(66, 'مراقبة الشبكات وتوثيقها', 'مراقبة الشبكات وتوثيقها', 'خطة Cyber', 1, 0),
(66, 'مشروع تخرج تطبيقي (1)', 'مشروع تخرج تطبيقي (1)', 'خطة Cyber', 1, 0),
(66, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة Cyber', 1, 0),
(66, 'مقدمة في الأمن السيبراني', 'مقدمة في الأمن السيبراني', 'خطة Cyber', 1, 0),
(66, 'مقدمة في البرمجة', 'مقدمة في البرمجة', 'خطة Cyber', 1, 0),
(66, 'نظم التشغيل', 'نظم التشغيل', 'خطة Cyber', 1, 0),
(68, 'أدوات هندسة البرمجيات', 'أدوات هندسة البرمجيات', 'خطة SE', 1, 0),
(68, 'أساسيات تكنولوجيا المعلومات', 'أساسيات تكنولوجيا المعلومات', 'خطة SE', 1, 0),
(68, 'أساسيات هندسة البرمجيات', 'أساسيات هندسة البرمجيات', 'خطة SE', 1, 0),
(68, 'إدارة المشروع البرمجي', 'إدارة المشروع البرمجي', 'خطة SE', 1, 0),
(68, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة SE', 1, 0),
(68, 'التدريب العملي في مجال هندسة البرمجيات', 'التدريب العملي في مجال هندسة البرمجيات', 'خطة SE', 1, 0),
(68, 'برمجة تطبيقات الإنترنت', 'برمجة تطبيقات الإنترنت', 'خطة SE', 1, 0),
(68, 'برمجة قواعد البيانات', 'برمجة قواعد البيانات', 'خطة SE', 1, 0),
(68, 'برمجة مرئية', 'برمجة مرئية', 'خطة SE', 1, 0),
(68, 'تحليل عددي (1)', 'تحليل عددي (1)', 'خطة SE', 1, 0),
(68, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة SE', 1, 0),
(68, 'تحليل وتصميم النظم', 'تحليل وتصميم النظم', 'خطة SE', 1, 0),
(68, 'تراكيب البيانات', 'تراكيب البيانات', 'خطة SE', 1, 0),
(68, 'تصميم المنطق الرقمي', 'تصميم المنطق الرقمي', 'خطة SE', 1, 0),
(68, 'تصميم مواقع إلكترونية', 'تصميم مواقع إلكترونية', 'خطة SE', 1, 0),
(68, 'تطوير البرمجيات وتوثيقها', 'تطوير البرمجيات وتوثيقها', 'خطة SE', 1, 0),
(68, 'تفاعل الإنسان مع الحاسوب', 'تفاعل الإنسان مع الحاسوب', 'خطة SE', 1, 0),
(68, 'تنظيم الحاسوب ومعماريته', 'تنظيم الحاسوب ومعماريته', 'خطة SE', 1, 0),
(68, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة SE', 1, 0),
(68, 'شبكات الحاسوب', 'شبكات الحاسوب', 'خطة SE', 1, 0),
(68, 'فحص البرمجيات وجودتها', 'فحص البرمجيات وجودتها', 'خطة SE', 1, 0),
(68, 'قواعد بيانات', 'قواعد بيانات', 'خطة SE', 1, 0),
(68, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة SE', 1, 0),
(68, 'مختبر قواعد البيانات', 'مختبر قواعد البيانات', 'خطة SE', 1, 0),
(68, 'مختبر مقدمة في البرمجة', 'مختبر مقدمة في البرمجة', 'خطة SE', 1, 0),
(68, 'مشروع تخرج تطبيقي (1)', 'مشروع تخرج تطبيقي (1)', 'خطة SE', 1, 0),
(68, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة SE', 1, 0),
(68, 'معمارية البرمجيات', 'معمارية البرمجيات', 'خطة SE', 1, 0),
(68, 'مقدمة في البرمجة', 'مقدمة في البرمجة', 'خطة SE', 1, 0),
(68, 'مهارات الاتصال والكتابة', 'مهارات الاتصال والكتابة', 'خطة SE', 1, 0),
(68, 'مواصفات البرمجيات وتصميمها', 'مواصفات البرمجيات وتصميمها', 'خطة SE', 1, 0),
(68, 'نظم إدارة قواعد البيانات', 'نظم إدارة قواعد البيانات', 'خطة SE', 1, 0),
(68, 'نظم التشغيل', 'نظم التشغيل', 'خطة SE', 1, 0),
(68, 'هندسة متطلبات البرمجيات', 'هندسة متطلبات البرمجيات', 'خطة SE', 1, 0),
(69, 'أخلاقيات علم البيانات والذكاء الاصطناعي', 'أخلاقيات علم البيانات والذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(69, 'أساسيات تكنولوجيا المعلومات', 'أساسيات تكنولوجيا المعلومات', 'خطة AI/DS', 1, 0),
(69, 'أساسيات علم البيانات', 'أساسيات علم البيانات', 'خطة AI/DS', 1, 0),
(69, 'أمن البيانات', 'أمن البيانات', 'خطة AI/DS', 1, 0),
(69, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة AI/DS', 1, 0),
(69, 'البيانات الكبيرة والمفتوحة', 'البيانات الكبيرة والمفتوحة', 'خطة AI/DS', 1, 0),
(69, 'البيانات المرئية', 'البيانات المرئية', 'خطة AI/DS', 1, 0),
(69, 'التدريب الميداني', 'التدريب الميداني', 'خطة AI/DS', 1, 0),
(69, 'التعلم الآلي', 'التعلم الآلي', 'خطة AI/DS', 1, 0),
(69, 'التعلم العميق', 'التعلم العميق', 'خطة AI/DS', 1, 0),
(69, 'التنقيب عن البيانات', 'التنقيب عن البيانات', 'خطة AI/DS', 1, 0),
(69, 'الذكاء الاصطناعي', 'الذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(69, 'الروبوتات المتنقلة الذكية', 'الروبوتات المتنقلة الذكية', 'خطة AI/DS', 1, 0),
(69, 'الطرق الإحصائية', 'الطرق الإحصائية', 'خطة AI/DS', 1, 0),
(69, 'برمجة علم البيانات والذكاء الاصطناعي', 'برمجة علم البيانات والذكاء الاصطناعي', 'خطة AI/DS', 1, 0),
(69, 'برمجة متقدمة', 'برمجة متقدمة', 'خطة AI/DS', 1, 0),
(69, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة AI/DS', 1, 0),
(69, 'تراكيب البيانات', 'تراكيب البيانات', 'خطة AI/DS', 1, 0),
(69, 'تصميم مواقع إلكترونية', 'تصميم مواقع إلكترونية', 'خطة AI/DS', 1, 0),
(69, 'تفاضل وتكامل (1)', 'تفاضل وتكامل (1)', 'خطة AI/DS', 1, 0),
(69, 'تمثيل واستدلال المعرفة', 'تمثيل واستدلال المعرفة', 'خطة AI/DS', 1, 0),
(69, 'جبر خطي (1)', 'جبر خطي (1)', 'خطة AI/DS', 1, 0),
(69, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة AI/DS', 1, 0),
(69, 'قواعد البيانات', 'قواعد البيانات', 'خطة AI/DS', 1, 0),
(69, 'مبادئ الإحصاء والاحتمالات', 'مبادئ الإحصاء والاحتمالات', 'خطة AI/DS', 1, 0),
(69, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة AI/DS', 1, 0),
(69, 'مختبر تراكيب البيانات', 'مختبر تراكيب البيانات', 'خطة AI/DS', 1, 0),
(69, 'مختبر قواعد البيانات', 'مختبر قواعد البيانات', 'خطة AI/DS', 1, 0),
(69, 'مختبر مقدمة في البرمجة', 'مختبر مقدمة في البرمجة', 'خطة AI/DS', 1, 0),
(69, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة AI/DS', 1, 0),
(69, 'مشروع تطبيقي (1)', 'مشروع تطبيقي (1)', 'خطة AI/DS', 1, 0),
(69, 'معالجة اللغات الطبيعية وتطبيقاتها', 'معالجة اللغات الطبيعية وتطبيقاتها', 'خطة AI/DS', 1, 0),
(69, 'مقدمة في البرمجة', 'مقدمة في البرمجة', 'خطة AI/DS', 1, 0),
(69, 'مهارات الاتصال والكتابة', 'مهارات الاتصال والكتابة', 'خطة AI/DS', 1, 0),
(69, 'نظم التشغيل', 'نظم التشغيل', 'خطة AI/DS', 1, 0),
(69, 'هندسة البيانات وتحليلها', 'هندسة البيانات وتحليلها', 'خطة AI/DS', 1, 0),
(70, 'أساسيات هندسة البرمجيات', 'أساسيات هندسة البرمجيات', 'خطة CS', 1, 0),
(70, 'أمن المعلومات', 'أمن المعلومات', 'خطة CS', 1, 0),
(70, 'اخلاقيات الحاسوب', 'اخلاقيات الحاسوب', 'خطة CS', 1, 0),
(70, 'اساسيات تكنولوجيا المعلومات', 'اساسيات تكنولوجيا المعلومات', 'خطة CS', 1, 0),
(70, 'البرمجة الكينونية', 'البرمجة الكينونية', 'خطة CS', 1, 0),
(70, 'التدريب الميداني', 'التدريب الميداني', 'خطة CS', 1, 0),
(70, 'الذكاء الاصطناعي', 'الذكاء الاصطناعي', 'خطة CS', 1, 0),
(70, 'النظم الحاسوبية المتوازية و الموزعة', 'النظم الحاسوبية المتوازية و الموزعة', 'خطة CS', 1, 0),
(70, 'برمجة تطبيقات الانترنت', 'برمجة تطبيقات الانترنت', 'خطة CS', 1, 0),
(70, 'برمجة متقدمة', 'برمجة متقدمة', 'خطة CS', 1, 0),
(70, 'برمجة مرئية', 'برمجة مرئية', 'خطة CS', 1, 0),
(70, 'تحليل عددي (1)', 'تحليل عددي (1)', 'خطة CS', 1, 0),
(70, 'تحليل وتصميم الخوارزميات', 'تحليل وتصميم الخوارزميات', 'خطة CS', 1, 0),
(70, 'تحليل وتصميم النظم', 'تحليل وتصميم النظم', 'خطة CS', 1, 0),
(70, 'تراكيب البيانات', 'تراكيب البيانات', 'خطة CS', 1, 0),
(70, 'تصميم المترجمات', 'تصميم المترجمات', 'خطة CS', 1, 0),
(70, 'تصميم المنطق الرقمي', 'تصميم المنطق الرقمي', 'خطة CS', 1, 0),
(70, 'تصميم مواقع الكترونية', 'تصميم مواقع الكترونية', 'خطة CS', 1, 0),
(70, 'تفاضل وتكامل (1)', 'تفاضل وتكامل (1)', 'خطة CS', 1, 0),
(70, 'تنظيم الحاسوب ومعماريته', 'تنظيم الحاسوب ومعماريته', 'خطة CS', 1, 0),
(70, 'جبر خطي (1)', 'جبر خطي (1)', 'خطة CS', 1, 0),
(70, 'رياضيات متقطعة', 'رياضيات متقطعة', 'خطة CS', 1, 0),
(70, 'شبكات الحاسوب', 'شبكات الحاسوب', 'خطة CS', 1, 0),
(70, 'فيزياء عامة (1)', 'فيزياء عامة (1)', 'خطة CS', 1, 0),
(70, 'قواعد البيانات', 'قواعد البيانات', 'خطة CS', 1, 0),
(70, 'مبادئ الاحصاء والاحتمالات', 'مبادئ الاحصاء والاحتمالات', 'خطة CS', 1, 0),
(70, 'مختبر البرمجة الكينونية', 'مختبر البرمجة الكينونية', 'خطة CS', 1, 0),
(70, 'مختبر تراكيب البيانات', 'مختبر تراكيب البيانات', 'خطة CS', 1, 0),
(70, 'مختبر فيزياء عامة (1)', 'مختبر فيزياء عامة (1)', 'خطة CS', 1, 0),
(70, 'مختبر قواعد البيانات', 'مختبر قواعد البيانات', 'خطة CS', 1, 0),
(70, 'مختبر مقدمة في البرمجة', 'مختبر مقدمة في البرمجة', 'خطة CS', 1, 0),
(70, 'مشروع تخرج تطبيقي (1)', 'مشروع تخرج تطبيقي (1)', 'خطة CS', 1, 0),
(70, 'مشروع تخرج تطبيقي (2)', 'مشروع تخرج تطبيقي (2)', 'خطة CS', 1, 0),
(70, 'مقدمة في البرمجة', 'مقدمة في البرمجة', 'خطة CS', 1, 0),
(70, 'مهارات الاتصال والكتابة', 'مهارات الاتصال والكتابة', 'خطة CS', 1, 0),
(70, 'نظرية الحسابات', 'نظرية الحسابات', 'خطة CS', 1, 0),
(70, 'نظم التشغيل', 'نظم التشغيل', 'خطة CS', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_plan_profile`
--

DROP TABLE IF EXISTS `user_plan_profile`;
CREATE TABLE `user_plan_profile` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `major_name` varchar(200) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

DROP TABLE IF EXISTS `user_settings`;
CREATE TABLE `user_settings` (
  `user_id` int(11) NOT NULL,
  `term_credits` int(11) DEFAULT 15,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`user_id`, `term_credits`, `updated_at`) VALUES
(2, 15, '2026-02-03 12:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE `videos` (
  `id` int(10) UNSIGNED NOT NULL,
  `training_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(180) NOT NULL,
  `youtube_id` varchar(20) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `training_id`, `title`, `youtube_id`, `sort_order`, `created_at`) VALUES
(1, 1, 'Intro', 'dQw4w9WgXcQ', 1, '2026-02-06 13:51:22'),
(2, 1, 'Arrays', 'dQw4w9WgXcQ', 2, '2026-02-06 13:51:22'),
(3, 1, 'Linked Lists', 'dQw4w9WgXcQ', 3, '2026-02-06 13:51:22'),
(4, 1, 'Stacks', 'dQw4w9WgXcQ', 4, '2026-02-06 13:51:22'),
(5, 1, 'Queues', 'dQw4w9WgXcQ', 5, '2026-02-06 13:51:22');

-- --------------------------------------------------------

--
-- Table structure for table `video_progress`
--

DROP TABLE IF EXISTS `video_progress`;
CREATE TABLE `video_progress` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `video_id` varchar(32) NOT NULL,
  `watched_seconds` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `duration_seconds` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watched_percent` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_progress`
--

INSERT INTO `video_progress` (`user_id`, `video_id`, `watched_seconds`, `duration_seconds`, `watched_percent`, `is_completed`, `completed_at`, `updated_at`) VALUES
(2, '61yz7sVvQgs', 0, 0, 0.0000, 1, '2026-02-11 17:28:22', NULL),
(2, '95RhuC30R5U', 0, 0, 0.0000, 1, '2026-02-08 14:38:35', NULL),
(2, '98LuqEZlZis', 0, 0, 0.0000, 1, '2026-02-12 21:04:10', NULL),
(2, '9Gf5f8xvnis', 0, 0, 0.0000, 1, '2026-02-08 14:17:32', NULL),
(2, 'AbqvCnIN7DM', 0, 0, 0.0000, 1, '2026-02-12 19:56:34', NULL),
(2, 'AhBBJZxLJ10', 0, 0, 0.0000, 1, '2026-02-08 14:39:01', NULL),
(2, 'Au7aD0fSHl4', 0, 0, 0.0000, 1, '2026-02-13 18:28:09', NULL),
(2, 'aYbLU--MUR8', 0, 0, 0.0000, 1, '2026-02-07 20:02:09', NULL),
(2, 'B1z0FPFo24A', 0, 0, 0.0000, 0, NULL, NULL),
(2, 'CxGTRLkXUYg', 0, 0, 0.0000, 1, '2026-02-13 04:04:02', NULL),
(2, 'eFDzhn1Inc4', 0, 0, 0.0000, 1, '2026-02-12 19:55:15', NULL),
(2, 'fK2lLVqc8UY', 0, 0, 0.0000, 1, '2026-02-08 14:57:38', NULL),
(2, 'h3VCQjyaLws', 0, 0, 0.0000, 1, '2026-02-15 22:19:10', NULL),
(2, 'H5WUwwivEaI', 0, 0, 0.0000, 1, '2026-02-15 19:42:44', NULL),
(2, 'i0la0uLPOLQ', 0, 0, 0.0000, 1, '2026-02-08 15:10:20', NULL),
(2, 'ITJRTb6Yv1Y', 0, 0, 0.0000, 1, '2026-02-09 20:57:51', NULL),
(2, 'iUGV8qBQtCc', 0, 0, 0.0000, 1, '2026-02-12 19:48:03', NULL),
(2, 'NQE7UMZc0tE', 0, 0, 0.0000, 0, NULL, NULL),
(2, 'Olm22l7gae4', 0, 0, 0.0000, 1, '2026-02-12 19:56:07', NULL),
(2, 'Q1vMc8IaheI', 0, 0, 0.0000, 1, '2026-02-13 03:39:31', NULL),
(2, 'qn5fHGXt1Gw', 0, 0, 0.0000, 1, '2026-02-15 19:19:42', NULL),
(2, 'SaqjDiZJosk', 0, 0, 0.0000, 1, '2026-02-15 19:31:04', NULL),
(2, 'VyXQ8CMIQl4', 0, 0, 0.0000, 1, '2026-02-07 19:35:12', NULL),
(2, 'WM2LiUDa4jE', 0, 0, 0.0000, 1, '2026-02-07 19:31:09', NULL),
(2, 'WQowTGI4QFg', 0, 0, 0.0000, 1, '2026-02-08 14:47:58', NULL),
(2, 'WWn8tOHjZbw', 0, 0, 0.0000, 1, '2026-02-15 19:42:01', NULL),
(4, '61yz7sVvQgs', 0, 0, 0.0000, 1, '2026-02-10 18:57:42', NULL),
(4, 'AhBBJZxLJ10', 0, 0, 0.0000, 1, '2026-02-08 18:37:01', NULL),
(4, 'aYbLU--MUR8', 0, 0, 0.0000, 1, '2026-02-10 21:30:56', NULL),
(4, 'owCqVRbZlbg', 0, 0, 0.0000, 1, '2026-02-10 18:34:13', NULL),
(4, 'VyXQ8CMIQl4', 0, 0, 0.0000, 1, '2026-02-11 14:01:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `video_quiz_attempts`
--

DROP TABLE IF EXISTS `video_quiz_attempts`;
CREATE TABLE `video_quiz_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `video_id` varchar(32) NOT NULL,
  `quiz_type` varchar(10) NOT NULL,
  `quiz_json` mediumtext NOT NULL,
  `answers_json` mediumtext NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_quiz_attempts`
--

INSERT INTO `video_quiz_attempts` (`id`, `user_id`, `video_id`, `quiz_type`, `quiz_json`, `answers_json`, `score`, `total`, `updated_at`, `created_at`) VALUES
(1, 4, 'xo1R1nYM4aw', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (مختبر مقدمة في البرمجة)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[null,null,null,null,null,null],\"trueFalse\":[null,null,true,false],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 2, 10, '2026-02-06 13:06:01', '2026-02-06 13:06:01'),
(2, 4, '9ndH9Qo05F4', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (مقدمة في البرمجة)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,1,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 4, 10, '2026-02-06 14:14:47', '2026-02-06 13:40:08'),
(6, 4, 'WWn8tOHjZbw', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (تصميم مواقع الكترونية)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,1,1,1],\"trueFalse\":[false,true,true,false],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 10, 10, '2026-02-06 14:25:37', '2026-02-06 14:23:21'),
(9, 4, '0', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (أخلاقيات علم البيانات والذكاء الاصطناعي)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,null,null,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 2, 10, '2026-02-11 14:01:22', '2026-02-07 18:58:47'),
(10, 2, '0', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (تحليل وتصميم الخوارزميات)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,1,1,1],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 6, 10, '2026-02-15 22:54:43', '2026-02-07 18:59:43'),
(19, 2, '9', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (أساسيات تكنولوجيا المعلومات)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,null,null,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 2, 10, '2026-02-08 14:38:13', '2026-02-08 14:17:38'),
(28, 2, '95', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (التعلم الآلي)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,1,1,1],\"trueFalse\":[false,true,true,false],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 10, 10, '2026-02-08 14:38:46', '2026-02-08 14:38:46'),
(40, 4, '61', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (الذكاء الاصطناعي)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,1,1,1],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 6, 10, '2026-02-10 18:57:49', '2026-02-10 18:57:49'),
(44, 2, '61', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (الذكاء الاصطناعي)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,null,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 3, 10, '2026-02-11 17:28:28', '2026-02-11 17:28:28'),
(46, 2, 'iUGV8qBQtCc', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (التعلم العميق)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,null,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 3, 10, '2026-02-12 19:48:09', '2026-02-12 19:48:09'),
(47, 2, '61yz7sVvQgs', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (برمجة علم البيانات والذكاء الاصطناعي)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,null,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 3, 10, '2026-02-12 19:55:01', '2026-02-12 19:55:01'),
(48, 2, 'eFDzhn1Inc4', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (رياضيات متقطعة)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,null,null,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 2, 10, '2026-02-12 19:55:19', '2026-02-12 19:55:19'),
(49, 2, 'Olm22l7gae4', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (أساسيات تكنولوجيا المعلومات)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,1,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 4, 10, '2026-02-12 19:56:15', '2026-02-12 19:56:15'),
(50, 2, 'AbqvCnIN7DM', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (أساسيات علم البيانات)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,null,null,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 2, 10, '2026-02-12 19:56:38', '2026-02-12 19:56:38'),
(51, 2, '98LuqEZlZis', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (التدريب الميداني)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,null,null,null,null],\"trueFalse\":[null,null,null,null],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 2, 10, '2026-02-12 21:04:25', '2026-02-12 21:04:25'),
(52, 2, '98', 'deep', '{\"mcq\":[{\"q\":\"ما الهدف الأساسي من هذا الفيديو ضمن سياق (التدريب الميداني)؟\",\"choices\":[\"شرح مفهوم\\/مهارة\",\"عرض أخبار فقط\",\"تجربة عشوائية\",\"بدون هدف\"],\"answerIndex\":0,\"explain\":\"الأقرب عادةً: شرح مفهوم\\/مهارة مرتبطة بالعنوان.\"},{\"q\":\"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟\",\"choices\":[\"المشاهدة فقط\",\"التطبيق العملي بعد كل جزء\",\"حفظ الكلمات\",\"تجاهل الأمثلة\"],\"answerIndex\":1,\"explain\":\"التطبيق يثبت الفهم.\"},{\"q\":\"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟\",\"choices\":[\"إيقاف التعلّم\",\"قراءة رسالة الخطأ وتتبع السبب\",\"حذف المشروع\",\"تغيير الموضوع\"],\"answerIndex\":1,\"explain\":\"رسالة الخطأ دليل مباشر.\"},{\"q\":\"كيف تقيس فهمك بعد الفيديو؟\",\"choices\":[\"تخمين\",\"حل أسئلة وشرح السبب\",\"تجاوز\",\"مشاهدة غير مرتبط\"],\"answerIndex\":1,\"explain\":\"الأسئلة + شرح السبب أفضل.\"},{\"q\":\"أفضل طريقة لتوثيق ما تعلمته؟\",\"choices\":[\"بدون ملاحظات\",\"ملاحظات مختصرة + مثال\",\"نسخ كل شيء\",\"الذاكرة فقط\"],\"answerIndex\":1,\"explain\":\"ملاحظات + مثال.\"},{\"q\":\"علامة أن الشرح كان واضح؟\",\"choices\":[\"لا تستطيع تطبيق\",\"تستطيع شرح وتطبيق مثال\",\"لا تتذكر العنوان\",\"تتشتت\"],\"answerIndex\":1,\"explain\":\"التطبيق دليل فهم.\"}],\"trueFalse\":[{\"q\":\"المشاهدة وحدها تكفي بدون تطبيق.\",\"answer\":false,\"explain\":\"التطبيق ضروري.\"},{\"q\":\"قراءة رسالة الخطأ تساعد على الحل.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.\",\"answer\":true,\"explain\":\"صحيح.\"},{\"q\":\"تخطي الأمثلة أفضل طريقة للتعلم.\",\"answer\":false,\"explain\":\"الأمثلة مهمة.\"}],\"short\":[{\"q\":\"اكتب أهم نقطة تعلمتها من الفيديو.\",\"answer\":\"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.\",\"rubric\":\"اذكر الفكرة + مثال.\"},{\"q\":\"اذكر خطأ شائع وكيف تتعامل معه.\",\"answer\":\"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.\",\"rubric\":\"خطأ + حل.\"},{\"q\":\"ما خطوة عملية ستنفذها اليوم؟\",\"answer\":\"أطبق مثال صغير ثم أحسّنه تدريجيًا.\",\"rubric\":\"خطوة قابلة للتنفيذ.\"}],\"application\":[{\"q\":\"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.\",\"answer\":\"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين\",\"rubric\":\"خطة واضحة.\"},{\"q\":\"ما معيار نجاح تطبيقك؟\",\"answer\":\"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.\",\"rubric\":\"معيار قابل للقياس.\"}]}', '{\"mcq\":[0,1,1,1,1,1],\"trueFalse\":[false,true,true,false],\"short\":[\"\",\"\",\"\"],\"application\":[\"\",\"\"]}', 10, 10, '2026-02-15 19:43:59', '2026-02-12 21:05:30');

-- --------------------------------------------------------

--
-- Table structure for table `video_quiz_cache`
--

DROP TABLE IF EXISTS `video_quiz_cache`;
CREATE TABLE `video_quiz_cache` (
  `video_id` varchar(32) NOT NULL,
  `quiz_json` mediumtext NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_rewards`
--

DROP TABLE IF EXISTS `video_rewards`;
CREATE TABLE `video_rewards` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `video_id` varchar(32) NOT NULL,
  `base_coin` int(11) NOT NULL DEFAULT 0,
  `quiz_coin` int(11) NOT NULL DEFAULT 0,
  `total_coin` int(11) NOT NULL DEFAULT 0,
  `rewarded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `youtube_id` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_rewards`
--

INSERT INTO `video_rewards` (`user_id`, `video_id`, `base_coin`, `quiz_coin`, `total_coin`, `rewarded_at`, `youtube_id`) VALUES
(2, '', 0, 0, 20, '2026-02-07 20:29:29', 'aYbLU--MUR8'),
(2, 'VyXQ8CMIQl4', 10, 10, 20, '2026-02-07 19:35:28', NULL),
(2, 'WM2LiUDa4jE', 10, 0, 10, '2026-02-07 19:31:10', NULL),
(2, 'fK2lLVqc8UY', 10, 10, 20, '2026-02-08 15:09:55', 'fK2lLVqc8UY'),
(2, 'i0la0uLPOLQ', 10, 10, 20, '2026-02-08 15:10:35', 'i0la0uLPOLQ'),
(4, 'WM2LiUDa4jE', 10, 10, 20, '2026-02-08 15:12:30', 'WM2LiUDa4jE'),
(4, 'p_8', 10, 10, 20, '2026-02-09 20:37:16', 'p_8'),
(2, 'ITJRTb6Yv1Y', 10, 10, 20, '2026-02-09 20:57:58', 'ITJRTb6Yv1Y'),
(4, 'owCqVRbZlbg', 10, 10, 20, '2026-02-10 18:34:19', 'owCqVRbZlbg'),
(4, '61yz7sVvQgs', 10, 10, 20, '2026-02-10 18:57:49', '61yz7sVvQgs'),
(4, 'aYbLU--MUR8', 10, 10, 20, '2026-02-10 21:31:02', 'aYbLU--MUR8'),
(4, 'VyXQ8CMIQl4', 10, 10, 20, '2026-02-11 14:01:22', 'VyXQ8CMIQl4'),
(2, '61yz7sVvQgs', 10, 10, 20, '2026-02-11 17:28:28', '61yz7sVvQgs'),
(2, '', 0, 0, 9000, '2026-02-11 17:39:04', NULL),
(2, 'WQowTGI4QFg', 10, 10, 20, '2026-02-12 19:29:42', 'WQowTGI4QFg'),
(2, 'iUGV8qBQtCc', 10, 10, 20, '2026-02-12 19:48:09', 'iUGV8qBQtCc'),
(2, 'eFDzhn1Inc4', 10, 10, 20, '2026-02-12 19:55:19', 'eFDzhn1Inc4'),
(2, 'Olm22l7gae4', 10, 10, 20, '2026-02-12 19:56:15', 'Olm22l7gae4'),
(2, 'AbqvCnIN7DM', 10, 10, 20, '2026-02-12 19:56:38', 'AbqvCnIN7DM'),
(2, '98LuqEZlZis', 10, 10, 20, '2026-02-12 21:05:30', '98LuqEZlZis'),
(2, 'Q1vMc8IaheI', 10, 10, 20, '2026-02-13 03:39:41', 'Q1vMc8IaheI'),
(2, 'CxGTRLkXUYg', 10, 10, 20, '2026-02-13 04:04:09', 'CxGTRLkXUYg'),
(2, 'B1z0FPFo24A', 10, 10, 20, '2026-02-13 18:26:59', 'B1z0FPFo24A'),
(2, 'qn5fHGXt1Gw', 10, 10, 20, '2026-02-15 19:19:42', 'qn5fHGXt1Gw'),
(2, 'KT6ASzh_JXs', 10, 10, 20, '2026-02-15 19:21:25', 'KT6ASzh_JXs'),
(2, 'HMoH6b7Qu8A', 10, 10, 20, '2026-02-15 19:29:23', 'HMoH6b7Qu8A'),
(2, 'SaqjDiZJosk', 10, 10, 20, '2026-02-15 19:31:06', 'SaqjDiZJosk'),
(2, 'WWn8tOHjZbw', 10, 10, 20, '2026-02-15 19:42:10', 'WWn8tOHjZbw'),
(2, 'H5WUwwivEaI', 10, 10, 20, '2026-02-15 19:42:54', 'H5WUwwivEaI'),
(2, 'h3VCQjyaLws', 10, 10, 20, '2026-02-15 22:19:12', 'h3VCQjyaLws'),
(2, 'McifeJjrvpI', 10, 10, 20, '2026-02-15 22:54:43', 'McifeJjrvpI'),
(2, 'p_31', 10, 10, 20, '2026-02-16 20:34:12', 'p_31'),
(2, 'p_35', 10, 10, 20, '2026-02-18 17:45:29', 'p_35'),
(2, 'partner_38', 10, 10, 20, '2026-02-18 21:53:28', 'partner_38'),
(2, 'partner_41', 0, 20, 20, '2026-02-18 22:12:04', 'partner_41'),
(2, 'partner_40', 0, 20, 20, '2026-02-18 22:12:21', 'partner_40'),
(2, 'partner_39', 0, 20, 20, '2026-02-18 22:12:38', 'partner_39'),
(2, 'partner_35', 0, 380, 380, '2026-02-18 22:23:42', 'partner_35'),
(2, 'partner_52', 0, 80, 80, '2026-02-19 19:15:20', 'partner_52'),
(2, 'partner_51', 0, 80, 80, '2026-02-19 19:15:31', 'partner_51'),
(2, 'partner_50', 0, 80, 80, '2026-02-19 19:15:58', 'partner_50'),
(69, 'partner_52', 0, 80, 80, '2026-02-19 19:22:54', 'partner_52'),
(69, 'partner_51', 0, 80, 80, '2026-02-19 19:23:17', 'partner_51'),
(69, 'partner_50', 0, 80, 80, '2026-02-19 19:23:27', 'partner_50'),
(69, 'partner_48', 0, 80, 80, '2026-02-19 19:23:41', 'partner_48'),
(2, 'partner_47', 0, 1800, 1800, '2026-02-20 18:55:41', 'partner_47'),
(2, 'partner_48', 0, 80, 80, '2026-02-20 19:13:28', 'partner_48');

-- --------------------------------------------------------

--
-- Table structure for table `youtube_cache`
--

DROP TABLE IF EXISTS `youtube_cache`;
CREATE TABLE `youtube_cache` (
  `cache_key` varchar(255) NOT NULL,
  `json` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `youtube_cache`
--

INSERT INTO `youtube_cache` (`cache_key`, `json`, `updated_at`) VALUES
('playlist:q:أخلاقيات الإتصال والإنتاج', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLDYfSqIW22dEmTXZpMwWgcU0CHgpQRyzF\",\"title\":\"مبادئ محاسبة - الفرقة الأولى - 2025\",\"channel\":\"درس خصوصي - علم المحاسبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gCHqLjdBJro\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDYfSqIW22dEmTXZpMwWgcU0CHgpQRyzF\"}}', '2026-02-05 16:51:37'),
('playlist:q:أساسيات تكنولوجيا المعلومات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLroS9tRyoUGpBwtwq8NUI85KnOQdVu7v7\",\"title\":\"أساسيات تكنولوجيا المعلومات | Fundamentals of Information Technology\",\"channel\":\"تكناوي دوت نيت\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9Gf5f8xvnis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLroS9tRyoUGpBwtwq8NUI85KnOQdVu7v7\"}}', '2026-02-05 16:51:36'),
('playlist:q:أمن البيانات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\",\"title\":\"احتراف أمن المعلومات - كورس كامل\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/j_rO-cNFYX0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\"}}', '2026-02-05 16:51:46'),
('playlist:q:استرجاع المعلومات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLRdABJkXXytBZEnoaSbhJhdLE2K8Nshca\",\"title\":\"دورة &quot;استرجاع المعلومات&quot; باللغة العربية - صيف ٢٠٢١\",\"channel\":\"Tamer Elsayed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LNK51UbY5BA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRdABJkXXytBZEnoaSbhJhdLE2K8Nshca\"}}', '2026-02-05 16:51:28'),
('playlist:q:الأنظمة التوصوية', '{\"ok\":true,\"playlist\":null}', '2026-02-05 16:51:26'),
('playlist:q:البرمجة الشيئية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PL8DDsWuvM_EWYUvtpxALB0Xx7L_f_phXm\",\"title\":\"كورس البرمجة الكائينية |  C++ Object Oriented OOP\",\"channel\":\"اتعلم ببساطة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aJG-KnmfFxM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8DDsWuvM_EWYUvtpxALB0Xx7L_f_phXm\"}}', '2026-02-05 16:51:34'),
('playlist:q:البيئة والمجتمع', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\",\"title\":\"محاضرات الاحصاء للمرحلة الاولى\",\"channel\":\"تقنيات انظمة الحاسوب المعهد التقني بعقوبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KT6ASzh_JXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\"}}', '2026-02-05 16:51:49'),
('playlist:q:التربية الإسلامية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLH4NxcjPhgkl0fu6RFd8617GzpIwePssi\",\"title\":\"التربية الإسلامية - المستوى الأول\",\"channel\":\"برنامج أكاديمية زاد - Zad academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6amuu4FC2js\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH4NxcjPhgkl0fu6RFd8617GzpIwePssi\"}}', '2026-02-05 16:51:30'),
('playlist:q:التربية الوطنية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLOWDN6fgx7gRg7r-TqDDfRHqqvwSg4ZOY\",\"title\":\"الدورة المجانية في الرخصة المهنية التربوي ( العام ) مع المدربين: محمد محيسن + ماهر سلام + خالد عزمي\",\"channel\":\"منصة ثقة التعليمية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/s4SYPBArv2g\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLOWDN6fgx7gRg7r-TqDDfRHqqvwSg4ZOY\"}}', '2026-02-05 16:51:32'),
('playlist:q:التعلم العميق', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLUQ5y3YcMclGp5zaZSgPMd-JbR39fWSZs\",\"title\":\"Deep Learning:  دورة التعلم العميق بالعربي\",\"channel\":\"منصة أكاديما للذكاء الأصنطاعي وعلوم الحاسب\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WQowTGI4QFg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUQ5y3YcMclGp5zaZSgPMd-JbR39fWSZs\"}}', '2026-02-05 16:51:26'),
('playlist:q:التنقيب عن البيانات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLdUHNiwJgn84ltB6_BypNnXC7HweUfYoF\",\"title\":\"Data Mining || Abdulrahman Ihsan\",\"channel\":\"Etihad IT HU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/i0la0uLPOLQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLdUHNiwJgn84ltB6_BypNnXC7HweUfYoF\"}}', '2026-02-05 16:51:43'),
('playlist:q:الذكاء الاصطناعي', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"}}', '2026-02-05 16:51:39'),
('playlist:q:الروبوتات المتنقلة الذكية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLeIN5gmPLA4ISbZFi0gYBmfV1oKbiuVbk\",\"title\":\"دورة برمجة الروبوت والأردوينو 2024 || Robot and Arduino programming course 2024\",\"channel\":\"د. عبدالعزيز الصعيدي | Dr. Abdalaziz Elsaidy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AhBBJZxLJ10\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLeIN5gmPLA4ISbZFi0gYBmfV1oKbiuVbk\"}}', '2026-02-05 16:51:44'),
('playlist:q:الريادة', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLo8CHEfAmimQUGvrwnIwNP0znU9ZiR6Ng\",\"title\":\"سلسلة ريادة الأعمال والبيزنس\",\"channel\":\"علم وعمل\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/G87Su7LO7C0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLo8CHEfAmimQUGvrwnIwNP0znU9ZiR6Ng\"}}', '2026-02-05 16:51:33'),
('playlist:q:اللغة التركية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PL5wP1cVEYkPVm7QvxJtQH-B-qBmzPwFvs\",\"title\":\"تعلم اللغة التركية من الصفر مع زينب\",\"channel\":\"زينب مصري - Zeynep Masri\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Kl9pTukIGxU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL5wP1cVEYkPVm7QvxJtQH-B-qBmzPwFvs\"}}', '2026-02-05 16:51:51'),
('playlist:q:اللغة الصينية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLxCFn5-t8kLULjilHcetdb9EZJlV3rRcV\",\"title\":\"دروس تعليم اللغة الصينية\",\"channel\":\"Taleek\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FJQ6EootB1U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxCFn5-t8kLULjilHcetdb9EZJlV3rRcV\"}}', '2026-02-05 16:51:52'),
('playlist:q:اللغة العربية (1)', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLLbiI--L9-ereChdYMwCJNI25rUj1l6en\",\"title\":\"دورة التأسيس في قواعد اللغة العربية\",\"channel\":\"الأستاذ إبراهيم حجاج\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mYDj-SDIOW0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLLbiI--L9-ereChdYMwCJNI25rUj1l6en\"}}', '2026-02-05 16:51:30'),
('playlist:q:المهارات الحياتية والمسؤولية المجتمعية', '{\"ok\":true,\"playlist\":null}', '2026-02-05 16:51:33'),
('playlist:q:برمجة اللغات الطبيعية وتطبيقاتها', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"}}', '2026-02-05 16:51:42'),
('playlist:q:برمجة متقدمة', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"}}', '2026-02-05 16:51:38'),
('playlist:q:تاريخ القدس والوصاية الهاشمية', '{\"ok\":true,\"playlist\":null}', '2026-02-05 16:51:49'),
('playlist:q:تحليل وتصميم الخوارزميات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLn_Cp0AAXFlG6iuwkiLjCrX_7b5waIMRJ\",\"title\":\"ِِAlgorithms design and analysis - تحليل وتصميم الخورازميات\",\"channel\":\"A Grade\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/p4_O6_rSCos\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLn_Cp0AAXFlG6iuwkiLjCrX_7b5waIMRJ\"}}', '2026-02-05 16:51:37'),
('playlist:q:تصميم مواقع الكترونية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\",\"title\":\"تعلم تصميم وتطوير المواقع 2022 دبلوم كامل مع 100 مشروع كامل\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WWn8tOHjZbw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\"}}', '2026-02-05 16:51:23'),
('playlist:q:تفاضل وتكامل (1)', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLWGBZlhzh4JxXD0jbIbgrf7QicsxySqb0\",\"title\":\"كالكولاس ١ - calculus 1\",\"channel\":\"منصة ألفا التعليمية - Alpha platform\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/uv39vB0C3uY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWGBZlhzh4JxXD0jbIbgrf7QicsxySqb0\"}}', '2026-02-05 16:51:53'),
('playlist:q:تفسير النماذج', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\",\"title\":\"تعلم الآلة بالعربي || Machine Learning in Arabic\",\"channel\":\"Elgouhary AI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/95RhuC30R5U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\"}}', '2026-02-05 16:51:27'),
('playlist:q:تنمية بشرية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLmpvAXGVagwVswF7HZFXV4q4SzDlQEgIe\",\"title\":\"الثقة بالنفس .. كورس كامل\",\"channel\":\"Adel Marzok\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lI3eal-K0kA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLmpvAXGVagwVswF7HZFXV4q4SzDlQEgIe\"}}', '2026-02-05 16:51:50'),
('playlist:q:جبر خطي (1)', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLxIvc-MGOs6iQXFnjF_STbhGdrZBphrv_\",\"title\":\"Linear Algebra | جبر خطي\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1RnKXrJwseo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6iQXFnjF_STbhGdrZBphrv_\"}}', '2026-02-05 16:51:47'),
('playlist:q:حقوق الانسان', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLm877Wx3hfJ3iM0iNgtLGJB_Zv2BTKe3p\",\"title\":\"Human Rights | حقوق الإنسان\",\"channel\":\"Mohamed Maher | محمد ماهر\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KHDkBHs27jk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLm877Wx3hfJ3iM0iNgtLGJB_Zv2BTKe3p\"}}', '2026-02-05 16:51:48'),
('playlist:q:رياضيات متقطعة', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLxIvc-MGOs6gZlMVYOOEtUHJmfUquCjwz\",\"title\":\"Discrete Mathematics | الرياضيات المتقطعة\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/eFDzhn1Inc4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6gZlMVYOOEtUHJmfUquCjwz\"}}', '2026-02-05 16:51:34'),
('playlist:q:شبكات الحاسوب', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"}}', '2026-02-05 16:51:24'),
('playlist:q:قواعد البيانات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"}}', '2026-02-05 16:51:25'),
('playlist:q:مبادئ الإحصاء والاحتمالات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\",\"title\":\"مبادئ الاحصاء\",\"channel\":\"Ahmed Ayish Aldehneen\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v-KEUmZD3fY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\"}}', '2026-02-05 16:51:47'),
('playlist:q:مختبر البرمجة الشيئية', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\",\"title\":\"Object Oriented Programming - شرح البرمجة كائنية التوجه\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fK2lLVqc8UY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\"}}', '2026-02-05 16:51:35'),
('playlist:q:مختبر الذكاء الاصطناعي', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"}}', '2026-02-05 16:51:40'),
('playlist:q:مختبر مقدمة في البرمجة', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\",\"title\":\"C++ Programming Course Level 1 Basics By Arabic   كورس لغه برمجه سي بلس بلس المستوي الاول الاساسيات بالعربي\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oKA0aq4BdiY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\"}}', '2026-02-05 16:51:22'),
('playlist:q:مشروع تخرج (1)', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLdbDarq2VLKbpcCowxrZ3khDhFpNYYPUT\",\"title\":\"كورس الاستقبال والطوارئ كاملEmergency course\",\"channel\":\"Dr. Islam Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_0sfioIHqoA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLdbDarq2VLKbpcCowxrZ3khDhFpNYYPUT\"}}', '2026-02-05 16:51:44'),
('playlist:q:مشروع تخرج (2)', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLShLh8nc0YkL9w6mvMDBs0pElL_jVOXT5\",\"title\":\"Project Planning &amp; Management - كورس التخطيط و إدارة المشروعات\",\"channel\":\"Youssuf El-Farmawy يوسف الفرماوي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/f8VPTYfkBBI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLShLh8nc0YkL9w6mvMDBs0pElL_jVOXT5\"}}', '2026-02-05 16:51:45'),
('playlist:q:معالجة البيانات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\",\"title\":\"كورس تحليل البيانات للمبتدأين من الصفر\",\"channel\":\"داتا ساينس بالعربي .. Data Science \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOv1gE0ER3I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\"}}', '2026-02-05 16:51:43'),
('playlist:q:مقدمة في البرمجة', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"}}', '2026-02-05 16:51:21'),
('playlist:q:مقدمة في الذكاء الاصطناعي', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"}}', '2026-02-05 16:51:38'),
('playlist:q:مقدمة في علم البيانات والذكاء الاصطناعي', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"}}', '2026-02-05 16:51:51'),
('playlist:q:مهارات الاتصال والتواصل 1 (اللغة العربية)', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\",\"title\":\"Communications Skills مهارات التواصل الفعال\",\"channel\":\"Mohamed Fathy Ibrahim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6Hi9ASB55n0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\"}}', '2026-02-05 16:51:28'),
('playlist:q:مهارات الاتصال والتواصل 2 (اللغة الإنجليزية)', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\",\"title\":\"Communications Skills مهارات التواصل الفعال\",\"channel\":\"Mohamed Fathy Ibrahim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6Hi9ASB55n0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\"}}', '2026-02-05 16:51:29'),
('playlist:q:مهارات الحاسوب (إجباري)', '{\"ok\":true,\"playlist\":null}', '2026-02-05 16:51:31'),
('playlist:q:موضوعات خاصة في علم البيانات والذكاء الاصطناعي', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"}}', '2026-02-05 16:51:25'),
('playlist:q:نمذجة وتحليل أنظمة المعرفة', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"}}', '2026-02-05 16:51:41'),
('playlist:q:هندسة البيانات والتحليلات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLPCVzH419ffot8zn1vkcRKh8Wowj4yWuv\",\"title\":\"Data Engineering End to End Course - هندسة البيانات نظري وعملي\",\"channel\":\"Arabs Data Talks\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/kxV-39mqRhA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLPCVzH419ffot8zn1vkcRKh8Wowj4yWuv\"}}', '2026-02-05 16:51:41'),
('playlist:q:هياكل البيانات', '{\"ok\":true,\"playlist\":{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"}}', '2026-02-05 16:44:01'),
('playlists:q:أخلاقيات الإتصال والإنتاج|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLDYfSqIW22dEmTXZpMwWgcU0CHgpQRyzF\",\"title\":\"مبادئ محاسبة - الفرقة الأولى - 2025\",\"channel\":\"درس خصوصي - علم المحاسبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gCHqLjdBJro\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDYfSqIW22dEmTXZpMwWgcU0CHgpQRyzF\"}]}', '2026-02-05 17:20:53'),
('playlists:q:أخلاقيات علم البيانات والذكاء الاصطناعي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\",\"title\":\"مقرر مبادئ نظم المعلومات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ITJRTb6Yv1Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"}]}', '2026-02-18 17:52:07'),
('playlists:q:أخلاقيات ومخاطر وسياسات الأمن السيبراني|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\",\"title\":\"الامن السيبراني والهكر الاخلاقي | Ethical Hacking\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B_Xf9HAT-fc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\"},{\"playlistId\":\"PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\",\"title\":\"كورس الإختراق الأخلاقي | Ethical Hacking Course (from 0 to hero)\",\"channel\":\"الباشمبرمج | Hamed Esam\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/01heBtXmSIw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\"},{\"playlistId\":\"PLs-5OWGJ9l6WZ9vvt7VcNP1GjfIeImR0P\",\"title\":\"كورس تعلم Kali Linux من الصفر | تعلم اختبار الاختراق والأمن السيبراني\",\"channel\":\"NOUR_TECH\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/n_SCQSAEKI0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLs-5OWGJ9l6WZ9vvt7VcNP1GjfIeImR0P\"}]}', '2026-02-12 17:29:56'),
('playlists:q:أدوات هندسة البرمجيات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\",\"title\":\"Software Engineering in Arabic |دورة هندسة البرمجيات\",\"channel\":\"EDU US\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Io_IAwUi4Ck\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\"},{\"playlistId\":\"PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\",\"title\":\"كورس هندسة البرمجيات كامل  - Software engineering course\",\"channel\":\"محمدنور\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PdER1jZ9nVI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\"},{\"playlistId\":\"PLvGNfY-tFUN-JHyP9JzNP0oME_Ldc_3W4\",\"title\":\"كورس هندسة البرمجيات\",\"channel\":\"غريب الشيخ || Ghareeb Elshaikh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/tEwcoLbGNg8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLvGNfY-tFUN-JHyP9JzNP0oME_Ldc_3W4\"},{\"playlistId\":\"PL08ef9eJxtJZvt5BOsT46vN6kWnflVKH4\",\"title\":\"Software Engineering  | هندسة برمجيات\",\"channel\":\"المقررات المفتوحة - Open Courses\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/23wr24zdmQM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL08ef9eJxtJZvt5BOsT46vN6kWnflVKH4\"},{\"playlistId\":\"PLquXYvvn8Qk-Yb-ytydSIePeSwTtQmPSX\",\"title\":\"Software Engineering - هندسة البرمجيات\",\"channel\":\"Ahmed Alageed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Kbt0cfPxTO8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLquXYvvn8Qk-Yb-ytydSIePeSwTtQmPSX\"},{\"playlistId\":\"PLfJcnsgJ9GzKS_1wKLgMaRnxOiqLDeLRA\",\"title\":\"مقدمة في هندسة البرمجيات - Introduction to Software Engineering\",\"channel\":\"Mohammed Al-Shawwa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aZcUoPaEuoM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfJcnsgJ9GzKS_1wKLgMaRnxOiqLDeLRA\"}]}', '2026-02-12 17:17:01'),
('playlists:q:أساسيات التشفير|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLqAMdk4yWL4tEngBHnfJnfkjU6t2U7G7h\",\"title\":\"كورس علم التشفير\",\"channel\":\"Root Security (‫للخدمات التقنية‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/rzyQvgRRBEk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLqAMdk4yWL4tEngBHnfJnfkjU6t2U7G7h\"},{\"playlistId\":\"PL2JGmfJf7X9EOIFr5NI36IkdWcWYXm1Gr\",\"title\":\"تشفير البيانات Data Encryption\",\"channel\":\"الدكتور احمد العتوم - أمن سيبراني\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/vVOQxUF8TIM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL2JGmfJf7X9EOIFr5NI36IkdWcWYXm1Gr\"},{\"playlistId\":\"PLfd3S8S4nJobN_DNECuLB7TTGfELpNbfO\",\"title\":\"مادة التشفير Cryptography بشكل مفصل\",\"channel\":\"Eng Mohammed Majid\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/BDg7DlYSKpo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfd3S8S4nJobN_DNECuLB7TTGfELpNbfO\"},{\"playlistId\":\"PLgunb39twAsoH10BkLmU0zB2z3VI11CTg\",\"title\":\"Cryptography Course\",\"channel\":\"Cryptography - علم التشفير\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/o9JvF0v36r0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLgunb39twAsoH10BkLmU0zB2z3VI11CTg\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLd2pEan0ZG_Y1lTa4mXV1y0h-iJjINrqX\",\"title\":\"Information Security and Cryptography - شرح بالعربي\",\"channel\":\"iTeam Academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/J4_R_bysWAI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLd2pEan0ZG_Y1lTa4mXV1y0h-iJjINrqX\"}]}', '2026-02-12 17:29:56'),
('playlists:q:أساسيات تكنولوجيا المعلومات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLroS9tRyoUGpBwtwq8NUI85KnOQdVu7v7\",\"title\":\"أساسيات تكنولوجيا المعلومات | Fundamentals of Information Technology\",\"channel\":\"تكناوي دوت نيت\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9Gf5f8xvnis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLroS9tRyoUGpBwtwq8NUI85KnOQdVu7v7\"},{\"playlistId\":\"PLYVtwh_fWpmhxKjnz7SosS3mjU7vNq5-Y\",\"title\":\"اساسيات تكنولوجيا المعلومات || Fund\",\"channel\":\"Nashamah_ IT\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aNdcjSeWZdc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYVtwh_fWpmhxKjnz7SosS3mjU7vNq5-Y\"},{\"playlistId\":\"PL02NhKT84RvUonRDSINTfGy4kgjBowvs0\",\"title\":\"شرح كامل ومبسط لتكنولوجيا المعلومات والاتصالات IT\",\"channel\":\"مصطفى العاصى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Au7aD0fSHl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL02NhKT84RvUonRDSINTfGy4kgjBowvs0\"},{\"playlistId\":\"PLvGNfY-tFUN8D7uAQzkBfMkJ7XAFWSsIv\",\"title\":\"كورس أساسيات الكمبيوتر\",\"channel\":\"غريب الشيخ || Ghareeb Elshaikh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aQp1lt5NHsE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLvGNfY-tFUN8D7uAQzkBfMkJ7XAFWSsIv\"},{\"playlistId\":\"PL-bW-lO3g5rpADIIPzXQTKGlTKewSNUi8\",\"title\":\"دورة الدعم الفني Technical Support course\",\"channel\":\"م . يوسف شحادة الأديب\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/qn5fHGXt1Gw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL-bW-lO3g5rpADIIPzXQTKGlTKewSNUi8\"},{\"playlistId\":\"PLZZdF7TtQ_kpNyAslI5YvINc-i_RhPnbX\",\"title\":\"تعلم اساسيات الكمبيوتر للمبتدئين Computer for beginners\",\"channel\":\"Nology - نولوجي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Olm22l7gae4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZZdF7TtQ_kpNyAslI5YvINc-i_RhPnbX\"}]}', '2026-02-12 19:29:25'),
('playlists:q:أساسيات علم البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLWd4nYaF_Vx65cPZF_I2OpWERatzh5Gdj\",\"title\":\"أساسيات علم البيانات | Data Science Foundations\",\"channel\":\"Mustafa Othman\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WM2LiUDa4jE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWd4nYaF_Vx65cPZF_I2OpWERatzh5Gdj\"},{\"playlistId\":\"PLkL_THhSKsIkG3QHYGttilD92puCAg8pm\",\"title\":\"أساسيات علم البيانات للمبتدئين ISE 291\",\"channel\":\"بروجكتات حماد \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Mp7fv1vFspc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLkL_THhSKsIkG3QHYGttilD92puCAg8pm\"},{\"playlistId\":\"PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\",\"title\":\"كورس تحليل البيانات للمبتدأين من الصفر\",\"channel\":\"داتا ساينس بالعربي .. Data Science \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOv1gE0ER3I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\"},{\"playlistId\":\"PLc6fIfUYhBEsxApl3LvEQy7qINdimu75n\",\"title\":\"لاب اساسيات علم البيانات - Lab IDS\",\"channel\":\"Aïdea HU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SaqjDiZJosk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLc6fIfUYhBEsxApl3LvEQy7qINdimu75n\"},{\"playlistId\":\"PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\",\"title\":\"كورس تحليل البيانات بالاكسل\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cPWC-WNchJk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\"},{\"playlistId\":\"PLof3yw6ZFPFg5W3Z3sv6GzY6WvvXBqtnD\",\"title\":\"Data Analysis with Python تحليل البيانات باستخدام البايثون\",\"channel\":\"ALMUNTHIR SAFFAN (‫المنذر سفان‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AbqvCnIN7DM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLof3yw6ZFPFg5W3Z3sv6GzY6WvvXBqtnD\"}]}', '2026-02-13 18:24:22'),
('playlists:q:أساسيات هندسة البرمجيات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL9fwy3NUQKwZehIm7RGZM5M1SWdayxKRo\",\"title\":\"هندسة البرمجيات 1 SOFTWARE ENGINEERING\",\"channel\":\"eLearning Centre - IUG - Video Lectures \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Oj8LAsuGo5k\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9fwy3NUQKwZehIm7RGZM5M1SWdayxKRo\"},{\"playlistId\":\"PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\",\"title\":\"كورس هندسة البرمجيات كامل  - Software engineering course\",\"channel\":\"محمدنور\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PdER1jZ9nVI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\"},{\"playlistId\":\"PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\",\"title\":\"Software Engineering in Arabic |دورة هندسة البرمجيات\",\"channel\":\"EDU US\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Io_IAwUi4Ck\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\"},{\"playlistId\":\"PL8Fh9xEtNNzdXl3n8VMFcZb6zzO4JkEXw\",\"title\":\"Introduction to Software Engineering - مقدمة في هندسة البرمجيات\",\"channel\":\"Software engineering\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Jhe_CnwKG-8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8Fh9xEtNNzdXl3n8VMFcZb6zzO4JkEXw\"},{\"playlistId\":\"PLvGNfY-tFUN-JHyP9JzNP0oME_Ldc_3W4\",\"title\":\"كورس هندسة البرمجيات\",\"channel\":\"غريب الشيخ || Ghareeb Elshaikh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/tEwcoLbGNg8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLvGNfY-tFUN-JHyP9JzNP0oME_Ldc_3W4\"},{\"playlistId\":\"PL08ef9eJxtJZvt5BOsT46vN6kWnflVKH4\",\"title\":\"Software Engineering  | هندسة برمجيات\",\"channel\":\"المقررات المفتوحة - Open Courses\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/23wr24zdmQM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL08ef9eJxtJZvt5BOsT46vN6kWnflVKH4\"}]}', '2026-02-11 20:40:44'),
('playlists:q:أمن البيانات والبرمجيات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\",\"title\":\"احتراف أمن المعلومات - كورس كامل\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/j_rO-cNFYX0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\"},{\"playlistId\":\"PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\",\"title\":\"كورس امن المعلومات\",\"channel\":\"GeekHood\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-Y1SvVnwvhU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\"},{\"playlistId\":\"PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\",\"title\":\"الامن السيبراني والهكر الاخلاقي | Ethical Hacking\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B_Xf9HAT-fc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"},{\"playlistId\":\"PLwmxtpoM1Vi5ZCjBaC9bFWbYiWtSkpfUC\",\"title\":\"Cisco Introduction to Cyber security مقدمة الامن السيبرانى\",\"channel\":\"Ahmed abdelrazik\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4KtcomKXO-Q\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwmxtpoM1Vi5ZCjBaC9bFWbYiWtSkpfUC\"}]}', '2026-02-12 17:29:57'),
('playlists:q:أمن البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\",\"title\":\"احتراف أمن المعلومات - كورس كامل\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/j_rO-cNFYX0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\",\"title\":\"كورس امن المعلومات\",\"channel\":\"GeekHood\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-Y1SvVnwvhU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\"},{\"playlistId\":\"PL37D52B7714788190\",\"title\":\"Database 1 - المقرر النظرى - Fundamentals of Database Systems\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yLc0Yp5QZlU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL37D52B7714788190\"},{\"playlistId\":\"PL1L2vT37K_WH2qk-aD6ArzhwCWxcaZXMk\",\"title\":\"مدخل الى اساسيات امن المعلومات\",\"channel\":\"Abdullah AlQahtani\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Rywmef-9rAc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1L2vT37K_WH2qk-aD6ArzhwCWxcaZXMk\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"}]}', '2026-02-12 19:29:24'),
('playlists:q:أمن المعلومات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\",\"title\":\"احتراف أمن المعلومات - كورس كامل\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/j_rO-cNFYX0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\"},{\"playlistId\":\"PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\",\"title\":\"كورس امن المعلومات\",\"channel\":\"GeekHood\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-Y1SvVnwvhU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLgIQibkrZYz2ILJG4WLv_CT8zUgicRcwV\",\"title\":\"IT Security Course كورس أمن المعلومات\",\"channel\":\"Altechnologya مجلة التكنولوجيا\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-JxSXbo_f58\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLgIQibkrZYz2ILJG4WLv_CT8zUgicRcwV\"},{\"playlistId\":\"PL1L2vT37K_WH2qk-aD6ArzhwCWxcaZXMk\",\"title\":\"مدخل الى اساسيات امن المعلومات\",\"channel\":\"Abdullah AlQahtani\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Rywmef-9rAc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1L2vT37K_WH2qk-aD6ArzhwCWxcaZXMk\"},{\"playlistId\":\"PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\",\"title\":\"الامن السيبراني والهكر الاخلاقي | Ethical Hacking\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B_Xf9HAT-fc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\"}]}', '2026-02-11 20:40:45'),
('playlists:q:أمن النظم والبنية التحتية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"}]}', '2026-02-12 17:29:58'),
('playlists:q:إدارة المشروع البرمجي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLf2pF7JliMAOixsrWeIzwiHORjux_XFUt\",\"title\":\"كورس (دورة) PMP Course\",\"channel\":\"Mohamed Ghanem,PMP\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Y3nO9lJDmQA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLf2pF7JliMAOixsrWeIzwiHORjux_XFUt\"},{\"playlistId\":\"PLhiFu-f80eo85KqtETktWWr61fH41XQnD\",\"title\":\"ادارة المشاريع البرمجية Software Project Management\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/s49L2kx9DXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo85KqtETktWWr61fH41XQnD\"},{\"playlistId\":\"PLShLh8nc0YkL9w6mvMDBs0pElL_jVOXT5\",\"title\":\"Project Planning &amp; Management - كورس التخطيط و إدارة المشروعات\",\"channel\":\"Youssuf El-Farmawy يوسف الفرماوي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/f8VPTYfkBBI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLShLh8nc0YkL9w6mvMDBs0pElL_jVOXT5\"},{\"playlistId\":\"PLsVjgU4UTUtH6MYm9faxNUdlOZtXtWffk\",\"title\":\"P.M.P Course | ادارة المشروعات الاحترافية - الاصدار السادس\",\"channel\":\"Dr. Ahmed Hassan\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LN7q8SqyedM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLsVjgU4UTUtH6MYm9faxNUdlOZtXtWffk\"},{\"playlistId\":\"PLJAH9RHXVr9_iMKY87Q_0HUAy7-5kAZ98\",\"title\":\"دورة إدارة المشاريع الاحترافية PMP\",\"channel\":\"Faruk HARHARAH\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pIRRUxDCWrg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLJAH9RHXVr9_iMKY87Q_0HUAy7-5kAZ98\"},{\"playlistId\":\"PLh2CtFJf35nWueQ3Ig3Las847gdtqkv75\",\"title\":\"كورس ادارة المشروعات الاحترافية PMP\",\"channel\":\"Shehab Abo El Azm\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/MXUocLlQ-YM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2CtFJf35nWueQ3Ig3Las847gdtqkv75\"}]}', '2026-02-12 17:17:01'),
('playlists:q:إدارة وأمن الشبكات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"},{\"playlistId\":\"PLoy5ygSSAs_oB_sg41WxATkSJmYaq53lI\",\"title\":\"كورس شبكات من البداية حتى الاحتراف الجزء الاول\",\"channel\":\"Future Technology Ahmed Salah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Qy4LgwCB8a4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoy5ygSSAs_oB_sg41WxATkSJmYaq53lI\"},{\"playlistId\":\"PLpwHU9rNXAVurp2h2Jh-cd4-8XjkT5osu\",\"title\":\"Cisco CCNA 200-301 - Arabic - Ahmed Elhefny كورس تأسيس الشبكات للمبتدئين من الصفر للأحتراف\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4u3LVXDOkyw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVurp2h2Jh-cd4-8XjkT5osu\"},{\"playlistId\":\"PLAqaqJU4wzYU_6EIzVoxHghQILgyY--yf\",\"title\":\"2026 Cisco CCNA v1.1 200-301 || المنهاج العربي الكامل\",\"channel\":\"III-Networking\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/TrQljnE49-o\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLAqaqJU4wzYU_6EIzVoxHghQILgyY--yf\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLZmPGUyBFvUrvoa-NYzcUWFpxoZR11id_\",\"title\":\"CCNA 200-301 - Full Arabic course شرح كورس الجديد ٢٠٢٥ شبكات من الصفر للاحتراف بالعربي\",\"channel\":\"CloudKode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1yv0kjxKwes\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZmPGUyBFvUrvoa-NYzcUWFpxoZR11id_\"}]}', '2026-02-12 17:29:59'),
('playlists:q:اختبار الاختراق|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLZeCRehnm9LODnv6f8UjpqEcCRftzXQIK\",\"title\":\"دورة اختبار الاختراق من الصفر للاحتراف ( المستوى 1 )\",\"channel\":\"PT110\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/EUCszI3jq68\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZeCRehnm9LODnv6f8UjpqEcCRftzXQIK\"},{\"playlistId\":\"PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\",\"title\":\"كورس الإختراق الأخلاقي | Ethical Hacking Course (from 0 to hero)\",\"channel\":\"الباشمبرمج | Hamed Esam\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/01heBtXmSIw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\"},{\"playlistId\":\"PLs-5OWGJ9l6WZ9vvt7VcNP1GjfIeImR0P\",\"title\":\"كورس تعلم Kali Linux من الصفر | تعلم اختبار الاختراق والأمن السيبراني\",\"channel\":\"NOUR_TECH\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/n_SCQSAEKI0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLs-5OWGJ9l6WZ9vvt7VcNP1GjfIeImR0P\"},{\"playlistId\":\"PLroS9tRyoUGpAJPAgygxEP9imIZpkxFMi\",\"title\":\"دورة إختبار إختراق تطبيقات الويب - Web Pentesting Course\",\"channel\":\"تكناوي دوت نيت\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/clLoyQeEVUw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLroS9tRyoUGpAJPAgygxEP9imIZpkxFMi\"},{\"playlistId\":\"PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\",\"title\":\"الامن السيبراني والهكر الاخلاقي | Ethical Hacking\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B_Xf9HAT-fc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\"},{\"playlistId\":\"PLMcTNyjWGrLjVxreSauCvjGeLid69ik26\",\"title\":\"كورس بناء أدوات الاختراق بلغة بايثون\",\"channel\":\"cryptodome\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FZk9N0rvoYo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLMcTNyjWGrLjVxreSauCvjGeLid69ik26\"}]}', '2026-02-12 17:29:58'),
('playlists:q:اخلاقيات الحاسوب|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\",\"title\":\"كورس الإختراق الأخلاقي | Ethical Hacking Course (from 0 to hero)\",\"channel\":\"الباشمبرمج | Hamed Esam\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/01heBtXmSIw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PL8Fh9xEtNNzdXl3n8VMFcZb6zzO4JkEXw\",\"title\":\"Introduction to Software Engineering - مقدمة في هندسة البرمجيات\",\"channel\":\"Software engineering\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Jhe_CnwKG-8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8Fh9xEtNNzdXl3n8VMFcZb6zzO4JkEXw\"},{\"playlistId\":\"PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\",\"title\":\"مقرر مبادئ نظم المعلومات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ITJRTb6Yv1Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\"},{\"playlistId\":\"PLvGNfY-tFUN-JHyP9JzNP0oME_Ldc_3W4\",\"title\":\"كورس هندسة البرمجيات\",\"channel\":\"غريب الشيخ || Ghareeb Elshaikh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/tEwcoLbGNg8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLvGNfY-tFUN-JHyP9JzNP0oME_Ldc_3W4\"},{\"playlistId\":\"PLESN0xEC02Pbpo6fFtF_b7sKMfuOmN6pb\",\"title\":\"🎓 شرح منهج البرمجة الترم الاول للصف الأول الثانوي 2026\",\"channel\":\"اسماعيل محمد_esmail mohamed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/wy9njhm4_9c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLESN0xEC02Pbpo6fFtF_b7sKMfuOmN6pb\"}]}', '2026-02-11 20:40:44');
INSERT INTO `youtube_cache` (`cache_key`, `json`, `updated_at`) VALUES
('playlists:q:اساسيات تكنولوجيا المعلومات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLroS9tRyoUGpBwtwq8NUI85KnOQdVu7v7\",\"title\":\"أساسيات تكنولوجيا المعلومات | Fundamentals of Information Technology\",\"channel\":\"تكناوي دوت نيت\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9Gf5f8xvnis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLroS9tRyoUGpBwtwq8NUI85KnOQdVu7v7\"},{\"playlistId\":\"PLYVtwh_fWpmhxKjnz7SosS3mjU7vNq5-Y\",\"title\":\"اساسيات تكنولوجيا المعلومات || Fund\",\"channel\":\"Nashamah_ IT\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aNdcjSeWZdc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYVtwh_fWpmhxKjnz7SosS3mjU7vNq5-Y\"},{\"playlistId\":\"PL02NhKT84RvUonRDSINTfGy4kgjBowvs0\",\"title\":\"شرح كامل ومبسط لتكنولوجيا المعلومات والاتصالات IT\",\"channel\":\"مصطفى العاصى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Au7aD0fSHl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL02NhKT84RvUonRDSINTfGy4kgjBowvs0\"},{\"playlistId\":\"PLwmxtpoM1Vi72kjroDlkGc7txuSqt2Kv3\",\"title\":\"التحول الرقمى الاصدار الثالث (تكنولوجيا المعلومات ونظم التشغيل) باللغة العربية\",\"channel\":\"Ahmed abdelrazik\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Yx7YB1vC89g\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwmxtpoM1Vi72kjroDlkGc7txuSqt2Kv3\"},{\"playlistId\":\"PLZZdF7TtQ_kpNyAslI5YvINc-i_RhPnbX\",\"title\":\"تعلم اساسيات الكمبيوتر للمبتدئين Computer for beginners\",\"channel\":\"Nology - نولوجي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Olm22l7gae4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZZdF7TtQ_kpNyAslI5YvINc-i_RhPnbX\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PL-bW-lO3g5rpADIIPzXQTKGlTKewSNUi8\",\"title\":\"دورة الدعم الفني Technical Support course\",\"channel\":\"م . يوسف شحادة الأديب\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/qn5fHGXt1Gw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL-bW-lO3g5rpADIIPzXQTKGlTKewSNUi8\"},{\"playlistId\":\"PLvGNfY-tFUN8D7uAQzkBfMkJ7XAFWSsIv\",\"title\":\"كورس أساسيات الكمبيوتر\",\"channel\":\"غريب الشيخ || Ghareeb Elshaikh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aQp1lt5NHsE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLvGNfY-tFUN8D7uAQzkBfMkJ7XAFWSsIv\"},{\"playlistId\":\"PLBYJSOYbCXnDglV7JlmJKV3dwwVZEZ662\",\"title\":\"ICDL Course | شرح لكامل كورس ICDL\",\"channel\":\"Mohammad Jawish\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/3kjDExnUnRQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBYJSOYbCXnDglV7JlmJKV3dwwVZEZ662\"},{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\",\"title\":\"مقرر مبادئ نظم المعلومات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ITJRTb6Yv1Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\"},{\"playlistId\":\"PLH-n8YK76vIiDdOMRB-ylvns-_8Zl1euV\",\"title\":\"CompTIA A+\",\"channel\":\"Sameh Ramadan Tech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/zIpF33NCgrA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH-n8YK76vIiDdOMRB-ylvns-_8Zl1euV\"},{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"},{\"playlistId\":\"PLPn4eVPZKtrLbGOYXYClbXcdF2j58a7ZM\",\"title\":\"|| Fund  - أساسيات تكنولوجيا المعلومات  ||\",\"channel\":\"Smart Team\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cG-lIKZ019w\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLPn4eVPZKtrLbGOYXYClbXcdF2j58a7ZM\"},{\"playlistId\":\"PLWqI2o3n-uhdkH6fZv03PzACyrD2mULs0\",\"title\":\"كورس أساسيات الإلكترونيات من الصفر\",\"channel\":\"ابن تسلا\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yYuXHRBDdXg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWqI2o3n-uhdkH6fZv03PzACyrD2mULs0\"},{\"playlistId\":\"PLhvHGWDMvdoQXBy9RAGGszUhdx-5Ady97\",\"title\":\"الدليل الشامل للمبتدئين في إتقان أساسيات نظم المعلومات الجغرافية GIS\",\"channel\":\"م محمد دامس | Eng Mohammad S Dames\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/K7ZwE-7Qhgo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhvHGWDMvdoQXBy9RAGGszUhdx-5Ady97\"},{\"playlistId\":\"PL3FnSM5rs_BqMFevam6HiJVXisD6-93xB\",\"title\":\"الرخصة الدولية للحاسوب ادراك\",\"channel\":\"Kareem\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Km_p9Pn8fX0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3FnSM5rs_BqMFevam6HiJVXisD6-93xB\"},{\"playlistId\":\"PLnNCQeUCNSCwGLgsq5PaWLfhqRF0tEPYe\",\"title\":\"اساسيات تكنولوجيا المعلومات للمبتدئين IT Basics for beginners\",\"channel\":\"Ahmed Sameh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-VuPqn1NMrc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnNCQeUCNSCwGLgsq5PaWLfhqRF0tEPYe\"},{\"playlistId\":\"PLRtfJqT1hc31ZP4tr3ijypE_0T-4PE_kZ\",\"title\":\"تعلم لغة C من الصفر | c programming course\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8lGYaQXeviM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc31ZP4tr3ijypE_0T-4PE_kZ\"},{\"playlistId\":\"PLltPpA0o0tmyDULlYJGL5LVzI3pV0ETck\",\"title\":\"Computer Introduction - أساسيات الحاسب الآلي\",\"channel\":\"فارس خالد - Fares Khalid\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yLEqH5qgmbU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLltPpA0o0tmyDULlYJGL5LVzI3pV0ETck\"},{\"playlistId\":\"PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\",\"title\":\"C++ Programming Course Level 1 Basics By Arabic   كورس لغه برمجه سي بلس بلس المستوي الاول الاساسيات بالعربي\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oKA0aq4BdiY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\"},{\"playlistId\":\"PLSRx5jmWD9u1U8kqwkb43AP35Ssg7yPrS\",\"title\":\"Digital Logic Design | شرح عربي | تصميم منطقي رقمي\",\"channel\":\"si-manual\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/a0KqOJejO5Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSRx5jmWD9u1U8kqwkb43AP35Ssg7yPrS\"},{\"playlistId\":\"PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\",\"title\":\"تعلم الخوارزميات من الصفر || Learn algorithms from scratch\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GZHJcvzEWCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\"}]}', '2026-02-11 20:40:43'),
('playlists:q:استرجاع المعلومات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLRdABJkXXytBZEnoaSbhJhdLE2K8Nshca\",\"title\":\"دورة &quot;استرجاع المعلومات&quot; باللغة العربية - صيف ٢٠٢١\",\"channel\":\"Tamer Elsayed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LNK51UbY5BA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRdABJkXXytBZEnoaSbhJhdLE2K8Nshca\"},{\"playlistId\":\"PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\",\"title\":\"كورس شامل لتحليل البيانات – Excel &amp; Power BI خطوة بخطوة\",\"channel\":\"ElSearshgy - السيرشجى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/RMQLzz1rwBE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\"},{\"playlistId\":\"PLIuSWZNh8VmIEvyrRi8vGdvDPK3d4CwFA\",\"title\":\"نظام المتكامل بلس من شركة يمن سوفت\",\"channel\":\"قناة عماد عقلان التعليمية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/dfs2GyFW7AY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLIuSWZNh8VmIEvyrRi8vGdvDPK3d4CwFA\"},{\"playlistId\":\"PLfkRhUaDLeWiik5JXTaU4QlaWuPsynp7A\",\"title\":\"سلسلة دروس تعلم نظام ايليت خطوة بخطوه\",\"channel\":\"Elitesoftsys-ايليت_سوفت\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/rL3XP_IHlCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfkRhUaDLeWiik5JXTaU4QlaWuPsynp7A\"},{\"playlistId\":\"PL_qzovEogHYTecUxY7AD5tOljUPhV_Ree\",\"title\":\"كورس اماديوس كامل مجاني لحجز تذاكر الطيران\",\"channel\":\"ِAbobakrStudio\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AUxffIHRUzM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL_qzovEogHYTecUxY7AD5tOljUPhV_Ree\"},{\"playlistId\":\"PLIuSWZNh8VmJevLehaUrAK3Dv1Tqpjz3M\",\"title\":\"نظام الاونكس ERP  من شركة يمن سوفت\",\"channel\":\"قناة عماد عقلان التعليمية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/r0vKcUF23io\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLIuSWZNh8VmJevLehaUrAK3Dv1Tqpjz3M\"}]}', '2026-02-05 17:20:45'),
('playlists:q:الأنظمة التوصوية|max:6', '{\"ok\":true,\"items\":[]}', '2026-02-05 17:20:43'),
('playlists:q:البرمجة الشيئية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\",\"title\":\"Object Oriented Programming - شرح البرمجة كائنية التوجه\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fK2lLVqc8UY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\"},{\"playlistId\":\"PL1DUmTEdeA6Icttz-O9C3RPRF8R8Px5vk\",\"title\":\"Programming 2 - Object Oriented Programming With Java\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/M3Na5luSx50\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6Icttz-O9C3RPRF8R8Px5vk\"},{\"playlistId\":\"PLeIN5gmPLA4KiRN71_FhU1uQnc-TVZZ3z\",\"title\":\"كورس البرمجة الشيئية في بايثون || Python Object-Oriented Programming Course\",\"channel\":\"د. عبدالعزيز الصعيدي | Dr. Abdalaziz Elsaidy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/0q_x5oHyJO4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLeIN5gmPLA4KiRN71_FhU1uQnc-TVZZ3z\"},{\"playlistId\":\"PLCInYL3l2AagY7fFlhCrjpLiIFybW3yQv\",\"title\":\"Object-Oriented Programming JAVA in Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FaaM6uVbuJM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AagY7fFlhCrjpLiIFybW3yQv\"},{\"playlistId\":\"PLjTzpE6cvFako1dQqc4H5XbLIqGN57vbl\",\"title\":\"البرمجة الشيئية - OOP\",\"channel\":\"Lazy programmers\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OaOJyNNlt1E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjTzpE6cvFako1dQqc4H5XbLIqGN57vbl\"},{\"playlistId\":\"PL_5S2gz78CPrqVkMioeSY4FANiOeJqZUT\",\"title\":\"java object oriented programming ( OOP )\",\"channel\":\"Developers Academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4XCPSdMMkoc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL_5S2gz78CPrqVkMioeSY4FANiOeJqZUT\"}]}', '2026-02-05 17:20:51'),
('playlists:q:البرمجة الكينونية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\",\"title\":\"Object Oriented Programming - شرح البرمجة كائنية التوجه\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fK2lLVqc8UY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\"},{\"playlistId\":\"PL8DDsWuvM_EWYUvtpxALB0Xx7L_f_phXm\",\"title\":\"كورس البرمجة الكائينية |  C++ Object Oriented OOP\",\"channel\":\"اتعلم ببساطة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aJG-KnmfFxM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8DDsWuvM_EWYUvtpxALB0Xx7L_f_phXm\"},{\"playlistId\":\"PLCInYL3l2AagY7fFlhCrjpLiIFybW3yQv\",\"title\":\"Object-Oriented Programming JAVA in Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FaaM6uVbuJM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AagY7fFlhCrjpLiIFybW3yQv\"},{\"playlistId\":\"PL_5S2gz78CPrqVkMioeSY4FANiOeJqZUT\",\"title\":\"java object oriented programming ( OOP )\",\"channel\":\"Developers Academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4XCPSdMMkoc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL_5S2gz78CPrqVkMioeSY4FANiOeJqZUT\"},{\"playlistId\":\"PL1DUmTEdeA6KLEvIO0NyrkT91BVle8BOU\",\"title\":\"Programming 2 - Object Oriented Programming with C++\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6U6WtWG3NrA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6KLEvIO0NyrkT91BVle8BOU\"},{\"playlistId\":\"PL1DUmTEdeA6Icttz-O9C3RPRF8R8Px5vk\",\"title\":\"Programming 2 - Object Oriented Programming With Java\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/M3Na5luSx50\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6Icttz-O9C3RPRF8R8Px5vk\"}]}', '2026-02-13 18:24:23'),
('playlists:q:البيئة والمجتمع|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\",\"title\":\"محاضرات الاحصاء للمرحلة الاولى\",\"channel\":\"تقنيات انظمة الحاسوب المعهد التقني بعقوبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KT6ASzh_JXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\"},{\"playlistId\":\"PL9ZeZH9h9l9tfYRDs_cEQLCoM6VmMo0nH\",\"title\":\"دورة السلامة والصحة المهنية\",\"channel\":\"Prof.Dr.Mushtaq Al-Ebrahimy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4Y3G0yk5MVY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9ZeZH9h9l9tfYRDs_cEQLCoM6VmMo0nH\"},{\"playlistId\":\"PL0t32hAqUW2Lwf1bW1VVFdKSH5BfObwDh\",\"title\":\"إدارة المنظمات الغير حكومية\",\"channel\":\"Yasser Mohamed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q_es33C3ldA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL0t32hAqUW2Lwf1bW1VVFdKSH5BfObwDh\"},{\"playlistId\":\"PLIhV29MD3pXQ89Cfvk4dQNNBHdkOVwfFA\",\"title\":\"احياء مجهرية - شرح جديد 2024 🔥\",\"channel\":\"المحلل الطبي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FV3jNylR0dc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLIhV29MD3pXQ89Cfvk4dQNNBHdkOVwfFA\"},{\"playlistId\":\"PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\",\"title\":\"كورس مقدمه في الذكاء الاصطناعي - AI Introduction for Beginners\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lvuhqwLwJZ4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\"},{\"playlistId\":\"PLOPiW6b2K6wKkpFz-zA-IF_-nvXbiBAHY\",\"title\":\"الكيمياء الحيوية 1\",\"channel\":\"فريق نبض الطلابي دفعة 2028\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VBbP41h1OQk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLOPiW6b2K6wKkpFz-zA-IF_-nvXbiBAHY\"}]}', '2026-02-05 17:21:10'),
('playlists:q:البيانات الكبيرة والمفتوحة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLuNK096Q36cbGNuMXoaL05G1QIp_ZGkdx\",\"title\":\"Big Data Arabic course\",\"channel\":\"Huawei ICT Academy Egypt\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aYbLU--MUR8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuNK096Q36cbGNuMXoaL05G1QIp_ZGkdx\"},{\"playlistId\":\"PLxNoJq6k39G_m6DYjpz-V92DkaQEiXxkF\",\"title\":\"Big Data Engineering In Depth\",\"channel\":\"Garage Education\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/p_jl-gFinlA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxNoJq6k39G_m6DYjpz-V92DkaQEiXxkF\"},{\"playlistId\":\"PLRdABJkXXytDYPr5MIJgKsGtA_N2JxPuI\",\"title\":\"Big Data Analytics Spring 2021\",\"channel\":\"Tamer Elsayed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-BdUQ94AhlQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRdABJkXXytDYPr5MIJgKsGtA_N2JxPuI\"},{\"playlistId\":\"PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\",\"title\":\"كورس فلاتر كامل للمبتدئين من الصفر - تطوير وبرمجة تطبيقات الموبايل\",\"channel\":\"Ammar Alkhatib\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lRercKJaAes\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\"},{\"playlistId\":\"PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\",\"title\":\"محاضرات الاحصاء للمرحلة الاولى\",\"channel\":\"تقنيات انظمة الحاسوب المعهد التقني بعقوبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KT6ASzh_JXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\"},{\"playlistId\":\"PLfBzgS-uDB2Mscg5Zsuu-whjURMiTMnkj\",\"title\":\"المصفوفات شرح كامل\",\"channel\":\"Engineer Passion\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4ec201PNZr4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfBzgS-uDB2Mscg5Zsuu-whjURMiTMnkj\"}]}', '2026-02-13 18:24:25'),
('playlists:q:البيانات المرئية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\",\"title\":\"دورة برمجة قواعد البيانات من الصفر بلغة الفيجوال بسيك و SQL Server\",\"channel\":\"خالد السعداني - Khalid ESSAADANI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gaAkpRmZfhI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"},{\"playlistId\":\"PLF8OvnCBlEY0UU030QzRv506rculmtGQ2\",\"title\":\"Windows Forms تعلم برمجة سي شارب\",\"channel\":\"TheNewBaghdad (‫بغداد الجديدة‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VkUwJONdpvs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLF8OvnCBlEY0UU030QzRv506rculmtGQ2\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"},{\"playlistId\":\"PLZWE-jt5_26drpcl0glaLGsx3a_EJ6skL\",\"title\":\"دورة تحليل البيانات الاحترافية باستخدام برنامج Power BI\",\"channel\":\"Hani tech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/V3dVMNqrWFE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZWE-jt5_26drpcl0glaLGsx3a_EJ6skL\"},{\"playlistId\":\"PLknwEmKsW8OuTqUDaFRBiAViDZ5uI3VcE\",\"title\":\"كورس جافا سكريبت كامل | Javascript Tutorial\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PWuTLTFMtYw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OuTqUDaFRBiAViDZ5uI3VcE\"}]}', '2026-02-13 18:24:24'),
('playlists:q:التدريب العملي في مجال هندسة البرمجيات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\",\"title\":\"كورس هندسة البرمجيات كامل  - Software engineering course\",\"channel\":\"محمدنور\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PdER1jZ9nVI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\"},{\"playlistId\":\"PL0nlt2fVJXJOXl6y5kApre06l9Gtdy7UC\",\"title\":\"هندسة البرمجيات عملي مشروع كامل شرح عربي\",\"channel\":\"مبرمجون حول العالم\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lDPTV1IuchI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL0nlt2fVJXJOXl6y5kApre06l9Gtdy7UC\"},{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLww54WQ2wa5rOJ7FcXxi-CMNgmpybv7ei\",\"title\":\"دورة الالكترونيات العملية\",\"channel\":\"Walid Issa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/U_hw2RzNTI0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLww54WQ2wa5rOJ7FcXxi-CMNgmpybv7ei\"},{\"playlistId\":\"PLWqI2o3n-uhdkH6fZv03PzACyrD2mULs0\",\"title\":\"كورس أساسيات الإلكترونيات من الصفر\",\"channel\":\"ابن تسلا\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yYuXHRBDdXg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWqI2o3n-uhdkH6fZv03PzACyrD2mULs0\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"}]}', '2026-02-12 17:17:02'),
('playlists:q:التدريب الميداني|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLww54WQ2wa5rOJ7FcXxi-CMNgmpybv7ei\",\"title\":\"دورة الالكترونيات العملية\",\"channel\":\"Walid Issa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/U_hw2RzNTI0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLww54WQ2wa5rOJ7FcXxi-CMNgmpybv7ei\"},{\"playlistId\":\"PLBYJSOYbCXnDglV7JlmJKV3dwwVZEZ662\",\"title\":\"ICDL Course | شرح لكامل كورس ICDL\",\"channel\":\"Mohammad Jawish\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/3kjDExnUnRQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBYJSOYbCXnDglV7JlmJKV3dwwVZEZ662\"},{\"playlistId\":\"PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\",\"title\":\"كيف تتعلم الذكاء الاصطناعي بايثون - المسار الكامل دورة مجانية\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-9SqKQssejY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\"},{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"},{\"playlistId\":\"PLdbDarq2VLKbpcCowxrZ3khDhFpNYYPUT\",\"title\":\"كورس الاستقبال والطوارئ كاملEmergency course\",\"channel\":\"Dr. Islam Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_0sfioIHqoA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLdbDarq2VLKbpcCowxrZ3khDhFpNYYPUT\"}]}', '2026-02-13 18:24:26'),
('playlists:q:التربية الإسلامية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLH4NxcjPhgkl0fu6RFd8617GzpIwePssi\",\"title\":\"التربية الإسلامية - المستوى الأول\",\"channel\":\"برنامج أكاديمية زاد - Zad academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6amuu4FC2js\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH4NxcjPhgkl0fu6RFd8617GzpIwePssi\"},{\"playlistId\":\"PL1GJ09M00J5bf-SugF9a4RsPhkdmyPDW7\",\"title\":\"📘 تعليم الإسلام من الصفر ~ محمد بن شمس الدين\",\"channel\":\"محمد بن شمس الدين\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Blj1c1LpHqU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1GJ09M00J5bf-SugF9a4RsPhkdmyPDW7\"},{\"playlistId\":\"PLXzwwf5_wsiZN7_LIWN9eDYJt1S4SQYLe\",\"title\":\"دورة علوم القرآن كاملة ( الشرح على السبورة )\",\"channel\":\"باسم طاحون Bassem Tahoun\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OQ2qI8Oi30Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXzwwf5_wsiZN7_LIWN9eDYJt1S4SQYLe\"},{\"playlistId\":\"PLW4uuTD2FhciBLAxl3vQ_XB01mj5iQFQg\",\"title\":\"سلسلة دروس التربية الإسلامية - منهاج الصف الثالث الثانوي - طلاب الشهادة الثانوية - البكالوريا\",\"channel\":\"أبو مسلم العنداني Abo moslem Anadany\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/xJ8aNhMkz5w\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLW4uuTD2FhciBLAxl3vQ_XB01mj5iQFQg\"},{\"playlistId\":\"PL9VZX7QJ6DGCSA4r8UM3NYKQ8R5KZkW7p\",\"title\":\"دروس التربية الإسلامية \\/الصف الثالث متوسط\",\"channel\":\"الأستاذ محمد دحام العويسي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/dSMsYHmLZ2k\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9VZX7QJ6DGCSA4r8UM3NYKQ8R5KZkW7p\"},{\"playlistId\":\"PLMPqxr1nu2Zc_vfl_Kinxm2sJwUMjRucs\",\"title\":\"تربية الأبناء\",\"channel\":\"د.محمد خير الشعال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/hLwtl8GBgJs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLMPqxr1nu2Zc_vfl_Kinxm2sJwUMjRucs\"}]}', '2026-02-05 17:20:47'),
('playlists:q:التربية الوطنية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLOWDN6fgx7gRg7r-TqDDfRHqqvwSg4ZOY\",\"title\":\"الدورة المجانية في الرخصة المهنية التربوي ( العام ) مع المدربين: محمد محيسن + ماهر سلام + خالد عزمي\",\"channel\":\"منصة ثقة التعليمية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/s4SYPBArv2g\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLOWDN6fgx7gRg7r-TqDDfRHqqvwSg4ZOY\"},{\"playlistId\":\"PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\",\"title\":\"مبادئ الاحصاء\",\"channel\":\"Ahmed Ayish Aldehneen\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v-KEUmZD3fY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\"},{\"playlistId\":\"PLe_SAAskIE0jrx0c4gQ7tCxaBtaAqd4__\",\"title\":\"اختبار كفاءة الحاسوب\",\"channel\":\"تكنو Hd\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8BYjVuPbGF8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLe_SAAskIE0jrx0c4gQ7tCxaBtaAqd4__\"},{\"playlistId\":\"PLUKnfZDv-RdBLeK6_G28ws81LNAC45s9G\",\"title\":\"شرح تجميعات الرخصة المهنية  التربوي العام\",\"channel\":\"منصة إنجاز التعليمية \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gkR4kjnxVh0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUKnfZDv-RdBLeK6_G28ws81LNAC45s9G\"},{\"playlistId\":\"PL9gmfLRlkeUyolCau2wXP2uqOxBXDO7y2\",\"title\":\"كورس الإسعافات الأولية - SOS First Aid Course\",\"channel\":\"Alaa Makram Kharoub - علاء مكرم خروب\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PUha52d-0UQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9gmfLRlkeUyolCau2wXP2uqOxBXDO7y2\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"}]}', '2026-02-05 17:20:49'),
('playlists:q:التعلم الآلي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\",\"title\":\"تعلم الآلة بالعربي || Machine Learning in Arabic\",\"channel\":\"Elgouhary AI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/95RhuC30R5U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\"},{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLoOabVweB2r5dL0AVmuDbS54UvmCIlZsT\",\"title\":\"أساسيات تعلم الآلة | Basics of Machine Learning\",\"channel\":\"Omar Alharbi\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lUcZUCKDXbo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoOabVweB2r5dL0AVmuDbS54UvmCIlZsT\"},{\"playlistId\":\"PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\",\"title\":\"تعلم تصميم وتطوير المواقع 2022 دبلوم كامل مع 100 مشروع كامل\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WWn8tOHjZbw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\"},{\"playlistId\":\"PLhiFu-f80eo9LLLEPsWhuVL3ouYhamXVg\",\"title\":\"الدورة التعليمية الشاملة لتعلم الاله Machine Learning Full Course with Python:\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/0I-JjPjtw4I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9LLLEPsWhuVL3ouYhamXVg\"},{\"playlistId\":\"PLhx4zaYkEjI9kTHTV34iQqRcFoO8RvuxV\",\"title\":\"Introduction to Artificial Neural Networks and Deep Learning Course Arabic التعلم العميق بعمق والذكاء الاصطناعي مع الشبكات العصبية الاصطناعية عربى\",\"channel\":\"Hashim EduTech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/F4lF-uay-Ns\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhx4zaYkEjI9kTHTV34iQqRcFoO8RvuxV\"}]}', '2026-02-13 18:25:58'),
('playlists:q:التعلم العميق|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLUQ5y3YcMclGp5zaZSgPMd-JbR39fWSZs\",\"title\":\"Deep Learning:  دورة التعلم العميق بالعربي\",\"channel\":\"منصة أكاديما للذكاء الأصنطاعي وعلوم الحاسب\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WQowTGI4QFg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUQ5y3YcMclGp5zaZSgPMd-JbR39fWSZs\"},{\"playlistId\":\"PLH0em1f_fBoQCvAIJeWJtqSVoE3quQXue\",\"title\":\"كورس التعلم العميق - deep learning course\",\"channel\":\"TatworX\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/iUGV8qBQtCc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH0em1f_fBoQCvAIJeWJtqSVoE3quQXue\"},{\"playlistId\":\"PL6-3IRz2XF5X-lzMZdmkvGAx1a3kIm7_I\",\"title\":\"الدورة الكاملة للتعلم العميق Deep Learning , Computer Vision, NLP\",\"channel\":\"Hesham Asem\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/p93xuOXP1jI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL6-3IRz2XF5X-lzMZdmkvGAx1a3kIm7_I\"},{\"playlistId\":\"PL6-3IRz2XF5UiBoBDgeu5T3TyOIrgQ3r9\",\"title\":\"19 كورس التعلم العميق بالكامل\",\"channel\":\"Hesham Asem\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/UKk3K0g7cP8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL6-3IRz2XF5UiBoBDgeu5T3TyOIrgQ3r9\"},{\"playlistId\":\"PLhx4zaYkEjI9kTHTV34iQqRcFoO8RvuxV\",\"title\":\"Introduction to Artificial Neural Networks and Deep Learning Course Arabic التعلم العميق بعمق والذكاء الاصطناعي مع الشبكات العصبية الاصطناعية عربى\",\"channel\":\"Hashim EduTech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/F4lF-uay-Ns\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhx4zaYkEjI9kTHTV34iQqRcFoO8RvuxV\"},{\"playlistId\":\"PLH0em1f_fBoQ2sIU6oZXuXOEhZ8hLF_m2\",\"title\":\"التعلم العميق - Deep Learning\",\"channel\":\"TatworX\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/UWohkVbZpY0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH0em1f_fBoQ2sIU6oZXuXOEhZ8hLF_m2\"}]}', '2026-02-12 19:29:27'),
('playlists:q:التنقيب عن البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLdUHNiwJgn84ltB6_BypNnXC7HweUfYoF\",\"title\":\"Data Mining || Abdulrahman Ihsan\",\"channel\":\"Etihad IT HU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/i0la0uLPOLQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLdUHNiwJgn84ltB6_BypNnXC7HweUfYoF\"},{\"playlistId\":\"PL31yBkw48nf1MD0SvcrSEgEiXw8gLSerC\",\"title\":\"DSAI 2103 - Data mining (Lab) - التنقيب عن البيانات (عملي) - Lab\",\"channel\":\"Mahmoud Sammour\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fmiql0DX9hE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL31yBkw48nf1MD0SvcrSEgEiXw8gLSerC\"},{\"playlistId\":\"PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\",\"title\":\"كورس تحليل البيانات للمبتدأين من الصفر\",\"channel\":\"داتا ساينس بالعربي .. Data Science \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOv1gE0ER3I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\"},{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PLCVy8dqNAIgnrGoeg9_JHyncFWjpn-mbH\",\"title\":\"كورس تحليل البيانات\",\"channel\":\"كويري بلس- ماهر المحمد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/5lCRC_JnQpA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCVy8dqNAIgnrGoeg9_JHyncFWjpn-mbH\"},{\"playlistId\":\"PLF8OvnCBlEY3a1pbPrE6fvNuV3qi-6KRf\",\"title\":\"Data Structure and Algorithms analysis || الخوارزميات وهيكلة البيانات\",\"channel\":\"TheNewBaghdad (‫بغداد الجديدة‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/eNOZjwJVIxg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLF8OvnCBlEY3a1pbPrE6fvNuV3qi-6KRf\"},{\"playlistId\":\"PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\",\"title\":\"كورس شامل لتحليل البيانات – Excel &amp; Power BI خطوة بخطوة\",\"channel\":\"ElSearshgy - السيرشجى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/RMQLzz1rwBE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLZWE-jt5_26drpcl0glaLGsx3a_EJ6skL\",\"title\":\"دورة تحليل البيانات الاحترافية باستخدام برنامج Power BI\",\"channel\":\"Hani tech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/V3dVMNqrWFE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZWE-jt5_26drpcl0glaLGsx3a_EJ6skL\"},{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"},{\"playlistId\":\"PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\",\"title\":\"مبادئ الاحصاء\",\"channel\":\"Ahmed Ayish Aldehneen\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v-KEUmZD3fY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\"},{\"playlistId\":\"PLxIvc-MGOs6ilU3FPyJr3T-VkufZy2NGi\",\"title\":\"Statistical Analysis | التحليل الإحصائي\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/CL-fAMiysnk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6ilU3FPyJr3T-VkufZy2NGi\"},{\"playlistId\":\"PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\",\"title\":\"مقرر مبادئ نظم المعلومات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ITJRTb6Yv1Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\"},{\"playlistId\":\"PLPMoKzWbC0wUB7y4VsbkDoTk0gX9Wnrr_\",\"title\":\"كورس مدخل إلى تحليل البيانات باستخدام إكسل Excel\",\"channel\":\"Abdlraoof Masarani\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/p5_x4Te3LBs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLPMoKzWbC0wUB7y4VsbkDoTk0gX9Wnrr_\"},{\"playlistId\":\"PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\",\"title\":\"تعلم الخوارزميات من الصفر || Learn algorithms from scratch\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GZHJcvzEWCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\"},{\"playlistId\":\"PLof3yw6ZFPFgJ64ioThh8IR_X9Rc6i0Zk\",\"title\":\"دورة برنامج الأكسيس Microsoft Access\",\"channel\":\"ALMUNTHIR SAFFAN (‫المنذر سفان‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/I5B2G1At_lA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLof3yw6ZFPFgJ64ioThh8IR_X9Rc6i0Zk\"},{\"playlistId\":\"PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\",\"title\":\"كيف تتعلم الذكاء الاصطناعي بايثون - المسار الكامل دورة مجانية\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-9SqKQssejY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\"},{\"playlistId\":\"PL9GrBMsvivVUzs49w_nFdKu74AtwFdH5H\",\"title\":\"Research 101 أساسيات البحث العلمي\",\"channel\":\"Muhamed Elnaggar\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GIebqBeWNF0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9GrBMsvivVUzs49w_nFdKu74AtwFdH5H\"},{\"playlistId\":\"PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\",\"title\":\"دورة برمجة قواعد البيانات من الصفر بلغة الفيجوال بسيك و SQL Server\",\"channel\":\"خالد السعداني - Khalid ESSAADANI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gaAkpRmZfhI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\"},{\"playlistId\":\"PLi5-9SH2ORG1kDx2UU1oKaFZRaLJn9xqv\",\"title\":\"دورة برنامج ArcGIS Pro من البداية للاحتراف\",\"channel\":\"GIS 4 YOU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Ti3c7uEd-5o\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLi5-9SH2ORG1kDx2UU1oKaFZRaLJn9xqv\"},{\"playlistId\":\"PL5zwAWCFLn1uBTmik_yc18I0XGwnfb4h3\",\"title\":\"دورة متكاملة في برنامج التحليل الإحصائي SPSS.26\",\"channel\":\"Mohamad Dames -  محمد دامس\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GsISCQtyIj8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL5zwAWCFLn1uBTmik_yc18I0XGwnfb4h3\"},{\"playlistId\":\"PLhiFu-f80eo--m0YnPpiiWGd4mq-4V2rY\",\"title\":\"Data Structure هياكل البيانات\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/O2llLrg7Ys8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo--m0YnPpiiWGd4mq-4V2rY\"},{\"playlistId\":\"PL4lTDSNRMsOl0HiZX_dqqCSJVBOnZhU7i\",\"title\":\"كورس Microsoft Excel المتقدم – احتراف الإكسل 2025\",\"channel\":\"Alaa Essam - آلاء عصام\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4xw25BjIENE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL4lTDSNRMsOl0HiZX_dqqCSJVBOnZhU7i\"},{\"playlistId\":\"PL9GrBMsvivVWZ2Ztg5VXzKhxVF2F1q5Ld\",\"title\":\"Biostatistics Basics Series 101 - سلسلة  أساسيات الإحصاء الحيوي\",\"channel\":\"Muhamed Elnaggar\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/F7fcKHDnAz4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9GrBMsvivVWZ2Ztg5VXzKhxVF2F1q5Ld\"},{\"playlistId\":\"PL1DUmTEdeA6JlommmGP5wicYLxX5PVCQt\",\"title\":\"C++ Data Structures - تراكيب البيانات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/jGP19W5IObA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6JlommmGP5wicYLxX5PVCQt\"}]}', '2026-02-12 19:47:51'),
('playlists:q:الذكاء الاصطناعي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\",\"title\":\"كيف تتعلم الذكاء الاصطناعي بايثون - المسار الكامل دورة مجانية\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-9SqKQssejY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\"},{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLH0em1f_fBoT1xlF8F4bD2CQd--FZ3RR8\",\"title\":\"كورس الذكاء الاصطناعي و تعلم الالة\",\"channel\":\"TatworX\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WI9-woIhyL0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH0em1f_fBoT1xlF8F4bD2CQd--FZ3RR8\"},{\"playlistId\":\"PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\",\"title\":\"محاضرات ذكاء اصطناعي |  Artificial Intelligence (AI) Lectures\",\"channel\":\"محمد داود Mohammad Dawoud\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/H5WUwwivEaI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\"},{\"playlistId\":\"PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\",\"title\":\"كورس مقدمه في الذكاء الاصطناعي - AI Introduction for Beginners\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lvuhqwLwJZ4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\"},{\"playlistId\":\"PLuXYxBK7fwrabHiikIDO1bPuOXKhJeFBD\",\"title\":\"دورة الذكاء الاصطناعي\",\"channel\":\"By Osman\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FAxJ5SYLo-c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXYxBK7fwrabHiikIDO1bPuOXKhJeFBD\"}]}', '2026-02-12 19:29:28'),
('playlists:q:الروبوتات المتنقلة الذكية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL1xqdqtd6lFVtcpYt3O-xoRYlxx413ywc\",\"title\":\"Robots forward kinematics روبوت شرح بالعربي\",\"channel\":\"Instrument Tech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gQlaatNGYAE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1xqdqtd6lFVtcpYt3O-xoRYlxx413ywc\"},{\"playlistId\":\"PLeIN5gmPLA4ISbZFi0gYBmfV1oKbiuVbk\",\"title\":\"دورة برمجة الروبوت والأردوينو 2024 || Robot and Arduino programming course 2024\",\"channel\":\"د. عبدالعزيز الصعيدي | Dr. Abdalaziz Elsaidy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AhBBJZxLJ10\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLeIN5gmPLA4ISbZFi0gYBmfV1oKbiuVbk\"},{\"playlistId\":\"PLJsW6lND7s7PiwNb_KRpXckEcvPtFmi2p\",\"title\":\"كورس برمجة الروبوتات صيف 2023\",\"channel\":\"Sherif Fathy Mohamed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fRNyE7Ria5M\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLJsW6lND7s7PiwNb_KRpXckEcvPtFmi2p\"},{\"playlistId\":\"PL0weu5Z5uuDIl9B4PJ8sDDYPvc0Zpy-mZ\",\"title\":\"تعلم برمجة الروبوت في 10 دقائق\",\"channel\":\"Arabic Robotics\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/HMoH6b7Qu8A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL0weu5Z5uuDIl9B4PJ8sDDYPvc0Zpy-mZ\"}]}', '2026-02-12 19:47:53'),
('playlists:q:الريادة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLo8CHEfAmimQUGvrwnIwNP0znU9ZiR6Ng\",\"title\":\"سلسلة ريادة الأعمال والبيزنس\",\"channel\":\"علم وعمل\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/G87Su7LO7C0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLo8CHEfAmimQUGvrwnIwNP0znU9ZiR6Ng\"},{\"playlistId\":\"PL_2IpQXzdCUrkBKF3ojaHJu3__msiooan\",\"title\":\"كورس ريادة الأعمال Entrepreneurship\",\"channel\":\"غاوي علم\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/XZgyGpgFKYU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL_2IpQXzdCUrkBKF3ojaHJu3__msiooan\"},{\"playlistId\":\"PLoURjA1UEuTzB-xsuAhe1rhR2Q6ClQ4vo\",\"title\":\"مقرر ريادة الاعمال\",\"channel\":\"جامعة العلوم والتكنولوجيا التعليم الالكتروني والتعلم عن بُعد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/x8GP3_--Ao8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoURjA1UEuTzB-xsuAhe1rhR2Q6ClQ4vo\"},{\"playlistId\":\"PLqMvtJL6jLWzxFrxlzKaNjn6mX8TmRhZL\",\"title\":\"اساسيات ريادة الاعمال\",\"channel\":\"RISEUP\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ZgP6Agz91Ew\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLqMvtJL6jLWzxFrxlzKaNjn6mX8TmRhZL\"},{\"playlistId\":\"PLxWhTzaqFYtihsjXUdvl-jaNXhGBKV8WF\",\"title\":\"كتب ريادة الأعمال\",\"channel\":\"ReadTube - جيل يقرأ\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mRyvqqg1SbY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxWhTzaqFYtihsjXUdvl-jaNXhGBKV8WF\"},{\"playlistId\":\"PLAPt2chT6RMaNtaghxsyNmdujWjqP_FGX\",\"title\":\"تعلم الفراسة و قراءة الوجه الحديثة\",\"channel\":\"Ahmed Reyad\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/UicN_CRIKoU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLAPt2chT6RMaNtaghxsyNmdujWjqP_FGX\"}]}', '2026-02-05 17:20:50'),
('playlists:q:الطرق الإحصائية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLxIvc-MGOs6ilU3FPyJr3T-VkufZy2NGi\",\"title\":\"Statistical Analysis | التحليل الإحصائي\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/CL-fAMiysnk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6ilU3FPyJr3T-VkufZy2NGi\"},{\"playlistId\":\"PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\",\"title\":\"مبادئ الاحصاء\",\"channel\":\"Ahmed Ayish Aldehneen\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v-KEUmZD3fY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\"},{\"playlistId\":\"PL5zwAWCFLn1uBTmik_yc18I0XGwnfb4h3\",\"title\":\"دورة متكاملة في برنامج التحليل الإحصائي SPSS.26\",\"channel\":\"Mohamad Dames -  محمد دامس\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GsISCQtyIj8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL5zwAWCFLn1uBTmik_yc18I0XGwnfb4h3\"},{\"playlistId\":\"PLVpJGVBmPnw3eRSzC90oXA6gBcG-nEYIe\",\"title\":\"Foundations of Statistical Analysis (Full College Course)\\/تعلم اسس علم الاحصاء\",\"channel\":\"Professor X\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Fy2B6a8op8g\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLVpJGVBmPnw3eRSzC90oXA6gBcG-nEYIe\"},{\"playlistId\":\"PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\",\"title\":\"محاضرات الاحصاء للمرحلة الاولى\",\"channel\":\"تقنيات انظمة الحاسوب المعهد التقني بعقوبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KT6ASzh_JXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\"},{\"playlistId\":\"PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\",\"title\":\"كورس تحليل البيانات للمبتدأين من الصفر\",\"channel\":\"داتا ساينس بالعربي .. Data Science \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOv1gE0ER3I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\"}]}', '2026-02-13 18:26:00'),
('playlists:q:القرصنة الأخلاقية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\",\"title\":\"كورس الإختراق الأخلاقي | Ethical Hacking Course (from 0 to hero)\",\"channel\":\"الباشمبرمج | Hamed Esam\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/01heBtXmSIw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\"},{\"playlistId\":\"PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\",\"title\":\"الامن السيبراني والهكر الاخلاقي | Ethical Hacking\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B_Xf9HAT-fc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\"},{\"playlistId\":\"PLLlr6jKKdyK0S3btGkOyBUu8XyHXXJrCs\",\"title\":\"كورس الهاكر الأخلاقي للمهندس ياسر رمزي\",\"channel\":\"Information Technology\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/eUPk9Mnw6Zw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLLlr6jKKdyK0S3btGkOyBUu8XyHXXJrCs\"},{\"playlistId\":\"PLs-5OWGJ9l6WZ9vvt7VcNP1GjfIeImR0P\",\"title\":\"كورس تعلم Kali Linux من الصفر | تعلم اختبار الاختراق والأمن السيبراني\",\"channel\":\"NOUR_TECH\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/n_SCQSAEKI0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLs-5OWGJ9l6WZ9vvt7VcNP1GjfIeImR0P\"},{\"playlistId\":\"PLCecIlPD9Hi6QhbUcInStu4roKGvP0Oul\",\"title\":\"كورس الهاكر الأخلاقي Ethical Hacking course (CEH)\",\"channel\":\"Cyber Camps - Eng:Ahmed Sayed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/tdEMaXfkD20\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCecIlPD9Hi6QhbUcInStu4roKGvP0Oul\"},{\"playlistId\":\"PLLKT__MCUeixqHJ1TRqrHsEd6_EdEvo47\",\"title\":\"Full-Length Hacking Courses\",\"channel\":\"The Cyber Mentor\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/3FNYvj2U0HM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLLKT__MCUeixqHJ1TRqrHsEd6_EdEvo47\"}]}', '2026-02-12 17:30:00');
INSERT INTO `youtube_cache` (`cache_key`, `json`, `updated_at`) VALUES
('playlists:q:اللغة الإنجليزية لتكنولوجيا المعلومات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\",\"title\":\"مقرر مبادئ نظم المعلومات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ITJRTb6Yv1Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\"},{\"playlistId\":\"PL02NhKT84RvUonRDSINTfGy4kgjBowvs0\",\"title\":\"شرح كامل ومبسط لتكنولوجيا المعلومات والاتصالات IT\",\"channel\":\"مصطفى العاصى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Au7aD0fSHl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL02NhKT84RvUonRDSINTfGy4kgjBowvs0\"},{\"playlistId\":\"PLBYJSOYbCXnDglV7JlmJKV3dwwVZEZ662\",\"title\":\"ICDL Course | شرح لكامل كورس ICDL\",\"channel\":\"Mohammad Jawish\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/3kjDExnUnRQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBYJSOYbCXnDglV7JlmJKV3dwwVZEZ662\"},{\"playlistId\":\"PLroS9tRyoUGpBwtwq8NUI85KnOQdVu7v7\",\"title\":\"أساسيات تكنولوجيا المعلومات | Fundamentals of Information Technology\",\"channel\":\"تكناوي دوت نيت\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9Gf5f8xvnis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLroS9tRyoUGpBwtwq8NUI85KnOQdVu7v7\"},{\"playlistId\":\"PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\",\"title\":\"كورس بايثون - تعلم بايثون من الصفر للإحتراف\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Do34NKMq80c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"}]}', '2026-02-12 17:30:00'),
('playlists:q:اللغة التركية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL5wP1cVEYkPVm7QvxJtQH-B-qBmzPwFvs\",\"title\":\"تعلم اللغة التركية من الصفر مع زينب\",\"channel\":\"زينب مصري - Zeynep Masri\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Kl9pTukIGxU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL5wP1cVEYkPVm7QvxJtQH-B-qBmzPwFvs\"},{\"playlistId\":\"PLhGsy5f_c71DB156kUr5_i0O1gKwZJHS-\",\"title\":\"تعلم التركية من الصفر مستوى أول (للمبتدئين)\",\"channel\":\"YAPARSIN تعلم التركية مع\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/2mkgZnt9u5g\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhGsy5f_c71DB156kUr5_i0O1gKwZJHS-\"},{\"playlistId\":\"PLxCFn5-t8kLUU2LnDePYwLGJBA91K-K9T\",\"title\":\"دروس تعليم اللغة التركية\",\"channel\":\"Taleek\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/XsDVeJshjdw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxCFn5-t8kLUU2LnDePYwLGJBA91K-K9T\"},{\"playlistId\":\"PLtljVoPLt897v0Xfqlxm5LNlnRnA_jhI3\",\"title\":\"دروس تعلم اللغة التركية\",\"channel\":\"Walid Salahuddin - تعلم التركية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VcwPSxud-cc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLtljVoPLt897v0Xfqlxm5LNlnRnA_jhI3\"},{\"playlistId\":\"PLCWVmuKuKBbCu_sFh6KrPCOFoXeZfVV7z\",\"title\":\"سلسلة المحادثة باللغة التركية 🇹🇷\",\"channel\":\"Sumaya Mohamed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v555PlHbXJ4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCWVmuKuKBbCu_sFh6KrPCOFoXeZfVV7z\"},{\"playlistId\":\"PLbbGd1auNg7S5_KACR-jTFbBo3kCEBqei\",\"title\":\"دورة اللغة التركية مع اسا\",\"channel\":\"ASA\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/0ctEq1nejjw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbbGd1auNg7S5_KACR-jTFbBo3kCEBqei\"}]}', '2026-02-05 17:21:15'),
('playlists:q:اللغة الصينية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLxCFn5-t8kLULjilHcetdb9EZJlV3rRcV\",\"title\":\"دروس تعليم اللغة الصينية\",\"channel\":\"Taleek\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FJQ6EootB1U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxCFn5-t8kLULjilHcetdb9EZJlV3rRcV\"},{\"playlistId\":\"PL9_fyJVCwp0Qp-1AY3_uQUIeQ2c4C2csg\",\"title\":\"تعلم اللغة الصينية\",\"channel\":\"Madrasetna Breaktime\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/iYozrd-flzE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9_fyJVCwp0Qp-1AY3_uQUIeQ2c4C2csg\"},{\"playlistId\":\"PLa6a9WK66X-dDljf9SAGGjNq3dmsuUEz6\",\"title\":\"تعلم اللغة الصينية  مع   المعلمة حسناء الصينية\",\"channel\":\"التعليم الذكي للغة الصينية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/sHYzLpwdUJI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLa6a9WK66X-dDljf9SAGGjNq3dmsuUEz6\"},{\"playlistId\":\"PLf-lA7T7wExow1laC-3xkTuA8SgxBQ6Pk\",\"title\":\"تعلم اللغة الصينية من الصفر للمبتدئين (HSK 1)\",\"channel\":\"Chinese with Yanyan\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FNnPrBvJ6q8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLf-lA7T7wExow1laC-3xkTuA8SgxBQ6Pk\"},{\"playlistId\":\"PLA5oPvb-kCa3xQvpwfTt36-9jy6I8dVb5\",\"title\":\"تعلم اللغة الصينية\",\"channel\":\"FaReS  法利\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oEzozHuhQk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLA5oPvb-kCa3xQvpwfTt36-9jy6I8dVb5\"},{\"playlistId\":\"PLBzLRV1Di0Mkfg61D2Wprp_D_62yfDTUW\",\"title\":\"تعلم اللغة الصينية لإبراهيم عادل\",\"channel\":\"D M\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FJQ6EootB1U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBzLRV1Di0Mkfg61D2Wprp_D_62yfDTUW\"}]}', '2026-02-05 17:21:16'),
('playlists:q:اللغة العربية (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLLbiI--L9-ereChdYMwCJNI25rUj1l6en\",\"title\":\"دورة التأسيس في قواعد اللغة العربية\",\"channel\":\"الأستاذ إبراهيم حجاج\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mYDj-SDIOW0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLLbiI--L9-ereChdYMwCJNI25rUj1l6en\"},{\"playlistId\":\"PLKen8r7qRcuzVYdRgumhb7uTsr_ezCuxR\",\"title\":\"تعليم اللغة العربية من الصفر حتى الاحتراف\",\"channel\":\"قناة السلام العالمية MWT\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AEHJqatke6E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLKen8r7qRcuzVYdRgumhb7uTsr_ezCuxR\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"},{\"playlistId\":\"PL0znTfrLcc6lVtYQoM3QSQYB39rz4YVq_\",\"title\":\"النحو المستوى الأول للشيخ أدهم العاسمي\",\"channel\":\"مؤسسة زاد الرحيل الشرعية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/zDdzszXxlfs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL0znTfrLcc6lVtYQoM3QSQYB39rz4YVq_\"},{\"playlistId\":\"PLBf-TtlsnewboAoxjXfiAHVZJCEQoz6pL\",\"title\":\"نحو من الصفر 🎓 كورس كامل في النحو والإعراب\",\"channel\":\"بتاع عربي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AnfIS_8Al_s\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBf-TtlsnewboAoxjXfiAHVZJCEQoz6pL\"},{\"playlistId\":\"PLtrKA8JbD9_Af6BZQeG3EtSxDQeTWcHgB\",\"title\":\"دورة اساسيات قواعد اللغة العربية 2026\",\"channel\":\"الأستاذ هشام المعموري\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/hjsm_Rub2Dg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLtrKA8JbD9_Af6BZQeG3EtSxDQeTWcHgB\"},{\"playlistId\":\"PLpwHU9rNXAVurp2h2Jh-cd4-8XjkT5osu\",\"title\":\"Cisco CCNA 200-301 - Arabic - Ahmed Elhefny كورس تأسيس الشبكات للمبتدئين من الصفر للأحتراف\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4u3LVXDOkyw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVurp2h2Jh-cd4-8XjkT5osu\"},{\"playlistId\":\"PLbcnyqCpqMhY_jMIGj1IOp6-1qHh9Nld_\",\"title\":\"كورس كامل لتعليم الإملاء  من البداية\",\"channel\":\"مدرسة حمدي معارك التعليمية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/f8h5BKyf29M\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbcnyqCpqMhY_jMIGj1IOp6-1qHh9Nld_\"},{\"playlistId\":\"PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\",\"title\":\"C++ Programming Course Level 1 Basics By Arabic   كورس لغه برمجه سي بلس بلس المستوي الاول الاساسيات بالعربي\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oKA0aq4BdiY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\"},{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"},{\"playlistId\":\"PLknwEmKsW8OuN04Odt2sJqt4aAnkp-iYA\",\"title\":\"كورس html and css كامل بالعربي\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OuN04Odt2sJqt4aAnkp-iYA\"},{\"playlistId\":\"PLBYJSOYbCXnDglV7JlmJKV3dwwVZEZ662\",\"title\":\"ICDL Course | شرح لكامل كورس ICDL\",\"channel\":\"Mohammad Jawish\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/3kjDExnUnRQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBYJSOYbCXnDglV7JlmJKV3dwwVZEZ662\"},{\"playlistId\":\"PL1DUmTEdeA6K7rdxKiWJq6JIxTvHalY8f\",\"title\":\"Java Programming For Beginners - Course 1- بالعربى\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FbviMTJ_vP8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6K7rdxKiWJq6JIxTvHalY8f\"},{\"playlistId\":\"PLknwEmKsW8OtLRQPTLms79499meY2D6ij\",\"title\":\"كورس html كامل بالعربي | html tutorial for beginners\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij\"},{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PL7MvWKKNGEw_NyCc6TeuipmkgaXmdgpkD\",\"title\":\"تاسيس النحو محمد صلاح\",\"channel\":\"Mostafa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/7PJZh-EBaq8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7MvWKKNGEw_NyCc6TeuipmkgaXmdgpkD\"},{\"playlistId\":\"PLXzwwf5_wsiZN7_LIWN9eDYJt1S4SQYLe\",\"title\":\"دورة علوم القرآن كاملة ( الشرح على السبورة )\",\"channel\":\"باسم طاحون Bassem Tahoun\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OQ2qI8Oi30Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXzwwf5_wsiZN7_LIWN9eDYJt1S4SQYLe\"},{\"playlistId\":\"PLmotdda810ImH-Nldpl5QOTkHywj3dKE_\",\"title\":\"كورس اردوينو-دورة الاردوينو Arduino_Basic_Course_Arabic\",\"channel\":\"Electronicsandcoding\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yHoRxeWSgPY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLmotdda810ImH-Nldpl5QOTkHywj3dKE_\"},{\"playlistId\":\"PLaQzmDaYayMCuW3Y0LhKnYON8u5_QQYsW\",\"title\":\"كورس لغة الإشارة للمبتدئين ( صدقة جارية لصديقي إسلام صلاح)\",\"channel\":\"هحببك فى الإشارة 🫡\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Tw44A1185uc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLaQzmDaYayMCuW3Y0LhKnYON8u5_QQYsW\"},{\"playlistId\":\"PLUxZodnxUhN9oHJSz_1KxUFK8hAq9O4Zk\",\"title\":\"دورة متكاملة في منهج نور البيان\",\"channel\":\"Ayat Nawar\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/MGhSyY0fCUU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUxZodnxUhN9oHJSz_1KxUFK8hAq9O4Zk\"},{\"playlistId\":\"PLRtfJqT1hc31ZP4tr3ijypE_0T-4PE_kZ\",\"title\":\"تعلم لغة C من الصفر | c programming course\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8lGYaQXeviM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc31ZP4tr3ijypE_0T-4PE_kZ\"},{\"playlistId\":\"PLzfuLUD0zqw3_bFFpnAjcUK8gVEh_vi7U\",\"title\":\"كورس تعلم برنامج كورل درو باللغة العربية للمبتدئين من الأساسيات الى الإحتراف CorelDRAW\",\"channel\":\"Yasser Darouzi - ياسر درعوزي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/MVmrPeOvBBo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLzfuLUD0zqw3_bFFpnAjcUK8gVEh_vi7U\"},{\"playlistId\":\"PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\",\"title\":\"تعلم لغة البرمجة php من الصفر| php tutorial full course\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yDt8oy4-M9I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\"}]}', '2026-02-05 17:20:47'),
('playlists:q:المهارات الحياتية والمسؤولية المجتمعية|max:6', '{\"ok\":true,\"items\":[]}', '2026-02-05 17:20:49'),
('playlists:q:النظم الحاسوبية المتوازية و الموزعة|max:6', '{\"ok\":true,\"items\":[]}', '2026-02-11 20:40:47'),
('playlists:q:برمجة أمن المعلومات والشبكات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"},{\"playlistId\":\"PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\",\"title\":\"احتراف أمن المعلومات - كورس كامل\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/j_rO-cNFYX0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\"},{\"playlistId\":\"PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\",\"title\":\"الامن السيبراني والهكر الاخلاقي | Ethical Hacking\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B_Xf9HAT-fc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\"},{\"playlistId\":\"PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\",\"title\":\"كورس امن المعلومات\",\"channel\":\"GeekHood\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-Y1SvVnwvhU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\"},{\"playlistId\":\"PLoy5ygSSAs_oB_sg41WxATkSJmYaq53lI\",\"title\":\"كورس شبكات من البداية حتى الاحتراف الجزء الاول\",\"channel\":\"Future Technology Ahmed Salah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Qy4LgwCB8a4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoy5ygSSAs_oB_sg41WxATkSJmYaq53lI\"}]}', '2026-02-12 17:30:01'),
('playlists:q:برمجة اللغات الطبيعية وتطبيقاتها|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLZww4t5DGLBoEaquyzpgHtzBgzmDQG8dC\",\"title\":\"شرح برنامج الجدول المدرسي الالكتروني aSc Timetables\",\"channel\":\"ابراهيم محمود مرسي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/MuTobqf6Cbw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZww4t5DGLBoEaquyzpgHtzBgzmDQG8dC\"}]}', '2026-02-05 17:21:02'),
('playlists:q:برمجة تطبيقات الإنترنت|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL7VOgFQ42C7e-lWJiV4QeqRg3GqCOgoab\",\"title\":\"دروس تعلم برمجة الويب من الصفر\",\"channel\":\"Abdullah Diaa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/E4veOfnLu0E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7VOgFQ42C7e-lWJiV4QeqRg3GqCOgoab\"},{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"},{\"playlistId\":\"PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\",\"title\":\"كورس فلاتر كامل للمبتدئين من الصفر - تطوير وبرمجة تطبيقات الموبايل\",\"channel\":\"Ammar Alkhatib\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lRercKJaAes\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\"},{\"playlistId\":\"PLknwEmKsW8OtLRQPTLms79499meY2D6ij\",\"title\":\"كورس html كامل بالعربي | html tutorial for beginners\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij\"},{\"playlistId\":\"PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\",\"title\":\"تعلم تصميم وتطوير المواقع 2022 دبلوم كامل مع 100 مشروع كامل\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WWn8tOHjZbw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"}]}', '2026-02-12 17:17:03'),
('playlists:q:برمجة تطبيقات الانترنت|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL7VOgFQ42C7e-lWJiV4QeqRg3GqCOgoab\",\"title\":\"دروس تعلم برمجة الويب من الصفر\",\"channel\":\"Abdullah Diaa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/E4veOfnLu0E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7VOgFQ42C7e-lWJiV4QeqRg3GqCOgoab\"},{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"},{\"playlistId\":\"PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\",\"title\":\"كورس فلاتر كامل للمبتدئين من الصفر - تطوير وبرمجة تطبيقات الموبايل\",\"channel\":\"Ammar Alkhatib\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lRercKJaAes\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\"},{\"playlistId\":\"PLknwEmKsW8OtLRQPTLms79499meY2D6ij\",\"title\":\"كورس html كامل بالعربي | html tutorial for beginners\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"},{\"playlistId\":\"PLzTFSn-Bzi_yShxnBXoFz7vj-H3bcuULg\",\"title\":\"دورة تعليم البرمجة للأطفال وصناعة الألعاب باستخدام سكراتش من الالف الي الياء\",\"channel\":\"Codingua\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/BhRJ92pDDnQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLzTFSn-Bzi_yShxnBXoFz7vj-H3bcuULg\"}]}', '2026-02-11 20:40:46'),
('playlists:q:برمجة علم البيانات والذكاء الاصطناعي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\",\"title\":\"كيف تتعلم الذكاء الاصطناعي بايثون - المسار الكامل دورة مجانية\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-9SqKQssejY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\"},{\"playlistId\":\"PLH0em1f_fBoT1xlF8F4bD2CQd--FZ3RR8\",\"title\":\"كورس الذكاء الاصطناعي و تعلم الالة\",\"channel\":\"TatworX\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WI9-woIhyL0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH0em1f_fBoT1xlF8F4bD2CQd--FZ3RR8\"},{\"playlistId\":\"PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\",\"title\":\"كورس مقدمه في الذكاء الاصطناعي - AI Introduction for Beginners\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lvuhqwLwJZ4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\"},{\"playlistId\":\"PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\",\"title\":\"محاضرات ذكاء اصطناعي |  Artificial Intelligence (AI) Lectures\",\"channel\":\"محمد داود Mohammad Dawoud\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/H5WUwwivEaI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\"},{\"playlistId\":\"PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\",\"title\":\"تعلم الآلة بالعربي || Machine Learning in Arabic\",\"channel\":\"Elgouhary AI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/95RhuC30R5U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\"},{\"playlistId\":\"PLWd4nYaF_Vx65cPZF_I2OpWERatzh5Gdj\",\"title\":\"أساسيات علم البيانات | Data Science Foundations\",\"channel\":\"Mustafa Othman\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WM2LiUDa4jE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWd4nYaF_Vx65cPZF_I2OpWERatzh5Gdj\"},{\"playlistId\":\"PLuXYxBK7fwrabHiikIDO1bPuOXKhJeFBD\",\"title\":\"دورة الذكاء الاصطناعي\",\"channel\":\"By Osman\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FAxJ5SYLo-c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXYxBK7fwrabHiikIDO1bPuOXKhJeFBD\"},{\"playlistId\":\"PLXlHqMRg9lAbzySbK_1P6ZNAqI0ckBzqO\",\"title\":\"الذكاء الاصطناعي و Machine Learning بـ Python | كورس عملي مجاني شامل\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8t2DK3lcjXg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAbzySbK_1P6ZNAqI0ckBzqO\"},{\"playlistId\":\"PLUQDw_ve-LUA0a6OXM1JASSMeFWq44HsB\",\"title\":\"الذكاء الإصطناعي و تعلم الآلة 🎲\",\"channel\":\"Python Arabic Community\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/zPBj2kfvWIM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUQDw_ve-LUA0a6OXM1JASSMeFWq44HsB\"},{\"playlistId\":\"PLxe78bIBB8nVGSQbYw6MLKfWuNQmnjKdh\",\"title\":\"كورس التسويق الإلكتروني بالذكاء الاصطناعي - Ai Media Buying Course\",\"channel\":\"Bassem Magdy - Media Buyer\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/jgI5aDgRh44\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxe78bIBB8nVGSQbYw6MLKfWuNQmnjKdh\"},{\"playlistId\":\"PLSiLeKadTQ7lqL6hzFBFr282jR3m39CLL\",\"title\":\"بايثون تعلم الذكاء الاصطناعي باللغة العربية (اساسيات + تطبيق عملي)\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/kFmDaYDVOZs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7lqL6hzFBFr282jR3m39CLL\"},{\"playlistId\":\"PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\",\"title\":\"كورس تحليل البيانات بالاكسل\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cPWC-WNchJk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\"},{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"},{\"playlistId\":\"PLof3yw6ZFPFg5W3Z3sv6GzY6WvvXBqtnD\",\"title\":\"Data Analysis with Python تحليل البيانات باستخدام البايثون\",\"channel\":\"ALMUNTHIR SAFFAN (‫المنذر سفان‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AbqvCnIN7DM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLof3yw6ZFPFg5W3Z3sv6GzY6WvvXBqtnD\"},{\"playlistId\":\"PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\",\"title\":\"كورس بايثون - تعلم بايثون من الصفر للإحتراف\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Do34NKMq80c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\"},{\"playlistId\":\"PLDoPjvoNmBAyE_gei5d18qkfIe-Z8mocs\",\"title\":\"Mastering Python - تعلم بايثون\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mvZHDpCHphk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAyE_gei5d18qkfIe-Z8mocs\"},{\"playlistId\":\"PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\",\"title\":\"تعلم الخوارزميات من الصفر || Learn algorithms from scratch\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GZHJcvzEWCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\"},{\"playlistId\":\"PLxe78bIBB8nXMLkNzkUiRWA8Taee4oq0L\",\"title\":\"كورس اساسيات البرمجة بالذكاء الاصطناعي للأطفال و المبتدئين | Pictoblox\",\"channel\":\"Bassem Magdy - Media Buyer\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KW1-Zn8a5UM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxe78bIBB8nXMLkNzkUiRWA8Taee4oq0L\"},{\"playlistId\":\"PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\",\"title\":\"ما قبل تعلم البرمجة\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aK46A6jQ1RM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\"},{\"playlistId\":\"PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\",\"title\":\"C++ Programming Course Level 1 Basics By Arabic   كورس لغه برمجه سي بلس بلس المستوي الاول الاساسيات بالعربي\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oKA0aq4BdiY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\"},{\"playlistId\":\"PLH_uDwiVYu9wObIIrS6G4LOgWwNxfM6je\",\"title\":\"تعلم الذكاء الاصطناعي  AI من الصفر\",\"channel\":\"Engineering Society\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/IEtOZ2JZaJk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH_uDwiVYu9wObIIrS6G4LOgWwNxfM6je\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"}]}', '2026-02-13 18:25:59'),
('playlists:q:برمجة قواعد البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"},{\"playlistId\":\"PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\",\"title\":\"دورة برمجة قواعد البيانات من الصفر بلغة الفيجوال بسيك و SQL Server\",\"channel\":\"خالد السعداني - Khalid ESSAADANI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gaAkpRmZfhI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\"},{\"playlistId\":\"PLqPejUavRNTUTYamHD0n654J-w8vA1u-5\",\"title\":\"كورس برمجة قواعد البيانات بإستخدام سيكول سيرفر SQL Server\",\"channel\":\"ahmed mohamady\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ItvE1zqIINQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLqPejUavRNTUTYamHD0n654J-w8vA1u-5\"},{\"playlistId\":\"PL37D52B7714788190\",\"title\":\"Database 1 - المقرر النظرى - Fundamentals of Database Systems\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yLc0Yp5QZlU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL37D52B7714788190\"},{\"playlistId\":\"PLNENNuNEfKVF77k5vyDiEAZAZCee7v6G5\",\"title\":\"من الصفر الى الأحتراف SQL Server 2019 اقوى كورس\",\"channel\":\"Apps Scope\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/febj-wDBnHU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLNENNuNEfKVF77k5vyDiEAZAZCee7v6G5\"}]}', '2026-02-12 17:17:03'),
('playlists:q:برمجة متقدمة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"},{\"playlistId\":\"PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\",\"title\":\"كورس هندسة البرمجيات كامل  - Software engineering course\",\"channel\":\"محمدنور\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PdER1jZ9nVI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\",\"title\":\"ما قبل تعلم البرمجة\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aK46A6jQ1RM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"}]}', '2026-02-12 19:29:29'),
('playlists:q:برمجة مرئية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"},{\"playlistId\":\"PLF8OvnCBlEY0UU030QzRv506rculmtGQ2\",\"title\":\"Windows Forms تعلم برمجة سي شارب\",\"channel\":\"TheNewBaghdad (‫بغداد الجديدة‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VkUwJONdpvs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLF8OvnCBlEY0UU030QzRv506rculmtGQ2\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"},{\"playlistId\":\"PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\",\"title\":\"ما قبل تعلم البرمجة\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aK46A6jQ1RM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\"}]}', '2026-02-11 20:40:48'),
('playlists:q:بروتوكولات أمن المعلومات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"},{\"playlistId\":\"PLAqaqJU4wzYU_6EIzVoxHghQILgyY--yf\",\"title\":\"2026 Cisco CCNA v1.1 200-301 || المنهاج العربي الكامل\",\"channel\":\"III-Networking\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/TrQljnE49-o\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLAqaqJU4wzYU_6EIzVoxHghQILgyY--yf\"},{\"playlistId\":\"PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\",\"title\":\"الامن السيبراني والهكر الاخلاقي | Ethical Hacking\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B_Xf9HAT-fc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\"},{\"playlistId\":\"PLsPzjrOLeh_x-7-Yu0Yyc4Y7uJvpNJegL\",\"title\":\"دورة CCNA من البدايةالى الاحتراف على packet tracer\",\"channel\":\"AN GROUP\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/JplWfxhCE18\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLsPzjrOLeh_x-7-Yu0Yyc4Y7uJvpNJegL\"},{\"playlistId\":\"PL8s4OGp06499sD76hhwURj6vKDPPMhxHD\",\"title\":\"كورس CCNA ببساطة - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/03MsMSTjjvo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp06499sD76hhwURj6vKDPPMhxHD\"},{\"playlistId\":\"PLjEjN3kziQ45zTd6MjWiltUgybVCzGBao\",\"title\":\"اساسيات الشبكات Network Basics\",\"channel\":\"Mohammed Ibrahim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-BIbHsVrBQ8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjEjN3kziQ45zTd6MjWiltUgybVCzGBao\"}]}', '2026-02-12 17:30:02'),
('playlists:q:تاريخ القدس والوصاية الهاشمية|max:6', '{\"ok\":true,\"items\":[]}', '2026-02-05 17:21:11'),
('playlists:q:تحليل عددي (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLjiyR6nPcbxyRhkr9CcTEhFZ9lgBlh5wL\",\"title\":\"Numerical | التحليل العددي\",\"channel\":\"ElCoM HU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/g562gPU_-RM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjiyR6nPcbxyRhkr9CcTEhFZ9lgBlh5wL\"},{\"playlistId\":\"PL94bVIjsDWjqypouD5z3E-QPHsjjxesBv\",\"title\":\"التحليل العددي Numerical analysis\",\"channel\":\"سلام عدنان التميمي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/m_aUFmP0Veg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL94bVIjsDWjqypouD5z3E-QPHsjjxesBv\"},{\"playlistId\":\"PLhvHGWDMvdoQXBy9RAGGszUhdx-5Ady97\",\"title\":\"الدليل الشامل للمبتدئين في إتقان أساسيات نظم المعلومات الجغرافية GIS\",\"channel\":\"م محمد دامس | Eng Mohammad S Dames\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/K7ZwE-7Qhgo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhvHGWDMvdoQXBy9RAGGszUhdx-5Ady97\"},{\"playlistId\":\"PL7nhsj3rJk8OaqbNuZs222lRbc-cR3OSQ\",\"title\":\"Fundamentals in Mathematical Logic) (أسس الرياضيات والمنطق الرياضي)\",\"channel\":\"واستبقوا الخيرات\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/27xONR8d6ZI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7nhsj3rJk8OaqbNuZs222lRbc-cR3OSQ\"},{\"playlistId\":\"PLxIvc-MGOs6iQXFnjF_STbhGdrZBphrv_\",\"title\":\"Linear Algebra | جبر خطي\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1RnKXrJwseo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6iQXFnjF_STbhGdrZBphrv_\"},{\"playlistId\":\"PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\",\"title\":\"كورس تحليل البيانات للمبتدأين من الصفر\",\"channel\":\"داتا ساينس بالعربي .. Data Science \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOv1gE0ER3I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\"}]}', '2026-02-11 20:40:48'),
('playlists:q:تحليل وتصميم الخوارزميات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLn_Cp0AAXFlG6iuwkiLjCrX_7b5waIMRJ\",\"title\":\"ِِAlgorithms design and analysis - تحليل وتصميم الخورازميات\",\"channel\":\"A Grade\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/p4_O6_rSCos\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLn_Cp0AAXFlG6iuwkiLjCrX_7b5waIMRJ\"},{\"playlistId\":\"PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\",\"title\":\"تعلم الخوارزميات من الصفر || Learn algorithms from scratch\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GZHJcvzEWCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\"},{\"playlistId\":\"PLCZPUiJ5kQaHQG9LN3WjxxUmCOKV_heOF\",\"title\":\"Algorithm Analysis &amp; Design - شرح عربي\",\"channel\":\"Ahmed Elrefa3y\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/jZW2ikuEAS8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCZPUiJ5kQaHQG9LN3WjxxUmCOKV_heOF\"},{\"playlistId\":\"PLwCMLs3sjOY6KH-8c9F-lMWn-r02hyoV_\",\"title\":\"Algorithms - Full Coures In Arabic\",\"channel\":\"محمود سامي Hard-Code l\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/McifeJjrvpI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwCMLs3sjOY6KH-8c9F-lMWn-r02hyoV_\"},{\"playlistId\":\"PLbR_CTcUs10-SJibuqkNq0pZrvSxVuJZI\",\"title\":\"تحليل وتصميم الخوارزميات | Algorithms Analysis and Design\",\"channel\":\"محمد داود Mohammad Dawoud\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B1z0FPFo24A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbR_CTcUs10-SJibuqkNq0pZrvSxVuJZI\"},{\"playlistId\":\"PLlb4XY5KRGzL46eOCxiiNdq5wB0fa-c3M\",\"title\":\"تحليل وتصميم الخوارزميات\",\"channel\":\"Mohammed Eydan\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Q3W2XTASdxo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLlb4XY5KRGzL46eOCxiiNdq5wB0fa-c3M\"}]}', '2026-02-12 19:54:49'),
('playlists:q:تحليل وتصميم النظم|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\",\"title\":\"كورس تحليل وتصميم نظم المعلومات\",\"channel\":\"Yasser Salah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/r1vj22x573Q\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\"},{\"playlistId\":\"PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\",\"title\":\"تحليل وتصميم نظم System Analysis and Design\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-jnh18rUnYI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\"},{\"playlistId\":\"PLNvuKI7qTnnWorEOQbfxcsL0MPbNt-k8f\",\"title\":\"تحليل وتصميم النظم SAD - د.نسيم مطر\",\"channel\":\"Cache team\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/HHD7TJzh2fo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLNvuKI7qTnnWorEOQbfxcsL0MPbNt-k8f\"},{\"playlistId\":\"PL7DCUg4VywYVo5XLpIVKS4B_R6JnSVhhe\",\"title\":\"تحليل وتصميم نظم المعلومات\",\"channel\":\"Khalil Mohammed_خليل محمد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/iWf4i8DeG_M\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7DCUg4VywYVo5XLpIVKS4B_R6JnSVhhe\"},{\"playlistId\":\"PL1DUmTEdeA6I-bsfaErW12ro4_6hC7fSp\",\"title\":\"system analysis and design\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9jrN5BPx4MI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6I-bsfaErW12ro4_6hC7fSp\"},{\"playlistId\":\"PLutMVsUZuH-fARrWL2ee51jJyWrB71Fxl\",\"title\":\"تحليل وتصميم النظم\",\"channel\":\"Team El Manhag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/exSkyMmKj9M\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLutMVsUZuH-fARrWL2ee51jJyWrB71Fxl\"}]}', '2026-02-11 20:40:49'),
('playlists:q:تراكيب البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PL1DUmTEdeA6JlommmGP5wicYLxX5PVCQt\",\"title\":\"C++ Data Structures - تراكيب البيانات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/jGP19W5IObA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6JlommmGP5wicYLxX5PVCQt\"},{\"playlistId\":\"PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\",\"title\":\"كورس الـ data structure بلغة الـ c++\",\"channel\":\"Mega Code\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SbDUW7_G3Xc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\"},{\"playlistId\":\"PLnzqK5HvcpwS70RtOyS_DlH5iSa4U0IO1\",\"title\":\"كورس الداتا استركشر والخوارزميات بالعربي(course data structures and Algorithms  by arabic )\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/r0BQAKhrAh8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwS70RtOyS_DlH5iSa4U0IO1\"},{\"playlistId\":\"PLF8OvnCBlEY3a1pbPrE6fvNuV3qi-6KRf\",\"title\":\"Data Structure and Algorithms analysis || الخوارزميات وهيكلة البيانات\",\"channel\":\"TheNewBaghdad (‫بغداد الجديدة‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/eNOZjwJVIxg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLF8OvnCBlEY3a1pbPrE6fvNuV3qi-6KRf\"},{\"playlistId\":\"PLhiFu-f80eo--m0YnPpiiWGd4mq-4V2rY\",\"title\":\"Data Structure هياكل البيانات\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/O2llLrg7Ys8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo--m0YnPpiiWGd4mq-4V2rY\"}]}', '2026-02-11 20:40:49'),
('playlists:q:تصميم المترجمات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLof3yw6ZFPFgn2IQP-oOXC26RnopZcLxG\",\"title\":\"دورة الفوتوشوب\",\"channel\":\"ALMUNTHIR SAFFAN (‫المنذر سفان‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h2JqXt45BZU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLof3yw6ZFPFgn2IQP-oOXC26RnopZcLxG\"},{\"playlistId\":\"PLt4jH5ZbQeRvhcx_5o0Tri4Y6f5hY94pw\",\"title\":\"KNX Course\",\"channel\":\"Share Knowledge\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Ar3iocsToQY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLt4jH5ZbQeRvhcx_5o0Tri4Y6f5hY94pw\"},{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLknwEmKsW8OtLRQPTLms79499meY2D6ij\",\"title\":\"كورس html كامل بالعربي | html tutorial for beginners\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij\"},{\"playlistId\":\"PLQA612FPiM4hz2YXR0IgZxBMBrEoo4TSe\",\"title\":\"باب رزق ( كورس تصميم الجرافيك )\",\"channel\":\"My Channel\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/H-x1cit4GJA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQA612FPiM4hz2YXR0IgZxBMBrEoo4TSe\"},{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"}]}', '2026-02-11 20:40:50'),
('playlists:q:تصميم المنطق الرقمي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLjiyR6nPcbxy_nbEQHBj5MwGicZRTRM_w\",\"title\":\"Digital Logic | منطق رقمي\",\"channel\":\"ElCoM HU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ExxgXlYy9k0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjiyR6nPcbxy_nbEQHBj5MwGicZRTRM_w\"},{\"playlistId\":\"PLSRx5jmWD9u1U8kqwkb43AP35Ssg7yPrS\",\"title\":\"Digital Logic Design | شرح عربي | تصميم منطقي رقمي\",\"channel\":\"si-manual\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/a0KqOJejO5Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSRx5jmWD9u1U8kqwkb43AP35Ssg7yPrS\"},{\"playlistId\":\"PLhx4zaYkEjI8BuPybYhAotW2gKDIT9y2D\",\"title\":\"Learn Digital Logic Circuit Design step by step &quot;Arabic&quot; تعلم تصميم الدوائر المنطقية خطوة بخطوة عربى\",\"channel\":\"Hashim EduTech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8u_doj8CO1E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhx4zaYkEjI8BuPybYhAotW2gKDIT9y2D\"},{\"playlistId\":\"PLirUhi614V_f6hGWUnJrBlgWzIVsJWny9\",\"title\":\"Digital Logic شرح\",\"channel\":\"Ayatallah Salem\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/BB6NrgPmoPY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLirUhi614V_f6hGWUnJrBlgWzIVsJWny9\"},{\"playlistId\":\"PLYvBQARQqTs2on-ZkNhKb8E0iD0xuF-U1\",\"title\":\"Digital Logic Design\",\"channel\":\"MBI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/H9KAlcx9frY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYvBQARQqTs2on-ZkNhKb8E0iD0xuF-U1\"},{\"playlistId\":\"PLQikMCtL7cRh7tYNcqTpgdF_qJIzoByTt\",\"title\":\"CPE 321 Digital Logic Design ( تصميم المنطق الرقمي )\",\"channel\":\"Mohammed Hammori\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_fUKa0nQSqk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQikMCtL7cRh7tYNcqTpgdF_qJIzoByTt\"}]}', '2026-02-11 20:40:51');
INSERT INTO `youtube_cache` (`cache_key`, `json`, `updated_at`) VALUES
('playlists:q:تصميم مواقع إلكترونية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\",\"title\":\"تعلم تصميم وتطوير المواقع 2022 دبلوم كامل مع 100 مشروع كامل\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WWn8tOHjZbw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\"},{\"playlistId\":\"PLknwEmKsW8OtLRQPTLms79499meY2D6ij\",\"title\":\"كورس html كامل بالعربي | html tutorial for beginners\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij\"},{\"playlistId\":\"PLBEmrZ4G2sRf84eZ3faGAGROFAhmCqK41\",\"title\":\"كورس تصميم المواقع الشامل\",\"channel\":\"Computer Mind\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/O2qJoXCwhv0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBEmrZ4G2sRf84eZ3faGAGROFAhmCqK41\"},{\"playlistId\":\"PLO0GfC_g13c_hLCK4MGvuP_075i1xUwSq\",\"title\":\"WordPress Website Course - كورس انشاء موقع الكتروني\",\"channel\":\"Mostafa Hesham مصطفى هشام\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/xtzfp3bUI9M\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLO0GfC_g13c_hLCK4MGvuP_075i1xUwSq\"},{\"playlistId\":\"PL7VOgFQ42C7e-lWJiV4QeqRg3GqCOgoab\",\"title\":\"دروس تعلم برمجة الويب من الصفر\",\"channel\":\"Abdullah Diaa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/E4veOfnLu0E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7VOgFQ42C7e-lWJiV4QeqRg3GqCOgoab\"},{\"playlistId\":\"PLBkkpVXOta_uWo5f2D8hKZZguOhoCHxyn\",\"title\":\"دورة تصميم المواقع - كورس كامل للمبتدئين بدون تعلم برمجة\",\"channel\":\"محمد عبدالله Mohamed Abdullah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8oCkHKXLVwE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBkkpVXOta_uWo5f2D8hKZZguOhoCHxyn\"}]}', '2026-02-12 17:17:04'),
('playlists:q:تصميم مواقع الكترونية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\",\"title\":\"تعلم تصميم وتطوير المواقع 2022 دبلوم كامل مع 100 مشروع كامل\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WWn8tOHjZbw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kF7p-kd3gkHr6BAwQKYYSv\"},{\"playlistId\":\"PLknwEmKsW8OtLRQPTLms79499meY2D6ij\",\"title\":\"كورس html كامل بالعربي | html tutorial for beginners\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij\"},{\"playlistId\":\"PLBEmrZ4G2sRf84eZ3faGAGROFAhmCqK41\",\"title\":\"كورس تصميم المواقع الشامل\",\"channel\":\"Computer Mind\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/O2qJoXCwhv0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBEmrZ4G2sRf84eZ3faGAGROFAhmCqK41\"},{\"playlistId\":\"PLO0GfC_g13c_hLCK4MGvuP_075i1xUwSq\",\"title\":\"WordPress Website Course - كورس انشاء موقع الكتروني\",\"channel\":\"Mostafa Hesham مصطفى هشام\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/xtzfp3bUI9M\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLO0GfC_g13c_hLCK4MGvuP_075i1xUwSq\"},{\"playlistId\":\"PL7VOgFQ42C7e-lWJiV4QeqRg3GqCOgoab\",\"title\":\"دروس تعلم برمجة الويب من الصفر\",\"channel\":\"Abdullah Diaa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/E4veOfnLu0E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7VOgFQ42C7e-lWJiV4QeqRg3GqCOgoab\"},{\"playlistId\":\"PLBkkpVXOta_uWo5f2D8hKZZguOhoCHxyn\",\"title\":\"دورة تصميم المواقع - كورس كامل للمبتدئين بدون تعلم برمجة\",\"channel\":\"محمد عبدالله Mohamed Abdullah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8oCkHKXLVwE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBkkpVXOta_uWo5f2D8hKZZguOhoCHxyn\"}]}', '2026-02-05 17:20:39'),
('playlists:q:تطوير البرمجيات وتوثيقها|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\",\"title\":\"Software Engineering in Arabic |دورة هندسة البرمجيات\",\"channel\":\"EDU US\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Io_IAwUi4Ck\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\"},{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"},{\"playlistId\":\"PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\",\"title\":\"ما قبل تعلم البرمجة\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aK46A6jQ1RM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"}]}', '2026-02-12 17:17:06'),
('playlists:q:تطوير وتصميم الأنظمة الآمنة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\",\"title\":\"كورس تحليل وتصميم نظم المعلومات\",\"channel\":\"Yasser Salah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/r1vj22x573Q\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"}]}', '2026-02-12 17:30:02'),
('playlists:q:تفاضل وتكامل (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL9fwy3NUQKwY6bGFmuJGxU8kL7vx3U133\",\"title\":\"مساق: تفاضل وتكامل (أ)\",\"channel\":\"eLearning Centre - IUG - Video Lectures \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/a29rwypicLY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9fwy3NUQKwY6bGFmuJGxU8kL7vx3U133\"},{\"playlistId\":\"PLWGBZlhzh4JxXD0jbIbgrf7QicsxySqb0\",\"title\":\"كالكولاس ١ - calculus 1\",\"channel\":\"منصة ألفا التعليمية - Alpha platform\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/uv39vB0C3uY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWGBZlhzh4JxXD0jbIbgrf7QicsxySqb0\"},{\"playlistId\":\"PL8PqiXhu9aUoL3i2MwTSYi33gtIMAykUp\",\"title\":\"calculus 1 \\/ د.عبد اللطيف\",\"channel\":\"ChE Committe ll لجنة الهندسة الكيميائية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h_t0IzebFOs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8PqiXhu9aUoL3i2MwTSYi33gtIMAykUp\"},{\"playlistId\":\"PLxIvc-MGOs6gkSl_PPAVJpebKDLo-ijEC\",\"title\":\"Calculus | Math. (1) | حساب التفاضل والتكامل\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/EJkxkfgZ_yY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6gkSl_PPAVJpebKDLo-ijEC\"},{\"playlistId\":\"PLb8_80zcbEaJZ5-s9R05EtY4ExqdwxegW\",\"title\":\"كورس تفاضل اولى جامعة 👑\",\"channel\":\"YUSUF REFAAT\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cmHXI8fcrVo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLb8_80zcbEaJZ5-s9R05EtY4ExqdwxegW\"},{\"playlistId\":\"PL2r675ejq5l6ckHCYvzDSpVJNFdtjh_me\",\"title\":\"تفاضل وتكامل 1 - Calculus 1\",\"channel\":\"جامعتك\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9z6q--IZsJ0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL2r675ejq5l6ckHCYvzDSpVJNFdtjh_me\"}]}', '2026-02-12 19:54:50'),
('playlists:q:تفاعل الإنسان مع الحاسوب|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLVn5UigDlJB-KmiMUznGT8pnSiX0p-uzu\",\"title\":\"رواق : التفاعل بين الانسان والحاسوب - أ.مرام عبدالرحمن مكاوي\",\"channel\":\"منصة رواق للتعليم المفتوح\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/qk1LjtcOGmQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLVn5UigDlJB-KmiMUznGT8pnSiX0p-uzu\"},{\"playlistId\":\"PL8rrXgcgYeo1frC2fmfBqkB5X6HnVx34r\",\"title\":\"البرمجة المرئية C#\",\"channel\":\"Educational Technology 4 IBB _University\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Ygziu-tLnAA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8rrXgcgYeo1frC2fmfBqkB5X6HnVx34r\"}]}', '2026-02-12 17:17:05'),
('playlists:q:تفسير النماذج|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\",\"title\":\"تعلم الآلة بالعربي || Machine Learning in Arabic\",\"channel\":\"Elgouhary AI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/95RhuC30R5U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\"},{\"playlistId\":\"PLknwEmKsW8OtLRQPTLms79499meY2D6ij\",\"title\":\"كورس html كامل بالعربي | html tutorial for beginners\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij\"},{\"playlistId\":\"PLoOabVweB2r5dL0AVmuDbS54UvmCIlZsT\",\"title\":\"أساسيات تعلم الآلة | Basics of Machine Learning\",\"channel\":\"Omar Alharbi\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lUcZUCKDXbo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoOabVweB2r5dL0AVmuDbS54UvmCIlZsT\"},{\"playlistId\":\"PLknwEmKsW8OuN04Odt2sJqt4aAnkp-iYA\",\"title\":\"كورس html and css كامل بالعربي\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OuN04Odt2sJqt4aAnkp-iYA\"},{\"playlistId\":\"PLpTIcoD4Wsj1zL5vsUm9Za9vtQdPWLgzV\",\"title\":\"كورس لاحتراف ال SMC (د محمد مهدي)\",\"channel\":\"MAMO FX TRADING MOHAMED MAHDYY\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/d55Pn_hncp0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpTIcoD4Wsj1zL5vsUm9Za9vtQdPWLgzV\"},{\"playlistId\":\"PLXzwwf5_wsiZN7_LIWN9eDYJt1S4SQYLe\",\"title\":\"دورة علوم القرآن كاملة ( الشرح على السبورة )\",\"channel\":\"باسم طاحون Bassem Tahoun\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OQ2qI8Oi30Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXzwwf5_wsiZN7_LIWN9eDYJt1S4SQYLe\"}]}', '2026-02-05 17:20:44'),
('playlists:q:تمثيل واستدلال المعرفة|max:6', '{\"ok\":true,\"items\":[]}', '2026-02-16 20:35:05'),
('playlists:q:تنظيم الحاسوب ومعماريته|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLGqb1w5WKZITfAcpt_q8oUYb1yHtFEgeU\",\"title\":\"مادة تنظيم وعمارة الحاسب بالعربية - Arabic Course in Computer Organization and Architecture\",\"channel\":\"Ghassan Bati\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mYjvunyjdYM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLGqb1w5WKZITfAcpt_q8oUYb1yHtFEgeU\"},{\"playlistId\":\"PL-cKUB-e2KiswAkwZLJYlTzI69FpSJa50\",\"title\":\"Computer Organization &amp; Architecture\",\"channel\":\"ElhosseiniAcademy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_Z4BcXL1n7Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL-cKUB-e2KiswAkwZLJYlTzI69FpSJa50\"},{\"playlistId\":\"PLjiyR6nPcbxzv1MHhyxJqXIOyZ8c1Or_D\",\"title\":\"Computer Organization |تنظيم حاسوب\",\"channel\":\"ElCoM HU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/X8doI4xNfRk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjiyR6nPcbxzv1MHhyxJqXIOyZ8c1Or_D\"},{\"playlistId\":\"PLfhnBieD_d5W2GEdmUdNcbgohuFhceDI4\",\"title\":\"مادة معمارية الحاسوب - Computer architecture\",\"channel\":\"Bradford Company - برادفورد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/XTVYSfhk1PU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfhnBieD_d5W2GEdmUdNcbgohuFhceDI4\"},{\"playlistId\":\"PL4oatGewBr23juhuWuRTmSZQZwkHg5i_y\",\"title\":\"Computer Architecture\",\"channel\":\"FCI Channel & Tech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Jfm101jIn2E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL4oatGewBr23juhuWuRTmSZQZwkHg5i_y\"},{\"playlistId\":\"PLSRx5jmWD9u1-bH3_mewkP_cSZpRk5aNj\",\"title\":\"Computer Organization and Design | تصميم و تنظيم الحاسب | شرح بالعربي\",\"channel\":\"si-manual\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/L724HnBmdJo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSRx5jmWD9u1-bH3_mewkP_cSZpRk5aNj\"}]}', '2026-02-11 20:40:52'),
('playlists:q:تنمية بشرية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLmpvAXGVagwVswF7HZFXV4q4SzDlQEgIe\",\"title\":\"الثقة بالنفس .. كورس كامل\",\"channel\":\"Adel Marzok\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lI3eal-K0kA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLmpvAXGVagwVswF7HZFXV4q4SzDlQEgIe\"},{\"playlistId\":\"PLsRtYZTCYnEeCQ2Imkw67YJGXqbfhCCWc\",\"title\":\"دورات ومحاضرات ياسر الحزيمي\",\"channel\":\"ياسر الحزيمي | القناة الرسمية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/wvjBmpxNw1k\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLsRtYZTCYnEeCQ2Imkw67YJGXqbfhCCWc\"},{\"playlistId\":\"PLeuj-AmjixnCp99PtlXtKtFY6wUDXCef4\",\"title\":\"تعلم الاتش ار من الصفر - كورس HR\",\"channel\":\"صاحب بيزنس\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/uJWOKEgX8Ag\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLeuj-AmjixnCp99PtlXtKtFY6wUDXCef4\"},{\"playlistId\":\"PLqGEgt_8f5gRpHXDN_5qxKyCoI5gRqnbK\",\"title\":\"كورس ادارة الموارد البشرية\",\"channel\":\"Et3alem by Innovito\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/t-SZJhP7XIM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLqGEgt_8f5gRpHXDN_5qxKyCoI5gRqnbK\"},{\"playlistId\":\"PLxqW-Jwr-JpiFVoxoxbEuskQmXeNay-pw\",\"title\":\"ملخصات كتب تنمية بشرية تطوير الذات\",\"channel\":\"books summary - ملخصات كتب\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/b8ApfMpbdKA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxqW-Jwr-JpiFVoxoxbEuskQmXeNay-pw\"},{\"playlistId\":\"PLSc4EHOM4f-Rgv6l8PoQi05nFRgkoLoS_\",\"title\":\"الشخصية القوية- ياسر الحزيمي\",\"channel\":\"Awakening\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/C6iHu964axs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSc4EHOM4f-Rgv6l8PoQi05nFRgkoLoS_\"}]}', '2026-02-05 17:21:12'),
('playlists:q:جبر خطي (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLxIvc-MGOs6iQXFnjF_STbhGdrZBphrv_\",\"title\":\"Linear Algebra | جبر خطي\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1RnKXrJwseo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6iQXFnjF_STbhGdrZBphrv_\"},{\"playlistId\":\"PLHK0LiNgUJXZMM4ueEZYYl9fQlv0NBxd7\",\"title\":\"الجبر الخطي \\/ كلية التربية \\/ المرحلة الأولى\",\"channel\":\"مسلم عقيل - muslem akeel\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1n3m49XtW0k\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHK0LiNgUJXZMM4ueEZYYl9fQlv0NBxd7\"},{\"playlistId\":\"PLp5QO1iuiUkM2C_JBn5zLSE81dDIjyCy8\",\"title\":\"الجبر الخطي\",\"channel\":\"Tu Algebra\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4gVA64KlAwY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLp5QO1iuiUkM2C_JBn5zLSE81dDIjyCy8\"},{\"playlistId\":\"PLYmn-2dhM6ysE6bs7pnYO5T3bsIop496c\",\"title\":\"المصفوفات\\/مرحلة أولى (الجبر الخطي)\",\"channel\":\"نوار الأسدي (دورات إلكترونية)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OUWil-k0DcU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYmn-2dhM6ysE6bs7pnYO5T3bsIop496c\"},{\"playlistId\":\"PLZyQU-WOzZF1L2ne0_UDVVPpnTto5iDFd\",\"title\":\"linear Algebra | الجبر الخطى\",\"channel\":\"Spicy Coding\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Y074b0xjaao\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZyQU-WOzZF1L2ne0_UDVVPpnTto5iDFd\"},{\"playlistId\":\"PLE7DDD91010BC51F8\",\"title\":\"MIT 18.06 Linear Algebra, Spring 2005\",\"channel\":\"MIT OpenCourseWare\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/7UJ4CFRGd-U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLE7DDD91010BC51F8\"}]}', '2026-02-05 17:21:08'),
('playlists:q:حقوق الانسان|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLzJzj_sHNTJgCvHrrwZjpOVNCjo30WenH\",\"title\":\"حقوق انسان ومكافحة الفساد\",\"channel\":\"Malk Ahmad Hussien\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mOPp6zWHCP4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLzJzj_sHNTJgCvHrrwZjpOVNCjo30WenH\"},{\"playlistId\":\"PLm877Wx3hfJ3iM0iNgtLGJB_Zv2BTKe3p\",\"title\":\"Human Rights | حقوق الإنسان\",\"channel\":\"Mohamed Maher | محمد ماهر\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KHDkBHs27jk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLm877Wx3hfJ3iM0iNgtLGJB_Zv2BTKe3p\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLFGgC1KKHUI3ML-ThlCKLhfkjOqKXmYPV\",\"title\":\"مقدمة في الأمن السيبراني\",\"channel\":\"Saad Alqarni\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9etF0qpQ7BI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLFGgC1KKHUI3ML-ThlCKLhfkjOqKXmYPV\"},{\"playlistId\":\"PLYPYBWtAMWoTmxgl0mT2F_h4_R5N-b0Pl\",\"title\":\"مقرر (قانون دولي - عام) - تقديم: أ.د. صالح زيد قصيله\",\"channel\":\"عمادة التعليم الإلكتروني بجامعة القرآن الكريم\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/3DkKDR8dKo4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYPYBWtAMWoTmxgl0mT2F_h4_R5N-b0Pl\"},{\"playlistId\":\"PLE5V2gT9NbH57o6ztUSpruZUSpF2T6BB6\",\"title\":\"دورة نماء التكوينية الأولى || مقدمات أولية في الفلسفة واتجاهاتها\",\"channel\":\"مركز نماء للبحوث والدراسات\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/UXgDrxV48ig\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLE5V2gT9NbH57o6ztUSpruZUSpF2T6BB6\"}]}', '2026-02-05 17:21:09'),
('playlists:q:رياضيات متقطعة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLRaCy-L8neoYhrvlD_v647S5o_5IaXuJY\",\"title\":\"Discrete Mathematics | الرياضيات المتقطعة\",\"channel\":\"Ammar Jabe - عمار جابر\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SzCJHhxlvzU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRaCy-L8neoYhrvlD_v647S5o_5IaXuJY\"},{\"playlistId\":\"PLxIvc-MGOs6gZlMVYOOEtUHJmfUquCjwz\",\"title\":\"Discrete Mathematics | الرياضيات المتقطعة\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/eFDzhn1Inc4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6gZlMVYOOEtUHJmfUquCjwz\"},{\"playlistId\":\"PLZyQU-WOzZF1rmALoJZthmDKPsqxCV4mW\",\"title\":\"Discrete Maths Course | الرياضيات المنفصلة\",\"channel\":\"Spicy Coding\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Ho7BD7Yqqtk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZyQU-WOzZF1rmALoJZthmDKPsqxCV4mW\"},{\"playlistId\":\"PL7nhsj3rJk8OaqbNuZs222lRbc-cR3OSQ\",\"title\":\"Fundamentals in Mathematical Logic) (أسس الرياضيات والمنطق الرياضي)\",\"channel\":\"واستبقوا الخيرات\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/27xONR8d6ZI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7nhsj3rJk8OaqbNuZs222lRbc-cR3OSQ\"},{\"playlistId\":\"PLxIvc-MGOs6iQXFnjF_STbhGdrZBphrv_\",\"title\":\"Linear Algebra | جبر خطي\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1RnKXrJwseo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6iQXFnjF_STbhGdrZBphrv_\"},{\"playlistId\":\"PLxIvc-MGOs6gW9SgkmoxE5w9vQkID1_r-\",\"title\":\"Probability and Statistics | احتمالات وإحصاء\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GmJJ2iZz08c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6gW9SgkmoxE5w9vQkID1_r-\"}]}', '2026-02-12 19:58:39'),
('playlists:q:سلامة ومصادقة البيانات|max:6', '{\"ok\":true,\"items\":[]}', '2026-02-12 17:30:03'),
('playlists:q:شبكات الحاسوب|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL8v_bZALWLKE9Lo2BIy8nsdsakbSvQlEo\",\"title\":\"شبكات الحاسوب Computer Networking: A Top-Down Approach, 8th Edition\",\"channel\":\"Dr. Khawlah Harahsheh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/jHoWFIIj6zE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8v_bZALWLKE9Lo2BIy8nsdsakbSvQlEo\"},{\"playlistId\":\"PLW_bvpno-KN5kXouC0Y48urCyiaqfqPN-\",\"title\":\"الشبكات الحاسوبية - Computer Networks\",\"channel\":\"Dr. Omar Zakaria\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/0YdSALJ9ypM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLW_bvpno-KN5kXouC0Y48urCyiaqfqPN-\"},{\"playlistId\":\"PLpwHU9rNXAVurp2h2Jh-cd4-8XjkT5osu\",\"title\":\"Cisco CCNA 200-301 - Arabic - Ahmed Elhefny كورس تأسيس الشبكات للمبتدئين من الصفر للأحتراف\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4u3LVXDOkyw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVurp2h2Jh-cd4-8XjkT5osu\"},{\"playlistId\":\"PLoy5ygSSAs_oB_sg41WxATkSJmYaq53lI\",\"title\":\"كورس شبكات من البداية حتى الاحتراف الجزء الاول\",\"channel\":\"Future Technology Ahmed Salah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Qy4LgwCB8a4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoy5ygSSAs_oB_sg41WxATkSJmYaq53lI\"},{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"},{\"playlistId\":\"PLQ4MHTchEqH0SNORkbiWqIn-S_xZl6W-e\",\"title\":\"مادة شبكات الحاسوب\",\"channel\":\"Dr.Belal Ayyoub قناة الدكتور بلال ايوب\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/dp5ytKCTjH8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQ4MHTchEqH0SNORkbiWqIn-S_xZl6W-e\"}]}', '2026-02-05 17:20:40'),
('playlists:q:علوم جنائية رقمية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLJlY_xhi__IiunKGg3Lc9srt4jh0GGdeS\",\"title\":\"مادة مدخل لدراسة القانون S1\",\"channel\":\"طلبة القانون بالمغرب\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/o9EJx6ISwZI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLJlY_xhi__IiunKGg3Lc9srt4jh0GGdeS\"}]}', '2026-02-12 17:30:04'),
('playlists:q:فحص البرمجيات وجودتها|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLQUxWr2rTswl1D4f8Tkw3RvCD-ZB6LpgJ\",\"title\":\"Practical Software Testing Basics | SWQ Academy\",\"channel\":\"Software Quality Academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/D0PqgXsBX5g\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQUxWr2rTswl1D4f8Tkw3RvCD-ZB6LpgJ\"},{\"playlistId\":\"PLzNfs-3kBUJllCa8_6pLYDMnIlg6Lfvu4\",\"title\":\"Software Testing Course in Arabic - بالعربي software testing شرح\",\"channel\":\"Tresmerge\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/rQZ-NbN3RLA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLzNfs-3kBUJllCa8_6pLYDMnIlg6Lfvu4\"},{\"playlistId\":\"PLmO1HCianm08sNyD3RY97PskNcUcCeNuR\",\"title\":\"Software testing for beginnersكورس عملي اختبار البرمجيات\",\"channel\":\"سيد عادل\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/HYsxQ9qgxYw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLmO1HCianm08sNyD3RY97PskNcUcCeNuR\"},{\"playlistId\":\"PLJ2FoWouEU2zpjd0_ukTWM46D7tAXQ_Ln\",\"title\":\"Manual Testing Bootcamp\",\"channel\":\"QAcart\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lxUd1bz7jYA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLJ2FoWouEU2zpjd0_ukTWM46D7tAXQ_Ln\"},{\"playlistId\":\"PLL34mf651faM_nn8uKlnwbQPw5zSh_F84\",\"title\":\"SOFTWARE TESTING TUTORIAL - Master Software Testing and Crack Job in Testing - SOFTWARE TESTING BOOTCAMP - FULL COURSE\",\"channel\":\"Software Testing Mentor\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/E2t5XbWwj7I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLL34mf651faM_nn8uKlnwbQPw5zSh_F84\"},{\"playlistId\":\"PLUDwpEzHYYLseflPNg0bUKfLmAbO2JnE9\",\"title\":\"Manual Testing(Full Course)\",\"channel\":\"SDET- QA\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOvURgHcd4w\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUDwpEzHYYLseflPNg0bUKfLmAbO2JnE9\"}]}', '2026-02-12 17:17:24'),
('playlists:q:فيزياء عامة (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLWrUU7ljKR0RzX_CTA4cmjVrcqUj0ocD5\",\"title\":\"الفيزياء 101 Physics\",\"channel\":\"Afkar Academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/nJIQgRX70ik\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWrUU7ljKR0RzX_CTA4cmjVrcqUj0ocD5\"},{\"playlistId\":\"PL08ef9eJxtJZgdDPyPGDuv9kqDCQZgKpf\",\"title\":\"فيزياء عامة - 101 فيز\",\"channel\":\"المقررات المفتوحة - Open Courses\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gI95xrATzz4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL08ef9eJxtJZgdDPyPGDuv9kqDCQZgKpf\"},{\"playlistId\":\"PLBGoaHUIAOFMxNZJbw_TVohntL5mM1jn9\",\"title\":\"Physics 101 | Zaid Albashtawi\",\"channel\":\"Albashtawi Physics \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oonf0frmU00\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBGoaHUIAOFMxNZJbw_TVohntL5mM1jn9\"},{\"playlistId\":\"PLoiEx8wAxvXKeyghQIYJb2mUWNaMK2mx3\",\"title\":\"مقرر الفيزياء العامة 1 الميكانيكا الكلاسيكية وتطبيقاتها\",\"channel\":\"قناة الفيزياء التعليمية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/i2siM5oMj0Q\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoiEx8wAxvXKeyghQIYJb2mUWNaMK2mx3\"},{\"playlistId\":\"PL9fwy3NUQKwb6OQhcTn5SkdK0XkcfDNyC\",\"title\":\"كلية العلوم | مساق فيزياء عامة أ | د. بسام السقا\",\"channel\":\"eLearning Centre - IUG - Video Lectures \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Gsr87nrM9VY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9fwy3NUQKwb6OQhcTn5SkdK0XkcfDNyC\"},{\"playlistId\":\"PLGG93aooJidouU0mW4gA5k4FiJoOZBJHS\",\"title\":\"المتجهات - فيزياء عامة 101 أحمد صبحي - General Physics 101\",\"channel\":\"الفيزياء للجميع A FYsics\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GcaaLJes0aM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLGG93aooJidouU0mW4gA5k4FiJoOZBJHS\"}]}', '2026-02-11 20:40:53'),
('playlists:q:قواعد البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PL37D52B7714788190\",\"title\":\"Database 1 - المقرر النظرى - Fundamentals of Database Systems\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yLc0Yp5QZlU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL37D52B7714788190\"},{\"playlistId\":\"PLNENNuNEfKVF77k5vyDiEAZAZCee7v6G5\",\"title\":\"من الصفر الى الأحتراف SQL Server 2019 اقوى كورس\",\"channel\":\"Apps Scope\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/febj-wDBnHU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLNENNuNEfKVF77k5vyDiEAZAZCee7v6G5\"},{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"},{\"playlistId\":\"PL93xoMrxRJIuicqcd1UpFUYMfWKGp7JmI\",\"title\":\"تعلم التعامل مع قواعد البيانات Sql - mysql - course - من الصفر الى الاحتراف  - learn - tutorial - شرح - تعلم - كورس\",\"channel\":\"Wael abo hamza\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/vfUMzsUqqb0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL93xoMrxRJIuicqcd1UpFUYMfWKGp7JmI\"},{\"playlistId\":\"PLvsVwFBrUFQqjbfWjBh6N5I9YarRtl69x\",\"title\":\"دورة SQL\",\"channel\":\"akwad dev- اكواد ديف\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/f-1Ch5mY1Mc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLvsVwFBrUFQqjbfWjBh6N5I9YarRtl69x\"}]}', '2026-02-05 17:20:41'),
('playlists:q:قواعد بيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL37D52B7714788190\",\"title\":\"Database 1 - المقرر النظرى - Fundamentals of Database Systems\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yLc0Yp5QZlU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL37D52B7714788190\"},{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLNENNuNEfKVF77k5vyDiEAZAZCee7v6G5\",\"title\":\"من الصفر الى الأحتراف SQL Server 2019 اقوى كورس\",\"channel\":\"Apps Scope\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/febj-wDBnHU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLNENNuNEfKVF77k5vyDiEAZAZCee7v6G5\"},{\"playlistId\":\"PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\",\"title\":\"دورة برمجة قواعد البيانات من الصفر بلغة الفيجوال بسيك و SQL Server\",\"channel\":\"خالد السعداني - Khalid ESSAADANI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gaAkpRmZfhI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\"},{\"playlistId\":\"PLMTdZ61eBnyoQoEmLOcgTBdrAOVT-GFju\",\"title\":\"دورة تعلم SQL و MySQL من الصفر إلى الاحتراف - افضل دورة تعلم نظام إدارة قواعد البيانات كاملة\",\"channel\":\"Coder Shiyar (كودر شيار)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1MJibb8SZX4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLMTdZ61eBnyoQoEmLOcgTBdrAOVT-GFju\"}]}', '2026-02-12 17:17:23'),
('playlists:q:مبادئ الإحصاء والاحتمالات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLHqh9deMUhNox0DDH_TF4hpJjhz88xd9I\",\"title\":\"مبادئ الإحصاء\",\"channel\":\"Abdulrahman Arzanjani\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Q1vMc8IaheI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHqh9deMUhNox0DDH_TF4hpJjhz88xd9I\"},{\"playlistId\":\"PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\",\"title\":\"مبادئ الاحصاء\",\"channel\":\"Ahmed Ayish Aldehneen\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v-KEUmZD3fY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\"},{\"playlistId\":\"PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\",\"title\":\"محاضرات الاحصاء للمرحلة الاولى\",\"channel\":\"تقنيات انظمة الحاسوب المعهد التقني بعقوبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KT6ASzh_JXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\"},{\"playlistId\":\"PLVpJGVBmPnw3eRSzC90oXA6gBcG-nEYIe\",\"title\":\"Foundations of Statistical Analysis (Full College Course)\\/تعلم اسس علم الاحصاء\",\"channel\":\"Professor X\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Fy2B6a8op8g\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLVpJGVBmPnw3eRSzC90oXA6gBcG-nEYIe\"},{\"playlistId\":\"PLxIvc-MGOs6gW9SgkmoxE5w9vQkID1_r-\",\"title\":\"Probability and Statistics | احتمالات وإحصاء\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GmJJ2iZz08c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6gW9SgkmoxE5w9vQkID1_r-\"},{\"playlistId\":\"PLAZbsRZWaI8a_q4ibG0dzusBiiEbPONRK\",\"title\":\"مبادئ الإحصاء\",\"channel\":\"القناة التعليمية، كلية الدراسات المتوسطة _الأزهر\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/0POO1aZzpO4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLAZbsRZWaI8a_q4ibG0dzusBiiEbPONRK\"}]}', '2026-02-12 19:54:51'),
('playlists:q:مبادئ الاحصاء والاحتمالات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\",\"title\":\"مبادئ الاحصاء\",\"channel\":\"Ahmed Ayish Aldehneen\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v-KEUmZD3fY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjIbeeJSkyJcpbdNnxtZOL7obGoyS9wz5\"},{\"playlistId\":\"PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\",\"title\":\"محاضرات الاحصاء للمرحلة الاولى\",\"channel\":\"تقنيات انظمة الحاسوب المعهد التقني بعقوبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KT6ASzh_JXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\"},{\"playlistId\":\"PLxIvc-MGOs6gW9SgkmoxE5w9vQkID1_r-\",\"title\":\"Probability and Statistics | احتمالات وإحصاء\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GmJJ2iZz08c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6gW9SgkmoxE5w9vQkID1_r-\"},{\"playlistId\":\"PLHqh9deMUhNox0DDH_TF4hpJjhz88xd9I\",\"title\":\"مبادئ الإحصاء\",\"channel\":\"Abdulrahman Arzanjani\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Q1vMc8IaheI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHqh9deMUhNox0DDH_TF4hpJjhz88xd9I\"},{\"playlistId\":\"PLVpJGVBmPnw3eRSzC90oXA6gBcG-nEYIe\",\"title\":\"Foundations of Statistical Analysis (Full College Course)\\/تعلم اسس علم الاحصاء\",\"channel\":\"Professor X\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Fy2B6a8op8g\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLVpJGVBmPnw3eRSzC90oXA6gBcG-nEYIe\"},{\"playlistId\":\"PLKkck3IWQH4rjQPaPqBE6nPeGJO8pna1a\",\"title\":\"الاحصاء - الباب الاول (نظرية الاحتمالات)\",\"channel\":\"م.حسين العالم - Hussein Elalem\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/65FuRE2Gews\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLKkck3IWQH4rjQPaPqBE6nPeGJO8pna1a\"}]}', '2026-02-11 20:40:53'),
('playlists:q:مختبر البرمجة الشيئية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\",\"title\":\"Object Oriented Programming - شرح البرمجة كائنية التوجه\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fK2lLVqc8UY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\"},{\"playlistId\":\"PL8DDsWuvM_EWYUvtpxALB0Xx7L_f_phXm\",\"title\":\"كورس البرمجة الكائينية |  C++ Object Oriented OOP\",\"channel\":\"اتعلم ببساطة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aJG-KnmfFxM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8DDsWuvM_EWYUvtpxALB0Xx7L_f_phXm\"},{\"playlistId\":\"PL1DUmTEdeA6Icttz-O9C3RPRF8R8Px5vk\",\"title\":\"Programming 2 - Object Oriented Programming With Java\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/M3Na5luSx50\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6Icttz-O9C3RPRF8R8Px5vk\"},{\"playlistId\":\"PL1DUmTEdeA6KLEvIO0NyrkT91BVle8BOU\",\"title\":\"Programming 2 - Object Oriented Programming with C++\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6U6WtWG3NrA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6KLEvIO0NyrkT91BVle8BOU\"},{\"playlistId\":\"PLCi9_wQUeSMU9lnFeub4Vb27S5TnwesCy\",\"title\":\"الدورة الشاملة لتعلم لغة جافا من الصفر\",\"channel\":\"BarmajaOnline - برمجة أونلاين\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ezOvr_qK3f4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCi9_wQUeSMU9lnFeub4Vb27S5TnwesCy\"},{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"}]}', '2026-02-05 17:20:52'),
('playlists:q:مختبر البرمجة الكينونية|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLCInYL3l2AagY7fFlhCrjpLiIFybW3yQv\",\"title\":\"Object-Oriented Programming JAVA in Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FaaM6uVbuJM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AagY7fFlhCrjpLiIFybW3yQv\"},{\"playlistId\":\"PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\",\"title\":\"Object Oriented Programming - شرح البرمجة كائنية التوجه\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fK2lLVqc8UY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzUrgCyaX_WEIJljx_We-c1\"},{\"playlistId\":\"PLCInYL3l2AajYlZGzU_LVrHdoouf8W6ZN\",\"title\":\"Learn JAVA Programming From Scratch In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mNvJipMTKSM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajYlZGzU_LVrHdoouf8W6ZN\"},{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PLCInYL3l2AajFAiw4s1U4QbGszcQ-rAb3\",\"title\":\"Learn C++ Programming From Scratch In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/z1FdInL8sjg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajFAiw4s1U4QbGszcQ-rAb3\"},{\"playlistId\":\"PL_1ycbQtST2OP79P6SbhDai-VNAMrGwmY\",\"title\":\"شرح البرمجة بلغة c++ مرحلة اولى \\/  كورس 1+2\",\"channel\":\"سجاد باقر\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/kGVpv3vyii8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL_1ycbQtST2OP79P6SbhDai-VNAMrGwmY\"}]}', '2026-02-13 18:26:01'),
('playlists:q:مختبر الذكاء الاصطناعي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\",\"title\":\"كيف تتعلم الذكاء الاصطناعي بايثون - المسار الكامل دورة مجانية\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-9SqKQssejY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\"},{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\",\"title\":\"محاضرات ذكاء اصطناعي |  Artificial Intelligence (AI) Lectures\",\"channel\":\"محمد داود Mohammad Dawoud\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/H5WUwwivEaI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\"},{\"playlistId\":\"PLH0em1f_fBoT1xlF8F4bD2CQd--FZ3RR8\",\"title\":\"كورس الذكاء الاصطناعي و تعلم الالة\",\"channel\":\"TatworX\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WI9-woIhyL0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH0em1f_fBoT1xlF8F4bD2CQd--FZ3RR8\"},{\"playlistId\":\"PLuXYxBK7fwrabHiikIDO1bPuOXKhJeFBD\",\"title\":\"دورة الذكاء الاصطناعي\",\"channel\":\"By Osman\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/FAxJ5SYLo-c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXYxBK7fwrabHiikIDO1bPuOXKhJeFBD\"},{\"playlistId\":\"PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\",\"title\":\"كورس مقدمه في الذكاء الاصطناعي - AI Introduction for Beginners\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lvuhqwLwJZ4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\"}]}', '2026-02-05 17:20:57'),
('playlists:q:مختبر تراكيب البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\",\"title\":\"كورس الـ data structure بلغة الـ c++\",\"channel\":\"Mega Code\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SbDUW7_G3Xc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\"},{\"playlistId\":\"PL1DUmTEdeA6JlommmGP5wicYLxX5PVCQt\",\"title\":\"C++ Data Structures - تراكيب البيانات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/jGP19W5IObA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6JlommmGP5wicYLxX5PVCQt\"},{\"playlistId\":\"PLE_E4BccYnp3vpaLpNTQYZYvdxZViD0vs\",\"title\":\"Data Structures (new)\",\"channel\":\"Alaa Eddien Attar\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/7NNubfiURxk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLE_E4BccYnp3vpaLpNTQYZYvdxZViD0vs\"},{\"playlistId\":\"PLhiFu-f80eo--m0YnPpiiWGd4mq-4V2rY\",\"title\":\"Data Structure هياكل البيانات\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/O2llLrg7Ys8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo--m0YnPpiiWGd4mq-4V2rY\"},{\"playlistId\":\"PL88kafUXXgBaAgb0h3-ZMvzxb5J2qFrut\",\"title\":\"دورة الخوارزميات و هياكل البيانات | Algorithms and Data Structures Course in Arabic\",\"channel\":\"litprog\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/DLo8dLBtI-0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL88kafUXXgBaAgb0h3-ZMvzxb5J2qFrut\"}]}', '2026-02-11 20:40:54'),
('playlists:q:مختبر فيزياء عامة (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLpmrj9amQnIdOQhj5v9JGB3ErmIqpQqxn\",\"title\":\"فيزياء طبيه - مرحله اولى\",\"channel\":\"عثمان \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1Wx0H0SM4g8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpmrj9amQnIdOQhj5v9JGB3ErmIqpQqxn\"},{\"playlistId\":\"PLWqI2o3n-uhdkH6fZv03PzACyrD2mULs0\",\"title\":\"كورس أساسيات الإلكترونيات من الصفر\",\"channel\":\"ابن تسلا\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yYuXHRBDdXg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWqI2o3n-uhdkH6fZv03PzACyrD2mULs0\"},{\"playlistId\":\"PL60JGB5sOgqIgqFgfBfx3vtF8dTwZDcnr\",\"title\":\"الميكانيك الهندسي ( المرحلة الأولى )\",\"channel\":\"Eng. Abbas Fadhil Al-Zamili\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oBLP9RFIsXg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL60JGB5sOgqIgqFgfBfx3vtF8dTwZDcnr\"},{\"playlistId\":\"PLwYZnTL7XywAwSPNV1QJNpchwO5lVLO93\",\"title\":\"الكورس المجاني ميكانيك الهندسي\",\"channel\":\"ابراهيم الحسيني\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/T6KSuwZx-LU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwYZnTL7XywAwSPNV1QJNpchwO5lVLO93\"},{\"playlistId\":\"PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\",\"title\":\"محاضرات الاحصاء للمرحلة الاولى\",\"channel\":\"تقنيات انظمة الحاسوب المعهد التقني بعقوبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KT6ASzh_JXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\"},{\"playlistId\":\"PLdUHNiwJgn87b75K-TgLXiOR0nhz7leqD\",\"title\":\"Physics 1 || Dr.Sufian Alnemrat\",\"channel\":\"Etihad IT HU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8GdjDY2_xug\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLdUHNiwJgn87b75K-TgLXiOR0nhz7leqD\"}]}', '2026-02-11 20:40:55');
INSERT INTO `youtube_cache` (`cache_key`, `json`, `updated_at`) VALUES
('playlists:q:مختبر قواعد البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLvsVwFBrUFQqjbfWjBh6N5I9YarRtl69x\",\"title\":\"دورة SQL\",\"channel\":\"akwad dev- اكواد ديف\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/f-1Ch5mY1Mc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLvsVwFBrUFQqjbfWjBh6N5I9YarRtl69x\"},{\"playlistId\":\"PL1DUmTEdeA6KJxj_5joGnA2iSCszJ_Fnk\",\"title\":\"أساسيات قواعد البيانات - اوراكل - عملى\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Nvh5Ba_vo-Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6KJxj_5joGnA2iSCszJ_Fnk\"},{\"playlistId\":\"PL37D52B7714788190\",\"title\":\"Database 1 - المقرر النظرى - Fundamentals of Database Systems\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yLc0Yp5QZlU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL37D52B7714788190\"},{\"playlistId\":\"PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\",\"title\":\"دورة برمجة قواعد البيانات من الصفر بلغة الفيجوال بسيك و SQL Server\",\"channel\":\"خالد السعداني - Khalid ESSAADANI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gaAkpRmZfhI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwj1YcMhLRN1j8AqbgSRjbZmDTIQGOK4b\"},{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PL93xoMrxRJIuicqcd1UpFUYMfWKGp7JmI\",\"title\":\"تعلم التعامل مع قواعد البيانات Sql - mysql - course - من الصفر الى الاحتراف  - learn - tutorial - شرح - تعلم - كورس\",\"channel\":\"Wael abo hamza\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/vfUMzsUqqb0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL93xoMrxRJIuicqcd1UpFUYMfWKGp7JmI\"},{\"playlistId\":\"PLMTdZ61eBnyoQoEmLOcgTBdrAOVT-GFju\",\"title\":\"دورة تعلم SQL و MySQL من الصفر إلى الاحتراف - افضل دورة تعلم نظام إدارة قواعد البيانات كاملة\",\"channel\":\"Coder Shiyar (كودر شيار)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1MJibb8SZX4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLMTdZ61eBnyoQoEmLOcgTBdrAOVT-GFju\"},{\"playlistId\":\"PLfM2wZNebA2zROxUcAbGxNrpVZncsF3oD\",\"title\":\"Sql Server 2019 Query Course-كورس قواعد بيانات من البدايه -سيكوال كويرى-كورس تعلم سيكوال سيرفر من البدايه\",\"channel\":\"Ahmed Rezk\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B7evUQGmN6M\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfM2wZNebA2zROxUcAbGxNrpVZncsF3oD\"},{\"playlistId\":\"PLqPejUavRNTUTYamHD0n654J-w8vA1u-5\",\"title\":\"كورس برمجة قواعد البيانات بإستخدام سيكول سيرفر SQL Server\",\"channel\":\"ahmed mohamady\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ItvE1zqIINQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLqPejUavRNTUTYamHD0n654J-w8vA1u-5\"},{\"playlistId\":\"PLe_UJpVeP8qCHeTPVPc2gQXuQr7AniUbn\",\"title\":\"Database - MySql Course\",\"channel\":\"EraaSoft\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/JQT8d2tHwk0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLe_UJpVeP8qCHeTPVPc2gQXuQr7AniUbn\"},{\"playlistId\":\"PL1KA7P5hJ-obwTDetMyuMNUzkwTm-M6tG\",\"title\":\"Oracle SQL tutorials Arabic Course\",\"channel\":\"Ask Gad\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AGsKfeHM5VI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1KA7P5hJ-obwTDetMyuMNUzkwTm-M6tG\"},{\"playlistId\":\"PLuvT-i95N7yXeYtYgkfPaHbMgUzk_aYN9\",\"title\":\"Database &amp; SQL Course || شرح بالعربي\",\"channel\":\"Ahmed El-Gendy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/YuRmghkoaRI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuvT-i95N7yXeYtYgkfPaHbMgUzk_aYN9\"},{\"playlistId\":\"PLaKH4x6WxzvIXwTEPFdS2OkowWyBDlZyq\",\"title\":\"SQL للمبتدئين بالعربي  - قواعد البيانات والتعامل مع الداتا وتجهيزها لتحليل البيانات\",\"channel\":\"DA Club - نادي تحليل البيانات\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lvGTPlHz1Vo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLaKH4x6WxzvIXwTEPFdS2OkowWyBDlZyq\"},{\"playlistId\":\"PLoRh0POuk1Rw-BZU-DPI6cA_c5W9_2uF_\",\"title\":\"Database ITI - SQL Server ITI - بشمهندس رامي\",\"channel\":\"Bess Gates\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/nUiuyejbemc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoRh0POuk1Rw-BZU-DPI6cA_c5W9_2uF_\"},{\"playlistId\":\"PL85D9FC9DFD6B9484\",\"title\":\"Database 1- المقرر العملى - Learn SQL In Arabic\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dj1zTZwbMOQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL85D9FC9DFD6B9484\"},{\"playlistId\":\"PLfOk7Ih7aac9OlBgWylPlKX8Qv66zP7Ek\",\"title\":\"قواعد البيانات | Database\",\"channel\":\"Dr. Aya Nasser - د. آية ناصر\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q8VilMwPrR0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfOk7Ih7aac9OlBgWylPlKX8Qv66zP7Ek\"},{\"playlistId\":\"PLknwEmKsW8OveN7SrZN5QjwyXLBzUDkJB\",\"title\":\"كورس قواعد البيانات بالبايثون | python sqlite3\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Gtl35O5qU9U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OveN7SrZN5QjwyXLBzUDkJB\"},{\"playlistId\":\"PLDoPjvoNmBAz6DT8SzQ1CODJTH-NIA7R9\",\"title\":\"MySQL 5 Essential Training\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/DftlOK7fCtc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAz6DT8SzQ1CODJTH-NIA7R9\"},{\"playlistId\":\"PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\",\"title\":\"كورس الـ data structure بلغة الـ c++\",\"channel\":\"Mega Code\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SbDUW7_G3Xc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\"},{\"playlistId\":\"PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\",\"title\":\"C++ Programming Course Level 1 Basics By Arabic   كورس لغه برمجه سي بلس بلس المستوي الاول الاساسيات بالعربي\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oKA0aq4BdiY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PL1DUmTEdeA6J6oDLTveTt4Z7E5qEfFluE\",\"title\":\"MS SQL Server For Beginners\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fqA2t50vXjA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6J6oDLTveTt4Z7E5qEfFluE\"},{\"playlistId\":\"PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\",\"title\":\"Learn MATLAB in Arabic step by step تعلم كورس ماتلاب باللغة العربية) شرح برنامج الماتلاب كامل خطوة بخطوة عربى)\",\"channel\":\"Hashim EduTech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_di_oaAdEZM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\"}]}', '2026-02-11 20:41:42'),
('playlists:q:مختبر مقدمة في البرمجة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\",\"title\":\"C++ Programming Course Level 1 Basics By Arabic   كورس لغه برمجه سي بلس بلس المستوي الاول الاساسيات بالعربي\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oKA0aq4BdiY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\"},{\"playlistId\":\"PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\",\"title\":\"Learn MATLAB in Arabic step by step تعلم كورس ماتلاب باللغة العربية) شرح برنامج الماتلاب كامل خطوة بخطوة عربى)\",\"channel\":\"Hashim EduTech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_di_oaAdEZM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PLCInYL3l2AajFAiw4s1U4QbGszcQ-rAb3\",\"title\":\"Learn C++ Programming From Scratch In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/z1FdInL8sjg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajFAiw4s1U4QbGszcQ-rAb3\"},{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\",\"title\":\"كورس الـ data structure بلغة الـ c++\",\"channel\":\"Mega Code\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SbDUW7_G3Xc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\"}]}', '2026-02-05 17:20:39'),
('playlists:q:مراقبة الشبكات وتوثيقها|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"},{\"playlistId\":\"PLz_AsIGBQqJsHCKlZlkk6RbyOIO7zIDHu\",\"title\":\"أمن الشبكات Network Security\",\"channel\":\"Ahmed Al-Masri\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cj6UiIm9STQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLz_AsIGBQqJsHCKlZlkk6RbyOIO7zIDHu\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLV0U7xtoDnRv9w0l1f1iZo9QUfudjfNW0\",\"title\":\"كورس أمن الشبكات والمعلومات\",\"channel\":\"AHMED A. A. ALFARRA\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Xa0eXtjzt5Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLV0U7xtoDnRv9w0l1f1iZo9QUfudjfNW0\"},{\"playlistId\":\"PLW_bvpno-KN5kXouC0Y48urCyiaqfqPN-\",\"title\":\"الشبكات الحاسوبية - Computer Networks\",\"channel\":\"Dr. Omar Zakaria\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/0YdSALJ9ypM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLW_bvpno-KN5kXouC0Y48urCyiaqfqPN-\"},{\"playlistId\":\"PLMYF6NkLrdN_eoPRObGsiEMES1KtU7dyC\",\"title\":\"Network Security - أمن الشبكات\",\"channel\":\"Muhammed Essa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/12xdKUb3qeA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLMYF6NkLrdN_eoPRObGsiEMES1KtU7dyC\"}]}', '2026-02-12 17:30:04'),
('playlists:q:مشروع تخرج (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLdbDarq2VLKbpcCowxrZ3khDhFpNYYPUT\",\"title\":\"كورس الاستقبال والطوارئ كاملEmergency course\",\"channel\":\"Dr. Islam Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_0sfioIHqoA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLdbDarq2VLKbpcCowxrZ3khDhFpNYYPUT\"},{\"playlistId\":\"PLShLh8nc0YkL9w6mvMDBs0pElL_jVOXT5\",\"title\":\"Project Planning &amp; Management - كورس التخطيط و إدارة المشروعات\",\"channel\":\"Youssuf El-Farmawy يوسف الفرماوي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/f8VPTYfkBBI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLShLh8nc0YkL9w6mvMDBs0pElL_jVOXT5\"},{\"playlistId\":\"PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\",\"title\":\"كورس فلاتر كامل للمبتدئين من الصفر - تطوير وبرمجة تطبيقات الموبايل\",\"channel\":\"Ammar Alkhatib\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lRercKJaAes\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\"},{\"playlistId\":\"PLgWZcxtKR7JNC08tW0hxTSlntvgKWMeRB\",\"title\":\"إدارة المشاريع - الكورس الاول\",\"channel\":\"Civil_eng_kingdom\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ONFuVACZ5kM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLgWZcxtKR7JNC08tW0hxTSlntvgKWMeRB\"},{\"playlistId\":\"PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\",\"title\":\"تحليل وتصميم نظم System Analysis and Design\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-jnh18rUnYI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\"},{\"playlistId\":\"PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\",\"title\":\"تعلم لغة البرمجة php من الصفر| php tutorial full course\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yDt8oy4-M9I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\"}]}', '2026-02-05 17:21:05'),
('playlists:q:مشروع تخرج (2)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLShLh8nc0YkL9w6mvMDBs0pElL_jVOXT5\",\"title\":\"Project Planning &amp; Management - كورس التخطيط و إدارة المشروعات\",\"channel\":\"Youssuf El-Farmawy يوسف الفرماوي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/f8VPTYfkBBI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLShLh8nc0YkL9w6mvMDBs0pElL_jVOXT5\"},{\"playlistId\":\"PLgWZcxtKR7JNC08tW0hxTSlntvgKWMeRB\",\"title\":\"إدارة المشاريع - الكورس الاول\",\"channel\":\"Civil_eng_kingdom\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ONFuVACZ5kM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLgWZcxtKR7JNC08tW0hxTSlntvgKWMeRB\"},{\"playlistId\":\"PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\",\"title\":\"كورس بايثون - تعلم بايثون من الصفر للإحتراف\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Do34NKMq80c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\"},{\"playlistId\":\"PLdbDarq2VLKbpcCowxrZ3khDhFpNYYPUT\",\"title\":\"كورس الاستقبال والطوارئ كاملEmergency course\",\"channel\":\"Dr. Islam Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_0sfioIHqoA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLdbDarq2VLKbpcCowxrZ3khDhFpNYYPUT\"},{\"playlistId\":\"PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\",\"title\":\"تحليل وتصميم نظم System Analysis and Design\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-jnh18rUnYI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\"},{\"playlistId\":\"PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\",\"title\":\"كورس تحليل وتصميم نظم المعلومات\",\"channel\":\"Yasser Salah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/r1vj22x573Q\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\"}]}', '2026-02-05 17:21:06'),
('playlists:q:مشروع تخرج تطبيقي (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\",\"title\":\"كورس فلاتر كامل للمبتدئين من الصفر - تطوير وبرمجة تطبيقات الموبايل\",\"channel\":\"Ammar Alkhatib\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lRercKJaAes\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\"},{\"playlistId\":\"PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\",\"title\":\"تعلم لغة البرمجة php من الصفر| php tutorial full course\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yDt8oy4-M9I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\"},{\"playlistId\":\"PLJ6rITJKyxItOyRC9Tq0Y4uxIfcHao_av\",\"title\":\"كورس البوربوينت للمبتدئين\",\"channel\":\"PowerPoint Academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KrdijpIDzn0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLJ6rITJKyxItOyRC9Tq0Y4uxIfcHao_av\"},{\"playlistId\":\"PLjTzpE6cvFakLb80cpN-9vUcGgL_BbOPI\",\"title\":\"كورس الأندرويد كاملا\",\"channel\":\"Lazy programmers\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6nrx0PXAuhQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjTzpE6cvFakLb80cpN-9vUcGgL_BbOPI\"},{\"playlistId\":\"PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\",\"title\":\"كورس بايثون - تعلم بايثون من الصفر للإحتراف\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Do34NKMq80c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\"},{\"playlistId\":\"PLhiFu-f80eo9qDFU90ARVFpnbY71qJIZE\",\"title\":\"دورة ايتابس ETABS COURSE\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fQ44uTfDZRE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9qDFU90ARVFpnbY71qJIZE\"},{\"playlistId\":\"PLwj1YcMhLRN2Y5O_olVfMT7dFe-INcWQT\",\"title\":\"مشروع إدارة المبيعات بلغة سي شارب و SQL Server\",\"channel\":\"خالد السعداني - Khalid ESSAADANI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/hsGdS4yxSqU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwj1YcMhLRN2Y5O_olVfMT7dFe-INcWQT\"},{\"playlistId\":\"PL9DWh3cmLYo0UcEaVug2mzvbAou0A8843\",\"title\":\"كورس برنامج مايكروسوفت وورد بالكامل للمبتدئين\",\"channel\":\"Mohamed Qonswa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/xFKGAyou5jo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9DWh3cmLYo0UcEaVug2mzvbAou0A8843\"},{\"playlistId\":\"PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\",\"title\":\"تحليل وتصميم نظم System Analysis and Design\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-jnh18rUnYI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\"},{\"playlistId\":\"PLoi5HclIR7Djo8MhMPNTEc-mliQOfw5eP\",\"title\":\"دورة تعليم برنامج 2020 civil 3d للمبتدئين في دقائق\",\"channel\":\"eng. mohammed haj hasan\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OY4sulmX8FA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoi5HclIR7Djo8MhMPNTEc-mliQOfw5eP\"},{\"playlistId\":\"PLww54WQ2wa5rOJ7FcXxi-CMNgmpybv7ei\",\"title\":\"دورة الالكترونيات العملية\",\"channel\":\"Walid Issa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/U_hw2RzNTI0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLww54WQ2wa5rOJ7FcXxi-CMNgmpybv7ei\"},{\"playlistId\":\"PLZZdF7TtQ_kqdj2LyO0FaEcLZ0fALMsGv\",\"title\":\"شروحات برامج الأوفيس | Microsoft Office Tutorials\",\"channel\":\"Nology - نولوجي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/51l9s_YKN1E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZZdF7TtQ_kqdj2LyO0FaEcLZ0fALMsGv\"},{\"playlistId\":\"PLDQp-vFPLSqzEre_c7qsZ93Kiz7NLCKjX\",\"title\":\"كورس استخدام اودو للمحاسبين - Odoo ERP System\",\"channel\":\"(اتعلم صح) Ahmed Fahmy AMF\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PTr354taew8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDQp-vFPLSqzEre_c7qsZ93Kiz7NLCKjX\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLNUk9qMoRCS9GW1ChnnZBoHnNanHxMlAh\",\"title\":\"سلسلة شرح برنامج Odoo\",\"channel\":\"E-Accounting & ERP\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/qzdnIG4VBRc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLNUk9qMoRCS9GW1ChnnZBoHnNanHxMlAh\"},{\"playlistId\":\"PLBzRuNdfDyGN4DgsUeK8La1s6JoIqe3US\",\"title\":\"SAP B1 Arabic Journey\",\"channel\":\"Saad El Faleh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-o1GAdJWFOA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBzRuNdfDyGN4DgsUeK8La1s6JoIqe3US\"},{\"playlistId\":\"PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\",\"title\":\"Learn MATLAB in Arabic step by step تعلم كورس ماتلاب باللغة العربية) شرح برنامج الماتلاب كامل خطوة بخطوة عربى)\",\"channel\":\"Hashim EduTech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_di_oaAdEZM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\"},{\"playlistId\":\"PLZCxTtjfTKC1SmQkMKp_EPbjdM1f8nw3w\",\"title\":\"steel design course |دورة تصميم منشات معدنية م احمد دياب\",\"channel\":\"Ali abd elraheem\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Apyux5E29EA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZCxTtjfTKC1SmQkMKp_EPbjdM1f8nw3w\"},{\"playlistId\":\"PLO-c3F568Wm089PqYp1Gam1Vqtvw6yFqd\",\"title\":\"المحاضرة الأولي من الدورات\",\"channel\":\"EGY CET\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GzPkneALstE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLO-c3F568Wm089PqYp1Gam1Vqtvw6yFqd\"},{\"playlistId\":\"PLhvHGWDMvdoQXBy9RAGGszUhdx-5Ady97\",\"title\":\"الدليل الشامل للمبتدئين في إتقان أساسيات نظم المعلومات الجغرافية GIS\",\"channel\":\"م محمد دامس | Eng Mohammad S Dames\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/K7ZwE-7Qhgo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhvHGWDMvdoQXBy9RAGGszUhdx-5Ady97\"},{\"playlistId\":\"PLEvAC5E_AICCRuW_h-RVYzg8dY2hIm78_\",\"title\":\"دورة صناعة العطور الأولى - The first perfumery course\",\"channel\":\"The Perfumer\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/apB2Iw8TPbk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLEvAC5E_AICCRuW_h-RVYzg8dY2hIm78_\"},{\"playlistId\":\"PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\",\"title\":\"كورس تحليل وتصميم نظم المعلومات\",\"channel\":\"Yasser Salah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/r1vj22x573Q\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\"},{\"playlistId\":\"PLqGEgt_8f5gRpHXDN_5qxKyCoI5gRqnbK\",\"title\":\"كورس ادارة الموارد البشرية\",\"channel\":\"Et3alem by Innovito\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/t-SZJhP7XIM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLqGEgt_8f5gRpHXDN_5qxKyCoI5gRqnbK\"},{\"playlistId\":\"PLtP9iYg89G9c-tMS_EfEi2k55HNQJHlvD\",\"title\":\"كيف تبدأ وكالة سفر رقمية – خطوات إنشاء مشروع Travel Agency أونلاين\",\"channel\":\"أحمد أبو بكر\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KSaRIDXb2c0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLtP9iYg89G9c-tMS_EfEi2k55HNQJHlvD\"},{\"playlistId\":\"PL62FigrKPb91r-z5L8RSu3Gmde7Qf1lyC\",\"title\":\"AutoCAD Electrical -level 1\",\"channel\":\"تقنيات-الهيتي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fosVtn-TKyo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL62FigrKPb91r-z5L8RSu3Gmde7Qf1lyC\"}]}', '2026-02-11 20:41:43'),
('playlists:q:مشروع تخرج تطبيقي (2)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\",\"title\":\"كورس فلاتر كامل للمبتدئين من الصفر - تطوير وبرمجة تطبيقات الموبايل\",\"channel\":\"Ammar Alkhatib\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lRercKJaAes\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\"},{\"playlistId\":\"PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\",\"title\":\"تعلم لغة البرمجة php من الصفر| php tutorial full course\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yDt8oy4-M9I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\"},{\"playlistId\":\"PLZAUcbDZNzth1AR7z3M819c-45Ok2-3a9\",\"title\":\"التحليل الإحصائي باستخدام لغة البرمجة  R\",\"channel\":\"د. أسماء الميرغني\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_Ef9ySyvErU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZAUcbDZNzth1AR7z3M819c-45Ok2-3a9\"},{\"playlistId\":\"PLhiFu-f80eo9qDFU90ARVFpnbY71qJIZE\",\"title\":\"دورة ايتابس ETABS COURSE\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fQ44uTfDZRE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9qDFU90ARVFpnbY71qJIZE\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\",\"title\":\"كورس تحليل وتصميم نظم المعلومات\",\"channel\":\"Yasser Salah\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/r1vj22x573Q\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLGQJ43BOmb2KG5pN0sq5lUxflbckJE3Rx\"}]}', '2026-02-15 18:24:42'),
('playlists:q:مشروع تطبيقي (1)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL5mUs39LM9Xpp9x1--0axlUlEGPjxGugF\",\"title\":\"دروس تعلم Arc GIS من البداية الى الاحتراف\",\"channel\":\"حسين المزوري GIS\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mjqn_I6N7ro\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL5mUs39LM9Xpp9x1--0axlUlEGPjxGugF\"},{\"playlistId\":\"PLimOr7QpQ6G-aXro-ju04ESTtOYeD2AE-\",\"title\":\"Microsoft Project 2016 Basic Course دورة مايكروسوفت بروجكت للمبتدئيين بطريقة سهلة جدا\",\"channel\":\"فكر جديد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/qXGoXVm7kG0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLimOr7QpQ6G-aXro-ju04ESTtOYeD2AE-\"},{\"playlistId\":\"PLhiFu-f80eo9qDFU90ARVFpnbY71qJIZE\",\"title\":\"دورة ايتابس ETABS COURSE\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fQ44uTfDZRE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9qDFU90ARVFpnbY71qJIZE\"},{\"playlistId\":\"PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\",\"title\":\"كورس فلاتر كامل للمبتدئين من الصفر - تطوير وبرمجة تطبيقات الموبايل\",\"channel\":\"Ammar Alkhatib\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lRercKJaAes\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLw6Y5u47CYq47oDw63bMqkq06fjuoK_GJ\"},{\"playlistId\":\"PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\",\"title\":\"تعلم لغة البرمجة php من الصفر| php tutorial full course\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yDt8oy4-M9I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mfep8d_FXWLnoARZyXJ5ob\"},{\"playlistId\":\"PLZyQU-WOzZF3gU6j9hi2bz3NhV6KOw9Jo\",\"title\":\"ASP.NET MVC دورة المشروع الكامل كورس تطبيقى مباشر\",\"channel\":\"Spicy Coding\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/bTyxAQMq06s\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZyQU-WOzZF3gU6j9hi2bz3NhV6KOw9Jo\"}]}', '2026-02-13 18:26:03'),
('playlists:q:معالجة البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\",\"title\":\"كورس تحليل البيانات للمبتدأين من الصفر\",\"channel\":\"داتا ساينس بالعربي .. Data Science \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOv1gE0ER3I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\"},{\"playlistId\":\"PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\",\"title\":\"كورس شامل لتحليل البيانات – Excel &amp; Power BI خطوة بخطوة\",\"channel\":\"ElSearshgy - السيرشجى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/RMQLzz1rwBE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\"},{\"playlistId\":\"PLXlHqMRg9lAZPJ5loaPck60I91kAwMhT3\",\"title\":\"كورس تحليل البيانات ب باور بي أي - Power BI Course\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v_re3WCPOjI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAZPJ5loaPck60I91kAwMhT3\"},{\"playlistId\":\"PLCVy8dqNAIgnrGoeg9_JHyncFWjpn-mbH\",\"title\":\"كورس تحليل البيانات\",\"channel\":\"كويري بلس- ماهر المحمد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/5lCRC_JnQpA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCVy8dqNAIgnrGoeg9_JHyncFWjpn-mbH\"},{\"playlistId\":\"PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\",\"title\":\"كورس تحليل البيانات بالاكسل\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cPWC-WNchJk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\"},{\"playlistId\":\"PLimOr7QpQ6G8fLz3xaHNJkrMF6gBemGPm\",\"title\":\"Data Anlysis Course - دورة تحليل البيانات من البداية للاحتراف\",\"channel\":\"فكر جديد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/06CbCVDwiuU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLimOr7QpQ6G8fLz3xaHNJkrMF6gBemGPm\"}]}', '2026-02-05 17:21:03'),
('playlists:q:معالجة اللغات الطبيعية وتطبيقاتها|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PL0LAHBS4MolfHPkPKulpUa3UpfC1rfS9n\",\"title\":\"كورس تعلم لغة البرمجه c++ باستخدام برنامج code blocks على ويندو 10كورس تعلم لغة c++ بالعربى All C++ videos\",\"channel\":\"Online Courses\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Ga4hJ5SriRc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL0LAHBS4MolfHPkPKulpUa3UpfC1rfS9n\"},{\"playlistId\":\"PLZww4t5DGLBoEaquyzpgHtzBgzmDQG8dC\",\"title\":\"شرح برنامج الجدول المدرسي الالكتروني aSc Timetables\",\"channel\":\"ابراهيم محمود مرسي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/MuTobqf6Cbw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZww4t5DGLBoEaquyzpgHtzBgzmDQG8dC\"}]}', '2026-02-15 18:24:44'),
('playlists:q:معمارية البرمجيات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL8gkpIDjEtH1hyr9SF16XlimiPt0cj_Kx\",\"title\":\"Software Architecture &amp; Design\",\"channel\":\"Softwarian Team\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1wuTx4RMwKM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8gkpIDjEtH1hyr9SF16XlimiPt0cj_Kx\"},{\"playlistId\":\"PL4JxLacgYgqTgS8qQPC17fM-NWMTr5GW6\",\"title\":\"Software Architecture and Design\",\"channel\":\"A Dev\' Story\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8UlLgOf20Ho\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL4JxLacgYgqTgS8qQPC17fM-NWMTr5GW6\"},{\"playlistId\":\"PLgAqrVq84PDdfiDow3YVsgc1q34JD415Z\",\"title\":\"Microservices Architecture in Arabic | ميكروسيرفيس بالعربي\",\"channel\":\"Software ArchTalks\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9pGXaUaMyBo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLgAqrVq84PDdfiDow3YVsgc1q34JD415Z\"},{\"playlistId\":\"PLfhnBieD_d5W2GEdmUdNcbgohuFhceDI4\",\"title\":\"مادة معمارية الحاسوب - Computer architecture\",\"channel\":\"Bradford Company - برادفورد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/XTVYSfhk1PU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfhnBieD_d5W2GEdmUdNcbgohuFhceDI4\"},{\"playlistId\":\"PL-cKUB-e2KiswAkwZLJYlTzI69FpSJa50\",\"title\":\"Computer Organization &amp; Architecture\",\"channel\":\"ElhosseiniAcademy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_Z4BcXL1n7Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL-cKUB-e2KiswAkwZLJYlTzI69FpSJa50\"},{\"playlistId\":\"PLGrx7vJzUjK7qFurow-N5a25YIbHbNQ6-\",\"title\":\"Software Architecture (rgpv 7th)\",\"channel\":\"Jishan Ahmad Education\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/dQ1Cn5-ugT0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLGrx7vJzUjK7qFurow-N5a25YIbHbNQ6-\"},{\"playlistId\":\"PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\",\"title\":\"تحليل وتصميم نظم System Analysis and Design\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-jnh18rUnYI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo9hJvZOkqVItWHVA4ucXSxb\"},{\"playlistId\":\"PLPZvv4Sjz6uECy67jbHG7QtM2nCylp4YR\",\"title\":\"Clean Architecture From Scratch شرح بالعربي\",\"channel\":\"Coding-Future\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/iowmQe-lEpc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLPZvv4Sjz6uECy67jbHG7QtM2nCylp4YR\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PLSRx5jmWD9u3d3H5BKkYqhAOgRlK1iSwe\",\"title\":\"Operating Systems | نظم التشغيل | شرح عربي (كلية حاسبات جامعة الطائف)\",\"channel\":\"si-manual\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/a0zuH0U4Ab8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSRx5jmWD9u3d3H5BKkYqhAOgRlK1iSwe\"},{\"playlistId\":\"PLdM_HFqhquliyvog18enYAWWgbsO6e51a\",\"title\":\"Software Engineering ( شرح بالعربي )\",\"channel\":\"Ahmed Ashraf\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/8sgYWjI2Ozc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLdM_HFqhquliyvog18enYAWWgbsO6e51a\"},{\"playlistId\":\"PLDxVq3TlR9y2sMXaL_yLp-r6pUpevgC-w\",\"title\":\"Windows Server Administration (MCSA) - Arabic | By Mohamed Zohdy - شرح كورس عربي\",\"channel\":\"Mohamed Zohdy - محمد زهدي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PrGzyV99cm0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDxVq3TlR9y2sMXaL_yLp-r6pUpevgC-w\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLLlr6jKKdyK0ZbS1lSWOLipuiXWJZhEHG\",\"title\":\"AWS Certified Solutions Architect - Associate شرح بالعربي للمهندس محمد عدي\",\"channel\":\"Information Technology\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lipqPP5Rco4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLLlr6jKKdyK0ZbS1lSWOLipuiXWJZhEHG\"},{\"playlistId\":\"PL5FXWpfgMQ-zfRlDRFZGmCHbL4OslSiAD\",\"title\":\"كورس تعلم الاوتوكاد AutoCAD\",\"channel\":\"المهندس مصطفى جبار\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/YoXe_HU0b6M\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL5FXWpfgMQ-zfRlDRFZGmCHbL4OslSiAD\"},{\"playlistId\":\"PL9YV3aQrrVgCFS3UxYk-9dbXkvT3NS04c\",\"title\":\"لغة التجميع الاسمبلي (EMU8086) - معمارية الحاسوب\",\"channel\":\"Mahmoud Saeed Al-Gammal م.محمود سعيد الجمال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/efSafqK2NJA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9YV3aQrrVgCFS3UxYk-9dbXkvT3NS04c\"},{\"playlistId\":\"PLYvBQARQqTs2on-ZkNhKb8E0iD0xuF-U1\",\"title\":\"Digital Logic Design\",\"channel\":\"MBI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/H9KAlcx9frY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYvBQARQqTs2on-ZkNhKb8E0iD0xuF-U1\"},{\"playlistId\":\"PLpHNtIV1_lid-kpZ36nD8CMS8mOoUNhHB\",\"title\":\"شرح مادة المعالجات الدقيقة 2025\\/2026\",\"channel\":\"قهوه المبرمجين \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ToOIema2CHg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpHNtIV1_lid-kpZ36nD8CMS8mOoUNhHB\"},{\"playlistId\":\"PLjiyR6nPcbxy_nbEQHBj5MwGicZRTRM_w\",\"title\":\"Digital Logic | منطق رقمي\",\"channel\":\"ElCoM HU\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ExxgXlYy9k0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLjiyR6nPcbxy_nbEQHBj5MwGicZRTRM_w\"},{\"playlistId\":\"PLIVlH5-25yHLDBjbuROl1Cp5F3uGwQ5bY\",\"title\":\"Logic Circuits | الدوائر المنطقية\",\"channel\":\"Mohamed Samy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/F6XhYJzYmHY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLIVlH5-25yHLDBjbuROl1Cp5F3uGwQ5bY\"},{\"playlistId\":\"PLYmn-2dhM6ysE6bs7pnYO5T3bsIop496c\",\"title\":\"المصفوفات\\/مرحلة أولى (الجبر الخطي)\",\"channel\":\"نوار الأسدي (دورات إلكترونية)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OUWil-k0DcU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYmn-2dhM6ysE6bs7pnYO5T3bsIop496c\"},{\"playlistId\":\"PL2_UsKPLt_N_8Ti5AcVM-xnWBgSFrP3Mg\",\"title\":\"انظمة العد Numeral system\",\"channel\":\"Qweas \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/s1VPqOoW8Jk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL2_UsKPLt_N_8Ti5AcVM-xnWBgSFrP3Mg\"},{\"playlistId\":\"PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\",\"title\":\"محاضرات الاحصاء للمرحلة الاولى\",\"channel\":\"تقنيات انظمة الحاسوب المعهد التقني بعقوبة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/KT6ASzh_JXs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8qZ7qAv2Rnx9a34l1ahRJGq4x1HQsmz2\"},{\"playlistId\":\"PLfBzgS-uDB2PQ4hxgT17x63nBgqOnH36w\",\"title\":\"المصفوفات شرح كامل\",\"channel\":\"Engineer Passion\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4ec201PNZr4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLfBzgS-uDB2PQ4hxgT17x63nBgqOnH36w\"},{\"playlistId\":\"PLYmn-2dhM6yvhLz4c8WjO_I2V5HaOQh26\",\"title\":\"التفاضل للمرحلة الأولى (كورس أول). نوار الأسدي\",\"channel\":\"نوار الأسدي (دورات إلكترونية)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/UgRYH3rjVug\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYmn-2dhM6yvhLz4c8WjO_I2V5HaOQh26\"}]}', '2026-02-12 17:17:24'),
('playlists:q:مقدمة في الأمن السيبراني|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLFGgC1KKHUI3ML-ThlCKLhfkjOqKXmYPV\",\"title\":\"مقدمة في الأمن السيبراني\",\"channel\":\"Saad Alqarni\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9etF0qpQ7BI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLFGgC1KKHUI3ML-ThlCKLhfkjOqKXmYPV\"},{\"playlistId\":\"PLwmxtpoM1Vi5ZCjBaC9bFWbYiWtSkpfUC\",\"title\":\"Cisco Introduction to Cyber security مقدمة الامن السيبرانى\",\"channel\":\"Ahmed abdelrazik\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4KtcomKXO-Q\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwmxtpoM1Vi5ZCjBaC9bFWbYiWtSkpfUC\"},{\"playlistId\":\"PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\",\"title\":\"الامن السيبراني والهكر الاخلاقي | Ethical Hacking\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/B_Xf9HAT-fc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7kMvzAOPNg8On9BfhV-us6p\"},{\"playlistId\":\"PLSybXM0LmOvkdJTVmWJiaajM3kmcr588-\",\"title\":\"مقدمة امن سيبراني - introduction to cybersecurity\",\"channel\":\"Dr. Kamal Alieyan (د. كمال عليان)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/1BCRJezZNrc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSybXM0LmOvkdJTVmWJiaajM3kmcr588-\"},{\"playlistId\":\"PLt7MnXUBn4g_NAael1IGBaPuzHSf6kcH8\",\"title\":\"كورس linux للامن السيبراني\",\"channel\":\"Zero One\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/tz1xkNwMWyw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLt7MnXUBn4g_NAael1IGBaPuzHSf6kcH8\"},{\"playlistId\":\"PLAQ9093fDW_bDj8o3n7hMjaIZgVimhCHI\",\"title\":\"سلسلة احترافية لشرح CompTIA Security+ (SY0-701) بالعربي | من البداية للاحتراف\",\"channel\":\"IT Learning and Troubleshooting\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/wiHAaX4qo78\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLAQ9093fDW_bDj8o3n7hMjaIZgVimhCHI\"},{\"playlistId\":\"PL9ooVrP1hQOGPQVeapGsJCktzIO4DtI4_\",\"title\":\"Cyber Security Training for Beginners | Edureka\",\"channel\":\"edureka!\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lpa8uy4DyMo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL9ooVrP1hQOGPQVeapGsJCktzIO4DtI4_\"},{\"playlistId\":\"PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\",\"title\":\"كورس الإختراق الأخلاقي | Ethical Hacking Course (from 0 to hero)\",\"channel\":\"الباشمبرمج | Hamed Esam\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/01heBtXmSIw\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8WMeMsGLndd6k07bHJkhTEpSaseMOfLq\"},{\"playlistId\":\"PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\",\"title\":\"احتراف أمن المعلومات - كورس كامل\",\"channel\":\"Ahmed El Hefny - Cybersecurity & GRC - بالعربي \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/j_rO-cNFYX0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpwHU9rNXAVs5dnnpmbuzcCB6lR_xKpRo\"},{\"playlistId\":\"PLhQjrBD2T383Cqo5I1oRrbC1EKRAKGKUE\",\"title\":\"CS50&#39;s Introduction to Cybersecurity\",\"channel\":\"CS50\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/kmJlnUfMd7I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhQjrBD2T383Cqo5I1oRrbC1EKRAKGKUE\"},{\"playlistId\":\"PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\",\"title\":\"شرح اساسيات ومفاهيم الشبكات - عماد نشأت\",\"channel\":\"IT Dose\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/q6tUCEUqxTQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8s4OGp0649_e_Wbz5MlBgW5rBW-9hD0c\"},{\"playlistId\":\"PLAqaqJU4wzYU_6EIzVoxHghQILgyY--yf\",\"title\":\"2026 Cisco CCNA v1.1 200-301 || المنهاج العربي الكامل\",\"channel\":\"III-Networking\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/TrQljnE49-o\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLAqaqJU4wzYU_6EIzVoxHghQILgyY--yf\"},{\"playlistId\":\"PLX621demLUSaA7ngeN7UfVzYJihHnEfv0\",\"title\":\"دورة اكتشاف ثغرات المواقع للمبتدئين شروحات وتطبيقات عملية | تعلم الامن السيبراني - Bug Bounty\",\"channel\":\"GenTiL Security | Ahmed Hamdy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/2G5EiZq8O4E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLX621demLUSaA7ngeN7UfVzYJihHnEfv0\"},{\"playlistId\":\"PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\",\"title\":\"كورس امن المعلومات\",\"channel\":\"GeekHood\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-Y1SvVnwvhU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYpOIWrvoIFMcZ04bKQlmY9Dt2hxSGQco\"},{\"playlistId\":\"PLe3wTuMiw39ppeYLp7NwhCYYeGjPKn5e1\",\"title\":\"دورة Cyber Security 101 من موقع TryHackMe\",\"channel\":\"Boot2Root\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ILTNfGPszCk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLe3wTuMiw39ppeYLp7NwhCYYeGjPKn5e1\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"},{\"playlistId\":\"PLH-n8YK76vIiDdOMRB-ylvns-_8Zl1euV\",\"title\":\"CompTIA A+\",\"channel\":\"Sameh Ramadan Tech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/zIpF33NCgrA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH-n8YK76vIiDdOMRB-ylvns-_8Zl1euV\"},{\"playlistId\":\"PLqAMdk4yWL4tEngBHnfJnfkjU6t2U7G7h\",\"title\":\"كورس علم التشفير\",\"channel\":\"Root Security (‫للخدمات التقنية‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/rzyQvgRRBEk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLqAMdk4yWL4tEngBHnfJnfkjU6t2U7G7h\"},{\"playlistId\":\"PLLlr6jKKdyK3C22AuU70h5xWe0ePmsbT7\",\"title\":\"CompTIA Security+ شرح بالعربي للمهندس حسن صالح\",\"channel\":\"Information Technology\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/dWH6BqyxRmc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLLlr6jKKdyK3C22AuU70h5xWe0ePmsbT7\"},{\"playlistId\":\"PLky4bd7_03m8o1NB0j96OsxZs0KcKlgMO\",\"title\":\"Security+ (SY0-601) شرح عربي\",\"channel\":\"Netriders - نت رايدرز\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/dyKg_bQOXfU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLky4bd7_03m8o1NB0j96OsxZs0KcKlgMO\"},{\"playlistId\":\"PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\",\"title\":\"مقرر مبادئ نظم المعلومات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ITJRTb6Yv1Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\"},{\"playlistId\":\"PLTr1xN4uMK5seRz6IO7Am9Zp2UKdnzO_n\",\"title\":\"Operating Systems in Arabic - شرح نظم التشغيل\",\"channel\":\"SoftwareTube\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/BW90V5-J4a0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLTr1xN4uMK5seRz6IO7Am9Zp2UKdnzO_n\"},{\"playlistId\":\"PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\",\"title\":\"C++ Programming Course Level 1 Basics By Arabic   كورس لغه برمجه سي بلس بلس المستوي الاول الاساسيات بالعربي\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oKA0aq4BdiY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\"},{\"playlistId\":\"PLSRx5jmWD9u3d3H5BKkYqhAOgRlK1iSwe\",\"title\":\"Operating Systems | نظم التشغيل | شرح عربي (كلية حاسبات جامعة الطائف)\",\"channel\":\"si-manual\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/a0zuH0U4Ab8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSRx5jmWD9u3d3H5BKkYqhAOgRlK1iSwe\"}]}', '2026-02-12 17:30:05'),
('playlists:q:مقدمة في البرمجة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\",\"title\":\"تعلم أساسيات البرمجة للمبتدئين\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9ndH9Qo05F4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN\"},{\"playlistId\":\"PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\",\"title\":\"ما قبل تعلم البرمجة\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aK46A6jQ1RM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"},{\"playlistId\":\"PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\",\"title\":\"كورس رقم 1 - البداية من هنا: سلسة اساسيات مهمه لكل مبرمج - المستوى الاول\",\"channel\":\"Programming Advices\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LWCBg5tb64I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3X--QIIK-OHgMV2yBz3GLfM5d_5BxOSj\"},{\"playlistId\":\"PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\",\"title\":\"كورس cs50 بالعربي كامل | Cs50 Tutorial In Arabic\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pSc6RGEBLAQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OvMsFbU9zo8oJCprAsgc4LO\"},{\"playlistId\":\"PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\",\"title\":\"C++ Programming Course Level 1 Basics By Arabic   كورس لغه برمجه سي بلس بلس المستوي الاول الاساسيات بالعربي\",\"channel\":\"محمد شوشان\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oKA0aq4BdiY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLnzqK5HvcpwQ_nQt-hKGAEIDJjTJBCV02\"}]}', '2026-02-05 17:20:38');
INSERT INTO `youtube_cache` (`cache_key`, `json`, `updated_at`) VALUES
('playlists:q:مقدمة في الذكاء الاصطناعي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\",\"title\":\"كيف تتعلم الذكاء الاصطناعي بايثون - المسار الكامل دورة مجانية\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-9SqKQssejY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\"},{\"playlistId\":\"PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\",\"title\":\"كورس مقدمه في الذكاء الاصطناعي - AI Introduction for Beginners\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lvuhqwLwJZ4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\"},{\"playlistId\":\"PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\",\"title\":\"محاضرات ذكاء اصطناعي |  Artificial Intelligence (AI) Lectures\",\"channel\":\"محمد داود Mohammad Dawoud\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/H5WUwwivEaI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\"},{\"playlistId\":\"PLH0em1f_fBoT1xlF8F4bD2CQd--FZ3RR8\",\"title\":\"كورس الذكاء الاصطناعي و تعلم الالة\",\"channel\":\"TatworX\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WI9-woIhyL0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH0em1f_fBoT1xlF8F4bD2CQd--FZ3RR8\"},{\"playlistId\":\"PL6uPDJZGhQEoUxpgEZckEY_BSsamvqRwR\",\"title\":\"محاضرات الذكاء الاصطناعي\",\"channel\":\"Dr. Ali Hilal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/O0vQapzQMvo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL6uPDJZGhQEoUxpgEZckEY_BSsamvqRwR\"}]}', '2026-02-05 17:20:56'),
('playlists:q:مقدمة في علم البيانات والذكاء الاصطناعي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\",\"title\":\"محاضرات ذكاء اصطناعي |  Artificial Intelligence (AI) Lectures\",\"channel\":\"محمد داود Mohammad Dawoud\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/H5WUwwivEaI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbR_CTcUs1088jfqbbO5AqwODO9MgYA85\"},{\"playlistId\":\"PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\",\"title\":\"كورس مقدمه في الذكاء الاصطناعي - AI Introduction for Beginners\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lvuhqwLwJZ4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAa48zcXmM08DonedIxZcoz5\"},{\"playlistId\":\"PLWd4nYaF_Vx65cPZF_I2OpWERatzh5Gdj\",\"title\":\"أساسيات علم البيانات | Data Science Foundations\",\"channel\":\"Mustafa Othman\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WM2LiUDa4jE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWd4nYaF_Vx65cPZF_I2OpWERatzh5Gdj\"},{\"playlistId\":\"PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\",\"title\":\"كيف تتعلم الذكاء الاصطناعي بايثون - المسار الكامل دورة مجانية\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-9SqKQssejY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7mmj3OrpQK5yJi7wPvu59jN\"},{\"playlistId\":\"PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\",\"title\":\"تعلم الآلة بالعربي || Machine Learning in Arabic\",\"channel\":\"Elgouhary AI\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/95RhuC30R5U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLtsZ69x5q-X9j44MdSX-NGuOhGXOY0aqH\"},{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PLoOabVweB2r5dL0AVmuDbS54UvmCIlZsT\",\"title\":\"أساسيات تعلم الآلة | Basics of Machine Learning\",\"channel\":\"Omar Alharbi\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lUcZUCKDXbo\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoOabVweB2r5dL0AVmuDbS54UvmCIlZsT\"},{\"playlistId\":\"PL6-3IRz2XF5Vf1RAHyBo4tRzT8lEavPhR\",\"title\":\"01 machine learning  تعليم الآلة , القسم الأول  :  مقدمة\",\"channel\":\"Hesham Asem\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/QQeT4z5rdtE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL6-3IRz2XF5Vf1RAHyBo4tRzT8lEavPhR\"},{\"playlistId\":\"PLUQDw_ve-LUA0a6OXM1JASSMeFWq44HsB\",\"title\":\"الذكاء الإصطناعي و تعلم الآلة 🎲\",\"channel\":\"Python Arabic Community\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/zPBj2kfvWIM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUQDw_ve-LUA0a6OXM1JASSMeFWq44HsB\"},{\"playlistId\":\"PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\",\"title\":\"كورس تحليل البيانات بالاكسل\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cPWC-WNchJk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\"},{\"playlistId\":\"PL-2sBQtgS7Y4c_fxh5udTpuVXIGx9cDOe\",\"title\":\"دورة د. جمعة داود: مقدمة نظرية في علم نظم المعلومات الجغرافية\",\"channel\":\"Gomaa Dawod\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GbZ3MQn-cOU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL-2sBQtgS7Y4c_fxh5udTpuVXIGx9cDOe\"},{\"playlistId\":\"PLWd4nYaF_Vx6EQZAyuh8F_eEsPhqNZ75K\",\"title\":\"خوارزميات تعلم الآلة والذكاء الاصطناعي | Machine Learning Algorithms\",\"channel\":\"Mustafa Othman\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ZehK8msMqus\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWd4nYaF_Vx6EQZAyuh8F_eEsPhqNZ75K\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\",\"title\":\"مقرر مبادئ نظم المعلومات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/ITJRTb6Yv1Y\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6LXpHtaTyRBok5XnpNzRIfA\"},{\"playlistId\":\"PLSiLeKadTQ7lqL6hzFBFr282jR3m39CLL\",\"title\":\"بايثون تعلم الذكاء الاصطناعي باللغة العربية (اساسيات + تطبيق عملي)\",\"channel\":\"راكوان للبرمجة - CodeRK\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/kFmDaYDVOZs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSiLeKadTQ7lqL6hzFBFr282jR3m39CLL\"},{\"playlistId\":\"PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\",\"title\":\"دورة تعلم بايثون من الصفر كاملة للمبتدئين - Master Python from Beginner to Advanced in Arabic\",\"channel\":\"Codezilla\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/h3VCQjyaLws\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLuXY3ddo_8nzrO74UeZQVZOb5-wIS6krJ\"},{\"playlistId\":\"PL02NhKT84RvUonRDSINTfGy4kgjBowvs0\",\"title\":\"شرح كامل ومبسط لتكنولوجيا المعلومات والاتصالات IT\",\"channel\":\"مصطفى العاصى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Au7aD0fSHl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL02NhKT84RvUonRDSINTfGy4kgjBowvs0\"},{\"playlistId\":\"PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\",\"title\":\"تعلم الخوارزميات من الصفر || Learn algorithms from scratch\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GZHJcvzEWCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\"},{\"playlistId\":\"PLDoPjvoNmBAyE_gei5d18qkfIe-Z8mocs\",\"title\":\"Mastering Python - تعلم بايثون\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mvZHDpCHphk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAyE_gei5d18qkfIe-Z8mocs\"},{\"playlistId\":\"PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\",\"title\":\"كورس بايثون - تعلم بايثون من الصفر للإحتراف\",\"channel\":\"OctuCode\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Do34NKMq80c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLoP3S2S1qTfCUdNazAZY1LFALcUr0Vbs9\"},{\"playlistId\":\"PLltPpA0o0tmyDULlYJGL5LVzI3pV0ETck\",\"title\":\"Computer Introduction - أساسيات الحاسب الآلي\",\"channel\":\"فارس خالد - Fares Khalid\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yLEqH5qgmbU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLltPpA0o0tmyDULlYJGL5LVzI3pV0ETck\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\",\"title\":\"ما قبل تعلم البرمجة\",\"channel\":\"Elzero Web School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aK46A6jQ1RM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDoPjvoNmBAx8xKvAXpb6f0Urj98Xo7zg\"},{\"playlistId\":\"PLxIvc-MGOs6gW9SgkmoxE5w9vQkID1_r-\",\"title\":\"Probability and Statistics | احتمالات وإحصاء\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GmJJ2iZz08c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6gW9SgkmoxE5w9vQkID1_r-\"}]}', '2026-02-05 17:21:13'),
('playlists:q:مهارات الاتصال والتواصل 1 (اللغة العربية)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\",\"title\":\"Communications Skills مهارات التواصل الفعال\",\"channel\":\"Mohamed Fathy Ibrahim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6Hi9ASB55n0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\"},{\"playlistId\":\"PLYPYBWtAMWoS3wS52pUzvcu_1KdhaK5WR\",\"title\":\"مقرر مهارات الإتصال\",\"channel\":\"عمادة التعليم الإلكتروني بجامعة القرآن الكريم\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SbV9j8ArIko\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYPYBWtAMWoS3wS52pUzvcu_1KdhaK5WR\"},{\"playlistId\":\"PL0VjGd7nigDsCC1OrJgvFLRVTmKWOcusP\",\"title\":\"كورس فن الخطابة والإلقاء\",\"channel\":\"مدرسة المهارات Skills School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PbHFXEcmwHI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL0VjGd7nigDsCC1OrJgvFLRVTmKWOcusP\"},{\"playlistId\":\"PL1sRzpnvOnXdJJpCQvDJaERXoz_7P-Xkz\",\"title\":\"كورس لغة الجسد ومهارات التواصل | أحمد صبري غباشي\",\"channel\":\"A. S. Ghobashy أحمد صبري غباشي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lKrKyJX87io\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1sRzpnvOnXdJJpCQvDJaERXoz_7P-Xkz\"},{\"playlistId\":\"PLDLBBwNEzOi2ufiMWPyUQE_rlYP1ERt2F\",\"title\":\"دورة معلمي اللغة العربية - موقع رواق\",\"channel\":\"العربية للجميع\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/O9X-na5AbUM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDLBBwNEzOi2ufiMWPyUQE_rlYP1ERt2F\"},{\"playlistId\":\"PL0VjGd7nigDsNi4bpLjfK4z9If7oB8GUr\",\"title\":\"كورس أساسيات العلاقات العامة\",\"channel\":\"مدرسة المهارات Skills School\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/AhVSJJC_AYU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL0VjGd7nigDsNi4bpLjfK4z9If7oB8GUr\"},{\"playlistId\":\"PLp22-4PivYmKp2aC7PI7K0POsf812xLBN\",\"title\":\"تعلم المحادثة باللغة الانجليزية - كورس المحادثة - المستوى الاول\",\"channel\":\"ZAmericanEnglish\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/82a1udwePtk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLp22-4PivYmKp2aC7PI7K0POsf812xLBN\"},{\"playlistId\":\"PLaQzmDaYayMCuW3Y0LhKnYON8u5_QQYsW\",\"title\":\"كورس لغة الإشارة للمبتدئين ( صدقة جارية لصديقي إسلام صلاح)\",\"channel\":\"هحببك فى الإشارة 🫡\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Tw44A1185uc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLaQzmDaYayMCuW3Y0LhKnYON8u5_QQYsW\"},{\"playlistId\":\"PLH_VAcx8r2csWeil-CsI8u1-sleQo0RL_\",\"title\":\"تعلم المحادثة باللغة الانجليزية - كورس المحادثة - المستوى الاول\",\"channel\":\"Let\'s Practice English | تعلم اللغة الانجليزية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mP1viG7l3bA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH_VAcx8r2csWeil-CsI8u1-sleQo0RL_\"},{\"playlistId\":\"PLRLeIfWP9Ccj_He_AFZVw3e9A20byPhOj\",\"title\":\"حصص حقيقية في تدريس القرآن الكريم واللغة العربية للأعاجم\",\"channel\":\"Online Tutors\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OAOb-DFtqhU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRLeIfWP9Ccj_He_AFZVw3e9A20byPhOj\"},{\"playlistId\":\"PL08ef9eJxtJZY7JdakCiFeovqAWwPDcaQ\",\"title\":\"مدخل إلى التربية الخاصة - 101 خاص\",\"channel\":\"المقررات المفتوحة - Open Courses\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/g7nckhJenFU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL08ef9eJxtJZY7JdakCiFeovqAWwPDcaQ\"},{\"playlistId\":\"PLUxZodnxUhN9oHJSz_1KxUFK8hAq9O4Zk\",\"title\":\"دورة متكاملة في منهج نور البيان\",\"channel\":\"Ayat Nawar\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/MGhSyY0fCUU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUxZodnxUhN9oHJSz_1KxUFK8hAq9O4Zk\"},{\"playlistId\":\"PLRScf6QNM5oUQ_Ylm04pYqr3cCafbyKmQ\",\"title\":\"كورس شامل لتعلم اللغة الانجليزية من الصفر للمبتدئين كورس كامل من البداية الى الاحتراف\",\"channel\":\"Learn English & French with Asmae\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/0rgISULLrY4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRScf6QNM5oUQ_Ylm04pYqr3cCafbyKmQ\"},{\"playlistId\":\"PL2ZbvaoJe0oSXhkaBMp6EuTygRGgAxU5g\",\"title\":\"مهارات لغوية انجليزي - انجليزي 101 -\",\"channel\":\"Dr. Raghda AlSharabati\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/TvYk0YHKTQ0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL2ZbvaoJe0oSXhkaBMp6EuTygRGgAxU5g\"},{\"playlistId\":\"PLzYkiToi3vWqLdUE9IgK63rfIg7Kjtxbj\",\"title\":\"دورة تعلم الحاسوب الشاملة\",\"channel\":\"‫اخذ فكرة (‪Idea4arab‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/IO-kVpdFxh4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLzYkiToi3vWqLdUE9IgK63rfIg7Kjtxbj\"},{\"playlistId\":\"PLbgETY0o4pfF8H-vxC6NE1blfwvbpnJhf\",\"title\":\"سلسلة شرح ديداكتيك اللغة العربية\",\"channel\":\"تربويات الأستاذ محمد بوالداج Bouddaj Mohamed \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/13Suy8hlcO4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLbgETY0o4pfF8H-vxC6NE1blfwvbpnJhf\"},{\"playlistId\":\"PLWTeUCa6D1KSDY5YJickJOHV6YuCQpGcD\",\"title\":\"كل ما تحتاجه عن التدريس أونلاين\",\"channel\":\"مدرسين أونلاين \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/pxRTjJxrn14\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWTeUCa6D1KSDY5YJickJOHV6YuCQpGcD\"},{\"playlistId\":\"PLXAtGPGIgEPNmmPyHqkL5j44911Ejzdlt\",\"title\":\"لغة إنجليزية إدارة الأعمال\",\"channel\":\"Mashaer Zyadi\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/X4yAWxcyXT0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXAtGPGIgEPNmmPyHqkL5j44911Ejzdlt\"},{\"playlistId\":\"PL2ZbvaoJe0oSV9iQCG2wlzq4mdup8Evvt\",\"title\":\"شرح كتاب interchange - مهارات لغوية - انجليزي ١٠١ - لغة انجليزية 101\",\"channel\":\"Dr. Raghda AlSharabati\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/T3QFkr5mhFI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL2ZbvaoJe0oSV9iQCG2wlzq4mdup8Evvt\"},{\"playlistId\":\"PL3AbfN3XjccBiK4wIb95H3bnOjtjL7NLc\",\"title\":\"الحروف العربية - منهج الوزارة الجديد - منهج كي جي1 - Arabic letters - KG1 curriculum - الحركات - التشكيل - فتحة - ضمة - كسرة\",\"channel\":\"Magic Land\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lt8KYO4-EgY\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL3AbfN3XjccBiK4wIb95H3bnOjtjL7NLc\"},{\"playlistId\":\"PL8giuXZQOjfYm8pFKk9NXs30bZjNkNyPW\",\"title\":\"تعلم الفرنسية من البداية إلى الاحتراف المستوى الأول A1\",\"channel\":\"Le français avec Nouma\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mGuJM1Ue0sk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8giuXZQOjfYm8pFKk9NXs30bZjNkNyPW\"},{\"playlistId\":\"PLmDpJ2TZD0x8Xp6P5jozjuVlS6qN00O1_\",\"title\":\"تأهيل معلمة رياض الأطفال .\",\"channel\":\"الطفل العبقرى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/vC94pyKPdeE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLmDpJ2TZD0x8Xp6P5jozjuVlS6qN00O1_\"},{\"playlistId\":\"PLxW3W5Tg2_5cJLmm_kaqCur1nzU0mM3LC\",\"title\":\"كورس اللغة الانجليزية للتمريض - English for Nursing\",\"channel\":\"Fouad\'s English\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/bH0JMqr4frs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxW3W5Tg2_5cJLmm_kaqCur1nzU0mM3LC\"},{\"playlistId\":\"PLRaCy-L8neoYhrvlD_v647S5o_5IaXuJY\",\"title\":\"Discrete Mathematics | الرياضيات المتقطعة\",\"channel\":\"Ammar Jabe - عمار جابر\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SzCJHhxlvzU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRaCy-L8neoYhrvlD_v647S5o_5IaXuJY\"},{\"playlistId\":\"PLW0eXp-s-kGCpV_mKPVf6YH4gP1Xm2WUV\",\"title\":\"تعليم الفرنسية للمبتدئين\",\"channel\":\"فرنشاوي Frenchawy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/fljUi554Qo4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLW0eXp-s-kGCpV_mKPVf6YH4gP1Xm2WUV\"}]}', '2026-02-05 17:20:45'),
('playlists:q:مهارات الاتصال والتواصل 2 (اللغة الإنجليزية)|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\",\"title\":\"Communications Skills مهارات التواصل الفعال\",\"channel\":\"Mohamed Fathy Ibrahim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6Hi9ASB55n0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\"},{\"playlistId\":\"PLHsMVYfJB8rkXhSmsQFCq7Sa6U-_A8ddc\",\"title\":\"مهارات تواصل Communication skills\",\"channel\":\"Dr. Ahmed Wael ‏𓂆🩺\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Kk3k7LGPm3E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHsMVYfJB8rkXhSmsQFCq7Sa6U-_A8ddc\"},{\"playlistId\":\"PLp22-4PivYmKp2aC7PI7K0POsf812xLBN\",\"title\":\"تعلم المحادثة باللغة الانجليزية - كورس المحادثة - المستوى الاول\",\"channel\":\"ZAmericanEnglish\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/82a1udwePtk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLp22-4PivYmKp2aC7PI7K0POsf812xLBN\"},{\"playlistId\":\"PLH_VAcx8r2csWeil-CsI8u1-sleQo0RL_\",\"title\":\"تعلم المحادثة باللغة الانجليزية - كورس المحادثة - المستوى الاول\",\"channel\":\"Let\'s Practice English | تعلم اللغة الانجليزية\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/mP1viG7l3bA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLH_VAcx8r2csWeil-CsI8u1-sleQo0RL_\"},{\"playlistId\":\"PLwxQU6XSUhX6GxfeiabCx2iRcka7puhAj\",\"title\":\"بزنس انجلش business English\",\"channel\":\"Sbeata Academy\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/gYMqtyKp81U\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLwxQU6XSUhX6GxfeiabCx2iRcka7puhAj\"},{\"playlistId\":\"PL2ZbvaoJe0oSXhkaBMp6EuTygRGgAxU5g\",\"title\":\"مهارات لغوية انجليزي - انجليزي 101 -\",\"channel\":\"Dr. Raghda AlSharabati\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/TvYk0YHKTQ0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL2ZbvaoJe0oSXhkaBMp6EuTygRGgAxU5g\"}]}', '2026-02-05 17:20:46'),
('playlists:q:مهارات الاتصال والكتابة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLqGEgt_8f5gTvVdZK6zIiQ6xCA_2RkQlM\",\"title\":\"كورس مهارات التواصل\",\"channel\":\"Et3alem by Innovito\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/29tewOLkmC0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLqGEgt_8f5gTvVdZK6zIiQ6xCA_2RkQlM\"},{\"playlistId\":\"PLFmDT2pAahUZameYtPpN7xUDxNf0OZ5AB\",\"title\":\"دورة مهارات الاتصال الفعال مع المدرب د. محمد العامري\",\"channel\":\"د. محمد العامري\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_8sjUN7oWVc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLFmDT2pAahUZameYtPpN7xUDxNf0OZ5AB\"},{\"playlistId\":\"PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\",\"title\":\"Communications Skills مهارات التواصل الفعال\",\"channel\":\"Mohamed Fathy Ibrahim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/6Hi9ASB55n0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHW3KAZhF3zVMDG7YzP4kqfWf90zcncuP\"},{\"playlistId\":\"PLYPYBWtAMWoS3wS52pUzvcu_1KdhaK5WR\",\"title\":\"مقرر مهارات الإتصال\",\"channel\":\"عمادة التعليم الإلكتروني بجامعة القرآن الكريم\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SbV9j8ArIko\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLYPYBWtAMWoS3wS52pUzvcu_1KdhaK5WR\"},{\"playlistId\":\"PLiY-jf8J2Mplc6151KJnXdzFYT9LO5Drs\",\"title\":\"Soft Skills - تعليم المهارات\",\"channel\":\"El Zatoona - الزتونة\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/LlnoeUO8-F8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLiY-jf8J2Mplc6151KJnXdzFYT9LO5Drs\"},{\"playlistId\":\"PL1sRzpnvOnXdJJpCQvDJaERXoz_7P-Xkz\",\"title\":\"كورس لغة الجسد ومهارات التواصل | أحمد صبري غباشي\",\"channel\":\"A. S. Ghobashy أحمد صبري غباشي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/lKrKyJX87io\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1sRzpnvOnXdJJpCQvDJaERXoz_7P-Xkz\"}]}', '2026-02-13 18:26:19'),
('playlists:q:مهارات الحاسوب (إجباري)|max:6', '{\"ok\":true,\"items\":[]}', '2026-02-05 17:20:48'),
('playlists:q:مواصفات البرمجيات وتصميمها|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\",\"title\":\"Software Engineering in Arabic |دورة هندسة البرمجيات\",\"channel\":\"EDU US\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Io_IAwUi4Ck\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\"},{\"playlistId\":\"PL08ef9eJxtJZvt5BOsT46vN6kWnflVKH4\",\"title\":\"Software Engineering  | هندسة برمجيات\",\"channel\":\"المقررات المفتوحة - Open Courses\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/23wr24zdmQM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL08ef9eJxtJZvt5BOsT46vN6kWnflVKH4\"},{\"playlistId\":\"PLknwEmKsW8OtLRQPTLms79499meY2D6ij\",\"title\":\"كورس html كامل بالعربي | html tutorial for beginners\",\"channel\":\"Abdelrahman Gamal\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Dv39fDYei9A\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij\"},{\"playlistId\":\"PLzTFSn-Bzi_yShxnBXoFz7vj-H3bcuULg\",\"title\":\"دورة تعليم البرمجة للأطفال وصناعة الألعاب باستخدام سكراتش من الالف الي الياء\",\"channel\":\"Codingua\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/BhRJ92pDDnQ\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLzTFSn-Bzi_yShxnBXoFz7vj-H3bcuULg\"},{\"playlistId\":\"PLDxVq3TlR9y2sMXaL_yLp-r6pUpevgC-w\",\"title\":\"Windows Server Administration (MCSA) - Arabic | By Mohamed Zohdy - شرح كورس عربي\",\"channel\":\"Mohamed Zohdy - محمد زهدي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PrGzyV99cm0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLDxVq3TlR9y2sMXaL_yLp-r6pUpevgC-w\"},{\"playlistId\":\"PLZZdF7TtQ_kqdj2LyO0FaEcLZ0fALMsGv\",\"title\":\"شروحات برامج الأوفيس | Microsoft Office Tutorials\",\"channel\":\"Nology - نولوجي\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/51l9s_YKN1E\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZZdF7TtQ_kqdj2LyO0FaEcLZ0fALMsGv\"}]}', '2026-02-12 17:17:26'),
('playlists:q:موضوعات خاصة في علم البيانات والذكاء الاصطناعي|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\",\"title\":\"دورة الذكاء الاصطناعي 2024\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/61yz7sVvQgs\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo-h0whWvRsE1KL_A08wpmSB\"},{\"playlistId\":\"PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\",\"title\":\"تعلم الخوارزميات من الصفر || Learn algorithms from scratch\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GZHJcvzEWCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\"},{\"playlistId\":\"PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\",\"title\":\"كورس تحليل البيانات بالاكسل\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cPWC-WNchJk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\"},{\"playlistId\":\"PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\",\"title\":\"دورة أساسيات الأمن السيبراني\",\"channel\":\"Secure The Humans\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/VyXQ8CMIQl4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLh2Jy0nKL_j1WZMzITHgUuzaadpSULlMm\"},{\"playlistId\":\"PLXWErycEfAf3uS1-3FwhGsbSmuCxw-r2v\",\"title\":\"دروس في الاعلام الآلي للمبتدئيين\",\"channel\":\"أخبار الساعة للتوظيف \\/Mr SALAH\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/nFM316v8OCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXWErycEfAf3uS1-3FwhGsbSmuCxw-r2v\"},{\"playlistId\":\"PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\",\"title\":\"Learn MATLAB in Arabic step by step تعلم كورس ماتلاب باللغة العربية) شرح برنامج الماتلاب كامل خطوة بخطوة عربى)\",\"channel\":\"Hashim EduTech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_di_oaAdEZM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\"}]}', '2026-02-05 17:20:42'),
('playlists:q:نظرية الحسابات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL6E1E19EB259776DC\",\"title\":\"مبادئ المحاسبة المالية\",\"channel\":\"Emad Khalifa\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_kpskjFFx3c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL6E1E19EB259776DC\"},{\"playlistId\":\"PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\",\"title\":\"تعلم الخوارزميات من الصفر || Learn algorithms from scratch\",\"channel\":\"aymen hadouara\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/GZHJcvzEWCU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLRtfJqT1hc33qtw3SV7SItJF1oV_jwEfu\"},{\"playlistId\":\"PLySiPMvmEyCTG5EThKH8s6mHTYkctPsj8\",\"title\":\"دورة مبادئ المحاسبة المالية\",\"channel\":\"Mohammed A.Hassan\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/7FYCg_S_ZYc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLySiPMvmEyCTG5EThKH8s6mHTYkctPsj8\"},{\"playlistId\":\"PL7nhsj3rJk8OaqbNuZs222lRbc-cR3OSQ\",\"title\":\"Fundamentals in Mathematical Logic) (أسس الرياضيات والمنطق الرياضي)\",\"channel\":\"واستبقوا الخيرات\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/27xONR8d6ZI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL7nhsj3rJk8OaqbNuZs222lRbc-cR3OSQ\"},{\"playlistId\":\"PLpTIcoD4Wsj1Wml7Xmsxdk_-oHcCDeSd-\",\"title\":\"كورس لاحتراف التحليل الفني (د محمد مهدي)\",\"channel\":\"MAMO FX TRADING MOHAMED MAHDYY\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/zw9sz0bW5_c\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLpTIcoD4Wsj1Wml7Xmsxdk_-oHcCDeSd-\"},{\"playlistId\":\"PLxIvc-MGOs6gZlMVYOOEtUHJmfUquCjwz\",\"title\":\"Discrete Mathematics | الرياضيات المتقطعة\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/eFDzhn1Inc4\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6gZlMVYOOEtUHJmfUquCjwz\"}]}', '2026-02-11 20:41:44'),
('playlists:q:نظم إدارة قواعد البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLZNz7wrFA85D5pSkOH0_eWLd3Ep2d4AOp\",\"title\":\"DBMS نظم إدارة قواعد البيانات | محمد الدسوقي\",\"channel\":\"1bit.\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/06cR85c55Oc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLZNz7wrFA85D5pSkOH0_eWLd3Ep2d4AOp\"},{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PL37D52B7714788190\",\"title\":\"Database 1 - المقرر النظرى - Fundamentals of Database Systems\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/yLc0Yp5QZlU\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL37D52B7714788190\"},{\"playlistId\":\"PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\",\"title\":\"SQL Course For Beginners - Learn in Arabic - دورة كاملة SQL للمبتدئين بالعربي\",\"channel\":\"كورسات في البرمجة - Korsat X Parmaga\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/98LuqEZlZis\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxbVBWjVdAEj8TmOUKPG0avUmLqSoQOpf\"},{\"playlistId\":\"PLU-YAlzJSL4jezoeWW7cA3Iqf5hHlE9A-\",\"title\":\"إدارة قواعد البيانات\",\"channel\":\"dr.Ramzi Matar\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/JYG9Dkq9tkI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLU-YAlzJSL4jezoeWW7cA3Iqf5hHlE9A-\"},{\"playlistId\":\"PL93xoMrxRJIuicqcd1UpFUYMfWKGp7JmI\",\"title\":\"تعلم التعامل مع قواعد البيانات Sql - mysql - course - من الصفر الى الاحتراف  - learn - tutorial - شرح - تعلم - كورس\",\"channel\":\"Wael abo hamza\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/vfUMzsUqqb0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL93xoMrxRJIuicqcd1UpFUYMfWKGp7JmI\"}]}', '2026-02-12 17:17:25'),
('playlists:q:نظم التشغيل|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLTr1xN4uMK5seRz6IO7Am9Zp2UKdnzO_n\",\"title\":\"Operating Systems in Arabic - شرح نظم التشغيل\",\"channel\":\"SoftwareTube\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/BW90V5-J4a0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLTr1xN4uMK5seRz6IO7Am9Zp2UKdnzO_n\"},{\"playlistId\":\"PLxIvc-MGOs6ib0oK1z9C46DeKd9rRcSMY\",\"title\":\"Operating Systems | نظم التشغيل\",\"channel\":\"Dr. Ahmed Hagag\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/NzyuxPtrRRM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLxIvc-MGOs6ib0oK1z9C46DeKd9rRcSMY\"},{\"playlistId\":\"PLSRx5jmWD9u3d3H5BKkYqhAOgRlK1iSwe\",\"title\":\"Operating Systems | نظم التشغيل | شرح عربي (كلية حاسبات جامعة الطائف)\",\"channel\":\"si-manual\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/a0zuH0U4Ab8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSRx5jmWD9u3d3H5BKkYqhAOgRlK1iSwe\"},{\"playlistId\":\"PLHKTPL-jkzUqgHIBdC2I16QqZCSDN_6oS\",\"title\":\"Operating Systems Course\",\"channel\":\"Mustafa S. Aljumaily\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/aRSo7wkV8Ks\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLHKTPL-jkzUqgHIBdC2I16QqZCSDN_6oS\"},{\"playlistId\":\"PLKlTaCM87WvrO0RElCsK30uS-ylzRLgFM\",\"title\":\"Operating System Concepts\",\"channel\":\"Dr. Hassan Alansary\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/qLxlWgm-N50\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLKlTaCM87WvrO0RElCsK30uS-ylzRLgFM\"},{\"playlistId\":\"PLSrgC_BTeDInJyR2E6H11wMdhY1xcIIV7\",\"title\":\"Operating Systems [In Arabic] انظمة التشغيل\",\"channel\":\"Prof. Saleh Oqeili Lectures\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/-eoajxtpSCM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSrgC_BTeDInJyR2E6H11wMdhY1xcIIV7\"}]}', '2026-02-13 18:26:21'),
('playlists:q:نمذجة وتحليل أنظمة المعرفة|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\",\"title\":\"قواعد بيانات نظري - DataBases\",\"channel\":\"نعمه ماجد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/4oDUwo39rx0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLUgzfC8Ca25u_fqLoYcAl2CVbDNfLWqVx\"},{\"playlistId\":\"PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\",\"title\":\"Learn MATLAB in Arabic step by step تعلم كورس ماتلاب باللغة العربية) شرح برنامج الماتلاب كامل خطوة بخطوة عربى)\",\"channel\":\"Hashim EduTech\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/_di_oaAdEZM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhx4zaYkEjI_UDzrbLqeaqPisFz1OPnoQ\"},{\"playlistId\":\"PLSTOfdieMgOIKevRNfpf7kd5QxxoykwnE\",\"title\":\"SAP 2000 Free Course | كورس ساب 2000 المجاني\",\"channel\":\"Mohamed Ayman - محمد ايمن\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cnu4YQk7ycM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLSTOfdieMgOIKevRNfpf7kd5QxxoykwnE\"},{\"playlistId\":\"PLBMU2ZVTzYmF6VGwYNEf29qAwFBNnvDs0\",\"title\":\"تصميم شبكة الصرف الصحي - Sewage Network Design\",\"channel\":\"برامج البنية التحتية - Dr Adel Sakr\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/zaD8P0qV0bI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLBMU2ZVTzYmF6VGwYNEf29qAwFBNnvDs0\"}]}', '2026-02-05 17:21:02'),
('playlists:q:هندسة البيانات والتحليلات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLPCVzH419ffot8zn1vkcRKh8Wowj4yWuv\",\"title\":\"Data Engineering End to End Course - هندسة البيانات نظري وعملي\",\"channel\":\"Arabs Data Talks\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/kxV-39mqRhA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLPCVzH419ffot8zn1vkcRKh8Wowj4yWuv\"},{\"playlistId\":\"PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\",\"title\":\"كورس تحليل البيانات للمبتدأين من الصفر\",\"channel\":\"داتا ساينس بالعربي .. Data Science \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOv1gE0ER3I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\"},{\"playlistId\":\"PLCVy8dqNAIgnrGoeg9_JHyncFWjpn-mbH\",\"title\":\"كورس تحليل البيانات\",\"channel\":\"كويري بلس- ماهر المحمد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/5lCRC_JnQpA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCVy8dqNAIgnrGoeg9_JHyncFWjpn-mbH\"},{\"playlistId\":\"PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\",\"title\":\"كورس شامل لتحليل البيانات – Excel &amp; Power BI خطوة بخطوة\",\"channel\":\"ElSearshgy - السيرشجى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/RMQLzz1rwBE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\"},{\"playlistId\":\"PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\",\"title\":\"كورس تحليل البيانات بالاكسل\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cPWC-WNchJk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\"},{\"playlistId\":\"PLXlHqMRg9lAZPJ5loaPck60I91kAwMhT3\",\"title\":\"كورس تحليل البيانات ب باور بي أي - Power BI Course\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v_re3WCPOjI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAZPJ5loaPck60I91kAwMhT3\"}]}', '2026-02-05 17:21:01'),
('playlists:q:هندسة البيانات وتحليلها|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\",\"title\":\"كورس تحليل البيانات للمبتدأين من الصفر\",\"channel\":\"داتا ساينس بالعربي .. Data Science \",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/oOv1gE0ER3I\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLonOrMR73lKiRTVZ65tKXwczwi09qzlUt\"},{\"playlistId\":\"PLCVy8dqNAIgnrGoeg9_JHyncFWjpn-mbH\",\"title\":\"كورس تحليل البيانات\",\"channel\":\"كويري بلس- ماهر المحمد\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/5lCRC_JnQpA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCVy8dqNAIgnrGoeg9_JHyncFWjpn-mbH\"},{\"playlistId\":\"PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\",\"title\":\"كورس شامل لتحليل البيانات – Excel &amp; Power BI خطوة بخطوة\",\"channel\":\"ElSearshgy - السيرشجى\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/RMQLzz1rwBE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLQc5NCWN0ZtStav6-Cz4u9dzEj6Z3djlQ\"},{\"playlistId\":\"PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\",\"title\":\"كورس تحليل البيانات بالاكسل\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/cPWC-WNchJk\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAbetpJy3ePXsN0sj9Zs-pvT\"},{\"playlistId\":\"PLWd4nYaF_Vx65cPZF_I2OpWERatzh5Gdj\",\"title\":\"أساسيات علم البيانات | Data Science Foundations\",\"channel\":\"Mustafa Othman\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/WM2LiUDa4jE\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLWd4nYaF_Vx65cPZF_I2OpWERatzh5Gdj\"},{\"playlistId\":\"PLXlHqMRg9lAZPJ5loaPck60I91kAwMhT3\",\"title\":\"كورس تحليل البيانات ب باور بي أي - Power BI Course\",\"channel\":\"Mohamed Al Assaal - اتعلم مع العسال\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/v_re3WCPOjI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLXlHqMRg9lAZPJ5loaPck60I91kAwMhT3\"}]}', '2026-02-13 18:26:21'),
('playlists:q:هندسة متطلبات البرمجيات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\",\"title\":\"Software Engineering in Arabic |دورة هندسة البرمجيات\",\"channel\":\"EDU US\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Io_IAwUi4Ck\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL4mqzqquSRgaJ9XMQMUvMQjPyllD1xY5f\"},{\"playlistId\":\"PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\",\"title\":\"كورس هندسة البرمجيات كامل  - Software engineering course\",\"channel\":\"محمدنور\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/PdER1jZ9nVI\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLePWW30iFqTDqvRJOYEZvaj4NZE5rf65x\"},{\"playlistId\":\"PL08ef9eJxtJZvt5BOsT46vN6kWnflVKH4\",\"title\":\"Software Engineering  | هندسة برمجيات\",\"channel\":\"المقررات المفتوحة - Open Courses\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/23wr24zdmQM\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL08ef9eJxtJZvt5BOsT46vN6kWnflVKH4\"},{\"playlistId\":\"PLvGNfY-tFUN-JHyP9JzNP0oME_Ldc_3W4\",\"title\":\"كورس هندسة البرمجيات\",\"channel\":\"غريب الشيخ || Ghareeb Elshaikh\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/tEwcoLbGNg8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLvGNfY-tFUN-JHyP9JzNP0oME_Ldc_3W4\"},{\"playlistId\":\"PL8Fh9xEtNNzdXl3n8VMFcZb6zzO4JkEXw\",\"title\":\"Introduction to Software Engineering - مقدمة في هندسة البرمجيات\",\"channel\":\"Software engineering\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Jhe_CnwKG-8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL8Fh9xEtNNzdXl3n8VMFcZb6zzO4JkEXw\"},{\"playlistId\":\"PLquXYvvn8Qk-Yb-ytydSIePeSwTtQmPSX\",\"title\":\"Software Engineering - هندسة البرمجيات\",\"channel\":\"Ahmed Alageed\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/Kbt0cfPxTO8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLquXYvvn8Qk-Yb-ytydSIePeSwTtQmPSX\"}]}', '2026-02-12 17:17:26'),
('playlists:q:هياكل البيانات|max:6', '{\"ok\":true,\"items\":[{\"playlistId\":\"PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\",\"title\":\"Data Structures Full Course In Arabic\",\"channel\":\"Adel Nasim\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/owCqVRbZlbg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp\"},{\"playlistId\":\"PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\",\"title\":\"كورس الـ data structure بلغة الـ c++\",\"channel\":\"Mega Code\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/SbDUW7_G3Xc\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLsGJzJ8SQXTcsXRVviurGei0lf_t_I4D8\"},{\"playlistId\":\"PLhiFu-f80eo--m0YnPpiiWGd4mq-4V2rY\",\"title\":\"Data Structure هياكل البيانات\",\"channel\":\"تكنو U\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/O2llLrg7Ys8\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLhiFu-f80eo--m0YnPpiiWGd4mq-4V2rY\"},{\"playlistId\":\"PLF8OvnCBlEY3a1pbPrE6fvNuV3qi-6KRf\",\"title\":\"Data Structure and Algorithms analysis || الخوارزميات وهيكلة البيانات\",\"channel\":\"TheNewBaghdad (‫بغداد الجديدة‬‎)\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/eNOZjwJVIxg\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PLF8OvnCBlEY3a1pbPrE6fvNuV3qi-6KRf\"},{\"playlistId\":\"PL1DUmTEdeA6JlommmGP5wicYLxX5PVCQt\",\"title\":\"C++ Data Structures - تراكيب البيانات\",\"channel\":\"‫محمد الدسوقى (‪Mohamed El Desouki‬‏)‬‎\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/jGP19W5IObA\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL1DUmTEdeA6JlommmGP5wicYLxX5PVCQt\"},{\"playlistId\":\"PL88kafUXXgBaAgb0h3-ZMvzxb5J2qFrut\",\"title\":\"دورة الخوارزميات و هياكل البيانات | Algorithms and Data Structures Course in Arabic\",\"channel\":\"litprog\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/DLo8dLBtI-0\\/mqdefault.jpg\",\"url\":\"https:\\/\\/www.youtube.com\\/playlist?list=PL88kafUXXgBaAgb0h3-ZMvzxb5J2qFrut\"}]}', '2026-02-05 17:20:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_rate_limits`
--
ALTER TABLE `api_rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_ip_action_window` (`ip`,`action`,`window_start`);

--
-- Indexes for table `auth_emails`
--
ALTER TABLE `auth_emails`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_title` (`user_id`,`title`),
  ADD UNIQUE KEY `uniq_cert_token` (`token`),
  ADD KEY `idx_cert_user` (`user_id`);

--
-- Indexes for table `code_rewards`
--
ALTER TABLE `code_rewards`
  ADD PRIMARY KEY (`user_id`,`problem_id`),
  ADD KEY `idx_cr_user` (`user_id`),
  ADD KEY `idx_cr_problem` (`problem_id`);

--
-- Indexes for table `coins_ledger`
--
ALTER TABLE `coins_ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_submissions`
--
ALTER TABLE `course_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `major_courses`
--
ALTER TABLE `major_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_training_id` (`training_id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `partner_phase2_projects`
--
ALTER TABLE `partner_phase2_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_user_id` (`partner_user_id`),
  ADD KEY `course_code` (`course_code`);

--
-- Indexes for table `partner_phase3_projects`
--
ALTER TABLE `partner_phase3_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_user_id` (`partner_user_id`);

--
-- Indexes for table `partner_playlists`
--
ALTER TABLE `partner_playlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_partner_slug` (`partner_user_id`,`slug`),
  ADD KEY `partner_user_id` (`partner_user_id`);

--
-- Indexes for table `partner_playlist_majors`
--
ALTER TABLE `partner_playlist_majors`
  ADD PRIMARY KEY (`playlist_id`,`major_id`),
  ADD KEY `major_id` (`major_id`);

--
-- Indexes for table `partner_videos`
--
ALTER TABLE `partner_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_user_id` (`partner_user_id`);

--
-- Indexes for table `partner_video_code_problems`
--
ALTER TABLE `partner_video_code_problems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pvcp_video` (`partner_video_id`),
  ADD KEY `idx_pvcp_partner` (`partner_user_id`,`partner_video_id`);

--
-- Indexes for table `partner_video_quizzes`
--
ALTER TABLE `partner_video_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_user_id` (`partner_user_id`),
  ADD KEY `partner_video_id` (`partner_video_id`);

--
-- Indexes for table `partner_video_submissions`
--
ALTER TABLE `partner_video_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_video_student` (`partner_video_id`,`student_user_id`),
  ADD KEY `idx_video` (`partner_video_id`),
  ADD KEY `idx_student` (`student_user_id`);

--
-- Indexes for table `plan_analysis`
--
ALTER TABLE `plan_analysis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user` (`user_id`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_ip_ep_window` (`ip`,`endpoint`,`window_start`);

--
-- Indexes for table `student_attachments`
--
ALTER TABLE `student_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_performance`
--
ALTER TABLE `student_performance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_quiz` (`user_id`,`quiz_id`),
  ADD KEY `idx_user_created` (`user_id`,`created_at`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `student_video_progress`
--
ALTER TABLE `student_video_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_video` (`user_id`,`video_id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `study_plans`
--
ALTER TABLE `study_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plan_key` (`plan_key`);

--
-- Indexes for table `study_plan_courses`
--
ALTER TABLE `study_plan_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_spc_plan` (`plan_id`);

--
-- Indexes for table `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `training_rewards`
--
ALTER TABLE `training_rewards`
  ADD PRIMARY KEY (`user_id`,`training_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_behavior`
--
ALTER TABLE `user_behavior`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_ts` (`user_id`,`ts`),
  ADD KEY `idx_event` (`event_type`);

--
-- Indexes for table `user_level_predictions`
--
ALTER TABLE `user_level_predictions`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_plan_courses`
--
ALTER TABLE `user_plan_courses`
  ADD PRIMARY KEY (`user_id`,`course_code`),
  ADD KEY `idx_upc_required` (`user_id`,`is_required`);

--
-- Indexes for table `user_plan_profile`
--
ALTER TABLE `user_plan_profile`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `training_id` (`training_id`),
  ADD KEY `idx_videos_youtube_id` (`youtube_id`);

--
-- Indexes for table `video_progress`
--
ALTER TABLE `video_progress`
  ADD PRIMARY KEY (`user_id`,`video_id`);

--
-- Indexes for table `video_quiz_attempts`
--
ALTER TABLE `video_quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_video` (`user_id`,`video_id`),
  ADD UNIQUE KEY `uniq_user_video_attempt` (`user_id`,`video_id`);

--
-- Indexes for table `video_quiz_cache`
--
ALTER TABLE `video_quiz_cache`
  ADD PRIMARY KEY (`video_id`);

--
-- Indexes for table `video_rewards`
--
ALTER TABLE `video_rewards`
  ADD UNIQUE KEY `uniq_user_youtube` (`user_id`,`youtube_id`);

--
-- Indexes for table `youtube_cache`
--
ALTER TABLE `youtube_cache`
  ADD PRIMARY KEY (`cache_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_rate_limits`
--
ALTER TABLE `api_rate_limits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coins_ledger`
--
ALTER TABLE `coins_ledger`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_submissions`
--
ALTER TABLE `course_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `major_courses`
--
ALTER TABLE `major_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `partner_phase2_projects`
--
ALTER TABLE `partner_phase2_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `partner_phase3_projects`
--
ALTER TABLE `partner_phase3_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `partner_playlists`
--
ALTER TABLE `partner_playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `partner_videos`
--
ALTER TABLE `partner_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `partner_video_code_problems`
--
ALTER TABLE `partner_video_code_problems`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `partner_video_quizzes`
--
ALTER TABLE `partner_video_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `partner_video_submissions`
--
ALTER TABLE `partner_video_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `plan_analysis`
--
ALTER TABLE `plan_analysis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_attachments`
--
ALTER TABLE `student_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `student_performance`
--
ALTER TABLE `student_performance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `student_video_progress`
--
ALTER TABLE `student_video_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `study_plans`
--
ALTER TABLE `study_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `study_plan_courses`
--
ALTER TABLE `study_plan_courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353;

--
-- AUTO_INCREMENT for table `trainings`
--
ALTER TABLE `trainings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `user_behavior`
--
ALTER TABLE `user_behavior`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `video_quiz_attempts`
--
ALTER TABLE `video_quiz_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `major_courses`
--
ALTER TABLE `major_courses`
  ADD CONSTRAINT `fk_major_training` FOREIGN KEY (`training_id`) REFERENCES `trainings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `partner_playlist_majors`
--
ALTER TABLE `partner_playlist_majors`
  ADD CONSTRAINT `fk_ppm_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `partner_playlists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_attachments`
--
ALTER TABLE `student_attachments`
  ADD CONSTRAINT `fk_student_attach_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `study_plan_courses`
--
ALTER TABLE `study_plan_courses`
  ADD CONSTRAINT `fk_spc_plan` FOREIGN KEY (`plan_id`) REFERENCES `study_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_plan_courses`
--
ALTER TABLE `user_plan_courses`
  ADD CONSTRAINT `fk_upc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_plan_profile`
--
ALTER TABLE `user_plan_profile`
  ADD CONSTRAINT `fk_upp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_quiz_attempts`
--
ALTER TABLE `video_quiz_attempts`
  ADD CONSTRAINT `fk_vqa_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
