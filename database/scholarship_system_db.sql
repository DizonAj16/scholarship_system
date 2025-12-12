-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 11:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scholarship_system_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dropdown_course_major`
--

CREATE TABLE `dropdown_course_major` (
  `id` int(11) NOT NULL,
  `course` varchar(50) NOT NULL,
  `major` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dropdown_course_major`
--

INSERT INTO `dropdown_course_major` (`id`, `course`, `major`) VALUES
(2, 'BS INFOTECH', 'PROGRAMMING'),
(3, 'BS INFOSYS', 'PROGRAMMING'),
(4, 'BS CE', 'ENGLISH');

-- --------------------------------------------------------

--
-- Table structure for table `dropdown_scholarship_grant`
--

CREATE TABLE `dropdown_scholarship_grant` (
  `id` int(11) NOT NULL,
  `grant_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dropdown_scholarship_grant`
--

INSERT INTO `dropdown_scholarship_grant` (`id`, `grant_name`) VALUES
(3, 'CHED TDP (TULONG DULONG PROGRAM)'),
(2, 'CHED-FULL MERIT'),
(1, 'CHED-HALF MERIT'),
(5, 'INTERNALLY FUNDED'),
(4, 'TES');

-- --------------------------------------------------------

--
-- Table structure for table `dropdown_sem_sy`
--

CREATE TABLE `dropdown_sem_sy` (
  `id` int(11) NOT NULL,
  `semester` enum('1st sem','2nd sem','Summer') NOT NULL,
  `school_year` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dropdown_sem_sy`
--

INSERT INTO `dropdown_sem_sy` (`id`, `semester`, `school_year`) VALUES
(3, '1st sem', '2025-2026'),
(5, '2nd sem', '2025-2026');

-- --------------------------------------------------------

--
-- Table structure for table `grant_requirements`
--

CREATE TABLE `grant_requirements` (
  `id` int(11) NOT NULL,
  `grant_name` varchar(100) NOT NULL,
  `requirement_name` varchar(255) NOT NULL,
  `requirement_type` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grant_requirements`
--

INSERT INTO `grant_requirements` (`id`, `grant_name`, `requirement_name`, `requirement_type`, `display_order`, `created_at`) VALUES
(1, 'CHED TDP (TULONG DULONG PROGRAM)', '2x2 Picture', NULL, 1, '2025-12-10 16:56:43'),
(2, 'CHED-FULL MERIT', '2x2 Picture', NULL, 1, '2025-12-10 17:49:51'),
(3, 'CHED TDP (TULONG DULONG PROGRAM)', 'CERTIFICATE OF INDIGENCY', NULL, 2, '2025-12-10 18:02:11');

-- --------------------------------------------------------

--
-- Table structure for table `house_info`
--

CREATE TABLE `house_info` (
  `id` int(11) NOT NULL,
  `application_id` varchar(20) NOT NULL,
  `house_status` enum('Owned','Rented','Living with relatives','Others') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `house_info`
--

INSERT INTO `house_info` (`id`, `application_id`, `house_status`) VALUES
(83, '20251204031307888', 'Rented'),
(84, '20251204033311414', 'Owned'),
(85, '20251204054601248', 'Owned'),
(86, '20251204055643624', 'Owned'),
(87, '20251204061649665', 'Owned'),
(88, '20251207161201798', 'Owned'),
(89, '20251207161919239', 'Living with relatives'),
(90, '20251210171214576', 'Owned'),
(91, '20251210173707266', 'Owned'),
(92, '20251210180040545', 'Living with relatives'),
(93, '20251210184716506', 'Owned'),
(94, '20251211231624619', 'Owned');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `details`, `timestamp`) VALUES
(1, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2024-12-08 13:32:21'),
(2, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 13:32:30'),
(3, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 13:33:35'),
(4, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 13:35:30'),
(5, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 13:37:57'),
(6, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-08 13:39:45'),
(7, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 13:39:55'),
(8, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-08 13:40:18'),
(9, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2024-12-08 13:40:29'),
(10, 2, 'User logged out', 'User \'useraccount01\' logged out successfully.', '2024-12-08 13:40:31'),
(11, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 13:42:49'),
(12, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-08 13:48:03'),
(13, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 13:49:20'),
(14, 1, 'Application approved and email notification sent', 'Application ID 20241208102821576 approved and notification sent to 123124 (arjecdizon99@gmail.com).', '2024-12-08 14:24:21'),
(15, 1, 'Application approved without email notification', 'Application ID 20241208102541840 approved but no email notification sent to Arjec Jose Dizon (arjecdizon99@gmail.com).', '2024-12-08 14:24:29'),
(16, 1, 'Application approved without email notification', 'Application ID 20241121190024540 approved but no email notification sent to Monkey D. Luffy (dizon.arjecjose@gmail.com).', '2024-12-08 14:26:22'),
(17, 1, 'Application Deleted', 'Application ID 20241208102821576 deleted successfully.', '2024-12-08 14:30:36'),
(18, 1, 'Application Status Updated', 'Application ID 20241120192554180 marked as \'pending\'.', '2024-12-08 14:33:06'),
(19, 1, 'Application Status Rejected', 'Application ID 20241120192554180 marked as \'rejected\'.', '2024-12-08 14:35:29'),
(20, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-08 14:56:31'),
(21, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2024-12-08 14:56:53'),
(22, 2, 'User logged out', 'User \'useraccount01\' logged out successfully.', '2024-12-08 15:03:17'),
(23, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 15:03:26'),
(24, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-08 15:34:13'),
(25, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-08 15:36:45'),
(26, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-09 13:31:18'),
(27, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2024-12-09 13:31:37'),
(28, 2, 'User logged out', 'User \'useraccount01\' logged out successfully.', '2024-12-09 13:43:45'),
(29, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-09 13:45:37'),
(30, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-09 14:11:49'),
(31, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2024-12-09 14:16:40'),
(32, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2024-12-09 14:26:29'),
(33, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-09 14:26:39'),
(34, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-09 14:37:04'),
(35, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-09 14:39:40'),
(36, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-09 14:39:54'),
(37, 13, 'Account created', 'User \'finn24\' account created.', '2024-12-09 14:40:06'),
(38, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-09 14:40:18'),
(39, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-09 15:57:58'),
(40, 1, 'Application approved and email notification sent', 'Application ID 20241209134236894 approved and notification sent to Al-khazri Sali Alim (arjecdizon99@gmail.com).', '2024-12-09 16:06:14'),
(41, 1, 'Application approved without email notification', 'Application ID 20241208160252795 approved but no email notification sent to Arjec Jose Dizon (asd@email.com).', '2024-12-09 16:22:32'),
(42, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-09 16:27:31'),
(43, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-09 16:27:40'),
(44, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-09 16:34:44'),
(45, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-09 16:41:57'),
(46, 1, 'Application approved without email notification', 'Application ID 20241209134236894 approved but no email notification sent to Al-khazri Sali Alim (arjecdizon99@gmail.com).', '2024-12-09 16:48:32'),
(47, 1, 'Application approved and email notification sent', 'Application ID 20241209134236894 approved and notification sent to Al-khazri Sali Alim (arjecdizon99@gmail.com).', '2024-12-09 16:49:36'),
(48, 1, 'Application approved and email notification sent', 'Application ID 20241121190024540 approved and notification sent to Monkey D. Luffy (dizon.arjecjose@gmail.com).', '2024-12-09 16:52:09'),
(49, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2024-12-09 17:46:35'),
(51, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2024-12-09 18:31:08'),
(52, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2024-12-09 18:41:06'),
(53, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2024-12-09 18:41:16'),
(61, 1, 'Scholarship application submitted', 'Application ID 20241209195822680 submitted by username admin.', '2024-12-09 18:58:22'),
(62, 1, 'Scholarship application submitted', 'Application ID 20241209195852501 submitted by admin.', '2024-12-09 18:58:52'),
(63, 1, 'Application Deleted', 'Application ID 20241209195852501 deleted successfully.', '2024-12-09 18:59:16'),
(64, 1, 'Application Deleted', 'Application ID 20241209195822680 deleted successfully.', '2024-12-09 18:59:23'),
(65, 1, 'Application Deleted', 'Application ID 20241209195616818 deleted successfully.', '2024-12-09 18:59:29'),
(66, 1, 'Application Deleted', 'Application ID 20241209195502495 deleted successfully.', '2024-12-09 18:59:33'),
(67, 1, 'Application Deleted', 'Application ID 20241209195138977 deleted successfully.', '2024-12-09 18:59:38'),
(68, 1, 'Application Deleted', 'Application ID 20241209195053809 deleted successfully.', '2024-12-09 18:59:43'),
(69, 1, 'Application Deleted', 'Application ID 20241209195035804 deleted successfully.', '2024-12-09 18:59:49'),
(70, 1, 'Application Deleted', 'Application ID 20241209194829770 deleted successfully.', '2024-12-09 18:59:55'),
(71, 1, 'Application Deleted', 'Application ID 20241209194754591 deleted successfully.', '2024-12-09 19:00:12'),
(72, 1, 'Application Deleted', 'Application ID 20241209194723474 deleted successfully.', '2024-12-09 19:00:18'),
(73, 1, 'Application Deleted', 'Application ID 20241209194653385 deleted successfully.', '2024-12-09 19:00:22'),
(74, 1, 'Application Deleted', 'Application ID 20241209194055048 deleted successfully.', '2024-12-09 19:00:26'),
(75, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-10-28 13:56:43'),
(76, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-10-28 13:56:53'),
(77, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-10-28 14:15:01'),
(78, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-10-29 00:09:05'),
(79, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-10-29 00:09:15'),
(80, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-10-29 00:09:25'),
(81, 1, 'Error Updating Status', 'No application found with ID 20241209194032167 or it has already been processed.', '2025-10-29 00:11:36'),
(82, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-14 06:35:07'),
(83, 1, 'Error Updating Status', 'No application found with ID 20241208160252795 or it has already been processed.', '2025-11-14 09:17:56'),
(84, 1, 'Application Status Updated', 'Application ID 20241119021745276 marked as \'pending\'.', '2025-11-14 09:18:14'),
(85, 1, 'Application approved', 'ID 20241209194032167 approved without notification.', '2025-11-14 09:18:43'),
(86, 1, 'Scholarship application submitted', 'Application ID 20251116114939779 submitted by admin.', '2025-11-16 11:49:39'),
(87, 1, 'Scholarship application submitted', 'Application ID 20251116182413313 submitted by admin.', '2025-11-16 18:24:13'),
(88, 1, 'Scholarship application submitted', 'Application ID 20251118014737885 submitted by admin.', '2025-11-18 01:47:37'),
(89, 1, 'Application Deleted', 'Application ID 20251118014737885 deleted successfully.', '2025-11-20 00:36:10'),
(90, 1, 'Scholarship application submitted', 'Application ID 20251120005243756 submitted by admin.', '2025-11-20 00:52:43'),
(91, 1, 'Application Deleted', 'Application ID 20251116182413313 deleted successfully.', '2025-11-20 00:54:24'),
(92, 1, 'Application Deleted', 'Application ID 20251116114939779 deleted successfully.', '2025-11-20 00:54:30'),
(93, 1, 'Scholarship application submitted', 'Application ID 20251120032139792 submitted by admin.', '2025-11-20 03:21:39'),
(94, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-20 03:23:37'),
(95, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-20 03:24:13'),
(96, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-20 03:29:37'),
(97, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2025-11-20 03:29:57'),
(98, 2, 'User logged out', 'User \'useraccount01\' logged out successfully.', '2025-11-20 03:34:01'),
(99, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-20 03:34:12'),
(100, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: Extension missing: openssl', '2025-11-20 03:34:43'),
(101, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: Extension missing: openssl', '2025-11-20 03:36:21'),
(102, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: Extension missing: openssl', '2025-11-20 08:42:14'),
(103, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: Extension missing: openssl', '2025-11-20 08:43:09'),
(104, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: Extension missing: openssl', '2025-11-20 08:48:47'),
(105, 1, 'Application approved and notified', 'ID 20251120005243756 approved and notified.', '2025-11-20 08:50:55'),
(106, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-20 17:37:12'),
(107, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2025-11-20 17:37:52'),
(108, 2, 'User logged out', 'User \'useraccount01\' logged out successfully.', '2025-11-20 17:38:35'),
(109, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-20 17:38:48'),
(110, 1, 'Application Status Rejected', 'Application ID 20241208160252795 marked as \'rejected\'.', '2025-11-20 17:44:46'),
(111, 1, 'Application Status Updated', 'Application ID 20241208160252795 marked as \'pending\'.', '2025-11-20 17:48:19'),
(112, 1, 'Application Status Rejected', 'Application ID 20241208160252795 marked as \'rejected\'.', '2025-11-20 17:48:25'),
(113, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-11-21 00:57:07'),
(114, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-21 00:57:22'),
(115, 1, 'Scholarship application submitted', 'Application ID 20251121010616855 submitted by admin.', '2025-11-21 01:06:16'),
(116, 1, 'Scholarship application submitted', 'Application ID 20251121012446449 submitted by admin.', '2025-11-21 01:24:46'),
(117, 1, 'Scholarship application submitted', 'Application ID 20251121062359913 submitted by admin.', '2025-11-21 06:23:59'),
(118, 1, 'Application approved', 'ID 20251121062359913 approved without notification.', '2025-11-21 06:25:55'),
(119, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-21 06:55:47'),
(120, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-11-21 06:56:18'),
(121, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-11-21 06:56:32'),
(122, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-11-21 06:56:47'),
(123, 14, 'Account created', 'User \'jan\' account created.', '2025-11-21 06:57:50'),
(124, 14, 'User logged in', 'User \'jan\' logged in successfully.', '2025-11-21 06:58:07'),
(125, 14, 'User logged out', 'User \'jan\' logged out successfully.', '2025-11-21 06:58:14'),
(126, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-11-21 06:58:29'),
(127, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-21 07:01:30'),
(128, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-21 07:06:02'),
(129, 14, 'User logged in', 'User \'jan\' logged in successfully.', '2025-11-21 07:06:18'),
(130, 14, 'User logged out', 'User \'jan\' logged out successfully.', '2025-11-21 07:07:01'),
(131, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-21 07:07:12'),
(132, 14, 'User logged in', 'User \'jan\' logged in successfully.', '2025-11-21 07:09:40'),
(133, 14, 'User logged out', 'User \'jan\' logged out successfully.', '2025-11-21 07:09:49'),
(134, 15, 'Account created', 'User \'mark\' account created.', '2025-11-21 07:10:38'),
(135, 15, 'User logged in', 'User \'mark\' logged in successfully.', '2025-11-21 07:11:02'),
(136, 15, 'User logged out', 'User \'mark\' logged out successfully.', '2025-11-21 07:31:02'),
(137, 14, 'User logged in', 'User \'jan\' logged in successfully.', '2025-11-21 07:31:44'),
(138, 14, 'User logged out', 'User \'jan\' logged out successfully.', '2025-11-21 07:35:50'),
(139, 15, 'User logged in', 'User \'mark\' logged in successfully.', '2025-11-21 07:36:34'),
(140, 15, 'User logged out', 'User \'mark\' logged out successfully.', '2025-11-21 07:36:38'),
(141, 15, 'Failed login attempt', 'Invalid password for user \'mark\'.', '2025-11-21 07:37:01'),
(142, 15, 'Failed login attempt', 'Invalid password for user \'mark\'.', '2025-11-21 07:37:21'),
(143, 15, 'Failed login attempt', 'Invalid password for user \'mark\'.', '2025-11-21 07:37:35'),
(144, 15, 'User logged in', 'User \'mark\' logged in successfully.', '2025-11-21 07:38:23'),
(145, 15, 'User logged out', 'User \'mark\' logged out successfully.', '2025-11-21 07:40:39'),
(146, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-21 07:40:53'),
(147, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-21 09:55:39'),
(148, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2025-11-21 09:55:52'),
(149, 2, 'Scholarship application submitted', 'Application ID 20251121095848774 submitted by useraccount01.', '2025-11-21 09:58:48'),
(150, 2, 'User logged out', 'User \'useraccount01\' logged out successfully.', '2025-11-21 10:02:13'),
(151, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-21 10:02:23'),
(152, 1, 'Scholarship application submitted', 'Application ID 20251121101153923 submitted by admin.', '2025-11-21 10:11:53'),
(153, 1, 'Scholarship application submitted', 'Application ID 20251121103141752 submitted by admin.', '2025-11-21 10:31:41'),
(154, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-21 10:32:46'),
(155, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2025-11-21 10:32:59'),
(156, 2, 'Scholarship application submitted', 'Application ID 20251121131725238 submitted by useraccount01.', '2025-11-21 13:17:25'),
(157, 2, 'Scholarship application submitted', 'Application ID 20251121134052201 submitted by useraccount01.', '2025-11-21 13:40:52'),
(158, 2, 'Scholarship application submitted', 'Application ID 20251121135639274 submitted by useraccount01.', '2025-11-21 13:56:39'),
(159, 2, 'User logged out', 'User \'useraccount01\' logged out successfully.', '2025-11-21 13:59:59'),
(160, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-21 14:00:10'),
(161, 1, 'Application approved and notified', 'ID 20251121135639274 approved and notified.', '2025-11-21 14:01:11'),
(162, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-21 14:01:24'),
(163, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-21 14:01:45'),
(164, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-21 14:01:50'),
(165, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2025-11-21 14:02:04'),
(166, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-22 02:18:13'),
(167, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2025-11-22 02:18:26'),
(168, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-11-22 04:32:28'),
(169, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-11-22 05:19:28'),
(170, 2, 'User logged in', 'User \'useraccount01\' logged in successfully.', '2025-11-22 05:19:41'),
(171, 2, 'Scholarship application submitted', 'Application ID 20251122124444832 submitted by useraccount01.', '2025-11-22 12:44:44'),
(172, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-03 12:56:19'),
(173, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-03 12:57:55'),
(174, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-03 13:13:31'),
(175, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-03 13:24:02'),
(176, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-03 13:24:08'),
(177, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-03 13:32:57'),
(178, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-03 13:33:03'),
(179, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-03 13:36:25'),
(180, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-03 13:36:30'),
(181, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-03 13:39:34'),
(182, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-03 13:39:51'),
(183, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-03 13:41:47'),
(184, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-03 13:41:52'),
(185, 1, 'Scholarship application submitted', 'Application ID 20251203162603263 submitted by admin.', '2025-12-03 16:26:03'),
(186, 1, 'Scholarship application submitted', 'Application ID 20251203170106386 submitted by admin.', '2025-12-03 17:01:06'),
(187, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-03 17:03:39'),
(188, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-03 17:03:49'),
(189, 11, 'Scholarship application submitted', 'Application ID 20251203170704141 submitted by finn22.', '2025-12-03 17:07:04'),
(190, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-03 17:09:44'),
(191, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-03 17:09:48'),
(192, 1, 'Application Deleted', 'Application ID 20251203170106386 deleted successfully.', '2025-12-04 00:06:42'),
(193, 1, 'Application Deleted', 'Application ID 20251203170704141 deleted successfully.', '2025-12-04 00:06:47'),
(194, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-04 00:23:16'),
(195, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-04 00:23:24'),
(196, 11, 'Scholarship application submitted', 'Application ID 20251204002525338 submitted by finn22.', '2025-12-04 00:25:25'),
(197, 11, 'Scholarship application submitted', 'Application ID 20251204012052556 submitted by finn22.', '2025-12-04 01:20:52'),
(198, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-04 01:22:48'),
(199, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-04 01:22:55'),
(200, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-04 01:23:15'),
(201, 12, 'User logged in', 'User \'finn23\' logged in successfully.', '2025-12-04 01:23:28'),
(202, 12, 'User logged out', 'User \'finn23\' logged out successfully.', '2025-12-04 01:23:36'),
(203, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-04 01:23:46'),
(204, 11, 'Scholarship application submitted', 'Application ID 20251204013339301 submitted by finn22.', '2025-12-04 01:33:39'),
(205, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-04 01:34:59'),
(206, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-04 01:35:05'),
(207, 1, 'Application updated by admin', 'Application ID 20251204013339301 updated by admin', '2025-12-04 01:37:08'),
(208, 1, 'Application updated by admin', 'Application ID 20251204013339301 updated by admin', '2025-12-04 01:37:47'),
(209, 1, 'Scholarship application submitted', 'Application ID 20251204014736619 submitted by admin.', '2025-12-04 01:47:36'),
(210, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: data not accepted.SMTP server error: DATA command failed Detail: Daily user sending limit exceeded. For more information on Gmail\r\nsending limits go to\r\n https://support.google.com/a/answer/166852 d9443c01a7336-29daeaab9e6sm1723845ad.71 - gsmtp\r\n SMTP code: 550 Additional SMTP info: 5.4.5', '2025-12-04 01:54:47'),
(211, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-04 02:33:56'),
(212, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-04 02:34:02'),
(213, 11, 'Scholarship application submitted', 'Application ID 20251204024139706 submitted by finn22.', '2025-12-04 02:41:39'),
(214, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-04 02:42:16'),
(215, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-04 02:42:21'),
(216, 1, 'Scholarship application submitted', 'Application ID 20251204030605781 submitted by admin.', '2025-12-04 03:06:05'),
(217, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: data not accepted.SMTP server error: DATA command failed Detail: Daily user sending limit exceeded. For more information on Gmail\r\nsending limits go to\r\n https://support.google.com/a/answer/166852 d9443c01a7336-29dae4cf8a1sm3227375ad.33 - gsmtp\r\n SMTP code: 550 Additional SMTP info: 5.4.5', '2025-12-04 03:06:57'),
(218, 1, 'Application Deleted', 'Application ID 20251204030605781 deleted successfully.', '2025-12-04 03:07:37'),
(219, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-04 03:09:29'),
(220, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-04 03:09:36'),
(221, 11, 'Scholarship application submitted', 'Application ID 20251204031307888 submitted by finn22.', '2025-12-04 03:13:07'),
(222, 11, 'Scholarship application submitted', 'Application ID 20251204033311414 submitted by finn22.', '2025-12-04 03:33:11'),
(223, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-04 03:33:56'),
(224, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-04 03:34:01'),
(225, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: data not accepted.SMTP server error: DATA command failed Detail: Daily user sending limit exceeded. For more information on Gmail\r\nsending limits go to\r\n https://support.google.com/a/answer/166852 98e67ed59e1d1-3494f59686csm257553a91.11 - gsmtp\r\n SMTP code: 550 Additional SMTP info: 5.4.5', '2025-12-04 03:36:05'),
(226, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-04 03:37:28'),
(227, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-04 05:39:51'),
(228, 1, 'Scholarship application submitted', 'Application ID 20251204054601248 submitted by admin.', '2025-12-04 05:46:01'),
(229, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-04 05:46:29'),
(230, 12, 'User logged in', 'User \'finn23\' logged in successfully.', '2025-12-04 05:46:37'),
(231, 12, 'Scholarship application submitted', 'Application ID 20251204055643624 submitted by finn23.', '2025-12-04 05:56:43'),
(232, 12, 'User logged out', 'User \'finn23\' logged out successfully.', '2025-12-04 05:58:38'),
(233, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-04 05:58:47'),
(234, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: Could not connect to SMTP host. Failed to connect to serverSMTP server error: Failed to connect to server Additional SMTP info: php_network_getaddresses: getaddrinfo failed: No such host is known. ', '2025-12-04 05:59:44'),
(235, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-04 06:00:03'),
(236, 12, 'User logged in', 'User \'finn23\' logged in successfully.', '2025-12-04 06:00:11'),
(237, 12, 'User logged out', 'User \'finn23\' logged out successfully.', '2025-12-04 06:01:18'),
(238, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-04 06:01:29'),
(239, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-04 06:03:20'),
(240, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-04 06:03:29'),
(241, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-04 06:09:30'),
(242, 13, 'User logged in', 'User \'finn24\' logged in successfully.', '2025-12-04 06:09:38'),
(243, 13, 'Scholarship application submitted', 'Application ID 20251204061649665 submitted by finn24.', '2025-12-04 06:16:49'),
(244, 13, 'User logged out', 'User \'finn24\' logged out successfully.', '2025-12-04 06:19:00'),
(245, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-04 06:19:07'),
(246, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-07 14:59:29'),
(247, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-07 15:05:38'),
(248, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-07 15:12:11'),
(249, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-07 15:13:07'),
(250, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-12-07 15:17:06'),
(251, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-07 15:17:17'),
(252, 1, 'Scholarship application submitted', 'Application ID 20251207161201798 submitted by admin.', '2025-12-07 16:12:01'),
(253, 1, 'Scholarship application submitted', 'Application ID 20251207161919239 submitted by admin.', '2025-12-07 16:19:19'),
(254, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-08 23:18:08'),
(255, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-08 23:18:17'),
(256, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-12-10 02:27:09'),
(257, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-10 02:27:30'),
(258, 1, 'Scholarship application submitted', 'Application ID 20251210171214576 submitted by admin.', '2025-12-10 17:12:14'),
(259, 1, 'Scholarship application submitted', 'Application ID 20251210173707266 submitted by admin.', '2025-12-10 17:37:07'),
(260, 1, 'Scholarship application submitted', 'Application ID 20251210180040545 submitted by admin.', '2025-12-10 18:00:40'),
(261, 1, 'Scholarship application submitted', 'Application ID 20251210184716506 submitted by admin.', '2025-12-10 18:47:16'),
(262, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-10 18:48:18'),
(263, 12, 'User logged in', 'User \'finn23\' logged in successfully.', '2025-12-10 18:48:25'),
(264, 12, 'User logged out', 'User \'finn23\' logged out successfully.', '2025-12-10 18:50:43'),
(265, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-10 18:50:48'),
(266, 1, 'Application approved and notified', 'ID 20251210184716506 approved and notified.', '2025-12-10 19:18:56'),
(267, 1, 'Application updated by admin', 'Application ID 20251210184716506 updated by admin', '2025-12-10 19:19:41'),
(268, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-10 19:32:20'),
(269, 11, 'User logged in', 'User \'finn22\' logged in successfully.', '2025-12-10 19:32:29'),
(270, 11, 'User logged out', 'User \'finn22\' logged out successfully.', '2025-12-10 19:35:55'),
(271, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-10 19:36:00'),
(272, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-12-11 12:38:10'),
(273, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-11 12:38:20'),
(274, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-11 23:12:17'),
(275, 1, 'Scholarship application submitted', 'Application ID 20251211231624619 submitted by admin.', '2025-12-11 23:16:24'),
(276, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-12 11:01:03');

-- --------------------------------------------------------

--
-- Table structure for table `parents_info`
--

CREATE TABLE `parents_info` (
  `id` int(11) NOT NULL,
  `application_id` varchar(100) NOT NULL,
  `father_lastname` varchar(255) DEFAULT NULL,
  `father_givenname` varchar(255) DEFAULT NULL,
  `father_middlename` varchar(255) DEFAULT NULL,
  `father_cellphone` varchar(15) DEFAULT NULL,
  `father_education` enum('No Formal Education','Elementary Undergraduate','Elementary Graduate','High School Undergraduate','High School Graduate','Vocational Course','College Undergraduate','College Graduate','Postgraduate') NOT NULL,
  `father_occupation` enum('Government','Private Sector','Self-Employed','Laborer','Freelancer','NGO/Non-Profit','Overseas Employment','Casual','Contractual','Intern') NOT NULL,
  `father_income` decimal(10,2) DEFAULT NULL,
  `mother_lastname` varchar(255) DEFAULT NULL,
  `mother_givenname` varchar(255) DEFAULT NULL,
  `mother_middlename` varchar(255) DEFAULT NULL,
  `mother_cellphone` varchar(15) DEFAULT NULL,
  `mother_education` enum('No Formal Education','Elementary Undergraduate','Elementary Graduate','High School Undergraduate','High School Graduate','Vocational Course','College Undergraduate','College Graduate','Postgraduate') NOT NULL,
  `mother_occupation` enum('Government','Private Sector','Self-Employed','Laborer','Freelancer','NGO/Non-Profit','Overseas Employment','Casual','Contractual','Intern') NOT NULL,
  `mother_income` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents_info`
--

INSERT INTO `parents_info` (`id`, `application_id`, `father_lastname`, `father_givenname`, `father_middlename`, `father_cellphone`, `father_education`, `father_occupation`, `father_income`, `mother_lastname`, `mother_givenname`, `mother_middlename`, `mother_cellphone`, `mother_education`, `mother_occupation`, `mother_income`) VALUES
(83, '20251204031307888', 'Dizon', 'Arman', 'Godoy', '09356455565', 'High School Graduate', 'Private Sector', 20000.00, 'Argonsola', 'Jessica', 'Atilano', '09552335871', 'College Graduate', 'Government', 30000.00),
(84, '20251204033311414', 'Dizon', 'Arman', 'Godoy', '09356455565', 'No Formal Education', 'Government', 5000.00, 'Argonsola', 'Jessica', 'Atilano', '094515426544', 'No Formal Education', 'Government', 10000.00),
(85, '20251204054601248', 'Dizon', 'Arman', 'Godoy', '09356455565', 'High School Graduate', 'Private Sector', 100000.00, 'Argonsola', 'givenname', 'Atilano', '094515426544', 'College Graduate', 'Government', 20000.00),
(86, '20251204055643624', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', 1324345.00, 'Argonsola', 'givenname', 'Atilano', '094515426544', 'Elementary Graduate', 'Overseas Employment', 2432556.00),
(87, '20251204061649665', 'legaspi', 'melane', 'Arjec Jose', '123456789', 'College Graduate', 'Freelancer', 123456.00, 'melnaie', 'Jessica', 'quindao', '1234561234', 'High School Graduate', 'Overseas Employment', 123456.00),
(88, '20251207161201798', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', 10000.00, 'Argonsola', 'givenname', 'Atilano', '09451542654', 'No Formal Education', 'Government', 10000.00),
(89, '20251207161919239', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'Postgraduate', 'Contractual', 10000.00, 'Argonsola', 'givenname', 'Atilano', '09552335871', 'College Undergraduate', 'Contractual', 20000.00),
(90, '20251210171214576', 'A.', 'Dizon', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', 20000.00, 'Argonsola', 'givenname', 'Atilano', '09451542654', 'College Graduate', 'Intern', 10000.00),
(91, '20251210173707266', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', 200000.00, 'Argonsola', 'givenname', 'Atilano', '09552335871', 'No Formal Education', 'Government', 300000.00),
(92, '20251210180040545', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', 10000.00, 'Argonsola', 'givenname', 'Atilano', '09451542654', 'No Formal Education', 'Government', 10000.00),
(93, '20251210184716506', 'Dizon', 'Arman', 'Godoy', '09356455565', 'High School Graduate', 'Private Sector', 15000.00, 'Argonsola', 'givenname', 'Atilano', '09451542654', 'College Undergraduate', 'Government', 20000.00),
(94, '20251211231624619', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'Postgraduate', 'Intern', 20000.00, 'Argonsola', 'givenname', '', '09451542654', 'No Formal Education', 'Casual', 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_applications`
--

CREATE TABLE `scholarship_applications` (
  `application_id` varchar(20) NOT NULL,
  `date` datetime DEFAULT NULL,
  `semester` varchar(10) NOT NULL,
  `school_year` varchar(9) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `course` varchar(50) NOT NULL,
  `yr_sec` varchar(15) NOT NULL,
  `major` varchar(50) NOT NULL,
  `cell_no` varchar(15) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `zip_code` int(11) NOT NULL,
  `present_address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sex` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age` int(11) NOT NULL,
  `place_of_birth` varchar(50) DEFAULT NULL,
  `civil_status` enum('single','married','widowed','divorced','separated') DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `scholarship_grant` varchar(100) DEFAULT NULL,
  `disability` varchar(100) DEFAULT NULL,
  `indigenous_group` varchar(100) DEFAULT NULL,
  `reason_scholarship` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','not qualified') NOT NULL DEFAULT 'pending',
  `notified` int(11) NOT NULL,
  `attachments` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_applications`
--

INSERT INTO `scholarship_applications` (`application_id`, `date`, `semester`, `school_year`, `full_name`, `course`, `yr_sec`, `major`, `cell_no`, `permanent_address`, `zip_code`, `present_address`, `email`, `sex`, `date_of_birth`, `age`, `place_of_birth`, `civil_status`, `religion`, `scholarship_grant`, `disability`, `indigenous_group`, `reason_scholarship`, `user_id`, `status`, `notified`, `attachments`) VALUES
('20251204031307888', '2025-12-04 11:10:26', '1st sem', '2025-2026', 'Arjec Jose Dizon', 'BS INFOTECH', '4A', 'PROGRAMMING', '09158423449', 'daisy road, guiwan, z.c', 7000, 'daisy road, guiwan, z.c', 'dizon.arjecjose@gmail.com', 'male', '1998-06-15', 27, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', '', 'test', 11, 'pending', 0, ''),
('20251204033311414', '2025-12-04 11:31:00', '1st sem', '2025-2026', 'Arjec Jose Dizon', 'BS INFOSYS', '4A', 'PROGRAMMING', '09158423449', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '1999-07-07', 26, 'Zamboanga City', 'single', 'roman catholic', 'CHED-FULL MERIT', 'None', 'Chavacano', 'test', 11, 'approved', 0, ''),
('20251204054601248', '2025-12-04 13:40:35', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS INFOSYS', '4A', 'PROGRAMMING', '09154785417', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2000-06-06', 25, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'None', 'N/A', 'qwredhfhdhjdhgdhtdjdf', 1, 'pending', 0, ''),
('20251204055643624', '2025-12-04 13:54:14', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS INFOSYS', '4F', 'PROGRAMMING', '09466456566', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '1999-02-02', 26, 'Zamboanga City', 'single', 'islam', 'CHED-HALF MERIT', 'N/A', 'N/A', 'ssfsgfdg', 12, 'approved', 0, ''),
('20251204061649665', '2025-12-04 14:12:58', '2nd sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4A', 'ENGLISH', '09132465790', 'daisy road', 7000, 'daisy road', 'arjecdizon99@gmail.com', 'male', '2006-11-15', 19, 'Zamboanga City', 'married', 'islam', 'CHED TDP (TULONG DULONG PROGRAM)', 'None', 'N/A', 'asdfghjzxcbnm', 13, 'pending', 0, ''),
('20251207161201798', '2025-12-08 00:07:44', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS INFOSYS', '4F', 'PROGRAMMING', '09486409573', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2001-01-01', 24, 'Zamboanga City', 'single', 'roman catholic', 'CHED-FULL MERIT', 'None', 'N/A', 'qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq', 1, 'pending', 0, ''),
('20251207161919239', '2025-12-08 00:13:03', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4G', 'ENGLISH', '09486409573', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2003-02-13', 22, 'Zamboanga City', 'separated', 'a biblical church', 'CHED-FULL MERIT', 'None', 'N/A', 'qweeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', 1, 'pending', 0, ''),
('20251210171214576', '2025-12-11 01:10:46', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4G', 'ENGLISH', '09548712364', 'daisy road', 7000, 'daisy road', 'arjecdizon99@gmail.com', 'male', '2003-01-01', 22, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qeqweqeqeqeqeqeqeqw', 1, 'pending', 0, ''),
('20251210173707266', '2025-12-11 01:34:41', '1st sem', '2025-2026', 'Al-khazri Sali Alim', 'BS CE', '4G', 'ENGLISH', '09154785417', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2004-07-08', 21, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'igorot', 'eqweqweqweqweqeqqweqeq', 1, 'pending', 0, ''),
('20251210180040545', '2025-12-11 01:56:47', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4A', 'ENGLISH', '09154785417', 'daisy road', 7000, 'daisy road', 'admin@admin.com', 'male', '2001-01-01', 24, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'None', 'N/A', 'qweqw', 1, 'pending', 0, ''),
('20251210184716506', '2025-12-11 02:43:36', '1st sem', '2025-2026', 'Marco Jean Pagotasidro', 'BS CE', '4A', 'ENGLISH', '09486409573', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2009-06-10', 16, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qqqqqqqqqqqqqqqqqqqqqqqqqq', 1, 'pending', 0, ''),
('20251211231624619', '2025-12-12 07:12:32', '1st sem', '2025-2026', 'Arjec Jose Dizon', 'BS INFOTECH', '4A', 'PROGRAMMING', '09466456566', 'daisy road', 7000, 'daisy road', 'arjecdizon99@gmail.com', 'male', '1994-06-21', 31, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qweqweqwe', 1, 'pending', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_files`
--

CREATE TABLE `scholarship_files` (
  `id` int(11) NOT NULL,
  `application_id` varchar(20) NOT NULL,
  `files` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_files`
--

INSERT INTO `scholarship_files` (`id`, `application_id`, `files`) VALUES
(1, '20251116182413313', '[\"1763317453_691a16cd4dc07.png\",\"1763317453_691a16cd4de86.png\",\"1763317453_691a16cd4df85.png\",\"1763317453_691a16cd4e14d.png\",\"1763317453_691a16cd4e244.png\"]'),
(2, '20251118014737885', '[\"1763430457_691bd039d9ee2.pdf\",\"1763430457_691bd039da69f.png\",\"1763430457_691bd039daa21.png\"]'),
(3, '20251120005243756', '[\"1763599963_691e665bb9aa3.jpg\",\"1763599963_691e665bb9ed4.pdf\"]'),
(4, '20251120032139792', '[\"1763608899_691e8943c2ee6.pdf\"]'),
(5, '20251121010616855', '[\"1763687176_691fbb08d34af.pdf\",\"1763687176_691fbb08d38d1.pdf\"]'),
(6, '20251121012446449', '[\"1763688286_691fbf5e6e487.pdf\",\"1763688286_691fbf5e6e862.pdf\"]'),
(7, '20251121062359913', '[\"1763706239_6920057fe03f8.pdf\"]'),
(8, '20251121095848774', '[\"1763719128_692037d8bef76.pdf\",\"1763719128_692037d8bf176.pdf\",\"1763719128_692037d8bf385.png\"]'),
(9, '20251121101153923', '[\"1763719913_69203ae9e22dc.pdf\",\"1763719913_69203ae9e24ca.png\"]'),
(10, '20251121103141752', '[\"1763721101_69203f8db86f9.pdf\"]'),
(11, '20251121131725238', '[\"1763731045_692066653a8a0.png\"]'),
(12, '20251121134052201', '[\"1763732452_69206be4317a5.jpg\",\"1763732452_69206be431df1.pdf\"]'),
(13, '20251121135639274', '[\"1763733399_69206f97454b7.png\",\"1763733399_69206f974573f.png\"]'),
(14, '20251122124444832', '[\"1763815484_6921b03ccbacc.pdf\",\"1763815484_6921b03ccbf18.jpg\"]'),
(15, '20251203162603263', '[\"1764779163_6930649b43c8d.png\",\"1764779163_6930649b4414a.png\"]'),
(16, '20251203170106386', '[\"1764781266_69306cd25ea9b.png\",\"1764781266_69306cd25ed18.pdf\"]'),
(17, '20251203170704141', '[\"1764781624_69306e38271d3.pdf\"]'),
(18, '20251204002525338', '[\"1764807925_6930d4f553005.pdf\",\"1764807925_6930d4f5531d5.pdf\"]'),
(19, '20251204012052556', '[\"1764811252_6930e1f48ad56.pdf\"]'),
(20, '20251204013339301', '[\"1764812019_6930e4f34a145.pdf\"]'),
(21, '20251204014736619', '[\"1764812856_6930e8389a88a.pdf\"]'),
(22, '20251204024139706', '[\"1764816099_6930f4e3af3ea.pdf\"]'),
(23, '20251204030605781', '[\"1764817565_6930fa9dc16a7.pdf\"]'),
(24, '20251204031307888', '[\"1764817987_6930fc43da33e.pdf\",\"1764817987_6930fc43da5b7.pdf\"]'),
(25, '20251204033311414', '[\"1764819191_693100f76887f.png\",\"1764819191_693100f768ae5.png\"]'),
(26, '20251204054601248', '[\"1764827161_693120193d72e.png\",\"1764827161_693120193d941.png\",\"1764827161_693120193db44.png\"]'),
(27, '20251204055643624', '[\"1764827803_6931229b98af6.pdf\"]'),
(28, '20251204061649665', '[\"1764829009_69312751a3c20.pdf\"]'),
(29, '20251207161201798', '[\"1765123921_6935a751c3c6f.pdf\"]'),
(30, '20251207161919239', '[\"1765124359_6935a9073bdb5.pdf\"]'),
(31, '20251210171214576', '[]'),
(32, '20251210173707266', '[\"1765388227_6939afc343802.jpg\"]'),
(33, '20251210180040545', '[\"1765389640_6939b54885c91.jpg\"]'),
(34, '20251210184716506', '[\"1765392436_6939c0347c0e3.pdf\"]'),
(35, '20251211231624619', '[\"1765494984_693b50c8980d5.pdf\",\"1765494984_693b50c89850b.jpg\"]');

-- --------------------------------------------------------

--
-- Table structure for table `schools_attended`
--

CREATE TABLE `schools_attended` (
  `id` int(11) NOT NULL,
  `application_id` varchar(100) NOT NULL,
  `elementary` varchar(255) DEFAULT NULL,
  `elementary_year_grad` year(4) DEFAULT NULL,
  `elementary_honors` varchar(255) DEFAULT NULL,
  `secondary` varchar(255) DEFAULT NULL,
  `secondary_year_grad` year(4) DEFAULT NULL,
  `secondary_honors` varchar(255) DEFAULT NULL,
  `college` varchar(255) DEFAULT NULL,
  `college_year_grad` year(4) DEFAULT NULL,
  `college_honors` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools_attended`
--

INSERT INTO `schools_attended` (`id`, `application_id`, `elementary`, `elementary_year_grad`, `elementary_honors`, `secondary`, `secondary_year_grad`, `secondary_honors`, `college`, `college_year_grad`, `college_honors`) VALUES
(85, '20251204031307888', 'ZCS', '2016', '1', 'ZNHS-WEST', '2022', 'none', 'ZPPSU', '0000', ''),
(86, '20251204033311414', 'ZCS', '2022', 'None', 'ZNHS-WEST', '2022', 'None', 'ZPPSU', '0000', ''),
(87, '20251204054601248', 'ZCS', '2016', '', 'ZNHS-WEST', '2022', '1', 'ZPPSU', '0000', ''),
(88, '20251204055643624', 'ZCS', '2023', '1', 'ZNHS-WEST', '2023', 'none', 'ZPPSU', '0000', ''),
(89, '20251204061649665', 'Harvard University', '2022', 'None', 'New York University', '2022', '2', 'ZPPSU', '2022', 'none'),
(90, '20251207161201798', 'ZCS', '2020', '1', 'ZNHS-WEST', '2020', '1', 'ZPPSU', '0000', ''),
(91, '20251207161919239', 'ZCS', '2024', '1', 'ZNHS-WEST', '2024', '1', '', '0000', ''),
(92, '20251210171214576', 'ZCS', '2016', 'None', 'ZNHS-WEST', '2022', 'none', 'ZPPSU', '0000', ''),
(93, '20251210173707266', 'ZCS', '2019', 'none', 'ZNHS-WEST', '2024', 'none', 'ZPPSU', '0000', ''),
(94, '20251210180040545', 'ZCS', '2016', 'None', 'ZNHS-WEST', '2022', 'none', 'ZPPSU', '0000', ''),
(95, '20251210184716506', 'ZCS', '2016', '1', 'ZNHS-WEST', '2016', '1', 'ZPPSU', '2000', ''),
(96, '20251211231624619', 'ZCS', '2016', 'none', 'ZNHS-WEST', '2022', 'none', 'ZPPSU', '0000', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `role`) VALUES
(1, 'admin', '$2y$10$CutO7I4nRGj/7sszz1IGQe1zsSrG8PCepxUpeajBSYsizlQ1adoBG', '2024-09-27 20:44:57', 'admin'),
(2, 'useraccount01', '$2y$10$79QCeYHFnoNTOXgqT.jxe.A7SfubRz.Yp0RFy6wjmnuM9Va.Gyo2q', '2024-10-01 09:04:55', 'student'),
(3, 'useraccount02', '$2y$10$kK/DWNxdS4L.lv8HEplzNejkxW5v1F64XXMvPHl88y1qut64NRp4e', '2024-10-15 20:00:04', 'student'),
(11, 'finn22', '$2y$10$DQkEIuauR1rVUwpCuZFWoOsb5LzKbBIuzQ3doL/daxXG7zLUtw1qK', '2024-12-09 22:16:28', 'student'),
(12, 'finn23', '$2y$10$REKMjRAurfLnjpnKhNb1V.oc9Hdmx2zzkrmgVjrKg/J9nu1Xrt3Pq', '2024-12-09 22:37:17', 'student'),
(13, 'finn24', '$2y$10$Dgd91Kwd5gwu5ngNv6QVQO8l13GHCwFPYU3Q5LpeXFJNxMAw7UrkG', '2024-12-09 22:40:06', 'student'),
(14, 'jan', '$2y$10$VjpJ4lYB9/rN48dMbIBsL.///qlTVt5PmX1nn0YI.8V5yuTiSd88e', '2025-11-21 14:57:50', 'student'),
(15, 'mark', '$2y$10$KItjpuaYCiWHXvsLzXsZweDehuZNdySnJCY0ht2rLYvu.8eiRMO.i', '2025-11-21 15:10:38', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dropdown_course_major`
--
ALTER TABLE `dropdown_course_major`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dropdown_scholarship_grant`
--
ALTER TABLE `dropdown_scholarship_grant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grant_name` (`grant_name`);

--
-- Indexes for table `dropdown_sem_sy`
--
ALTER TABLE `dropdown_sem_sy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grant_requirements`
--
ALTER TABLE `grant_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grant_name` (`grant_name`);

--
-- Indexes for table `house_info`
--
ALTER TABLE `house_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `house_info_ibfk_1` (`application_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parents_info`
--
ALTER TABLE `parents_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parents_info_ibfk_1` (`application_id`);

--
-- Indexes for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `scholarship_files`
--
ALTER TABLE `scholarship_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schools_attended`
--
ALTER TABLE `schools_attended`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schools_attended_ibfk_1` (`application_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `dropdown_course_major`
--
ALTER TABLE `dropdown_course_major`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dropdown_scholarship_grant`
--
ALTER TABLE `dropdown_scholarship_grant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `dropdown_sem_sy`
--
ALTER TABLE `dropdown_sem_sy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `grant_requirements`
--
ALTER TABLE `grant_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `house_info`
--
ALTER TABLE `house_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;

--
-- AUTO_INCREMENT for table `parents_info`
--
ALTER TABLE `parents_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `scholarship_files`
--
ALTER TABLE `scholarship_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `schools_attended`
--
ALTER TABLE `schools_attended`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `grant_requirements`
--
ALTER TABLE `grant_requirements`
  ADD CONSTRAINT `grant_requirements_ibfk_1` FOREIGN KEY (`grant_name`) REFERENCES `dropdown_scholarship_grant` (`grant_name`) ON DELETE CASCADE;

--
-- Constraints for table `house_info`
--
ALTER TABLE `house_info`
  ADD CONSTRAINT `house_info_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `scholarship_applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `parents_info`
--
ALTER TABLE `parents_info`
  ADD CONSTRAINT `parents_info_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `scholarship_applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scholarship_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `schools_attended`
--
ALTER TABLE `schools_attended`
  ADD CONSTRAINT `schools_attended_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `scholarship_applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
