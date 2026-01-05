-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2026 at 02:53 AM
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

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `created_at`) VALUES
(11, 'e', 'e', '2025-12-22 00:57:16'),
(12, 'q', 'q', '2025-12-22 00:57:45'),
(13, 'a', 'a', '2025-12-22 00:57:48'),
(14, 'b', 'b', '2025-12-22 00:57:52');

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
(4, 'BS CE', 'ENGLISH'),
(6, 'BPED', 'N/A');

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
  `school_year` varchar(9) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dropdown_sem_sy`
--

INSERT INTO `dropdown_sem_sy` (`id`, `semester`, `school_year`, `is_default`) VALUES
(3, '1st sem', '2025-2026', 0),
(5, '2nd sem', '2025-2026', 1);

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
(1, 'CHED TDP (TULONG DULONG PROGRAM)', '2x2 Picture', NULL, 2, '2025-12-10 16:56:43'),
(2, 'CHED-FULL MERIT', '2x2 Picture', NULL, 1, '2025-12-10 17:49:51'),
(3, 'CHED TDP (TULONG DULONG PROGRAM)', 'CERTIFICATE OF INDIGENCY', NULL, 1, '2025-12-10 18:02:11'),
(4, 'CHED TDP (TULONG DULONG PROGRAM)', 'Form 138', NULL, 3, '2025-12-19 03:42:50');

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
(94, '20251211231624619', 'Owned'),
(95, '20251214054304530', 'Owned'),
(96, '20251214100648858', 'Owned'),
(97, '20251214105201010', 'Owned'),
(98, '20251214110322580', 'Owned'),
(99, '20251214110552298', 'Owned'),
(101, '20251215104428645', 'Owned'),
(102, '20251215120415457', 'Owned'),
(123, '20251222004552424', 'Rented');

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
(469, 24, 'Account created with full profile', 'User \'aj111\' account created with complete profile information.', '2025-12-21 12:34:33'),
(470, 24, 'User logged in', 'User \'aj111\' logged in successfully.', '2025-12-21 12:34:58'),
(471, 24, 'Scholarship application submitted', 'Application ID 20251221132730203 submitted by aj111. Files uploaded to: Dizon_Arjec_Jose_A_20251221132730203', '2025-12-21 13:27:30'),
(472, 24, 'Application Deleted', 'Application ID 20251221132730203 deleted successfully.', '2025-12-21 13:35:27'),
(473, 24, 'Scholarship application submitted', 'Application ID 20251221133737430 submitted by aj111. Files uploaded to: Dizon_Arjec_Jose_A_20251221133737430', '2025-12-21 13:37:37'),
(474, 24, 'User logged out', 'User \'aj111\' logged out successfully.', '2025-12-21 13:43:15'),
(475, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-21 13:43:20'),
(476, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:10:10'),
(477, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:15:19'),
(478, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:15:51'),
(479, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:16:56'),
(480, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:17:17'),
(481, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:17:43'),
(482, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:18:20'),
(483, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:18:37'),
(484, 1, 'Application Status Updated', 'Application ID 20251221133737430 marked as \'pending\' without notification.', '2025-12-21 14:19:08'),
(485, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:28:38'),
(486, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:29:07'),
(487, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:30:07'),
(488, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2025-12-21 14:32:47'),
(489, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:33:20'),
(490, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:33:59'),
(491, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:34:45'),
(492, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:35:12'),
(493, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:36:59'),
(494, 1, 'Application updated by admin', 'Application ID 20251221133737430 updated by admin', '2025-12-21 14:37:07'),
(495, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-21 14:37:20'),
(496, 24, 'User logged in', 'User \'aj111\' logged in successfully.', '2025-12-21 14:37:25'),
(497, 24, 'Scholarship application submitted', 'Application ID 20251221143859144 submitted by aj111. Files uploaded to: Dizon_Arjec_Jose_A_20251221143859144', '2025-12-21 14:38:59'),
(498, 24, 'Application Deleted', 'Application ID 20251221143859144 deleted successfully.', '2025-12-22 00:43:46'),
(499, 24, 'Application Deleted', 'Application ID 20251221133737430 deleted successfully.', '2025-12-22 00:43:50'),
(500, 24, 'Scholarship application submitted', 'Application ID 20251222004552424 submitted by aj111. Files uploaded to: Dizon_Arjec_Jose_A_20251222004552424', '2025-12-22 00:45:52'),
(501, 24, 'User logged out', 'User \'aj111\' logged out successfully.', '2025-12-22 00:46:15'),
(502, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-22 00:46:22'),
(503, 1, 'Application updated by admin', 'Application ID 20251222004552424 updated by admin', '2025-12-22 00:49:48'),
(504, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-22 00:52:04'),
(505, 24, 'User logged in', 'User \'aj111\' logged in successfully.', '2025-12-22 00:52:32'),
(506, 24, 'User logged out', 'User \'aj111\' logged out successfully.', '2025-12-22 00:56:17'),
(507, 1, 'Failed login attempt', 'Invalid password for user \'admin\'.', '2025-12-22 00:56:21'),
(508, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-22 00:56:27'),
(509, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-22 00:57:23'),
(510, 24, 'User logged in', 'User \'aj111\' logged in successfully.', '2025-12-22 00:57:27'),
(511, 24, 'User logged out', 'User \'aj111\' logged out successfully.', '2025-12-22 00:57:35'),
(512, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-22 00:57:40'),
(513, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-22 00:57:53'),
(514, 24, 'User logged in', 'User \'aj111\' logged in successfully.', '2025-12-22 00:57:56'),
(515, 24, 'User logged out', 'User \'aj111\' logged out successfully.', '2025-12-22 01:02:42'),
(516, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-22 01:02:47'),
(517, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2025-12-22 01:05:46'),
(518, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-22 05:04:23'),
(519, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2025-12-22 05:04:40'),
(520, 24, 'User logged in', 'User \'aj111\' logged in successfully.', '2025-12-22 05:04:46'),
(521, 24, 'User logged out', 'User \'aj111\' logged out successfully.', '2025-12-22 05:05:46'),
(522, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2025-12-23 03:33:26'),
(523, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2025-12-23 03:34:03'),
(524, 1, 'Application updated by admin', 'Application ID 20251222004552424 updated by admin', '2025-12-23 03:34:13'),
(525, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2025-12-23 03:34:30'),
(526, 1, 'Application updated by admin', 'Application ID 20251222004552424 updated by admin', '2025-12-23 03:46:52'),
(527, 1, 'User logged in', 'User \'admin\' logged in successfully.', '2026-01-05 02:32:53'),
(528, 1, 'Error', 'Email could not be sent: Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2026-01-05 02:33:12'),
(529, 1, 'Application updated by admin', 'Application ID 20251222004552424 updated by admin', '2026-01-05 02:33:41'),
(530, 1, 'Application approved and notified', 'ID 20251222004552424 approved and notified.', '2026-01-05 02:45:43'),
(531, 1, 'Application updated by admin', 'Application ID 20251222004552424 updated by admin', '2026-01-05 02:46:30'),
(532, 1, 'Application Status Rejected', 'Application ID 20251222004552424 marked as \'not qualified\' without notification.', '2026-01-05 02:46:39'),
(533, 1, 'Application Status Updated', 'Application ID 20251222004552424 marked as \'pending\' without notification.', '2026-01-05 02:47:01'),
(534, 1, 'Application Status Rejected', 'Application ID 20251222004552424 marked as \'not qualified\' but email failed: Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2026-01-05 02:47:14'),
(535, 1, 'Error Updating Status', 'No application found with ID 20251222004552424 or it has already been processed.', '2026-01-05 02:47:50'),
(536, 1, 'Application Status Updated', 'Application ID 20251222004552424 marked as \'pending\' without notification.', '2026-01-05 02:47:56'),
(537, 1, 'Application Status Rejected', 'Application ID 20251222004552424 marked as \'not qualified\' but email failed: Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2026-01-05 02:48:03'),
(538, 1, 'Application Status Updated', 'Application ID 20251222004552424 marked as \'pending\' without notification.', '2026-01-05 02:48:21'),
(539, 1, 'Application Status Rejected', 'Application ID 20251222004552424 marked as \'not qualified\' but email failed: Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2026-01-05 02:48:29'),
(540, 1, 'Application Status Updated', 'Application ID 20251222004552424 marked as \'pending\' without notification.', '2026-01-05 02:50:04'),
(541, 1, 'Application Status Rejected', 'Application ID 20251222004552424 marked as \'not qualified\' and notified.', '2026-01-05 02:50:16'),
(542, 1, 'Application Status Updated', 'Application ID 20251222004552424 marked as \'pending\' without notification.', '2026-01-05 02:50:59'),
(543, 1, 'Application approved and notified', 'ID 20251222004552424 approved and notified.', '2026-01-05 02:51:08'),
(544, 1, 'User logged out', 'User \'admin\' logged out successfully.', '2026-01-05 02:52:05'),
(545, 24, 'User logged in', 'User \'aj111\' logged in successfully.', '2026-01-05 02:52:11');

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
  `father_income` varchar(50) DEFAULT NULL,
  `mother_lastname` varchar(255) DEFAULT NULL,
  `mother_givenname` varchar(255) DEFAULT NULL,
  `mother_middlename` varchar(255) DEFAULT NULL,
  `mother_cellphone` varchar(15) DEFAULT NULL,
  `mother_education` enum('No Formal Education','Elementary Undergraduate','Elementary Graduate','High School Undergraduate','High School Graduate','Vocational Course','College Undergraduate','College Graduate','Postgraduate') NOT NULL,
  `mother_occupation` enum('Government','Private Sector','Self-Employed','Laborer','Freelancer','NGO/Non-Profit','Overseas Employment','Casual','Contractual','Intern') NOT NULL,
  `mother_income` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents_info`
