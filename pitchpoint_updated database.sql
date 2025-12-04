-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 05:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pitchpoint`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `activity_description` text NOT NULL,
  `status` enum('Success','Failed','Warning') NOT NULL DEFAULT 'Success',
  `logged_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`log_id`, `admin_id`, `activity_description`, `status`, `logged_at`) VALUES
(1, 1, 'Admin account created successfully.', 'Success', '2025-10-29 11:59:23'),
(6, 1, 'Failed login', 'Failed', '2025-11-21 16:31:02'),
(7, 1, 'Failed login', 'Failed', '2025-11-21 16:34:55'),
(8, 1, 'Failed login', 'Failed', '2025-11-21 16:35:13'),
(9, 1, 'Failed login', 'Failed', '2025-11-21 16:36:01'),
(10, 1, 'Failed login', 'Failed', '2025-11-21 16:39:07'),
(11, 1, 'Failed login', 'Failed', '2025-11-21 16:53:06'),
(12, 1, 'Failed login', 'Failed', '2025-11-21 16:53:41'),
(13, 1, 'Logged in', 'Success', '2025-11-21 17:07:21'),
(14, 1, 'Logged out', 'Success', '2025-11-21 17:07:50'),
(15, 1, 'Logged in', 'Success', '2025-11-21 17:08:07'),
(16, 1, 'Logged out', 'Success', '2025-11-21 17:57:00'),
(17, 1, 'Logged in', 'Success', '2025-11-21 18:25:47'),
(18, 1, 'Logged in', 'Success', '2025-11-21 18:28:37'),
(19, 1, 'Logged in', 'Success', '2025-11-21 18:44:04'),
(20, 1, 'Logged in', 'Success', '2025-11-21 18:46:05'),
(21, 1, 'Logged out', 'Success', '2025-11-21 18:47:38'),
(22, 1, 'Logged in', 'Success', '2025-11-21 18:50:31'),
(23, 1, 'Logged out', 'Success', '2025-11-21 18:50:34'),
(24, 1, 'Logged in', 'Success', '2025-11-21 19:14:19'),
(25, 1, 'Logged in', 'Success', '2025-11-21 22:13:27'),
(26, 1, 'Logged in', 'Success', '2025-11-23 04:20:09'),
(27, 1, 'Logged out', 'Success', '2025-11-23 04:31:57'),
(28, 1, 'Logged in', 'Success', '2025-11-23 04:32:25'),
(29, 1, 'Logged in', 'Success', '2025-11-26 11:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `admin_name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`admin_id`, `admin_name`, `email`, `password_hash`, `is_active`, `last_login`) VALUES
(1, 'Rafia Tasnim', 'rafia@pitchpoint.com', '$2y$10$Mn7T8jnozDpcpF1DhiwM/uZbuGOspX7UATHNypvQHo9j/5N5xa4p6', 1, '2025-11-26 11:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `notification_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `receiver_email` varchar(190) DEFAULT NULL,
  `sent_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`notification_id`, `admin_id`, `message`, `receiver_email`, `sent_date`) VALUES
(1, 1, 'New project submitted: Time Management (ID 10). Please review.', 'rafia@pitchpoint.com', '2025-11-14 11:10:00'),
(2, 1, 'New investment recorded on project Rent-A-Skill Marketplace (ID 15).', 'rafia@pitchpoint.com', '2025-11-17 10:56:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(80) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Health', NULL),
(2, 'FinTech', NULL),
(3, 'EdTech', NULL),
(4, 'Technology', NULL),
(5, 'Education', NULL),
(6, 'Food', NULL),
(7, 'Social Impact', NULL),
(8, 'Other', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_management`
--

CREATE TABLE `email_management` (
  `email_id` int(10) UNSIGNED NOT NULL,
  `sender_email` varchar(190) NOT NULL,
  `receiver_email` varchar(190) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `sent_date` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_management`
--

INSERT INTO `email_management` (`email_id`, `sender_email`, `receiver_email`, `subject`, `body`, `sent_date`, `is_read`) VALUES
(1, 'admin@example.com', 'bob@example.com', 'Welcome to PitchPoint', 'Hi Bob, your entrepreneur account has been created successfully.', '2025-10-29 12:05:00', 1),
(2, 'admin@example.com', 'alice@example.com', 'New projects available', 'Hi Alice, new projects are now available in your interest area (FinTech).', '2025-11-13 11:30:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `entrepreneurs`
--

CREATE TABLE `entrepreneurs` (
  `entrepreneur_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `entrepreneurs`
--

INSERT INTO `entrepreneurs` (`entrepreneur_id`, `user_id`, `company_name`, `website`, `location`, `created_at`, `updated_at`) VALUES
(1, 2, 'Phoenix Labs', 'https://phoenix.example', NULL, '2025-10-29 11:59:23', '2025-10-29 11:59:23'),
(2, 5, 'Niels Brock', NULL, NULL, '2025-11-12 10:06:55', '2025-11-12 10:06:55');

-- --------------------------------------------------------

--
-- Table structure for table `idea_approval`
--

CREATE TABLE `idea_approval` (
  `approval_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `decision` enum('Approved','Rejected','Pending') NOT NULL DEFAULT 'Pending',
  `comments` text DEFAULT NULL,
  `approval_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `idea_approval`
--

INSERT INTO `idea_approval` (`approval_id`, `admin_id`, `project_id`, `decision`, `comments`, `approval_date`) VALUES
(1, 1, 10, 'Approved', 'Good scope for a student project. Approved for publishing.', '2025-11-13 10:10:00'),
(2, 1, 11, 'Approved', 'Strong impact for farmers. Proceed to beta testing.', '2025-11-13 10:15:00'),
(3, 1, 13, 'Rejected', 'Concept is interesting but needs a clearer implementation plan.', '2025-11-13 10:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `investment_id` int(10) UNSIGNED NOT NULL,
  `investor_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('card','bank','wallet','other') NOT NULL,
  `investment_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`investment_id`, `investor_id`, `project_id`, `amount`, `payment_method`, `investment_date`) VALUES
(1, 1, 1, 1000.00, 'card', '2025-10-29 11:59:23'),
(2, 1, 15, 67.00, 'card', '2025-11-17 10:52:21'),
(3, 1, 15, 67.00, 'card', '2025-11-17 10:55:51'),
(4, 2, 17, 20.00, 'card', '2025-11-21 22:08:10'),
(5, 2, 16, 80.00, 'card', '2025-11-21 22:11:29'),
(6, 2, 11, 500.00, 'bank', '2025-11-21 22:13:53'),
(7, 2, 1, 10990.00, 'card', '2025-11-21 22:17:30'),
(8, 2, 18, 20.00, 'card', '2025-11-23 00:01:58'),
(9, 2, 18, 78.00, 'card', '2025-11-23 00:16:15'),
(10, 2, 17, 90.00, 'wallet', '2025-11-23 03:43:57'),
(11, 2, 16, 12.00, 'card', '2025-11-23 03:44:42'),
(12, 2, 18, 12.00, 'other', '2025-11-23 03:47:47'),
(13, 2, 17, 90.00, 'card', '2025-11-24 14:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `investors`
--

CREATE TABLE `investors` (
  `investor_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `interest_area` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `investors`
--

INSERT INTO `investors` (`investor_id`, `user_id`, `interest_area`, `created_at`) VALUES
(1, 1, 'FinTech', '2025-10-29 11:59:23'),
(2, 16, NULL, '2025-11-18 20:49:40'),
(3, 22, NULL, '2025-11-21 15:22:37'),
(4, 27, NULL, '2025-11-27 18:31:36');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `sender_user_id` int(10) UNSIGNED NOT NULL,
  `receiver_user_id` int(10) UNSIGNED NOT NULL,
  `body` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `project_id`, `sender_user_id`, `receiver_user_id`, `body`, `is_read`, `created_at`) VALUES
(1, 1, 1, 2, 'Hi! Can we discuss traction?', 0, '2025-10-29 11:59:23'),
(2, 1, 2, 1, 'Sure, I can share the latest KPIs.', 0, '2025-10-29 11:59:23'),
(3, 11, 16, 5, 'Hi baby', 0, '2025-11-23 01:44:39');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(80) NOT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `type`, `payload_json`, `is_read`, `created_at`) VALUES
(3, 5, 'project_published', '{\"project_id\": 15, \"title\": \"Rent-A-Skill Marketplace\", \"visibility\": \"public\"}', 1, '2025-11-13 10:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(10) UNSIGNED NOT NULL,
  `entrepreneur_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(180) NOT NULL,
  `summary` varchar(500) DEFAULT NULL,
  `problem` text DEFAULT NULL,
  `solution` text DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `stage` enum('idea','mvp','beta','launched') NOT NULL DEFAULT 'idea',
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `visibility` enum('public','private') NOT NULL DEFAULT 'public',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `entrepreneur_id`, `title`, `summary`, `problem`, `solution`, `category_id`, `budget`, `stage`, `status`, `visibility`, `created_at`, `updated_at`) VALUES
(1, 1, 'Telehealth Lite', 'Lean telemedicine MVP', 'GP access delays', 'Video consults for SMEs', 1, 25000.00, 'mvp', 'published', 'public', '2025-10-29 11:59:23', '2025-10-29 11:59:23'),
(2, 1, 'GreenPay', 'Carbon-aware gateway', 'High fees, no footprint', 'Low fees + CO2 tracking', 2, 40000.00, 'beta', 'published', 'public', '2025-10-29 11:59:23', '2025-10-29 11:59:23'),
(10, 2, 'Time Management', 'Manage your Project Like a pro', 'People forget to manage their time, so that everything is sorted out.', 'Use our webApp to find out', 1, 123456.00, 'idea', 'published', 'public', '2025-11-13 09:35:43', '2025-11-15 11:30:02'),
(11, 2, '1. Smart Irrigation Controller', 'IoT-based solution for efficient water usage in small farms.', 'Most small and mid-scale farmers water their crops based on guesswork. This leads to over-irrigation, wasted water, and higher electricity bills. During dry seasons, they also struggle to understand real-time soil moisture levels and crop needs.', 'A solar-powered device with moisture sensors that automatically controls water flow. It connects to a mobile app, showing farmers when to irrigate and how much water is being used—reducing waste and increasing crop yield.', 8, 35000.00, 'beta', 'published', 'public', '2025-11-13 09:56:18', '2025-11-13 09:56:18'),
(12, 2, 'AI Resume & Cover Letter Generator', 'AI that generates job-ready resumes tailored to roles.', 'Most job seekers submit generic resumes that don’t match job descriptions. They don\'t know how to highlight measurable achievements or keywords that ATS systems look for.', 'An NLP engine that analyzes the job posting and creates a customized resume + cover letter optimized for both human recruiters and ATS scanning. Users can edit results in a clean dashboard.', 8, 25000.00, 'mvp', 'published', 'public', '2025-11-13 09:58:11', '2025-11-13 09:58:11'),
(13, 2, 'Campus Food Delivery Robot', 'Autonomous bot delivering food on campus.', 'University students waste 20–40 minutes waiting in queues during peak hours. Cafeterias get overcrowded, and deliveries inside the campus are slow.', 'A small self-driving robot that picks up food from restaurants and delivers it anywhere on campus using GPS and obstacle detection. Students track orders in real-time.', 4, 150000.00, 'idea', 'published', 'private', '2025-11-13 09:59:48', '2025-11-13 09:59:48'),
(14, 2, 'Digital Queue System for Clinics', 'App-based virtual token system for hospitals.', 'Patients wait in long physical lines, which leads to stress, mismanagement, and overcrowding—especially during busy hours.', 'A digital token system where patients book appointments, get live queue updates, and arrive only when their number is close—reducing crowding and improving patient flow.', 1, 123456.00, 'mvp', 'published', 'public', '2025-11-13 10:04:46', '2025-11-13 10:04:46'),
(15, 2, 'Rent-A-Skill Marketplace', 'Local marketplace for micro-freelancing tasks.', 'Many people need small tasks done—like fixing a tap, assembling furniture, or 1-hour tutoring—but hiring professionals is too costly and slow.', 'A marketplace where locals offer small services with fixed, affordable pricing. Users see reviews, availability, and book instantly.', 8, 60000.00, 'launched', 'published', 'public', '2025-11-13 10:06:49', '2025-11-13 10:06:49'),
(16, 2, 'Smart Expense Tracker With Bill Scanner', 'AI-based personal finance assistant.', 'People forget their monthly expenses and struggle with budgeting. Manual entry apps fail because users don’t regularly update them.', 'An app that scans receipts using OCR, automatically categorizes spending, and shows weekly/monthly insights using clear graphs.', 2, 45000.00, 'mvp', 'published', 'public', '2025-11-13 10:08:50', '2025-11-13 10:08:50'),
(17, 2, 'Home Energy Usage Analyzer', 'Device + app that tracks and reduces home electricity use.', 'Most households don’t know which appliances waste the most electricity. Bills rise unexpectedly, especially during winters and summers.', 'A smart plug system that measures energy consumption per device and gives recommendations to reduce usage—saving 15–30% monthly.', 8, 1324.00, 'mvp', 'published', 'public', '2025-11-13 10:11:15', '2025-11-14 12:56:28'),
(18, 1, 'war', 'qwrree', 'wrfrfref', 'wefrwfrefre', NULL, 23000.00, '', 'published', 'public', '2025-11-21 22:34:23', '2025-11-21 22:34:23');

-- --------------------------------------------------------

--
-- Table structure for table `project_files`
--

CREATE TABLE `project_files` (
  `file_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size_bytes` int(10) UNSIGNED DEFAULT NULL,
  `uploaded_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_files`
--

INSERT INTO `project_files` (`file_id`, `project_id`, `file_name`, `storage_path`, `mime_type`, `file_size_bytes`, `uploaded_by`, `created_at`) VALUES
(8, 10, '6712a207239b304660ebb238_6367ddbbfa8aebba50fd824c_6259f7cc35ba017efda63bee_Time-Management-Tips.png', 'uploads/cover_image_6915985f41b715.21841368.png', 'image/png', 81569, 5, '2025-11-13 09:35:43'),
(9, 10, 'ctec2712_wad_lab-worksheet-00_setup-p3t.pdf', 'uploads/pitch_deck_6915985f42e356.18766724.pdf', 'application/pdf', 111085, 5, '2025-11-13 09:35:43'),
(10, 11, 'hcc_thornton_012_rt.jpg', 'uploads/cover_image_69159d321337e0.89776582.jpg', 'image/jpeg', 66243, 5, '2025-11-13 09:56:18'),
(11, 11, 'Lecture 10.pdf', 'uploads/pitch_deck_69159d32137a43.68893023.pdf', 'application/pdf', 384713, 5, '2025-11-13 09:56:18'),
(12, 12, 'ai-cover-letter-generator.png', 'uploads/cover_image_69159da3a8de94.01454412.png', 'image/png', 152981, 5, '2025-11-13 09:58:11'),
(13, 12, 'ctec2712_wad_lab-worksheet-00_setup-p3t.pdf', 'uploads/pitch_deck_69159da3aa1432.32885120.pdf', 'application/pdf', 111085, 5, '2025-11-13 09:58:11'),
(14, 13, 'images.jfif', 'uploads/cover_image_69159e0438d351.47065432.jfif', 'image/jpeg', 10379, 5, '2025-11-13 09:59:48'),
(15, 13, 'ctec2712_wad_php-lab-worksheet-01_getting-started.pdf', 'uploads/pitch_deck_69159e043aa7e4.86517077.pdf', 'application/pdf', 373967, 5, '2025-11-13 09:59:48'),
(16, 14, '1_bjU9mUVoNSLqvCIMvIaf2g.jpg', 'uploads/cover_image_69159f2e498999.06481109.jpg', 'image/jpeg', 40969, 5, '2025-11-13 10:04:46'),
(17, 14, 'ctec2712_wad_lab-worksheet-00_setup-p3t.pdf', 'uploads/pitch_deck_69159f2e4a6663.75399514.pdf', 'application/pdf', 111085, 5, '2025-11-13 10:04:46'),
(18, 15, 'internal-talent-marketplace-1200x900.webp', 'uploads/cover_image_69159fa99767e8.48770240.webp', 'image/webp', 51040, 5, '2025-11-13 10:06:49'),
(19, 15, 'ctec2712_wad_lab-worksheet-00_setup-p3t.pdf', 'uploads/pitch_deck_69159fa99ef0b0.13859073.pdf', 'application/pdf', 111085, 5, '2025-11-13 10:06:49'),
(20, 16, 'internal-talent-marketplace-1200x900.webp', 'uploads/cover_image_6915a022641a79.25218380.webp', 'image/webp', 51040, 5, '2025-11-13 10:08:50'),
(21, 16, 'ctec2712_wad_lab-worksheet-00_setup-p3t.pdf', 'uploads/pitch_deck_6915a022649ff9.70379234.pdf', 'application/pdf', 111085, 5, '2025-11-13 10:08:50'),
(22, 17, '2b8e1b09-811d-4220-9d25-d68e535778ce-1024x454.jpg', 'uploads/cover_image_6915a0b3be7165.74929929.jpg', 'image/jpeg', 31897, 5, '2025-11-13 10:11:15'),
(23, 17, 'ctec2712_wad_lab-worksheet-00_setup-p3t.pdf', 'uploads/pitch_deck_6915a0b3c1c253.02092363.pdf', 'application/pdf', 111085, 5, '2025-11-13 10:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `project_interests`
--

CREATE TABLE `project_interests` (
  `interest_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `investor_id` int(10) UNSIGNED NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_interests`
--

INSERT INTO `project_interests` (`interest_id`, `project_id`, `investor_id`, `note`, `created_at`) VALUES
(2, 2, 1, NULL, '2025-11-12 11:58:18'),
(4, 15, 1, NULL, '2025-11-17 11:24:40'),
(6, 16, 2, NULL, '2025-11-18 20:52:09');

-- --------------------------------------------------------

--
-- Table structure for table `project_reviews`
--

CREATE TABLE `project_reviews` (
  `review_id` int(10) UNSIGNED NOT NULL,
  `staff_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `decision` enum('approved','rejected','changes_requested') NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_views`
--

CREATE TABLE `project_views` (
  `view_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `viewer_investor_id` int(10) UNSIGNED DEFAULT NULL,
  `viewed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_hash` char(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_views`
--

INSERT INTO `project_views` (`view_id`, `project_id`, `viewer_investor_id`, `viewed_at`, `ip_hash`) VALUES
(1, 1, 1, '2025-10-29 11:59:23', NULL),
(2, 2, 1, '2025-10-29 11:59:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `department` varchar(120) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `user_id`, `department`, `phone`, `created_at`) VALUES
(1, 23, 'General', NULL, '2025-11-23 05:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `investment_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_amount` decimal(12,2) NOT NULL,
  `transaction_status` enum('pending','succeeded','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `investment_id`, `project_id`, `transaction_amount`, `transaction_status`, `transaction_date`) VALUES
(1, 1, NULL, 1000.00, 'succeeded', '2025-10-29 11:59:23'),
(14, 4, 17, 20.00, 'succeeded', '2025-11-21 22:08:10'),
(15, 5, 16, 80.00, 'succeeded', '2025-11-21 22:11:29'),
(16, 6, 11, 500.00, 'succeeded', '2025-11-21 22:13:53'),
(17, 7, 1, 10990.00, 'succeeded', '2025-11-21 22:17:30'),
(18, 8, 18, 20.00, 'succeeded', '2025-11-23 00:01:58'),
(19, 9, 18, 78.00, 'pending', '2025-11-23 00:16:15'),
(20, 10, 17, 90.00, 'succeeded', '2025-11-23 03:43:57'),
(21, 11, 16, 12.00, 'succeeded', '2025-11-23 03:44:42'),
(22, 12, 18, 12.00, 'succeeded', '2025-11-23 03:47:47'),
(23, 13, 17, 90.00, 'succeeded', '2025-11-24 14:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `role` enum('entrepreneur','investor','staff','admin') NOT NULL DEFAULT 'entrepreneur',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verify_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `bio`, `role`, `is_active`, `created_at`, `updated_at`, `is_verified`, `verify_token`) VALUES
(1, 'Alice Jensen', 'alice@example.com', '$2y$10$examplehashalice', 'eifrhuipehpu4hrurefhurehduiprhuphfuhdfiuhfuihfuhfiurhfuprhfufhruerhurhhfiuhdsuihuahuifhuidsfuifyryffsfbrfuhufuifhfuihfuihfudfuufhirfhrffuhfugrygfuudfgfigrfypgry', 'investor', 1, '2025-10-29 11:59:23', '2025-11-13 10:56:54', 0, NULL),
(2, 'Bob Sørensen', 'bob@example.com', '$2y$10$examplehashbob', NULL, 'entrepreneur', 1, '2025-10-29 11:59:23', '2025-10-29 11:59:23', 0, NULL),
(3, 'Rafia Tasnim', 'rafia@example.com', '$2y$10$examplehashrafia', NULL, 'staff', 1, '2025-10-29 11:59:23', '2025-10-29 11:59:23', 0, NULL),
(4, 'System Admin', 'admin@example.com', '$2y$10$examplehashadmin', NULL, 'admin', 1, '2025-10-29 11:59:23', '2025-10-29 11:59:23', 0, NULL),
(5, 'Jhonny English', 'jhneng@gmail.com', '$2y$10$8haLML7U7bDGieFYwI7kXO2EkGw45ePVsZbSpwri59BiKHr5wDtyC', NULL, 'investor', 1, '2025-11-16 01:01:10', '2025-11-16 01:01:10', 0, NULL),
(6, 'Ben Jongli', 'benj@gmail.com', '$2y$10$G54pTwDHl.rRoNFprHc/uO.i/hxBss12ZGp/4Jg9T5C7CDHgs09.q', NULL, 'investor', 1, '2025-11-16 01:22:10', '2025-11-16 01:22:10', 0, '19c0350c6aede477bb7dc8316f7b0b657c62e2dd5c94590e9293375fca1d8b32'),
(7, 'jenku dok', 'jenku@gmail.com', '$2y$10$jy2vv0GO1FBFp8poXFuQoOmZlLyRpTbwaUeptfr90PLRs5MXOC11u', NULL, 'entrepreneur', 1, '2025-11-16 01:35:00', '2025-11-16 01:35:00', 0, 'fe1b1e9c35f5108dc5395247ecce83a92c4e4f594cbed8375427cc270f5271ac'),
(8, 'jenku dok', 'jenku1@gmail.com', '$2y$10$9ulgeHXh2Ws4pFMP.vKGYeqyEyQtjpjrSMhwN4SEmq0NM3l7J2HlW', NULL, 'entrepreneur', 1, '2025-11-16 01:41:28', '2025-11-16 01:41:28', 0, '67dedd931548dc6c75d3dd8c62825b85c7e605fc88cedd5e137701b1d2d68dcc'),
(9, 'jenku dokina', 'jenku21@gmail.com', '$2y$10$Tb18z9I2CL168rZ.tah9Ou0jaJsEfBxNor.hH68uvVpHjofN.l9tq', NULL, 'investor', 1, '2025-11-16 01:42:14', '2025-11-16 01:42:14', 0, '1b659e2dc02bbe820a629ce217046e81203be21ae36f9a735f447cbf7e0691bb'),
(10, 'jenku dokinai', 'jenku213@gmail.com', '$2y$10$z8sXySPqwwhIsNUTfrBgHe.l9ogAc9Rojym0YZTbKPs84kEef8OVe', NULL, 'entrepreneur', 1, '2025-11-16 01:44:13', '2025-11-16 01:44:13', 0, 'de99d7b7460c4bb55c335fb420dd762b0274a5e9b91c8aa4a78b3885807d6382'),
(11, 'jenku dokinaiu', 'jenku2113@gmail.com', '$2y$10$ECtIw4Iy2Vh38Vs.TicWpOdIKd5RztfRIlXlNiWgGRyBQSEjrZv9C', NULL, 'investor', 1, '2025-11-16 01:45:47', '2025-11-16 01:46:01', 1, NULL),
(12, 'opu', 'opu@gmail.com', '$2y$10$yZcy88mH7zLVrA77JrW6KOfWyIkcSeO8iq00okgD.KII0v27H6He6', NULL, 'investor', 1, '2025-11-16 01:48:57', '2025-11-16 01:48:57', 0, '24df7d117fd75fffc5c2819adfdc002f759de75994da6f35b6257e4a94b86b96'),
(13, 'opu', 'opi@gmail.com', '$2y$10$GQdKbX5Azx3kGnbkGXZMi.M27bQHR2I9r7g9Wg8vI9T/kTeZV12tC', NULL, 'investor', 1, '2025-11-16 01:52:58', '2025-11-16 02:38:47', 1, NULL),
(14, 'sayeed vai', 'sayeed@gmail.com', '$2y$10$kxCQNgEg0wSLcW93kGWdOO6/qwfRBmfhPDzc/B8VF3uz/.mAlbUDi', NULL, 'entrepreneur', 1, '2025-11-17 16:57:19', '2025-11-17 16:58:28', 1, NULL),
(16, 'moon', 'moon@gmail.com', '$2y$10$whYr1Im.lM6cng.tS6dwj.f43KrUEqH5ihJsk/Ad1hcGFgWCkqSzm', 'hi, im pretty', 'investor', 1, '2025-11-17 18:36:50', '2025-11-17 19:02:21', 1, NULL),
(17, 'omor', 'omors@jk.com', '$2y$10$gFXirFLfcF.QprRqxuIqh.zhVjJKVJbJa3m0OUTINp2/KQRdhDWl2', NULL, 'investor', 1, '2025-11-17 19:06:17', '2025-11-17 19:06:38', 1, NULL),
(19, 'pol', 'pol@gmail.coom', '$2y$10$I9JK1cY2oMp5dn3OTr0Fau4/iUhKWut6bK99kwBhsz8NCSyKYhZbu', NULL, 'entrepreneur', 1, '2025-11-18 19:26:30', '2025-11-18 19:26:38', 1, NULL),
(20, 'polk', 'polk@gmail.com', '$2y$10$J.1liivBlYYmdjDo7fUSm.i8zd8KAck4lENRzjBL9zS2dOA/TdxsC', NULL, 'entrepreneur', 1, '2025-11-18 19:28:51', '2025-11-18 19:28:58', 1, NULL),
(21, 'oio', 'ii@had.cc', '$2y$10$rJ0eJEZYA/MHgUmckZbuqu60BRXDUjDI5OYq930Y8ApLXRkCPt2SC', NULL, 'entrepreneur', 1, '2025-11-21 15:20:01', '2025-11-21 15:20:01', 0, 'c1ce5df9cf59205ecc7224253504b219945e97e2931344d2d62db2aed66541e5'),
(22, 'Nobi', 'nobi@gmail.com', '$2y$10$i0VrbRh1.w3OplXogKBsH.KOMxo7LaY/l6vDoHNSeVGk0Ne8Jxkiy', NULL, 'investor', 1, '2025-11-21 15:21:37', '2025-11-21 15:21:45', 1, NULL),
(23, 'Staff User', 'omor1@gmail.com', '$2y$10$WINaNsZxfD3S3Mz7FLeAzuE6A8zzSwQWXPImaHVwtGxUu7M2x.Z/m', NULL, 'staff', 1, '2025-11-23 05:01:59', '2025-11-23 05:01:59', 1, NULL),
(24, 'chumki', 'chumki@gmail.com', '$2y$10$ks8YaVvRWYuOKsLF9mK1Lu4KegTMcSOzj3.z5762aXINcyMWA8Bn.', NULL, 'entrepreneur', 1, '2025-11-24 14:16:29', '2025-11-24 14:16:40', 1, NULL),
(25, 'Green crow', 'greeny@gmail.com', '$2y$10$nGqQ0UdCxh1FD3eHiGCvDuh5/LgMEeet6g4JLi71KFktlsqZsDlmW', NULL, 'investor', 1, '2025-11-26 11:18:36', '2025-11-26 11:18:42', 1, NULL),
(26, 'zahid kakku', 'zahid@kakku.com', '$2y$10$bPMqLP9nYpuYxSDZ7aBoGuYczMrMZYtGrHyUvwOIs1ms9bJPV94L2', NULL, 'entrepreneur', 1, '2025-11-26 11:24:20', '2025-11-26 11:24:34', 1, NULL),
(27, 'bip', 'bip@jk.com', '$2y$10$jf/2U4MzB5vX5ipOeGTHQupt0aZo2yc.eRgYffzot6CBzld.pecG.', NULL, 'investor', 1, '2025-11-27 18:30:36', '2025-11-27 18:30:57', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_management`
--

CREATE TABLE `user_management` (
  `user_manage_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(50) NOT NULL,
  `action_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_log_admin_time` (`admin_id`,`logged_at`);

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_admin_notifications_admin` (`admin_id`,`sent_date`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `email_management`
--
ALTER TABLE `email_management`
  ADD PRIMARY KEY (`email_id`);

--
-- Indexes for table `entrepreneurs`
--
ALTER TABLE `entrepreneurs`
  ADD PRIMARY KEY (`entrepreneur_id`),
  ADD UNIQUE KEY `uq_entrepreneur_user` (`user_id`);

--
-- Indexes for table `idea_approval`
--
ALTER TABLE `idea_approval`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `idx_ia_admin_project` (`admin_id`,`project_id`),
  ADD KEY `fk_ia_project` (`project_id`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`investment_id`),
  ADD KEY `idx_investments_investor` (`investor_id`),
  ADD KEY `idx_investments_project` (`project_id`);

--
-- Indexes for table `investors`
--
ALTER TABLE `investors`
  ADD PRIMARY KEY (`investor_id`),
  ADD UNIQUE KEY `uq_investor_user` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_messages_project_time` (`project_id`,`created_at`),
  ADD KEY `fk_messages_sender` (`sender_user_id`),
  ADD KEY `fk_messages_receiver` (`receiver_user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_notifications_user` (`user_id`,`is_read`,`created_at`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_projects_owner_status` (`entrepreneur_id`,`status`),
  ADD KEY `fk_projects_category` (`category_id`);
ALTER TABLE `projects` ADD FULLTEXT KEY `ft_projects_text` (`title`,`summary`,`problem`,`solution`);

--
-- Indexes for table `project_files`
--
ALTER TABLE `project_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `idx_files_project` (`project_id`),
  ADD KEY `fk_files_uploader` (`uploaded_by`);

--
-- Indexes for table `project_interests`
--
ALTER TABLE `project_interests`
  ADD PRIMARY KEY (`interest_id`),
  ADD UNIQUE KEY `uq_interest_once` (`project_id`,`investor_id`),
  ADD KEY `idx_interests_investor` (`investor_id`);

--
-- Indexes for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `idx_reviews_staff` (`staff_id`),
  ADD KEY `idx_reviews_project` (`project_id`);

--
-- Indexes for table `project_views`
--
ALTER TABLE `project_views`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `idx_views_project_time` (`project_id`,`viewed_at`),
  ADD KEY `fk_views_viewer` (`viewer_investor_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `uq_staff_user` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_transactions_investment` (`investment_id`),
  ADD KEY `fk_transactions_project` (`project_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role_active` (`role`,`is_active`);

--
-- Indexes for table `user_management`
--
ALTER TABLE `user_management`
  ADD PRIMARY KEY (`user_manage_id`),
  ADD KEY `idx_um_admin_time` (`admin_id`,`action_date`),
  ADD KEY `fk_um_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `notification_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `email_management`
--
ALTER TABLE `email_management`
  MODIFY `email_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `entrepreneurs`
--
ALTER TABLE `entrepreneurs`
  MODIFY `entrepreneur_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `idea_approval`
--
ALTER TABLE `idea_approval`
  MODIFY `approval_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `investment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `investors`
--
ALTER TABLE `investors`
  MODIFY `investor_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `project_files`
--
ALTER TABLE `project_files`
  MODIFY `file_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `project_interests`
--
ALTER TABLE `project_interests`
  MODIFY `interest_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `project_reviews`
--
ALTER TABLE `project_reviews`
  MODIFY `review_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project_views`
--
ALTER TABLE `project_views`
  MODIFY `view_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user_management`
--
ALTER TABLE `user_management`
  MODIFY `user_manage_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `administrator` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD CONSTRAINT `fk_admin_notifications_admin` FOREIGN KEY (`admin_id`) REFERENCES `administrator` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `entrepreneurs`
--
ALTER TABLE `entrepreneurs`
  ADD CONSTRAINT `fk_entrepreneurs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `idea_approval`
--
ALTER TABLE `idea_approval`
  ADD CONSTRAINT `fk_ia_admin` FOREIGN KEY (`admin_id`) REFERENCES `administrator` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ia_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `investments`
--
ALTER TABLE `investments`
  ADD CONSTRAINT `fk_investments_investor` FOREIGN KEY (`investor_id`) REFERENCES `investors` (`investor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_investments_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `investors`
--
ALTER TABLE `investors`
  ADD CONSTRAINT `fk_investors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_projects_entrepreneur` FOREIGN KEY (`entrepreneur_id`) REFERENCES `entrepreneurs` (`entrepreneur_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_files`
--
ALTER TABLE `project_files`
  ADD CONSTRAINT `fk_files_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_files_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_interests`
--
ALTER TABLE `project_interests`
  ADD CONSTRAINT `fk_interests_investor` FOREIGN KEY (`investor_id`) REFERENCES `investors` (`investor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_interests_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD CONSTRAINT `fk_reviews_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_views`
--
ALTER TABLE `project_views`
  ADD CONSTRAINT `fk_views_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_views_viewer` FOREIGN KEY (`viewer_investor_id`) REFERENCES `investors` (`investor_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_investment` FOREIGN KEY (`investment_id`) REFERENCES `investments` (`investment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transactions_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_management`
--
ALTER TABLE `user_management`
  ADD CONSTRAINT `fk_um_admin` FOREIGN KEY (`admin_id`) REFERENCES `administrator` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_um_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