--

INSERT INTO `parents_info` (`id`, `application_id`, `father_lastname`, `father_givenname`, `father_middlename`, `father_cellphone`, `father_education`, `father_occupation`, `father_income`, `mother_lastname`, `mother_givenname`, `mother_middlename`, `mother_cellphone`, `mother_education`, `mother_occupation`, `mother_income`) VALUES
(83, '20251204031307888', 'Dizon', 'Arman', 'Godoy', '09356455565', 'High School Graduate', 'Private Sector', '20000.00', 'Argonsola', 'Jessica', 'Atilano', '09552335871', 'College Graduate', 'Government', '30000.00'),
(84, '20251204033311414', 'Dizon', 'Arman', 'Godoy', '09356455565', 'No Formal Education', 'Government', '5000.00', 'Argonsola', 'Jessica', 'Atilano', '094515426544', 'No Formal Education', 'Government', '10000.00'),
(85, '20251204054601248', 'Dizon', 'Arman', 'Godoy', '09356455565', 'High School Graduate', 'Private Sector', '100000.00', 'Argonsola', 'givenname', 'Atilano', '094515426544', 'College Graduate', 'Government', '20000.00'),
(86, '20251204055643624', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', '1324345.00', 'Argonsola', 'givenname', 'Atilano', '094515426544', 'Elementary Graduate', 'Overseas Employment', '2432556.00'),
(87, '20251204061649665', 'legaspi', 'melane', 'Arjec Jose', '123456789', 'College Graduate', 'Freelancer', '123456.00', 'melnaie', 'Jessica', 'quindao', '1234561234', 'High School Graduate', 'Overseas Employment', '123456.00'),
(88, '20251207161201798', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', '10000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'No Formal Education', 'Government', '10000.00'),
(89, '20251207161919239', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'Postgraduate', 'Contractual', '10000.00', 'Argonsola', 'givenname', 'Atilano', '09552335871', 'College Undergraduate', 'Contractual', '20000.00'),
(90, '20251210171214576', 'A.', 'Dizon', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', '20000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'College Graduate', 'Intern', '10000.00'),
(91, '20251210173707266', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', '200000.00', 'Argonsola', 'givenname', 'Atilano', '09552335871', 'No Formal Education', 'Government', '300000.00'),
(92, '20251210180040545', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'No Formal Education', 'Government', '10000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'No Formal Education', 'Government', '10000.00'),
(93, '20251210184716506', 'Dizon', 'Arman', 'Godoy', '09356455565', 'High School Graduate', 'Private Sector', '15000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'College Undergraduate', 'Government', '20000.00'),
(94, '20251211231624619', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'Postgraduate', 'Intern', '20000.00', 'Argonsola', 'givenname', '', '09451542654', 'No Formal Education', 'Casual', '20000.00'),
(95, '20251214054304530', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'Elementary Undergraduate', 'Intern', '20000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'College Graduate', 'Contractual', '20000.00'),
(96, '20251214100648858', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'College Graduate', 'Intern', '2132135.00', 'Argonsola', '1', 'Atilano', '09552335871', 'Vocational Course', 'Intern', '6341654.00'),
(97, '20251214105201010', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'College Undergraduate', 'NGO/Non-Profit', '20000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'College Undergraduate', 'Freelancer', '20000.00'),
(98, '20251214110322580', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'Postgraduate', 'Intern', '50000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'Postgraduate', 'Intern', '20000.00'),
(99, '20251214110552298', 'A DIZON', 'JESSICA', 'Arjec Jose', '09154542324', 'Postgraduate', 'Intern', '10000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'No Formal Education', 'Government', '20000.00'),
(101, '20251215104428645', 'A.', 'Dizon', 'Arjec Jose', '09356455565', 'Postgraduate', 'Intern', '10000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'No Formal Education', 'Intern', '20000.00'),
(102, '20251215120415457', 'A DIZON', 'JESSICA', 'Arjec Jose', '09356455565', 'College Graduate', 'Intern', '20000.00', 'Argonsola', 'givenname', 'Atilano', '09451542654', 'No Formal Education', 'Government', '10000.00'),
(123, '20251222004552424', 'Dizon', 'Arman', 'G.', '09356455565', 'High School Graduate', 'Private Sector', '₱15000-20000', 'Argonsola', 'Jessica', 'A.', '09552335871', 'College Graduate', 'Government', '₱15000-20000');

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
('20251204061649665', '2025-12-04 14:12:58', '2nd sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4A', 'ENGLISH', '09132465790', 'daisy road', 7000, 'daisy road', 'arjecdizon99@gmail.com', 'male', '2006-11-15', 19, 'Zamboanga City', 'married', 'islam', 'CHED TDP (TULONG DULONG PROGRAM)', 'None', 'N/A', 'asdfghjzxcbnm', 13, 'approved', 0, ''),
('20251207161201798', '2025-12-08 00:07:44', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS INFOSYS', '4F', 'PROGRAMMING', '09486409573', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2001-01-01', 24, 'Zamboanga City', 'single', 'roman catholic', 'CHED-FULL MERIT', 'None', 'N/A', 'qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq', 1, 'pending', 0, ''),
('20251207161919239', '2025-12-08 00:13:03', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4G', 'ENGLISH', '09486409573', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2003-02-13', 22, 'Zamboanga City', 'separated', 'a biblical church', 'CHED-FULL MERIT', 'None', 'N/A', 'qweeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', 1, 'pending', 0, ''),
('20251210171214576', '2025-12-11 01:10:46', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4G', 'ENGLISH', '09548712364', 'daisy road', 7000, 'daisy road', 'arjecdizon99@gmail.com', 'male', '2003-01-01', 22, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qeqweqeqeqeqeqeqeqw', 1, 'pending', 0, ''),
('20251210173707266', '2025-12-11 01:34:41', '1st sem', '2025-2026', 'Al-khazri Sali Alim', 'BS CE', '4G', 'ENGLISH', '09154785417', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2004-07-08', 21, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'igorot', 'eqweqweqweqweqeqqweqeq', 1, 'pending', 0, ''),
('20251210180040545', '2025-12-11 01:56:47', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4A', 'ENGLISH', '09154785417', 'daisy road', 7000, 'daisy road', 'admin@admin.com', 'male', '2001-01-01', 24, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'None', 'N/A', 'qweqw', 1, 'pending', 0, ''),
('20251210184716506', '2025-12-11 02:43:36', '1st sem', '2025-2026', 'Marco Jean Pagotasidro', 'BS CE', '4A', 'ENGLISH', '09486409573', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2009-06-10', 16, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qqqqqqqqqqqqqqqqqqqqqqqqqq', 1, 'pending', 0, ''),
('20251211231624619', '2025-12-12 07:12:32', '1st sem', '2025-2026', 'Arjec Jose Dizon', 'BS INFOTECH', '4A', 'PROGRAMMING', '09466456566', 'daisy road', 7000, 'daisy road', 'arjecdizon99@gmail.com', 'male', '1994-06-21', 31, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qweqweqwe', 1, 'approved', 0, ''),
('20251214054304530', '2025-12-14 13:38:46', '2nd sem', '2025-2026', 'Dizon Arjec Jose A.', 'BS INFOSYS', '3A', 'PROGRAMMING', '09158423449', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2006-03-08', 19, 'Zamboanga City', 'single', 'roman catholic', 'CHED-HALF MERIT', 'N/A', 'N/A', '123123\r\n', 11, 'pending', 0, ''),
('20251214100648858', '2025-12-14 18:03:49', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '4G', 'ENGLISH', '09158423449', 'daisy road', 7000, 'daisy road', 'arjecdizon99@gmail.com', 'male', '1979-10-17', 46, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'uygufufiufigiugig8', 1, 'pending', 0, ''),
('20251214105201010', '2025-12-14 18:50:01', '1st sem', '2025-2026', 'Al-khazri Sali Alim', 'BS INFOSYS', '4A', 'PROGRAMMING', '09548712364', 'daisy road', 7000, 'daisy road', 'arjecdizon99@gmail.com', 'male', '1997-06-17', 28, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qweqeqweqweqweqweqweqweqweqweqweqwe', 1, 'pending', 0, ''),
('20251214110322580', '2025-12-14 18:59:31', '1st sem', '2025-2026', 'Arjec Jose Dizon', 'BS CE', '4G', 'ENGLISH', '09158423449', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '1997-06-17', 28, 'Zamboanga City', 'single', 'a biblical church', 'CHED-FULL MERIT', 'N/A', 'N/A', 'qweqwe', 1, 'pending', 0, ''),
('20251214110552298', '2025-12-14 19:04:07', '1st sem', '2025-2026', 'Nurjan Idjad', 'BS CE', '2A', 'ENGLISH', '09154785417', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '1991-06-18', 34, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'tfdytdytdu6yt', 1, 'pending', 0, ''),
('20251215104428645', '2025-12-15 18:42:36', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '2A', 'ENGLISH', '09548712364', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2009-06-16', 16, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'abdvhysfdyafdsyuafdu\r\n', 16, 'pending', 0, ''),
('20251215120415457', '2025-12-15 20:01:54', '1st sem', '2025-2026', 'JESSICA A DIZON', 'BS CE', '1D', 'ENGLISH', '09548712364', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2009-05-15', 16, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qwwwwwwwwwwwwwwwww', 1, 'pending', 0, ''),
('20251222004552424', '2025-12-22 08:43:53', '2nd sem', '2025-2026', 'Dizon Arjec Jose A.', 'BS INFOTECH', '4A', 'PROGRAMMING', '09158423449', 'daisy road', 7000, 'daisy road', 'dizon.arjecjose@gmail.com', 'male', '2004-04-16', 21, 'Zamboanga City', 'single', 'roman catholic', 'CHED TDP (TULONG DULONG PROGRAM)', 'N/A', 'N/A', 'qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq', 24, 'approved', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_files`
--

CREATE TABLE `scholarship_files` (
  `id` int(11) NOT NULL,
  `application_id` varchar(20) NOT NULL,
  `files` text NOT NULL,
  `upload_folder` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_files`
--

INSERT INTO `scholarship_files` (`id`, `application_id`, `files`, `upload_folder`) VALUES
(1, '20251116182413313', '[\"1763317453_691a16cd4dc07.png\",\"1763317453_691a16cd4de86.png\",\"1763317453_691a16cd4df85.png\",\"1763317453_691a16cd4e14d.png\",\"1763317453_691a16cd4e244.png\"]', NULL),
(2, '20251118014737885', '[\"1763430457_691bd039d9ee2.pdf\",\"1763430457_691bd039da69f.png\",\"1763430457_691bd039daa21.png\"]', NULL),
(3, '20251120005243756', '[\"1763599963_691e665bb9aa3.jpg\",\"1763599963_691e665bb9ed4.pdf\"]', NULL),
(4, '20251120032139792', '[\"1763608899_691e8943c2ee6.pdf\"]', NULL),
(5, '20251121010616855', '[\"1763687176_691fbb08d34af.pdf\",\"1763687176_691fbb08d38d1.pdf\"]', NULL),
(6, '20251121012446449', '[\"1763688286_691fbf5e6e487.pdf\",\"1763688286_691fbf5e6e862.pdf\"]', NULL),
(7, '20251121062359913', '[\"1763706239_6920057fe03f8.pdf\"]', NULL),
(8, '20251121095848774', '[\"1763719128_692037d8bef76.pdf\",\"1763719128_692037d8bf176.pdf\",\"1763719128_692037d8bf385.png\"]', NULL),
(9, '20251121101153923', '[\"1763719913_69203ae9e22dc.pdf\",\"1763719913_69203ae9e24ca.png\"]', NULL),
(10, '20251121103141752', '[\"1763721101_69203f8db86f9.pdf\"]', NULL),
(11, '20251121131725238', '[\"1763731045_692066653a8a0.png\"]', NULL),
(12, '20251121134052201', '[\"1763732452_69206be4317a5.jpg\",\"1763732452_69206be431df1.pdf\"]', NULL),
(13, '20251121135639274', '[\"1763733399_69206f97454b7.png\",\"1763733399_69206f974573f.png\"]', NULL),
(14, '20251122124444832', '[\"1763815484_6921b03ccbacc.pdf\",\"1763815484_6921b03ccbf18.jpg\"]', NULL),
(15, '20251203162603263', '[\"1764779163_6930649b43c8d.png\",\"1764779163_6930649b4414a.png\"]', NULL),
(16, '20251203170106386', '[\"1764781266_69306cd25ea9b.png\",\"1764781266_69306cd25ed18.pdf\"]', NULL),
(17, '20251203170704141', '[\"1764781624_69306e38271d3.pdf\"]', NULL),
(18, '20251204002525338', '[\"1764807925_6930d4f553005.pdf\",\"1764807925_6930d4f5531d5.pdf\"]', NULL),
(19, '20251204012052556', '[\"1764811252_6930e1f48ad56.pdf\"]', NULL),
(20, '20251204013339301', '[\"1764812019_6930e4f34a145.pdf\"]', NULL),
(21, '20251204014736619', '[\"1764812856_6930e8389a88a.pdf\"]', NULL),
(22, '20251204024139706', '[\"1764816099_6930f4e3af3ea.pdf\"]', NULL),
(23, '20251204030605781', '[\"1764817565_6930fa9dc16a7.pdf\"]', NULL),
(24, '20251204031307888', '[\"1764817987_6930fc43da33e.pdf\",\"1764817987_6930fc43da5b7.pdf\"]', NULL),
(25, '20251204033311414', '[\"1764819191_693100f76887f.png\",\"1764819191_693100f768ae5.png\"]', NULL),
(26, '20251204054601248', '[\"1764827161_693120193d72e.png\",\"1764827161_693120193d941.png\",\"1764827161_693120193db44.png\"]', NULL),
(27, '20251204055643624', '[\"1764827803_6931229b98af6.pdf\"]', NULL),
(28, '20251204061649665', '[\"1764829009_69312751a3c20.pdf\"]', NULL),
(29, '20251207161201798', '[\"1765123921_6935a751c3c6f.pdf\"]', NULL),
(30, '20251207161919239', '[\"1765124359_6935a9073bdb5.pdf\"]', NULL),
(31, '20251210171214576', '[]', NULL),
(32, '20251210173707266', '[\"1765388227_6939afc343802.jpg\"]', NULL),
(33, '20251210180040545', '[\"1765389640_6939b54885c91.jpg\"]', NULL),
(34, '20251210184716506', '[\"1765392436_6939c0347c0e3.pdf\"]', NULL),
(35, '20251211231624619', '[\"1765494984_693b50c8980d5.pdf\",\"1765494984_693b50c89850b.jpg\"]', NULL),
(36, '20251214054304530', '[\"1765690984_693e4e6882ae6.pdf\"]', NULL),
(37, '20251214100648858', '[\"1765706808_693e8c38d22a5.pdf\"]', NULL),
(38, '20251214105201010', '[\"Al-khazri_Sali_Alim_20251214105201010\\/1765709521_693e96d103208_JAI.pdf\",\"Al-khazri_Sali_Alim_20251214105201010\\/1765709521_693e96d1033b2_neo.pdf\"]', 'Al-khazri_Sali_Alim_20251214105201010'),
(39, '20251214110322580', '[\"Arjec_Jose_Dizon_20251214110322580\\/1765710202_693e997a8ff6d_zppsu_logo1.png\",\"Arjec_Jose_Dizon_20251214110322580\\/1765710202_693e997a90107_1000046143.png\",\"Arjec_Jose_Dizon_20251214110322580\\/1765710202_693e997a902f4_reading_owl.jpg\"]', 'Arjec_Jose_Dizon_20251214110322580'),
(40, '20251214110552298', '[\"Nurjan_Idjad_20251214110552298\\/1765710352_693e9a1049412_activity-1-randg.pdf\",\"Nurjan_Idjad_20251214110552298\\/1765710352_693e9a10495d3_552977020_830361506389888_1907920262288961300_n.jpg\",\"Nurjan_Idjad_20251214110552298\\/1765710352_693e9a104980e_1000046143.png\"]', 'Nurjan_Idjad_20251214110552298'),
(41, '20251215103920689', '[\"Dizon_Arjec_Jose_A_20251215103920689\\/1765795160_693fe558aa09c_activity-1-randg.pdf\",\"Dizon_Arjec_Jose_A_20251215103920689\\/1765795160_693fe558aa2c2_552977020_830361506389888_1907920262288961300_n.jpg\"]', 'Dizon_Arjec_Jose_A_20251215103920689'),
(42, '20251215104428645', '[\"JESSICA_A_DIZON_20251215104428645\\/1765795468_693fe68c9e658_activity-1-randg.pdf\",\"JESSICA_A_DIZON_20251215104428645\\/1765795468_693fe68c9e817_neo.pdf\",\"JESSICA_A_DIZON_20251215104428645\\/1765795468_693fe68c9ea65_Building_a_cleaner_tomorrow_with_smart__sustainable_living_solutions..pdf\"]', 'JESSICA_A_DIZON_20251215104428645'),
(43, '20251215120415457', '[\"JESSICA_A_DIZON_20251215120415457\\/1765800255_693ff93f7027f_nurjan.pdf\",\"JESSICA_A_DIZON_20251215120415457\\/1765800255_693ff93f7044c_1000046143.png\"]', 'JESSICA_A_DIZON_20251215120415457'),
(44, '20251219030132150', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251219030132150\\/1766113292_CERTIFICATE_OF_INDIGENCY_6944c00c2693a.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251219030132150\\/1766113292_2x2_Picture_6944c00c26efc.jpg\"}]', 'Dizon_Arjec_Jose_A_20251219030132150'),
(45, '20251219031334371', '[{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251219031334371\\/1766114014_2x2_Picture_6944c2de5b0b0.jpg\"}]', 'Dizon_Arjec_Jose_A_20251219031334371'),
(46, '20251219032737619', '[{\"requirement_name\":\"General Documents\",\"file_path\":\"Dizon_Arjec_Jose_A_20251219032737619\\/1766114857_General_Documents_6944c6299835b.pdf\"}]', 'Dizon_Arjec_Jose_A_20251219032737619'),
(47, '20251219034601220', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251219034601220\\/1766115961_CERTIFICATE_OF_INDIGENCY_6944ca7936428.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251219034601220\\/1766115961_2x2_Picture_6944ca7936693.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251219034601220\\/1766115961_Form_138_6944ca793691b.pdf\"}]', 'Dizon_Arjec_Jose_A_20251219034601220'),
(48, '20251220042718371', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220042718371\\/1766204838_CERTIFICATE_OF_INDIGENCY_694625a65dcbb.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220042718371\\/1766204838_2x2_Picture_694625a65df4b.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220042718371\\/1766204838_Form_138_694625a65e2b0.jpg\"}]', 'Dizon_Arjec_Jose_A_20251220042718371'),
(49, '20251220051201997', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220051201997\\/1766207521_CERTIFICATE_OF_INDIGENCY_69463021f4163.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220051201997\\/1766207522_2x2_Picture_6946302200125.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220051201997\\/1766207522_Form_138_694630220032d.pdf\"}]', 'Dizon_Arjec_Jose_A_20251220051201997'),
(50, '20251220062713822', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220062713822\\/1766212033_CERTIFICATE_OF_INDIGENCY_694641c1cf2dc.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220062713822\\/1766212033_2x2_Picture_694641c1cf4b1.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220062713822\\/1766212033_Form_138_694641c1cf671.jpg\"}]', 'Dizon_Arjec_Jose_A_20251220062713822'),
(51, '20251220114402043', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220114402043\\/1766231042_CERTIFICATE_OF_INDIGENCY_69468c020b25d.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220114402043\\/1766231042_2x2_Picture_69468c020b534.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220114402043\\/1766231042_Form_138_69468c020b7c1.jpg\"}]', 'Dizon_Arjec_Jose_A_20251220114402043'),
(52, '20251220131852952', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220131852952\\/1766236732_CERTIFICATE_OF_INDIGENCY_6946a23ce8f42.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220131852952\\/1766236732_2x2_Picture_6946a23ce9104.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251220131852952\\/1766236732_Form_138_6946a23ce934a.jpg\"}]', 'Dizon_Arjec_Jose_A_20251220131852952'),
(53, '20251221033249693', '[{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221033249693\\/1766287969_2x2_Picture_69476a61aa68e.png\"}]', 'Dizon_Arjec_Jose_A_20251221033249693'),
(54, '20251221044358839', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221044358839\\/1766292238_CERTIFICATE_OF_INDIGENCY_69477b0ecdb1f.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221044358839\\/1766292238_2x2_Picture_69477b0ecdd75.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221044358839\\/1766292238_Form_138_69477b0ece179.jpg\"}]', 'Dizon_Arjec_Jose_A_20251221044358839'),
(55, '20251221044748226', '[{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221044748226\\/1766292468_2x2_Picture_69477bf43844f.png\"}]', 'Dizon_Arjec_Jose_A_20251221044748226'),
(56, '20251221050609454', '[]', 'Dizon_Arjec_Jose_A_20251221050609454'),
(57, '20251221050935076', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221050935076\\/1766293775_CERTIFICATE_OF_INDIGENCY_6947810f1334f.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221050935076\\/1766293775_2x2_Picture_6947810f134c5.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221050935076\\/1766293775_Form_138_6947810f136a9.jpg\"}]', 'Dizon_Arjec_Jose_A_20251221050935076'),
(58, '20251221051043567', '[{\"requirement_name\":\"General Documents\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221051043567\\/1766293843_General_Documents_694781538afd2.pdf\"},{\"requirement_name\":\"Additional Document\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221051043567\\/1766293843_694781538b172_neo.pdf\"}]', 'Dizon_Arjec_Jose_A_20251221051043567'),
(59, '20251221052320199', '[{\"requirement_name\":\"General Documents\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221052320199\\/1766294600_General_Documents_69478448315ea.pdf\"}]', 'Dizon_Arjec_Jose_A_20251221052320199'),
(60, '20251221102720067', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Marco_Jean_Pagotasidro_20251221102720067\\/1766312840_CERTIFICATE_OF_INDIGENCY_6947cb88119b0.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Marco_Jean_Pagotasidro_20251221102720067\\/1766312840_2x2_Picture_6947cb8811baf.png\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Marco_Jean_Pagotasidro_20251221102720067\\/1766312840_Form_138_6947cb8811eaa.jpg\"}]', 'Marco_Jean_Pagotasidro_20251221102720067'),
(61, '20251221132730203', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221132730203\\/1766323650_CERTIFICATE_OF_INDIGENCY_6947f5c2349d9.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221132730203\\/1766323650_2x2_Picture_6947f5c234bca.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221132730203\\/1766323650_Form_138_6947f5c234e10.jpg\"}]', 'Dizon_Arjec_Jose_A_20251221132730203'),
(62, '20251221133737430', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221133737430\\/1766324257_CERTIFICATE_OF_INDIGENCY_6947f82169860.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221133737430\\/1766324257_2x2_Picture_6947f82169a7d.jpg\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221133737430\\/1766324257_Form_138_6947f82169c93.jpg\"}]', 'Dizon_Arjec_Jose_A_20251221133737430'),
(63, '20251221143859144', '[{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251221143859144\\/1766327939_2x2_Picture_6948068324b3e.png\"}]', 'Dizon_Arjec_Jose_A_20251221143859144'),
(64, '20251222004552424', '[{\"requirement_name\":\"CERTIFICATE OF INDIGENCY\",\"file_path\":\"Dizon_Arjec_Jose_A_20251222004552424\\/1766364352_CERTIFICATE_OF_INDIGENCY_694894c06812f.pdf\"},{\"requirement_name\":\"2x2 Picture\",\"file_path\":\"Dizon_Arjec_Jose_A_20251222004552424\\/1766364352_2x2_Picture_694894c068295.pdf\"},{\"requirement_name\":\"Form 138\",\"file_path\":\"Dizon_Arjec_Jose_A_20251222004552424\\/1766364352_Form_138_694894c068485.jpg\"}]', 'Dizon_Arjec_Jose_A_20251222004552424');

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
(96, '20251211231624619', 'ZCS', '2016', 'none', 'ZNHS-WEST', '2022', 'none', 'ZPPSU', '0000', ''),
(97, '20251214054304530', 'ZCS', '2020', 'None', 'ZNHS-WEST', '2020', 'none', 'ZPPSU', '0000', ''),
(98, '20251214100648858', 'ZCS', '2020', 'None', 'New York University', '2013', 'none', 'ZPPSU', '0000', ''),
(99, '20251214105201010', 'ZCS', '2020', '1', 'MCLLNHS', '2020', 'none', 'ZPPSU', '0000', ''),
(100, '20251214110322580', 'ZCS', '2016', '1', 'ZNHS-WEST', '2020', '1', 'ZPPSU', '0000', ''),
(101, '20251214110552298', 'ZCS', '2016', 'none', 'ZNHS-WEST', '2022', 'none', 'ZPPSU', '0000', ''),
(103, '20251215104428645', 'ZCS', '2016', '1', 'ZNHS-WEST', '2020', 'none', 'ZPPSU', '0000', ''),
(104, '20251215120415457', 'ZCS', '2016', 'none', 'ZNHS-WEST', '2020', 'none', 'ZPPSU', '0000', ''),
(125, '20251222004552424', 'ZCS', '2016', '', 'AMA CC ZAMBOANGA', '2022', '', 'ZPPSU', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, '0', 'admin', '', '$2y$10$CutO7I4nRGj/7sszz1IGQe1zsSrG8PCepxUpeajBSYsizlQ1adoBG', '2024-09-27 20:44:57', 'admin'),
(2, '0', 'useraccount01', '', '$2y$10$79QCeYHFnoNTOXgqT.jxe.A7SfubRz.Yp0RFy6wjmnuM9Va.Gyo2q', '2024-10-01 09:04:55', 'student'),
(3, '0', 'useraccount02', '', '$2y$10$kK/DWNxdS4L.lv8HEplzNejkxW5v1F64XXMvPHl88y1qut64NRp4e', '2024-10-15 20:00:04', 'student'),
(11, '0', 'finn22', '', '$2y$10$DQkEIuauR1rVUwpCuZFWoOsb5LzKbBIuzQ3doL/daxXG7zLUtw1qK', '2024-12-09 22:16:28', 'student'),
(12, '0', 'finn23', '', '$2y$10$REKMjRAurfLnjpnKhNb1V.oc9Hdmx2zzkrmgVjrKg/J9nu1Xrt3Pq', '2024-12-09 22:37:17', 'student'),
(13, '0', 'finn24', '', '$2y$10$Dgd91Kwd5gwu5ngNv6QVQO8l13GHCwFPYU3Q5LpeXFJNxMAw7UrkG', '2024-12-09 22:40:06', 'student'),
(14, '0', 'jan', '', '$2y$10$VjpJ4lYB9/rN48dMbIBsL.///qlTVt5PmX1nn0YI.8V5yuTiSd88e', '2025-11-21 14:57:50', 'student'),
(15, '0', 'mark', '', '$2y$10$KItjpuaYCiWHXvsLzXsZweDehuZNdySnJCY0ht2rLYvu.8eiRMO.i', '2025-11-21 15:10:38', 'student'),
(24, 'Dizon Arjec Jose A.', 'aj111', 'dizon.arjecjose@gmail.com', '$2y$10$MDDWlPjrDGxTJuuTp4HCUOUhD8dZXGkJNRnNaCDLP3HDFepstsfh6', '2025-12-21 20:34:33', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `user_house_info`
--

CREATE TABLE `user_house_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `house_status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_house_info`
--

INSERT INTO `user_house_info` (`id`, `user_id`, `house_status`, `created_at`, `updated_at`) VALUES
(8, 24, 'rented', '2025-12-21 12:34:33', '2025-12-21 12:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_parents_info`
--

CREATE TABLE `user_parents_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `father_lastname` varchar(50) NOT NULL,
  `father_givenname` varchar(50) NOT NULL,
  `father_middlename` varchar(50) DEFAULT NULL,
  `father_cellphone` varchar(20) NOT NULL,
  `father_education` varchar(50) NOT NULL,
  `father_occupation` varchar(50) NOT NULL,
  `father_income` varchar(50) DEFAULT NULL,
  `mother_lastname` varchar(50) NOT NULL,
  `mother_givenname` varchar(50) NOT NULL,
  `mother_middlename` varchar(50) DEFAULT NULL,
  `mother_cellphone` varchar(20) NOT NULL,
  `mother_education` varchar(50) NOT NULL,
  `mother_occupation` varchar(50) NOT NULL,
  `mother_income` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_parents_info`
--

INSERT INTO `user_parents_info` (`id`, `user_id`, `father_lastname`, `father_givenname`, `father_middlename`, `father_cellphone`, `father_education`, `father_occupation`, `father_income`, `mother_lastname`, `mother_givenname`, `mother_middlename`, `mother_cellphone`, `mother_education`, `mother_occupation`, `mother_income`, `created_at`, `updated_at`) VALUES
(8, 24, 'Dizon', 'Arman', 'G.', '09356455565', 'High School Graduate', 'Private Sector', '₱20000-25000', 'Argonsola', 'Jessica', 'A.', '09552335871', 'College Graduate', 'Government', '₱30000-35000', '2025-12-21 12:34:33', '2025-12-21 12:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `course` varchar(100) NOT NULL,
  `major` varchar(100) DEFAULT NULL,
  `yr_sec` varchar(50) NOT NULL,
  `cell_no` varchar(20) NOT NULL,
  `present_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sex` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `age` int(11) NOT NULL,
  `place_of_birth` varchar(100) NOT NULL,
  `civil_status` varchar(20) NOT NULL,
  `religion` varchar(50) NOT NULL,
  `disability` varchar(100) NOT NULL,
  `indigenous_group` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `user_id`, `full_name`, `course`, `major`, `yr_sec`, `cell_no`, `present_address`, `permanent_address`, `zip_code`, `email`, `sex`, `date_of_birth`, `age`, `place_of_birth`, `civil_status`, `religion`, `disability`, `indigenous_group`, `created_at`, `updated_at`) VALUES
(8, 24, 'Dizon Arjec Jose A.', 'BS INFOTECH', 'PROGRAMMING', '4A', '09158423449', 'daisy road', 'daisy road', '7000', 'dizon.arjecjose@gmail.com', 'male', '2004-04-16', 21, 'Zamboanga City', 'single', 'roman catholic', 'N/A', 'N/A', '2025-12-21 12:34:33', '2025-12-21 12:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_schools_attended`
--

CREATE TABLE `user_schools_attended` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `elementary` varchar(100) NOT NULL,
  `elementary_year_grad` year(4) NOT NULL,
  `elementary_honors` varchar(200) DEFAULT NULL,
  `secondary` varchar(100) NOT NULL,
  `secondary_year_grad` year(4) NOT NULL,
  `secondary_honors` varchar(200) DEFAULT NULL,
  `college` varchar(100) DEFAULT NULL,
  `college_year_grad` year(4) DEFAULT NULL,
  `college_honors` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_schools_attended`
--

INSERT INTO `user_schools_attended` (`id`, `user_id`, `elementary`, `elementary_year_grad`, `elementary_honors`, `secondary`, `secondary_year_grad`, `secondary_honors`, `college`, `college_year_grad`, `college_honors`, `created_at`, `updated_at`) VALUES
(8, 24, 'ZCS', '2016', '', 'AMA CC ZAMBOANGA', '2022', '', 'ZPPSU', NULL, '', '2025-12-21 12:34:33', '2025-12-21 12:34:33');

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
-- Indexes for table `user_house_info`
--
ALTER TABLE `user_house_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_house` (`user_id`);

--
-- Indexes for table `user_parents_info`
--
ALTER TABLE `user_parents_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_parents` (`user_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_profile` (`user_id`);

--
-- Indexes for table `user_schools_attended`
--
ALTER TABLE `user_schools_attended`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_schools` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `dropdown_course_major`
--
ALTER TABLE `dropdown_course_major`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `dropdown_scholarship_grant`
--
ALTER TABLE `dropdown_scholarship_grant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dropdown_sem_sy`
--
ALTER TABLE `dropdown_sem_sy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `grant_requirements`
--
ALTER TABLE `grant_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `house_info`
--
ALTER TABLE `house_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=546;

--
-- AUTO_INCREMENT for table `parents_info`
--
ALTER TABLE `parents_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `scholarship_files`
--
ALTER TABLE `scholarship_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `schools_attended`
--
ALTER TABLE `schools_attended`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_house_info`
--
ALTER TABLE `user_house_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_parents_info`
--
ALTER TABLE `user_parents_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_schools_attended`
--
ALTER TABLE `user_schools_attended`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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

--
-- Constraints for table `user_house_info`
--
ALTER TABLE `user_house_info`
  ADD CONSTRAINT `user_house_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_parents_info`
--
ALTER TABLE `user_parents_info`
  ADD CONSTRAINT `user_parents_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_schools_attended`
--
ALTER TABLE `user_schools_attended`
  ADD CONSTRAINT `user_schools_attended_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
