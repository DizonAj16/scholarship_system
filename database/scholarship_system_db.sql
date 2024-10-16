-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2024 at 03:26 PM
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
-- Database: `scholarship_system_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_applicants_data`
--

CREATE TABLE `scholarship_applicants_data` (
  `sf_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `semester` enum('1st','2nd','Summer') NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `course` varchar(100) NOT NULL,
  `yr_sec` varchar(50) NOT NULL,
  `major` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `permanent_address` text NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `present_address` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `date_of_birth` date NOT NULL,
  `place_of_birth` varchar(100) NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `civil_status` enum('Single','Married','Divorced','Widowed') NOT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `scholarship_grant` varchar(100) DEFAULT NULL,
  `type_of_disability` varchar(100) DEFAULT NULL,
  `indigenous_group` varchar(100) DEFAULT NULL,
  `elementary_school` varchar(255) DEFAULT NULL,
  `year_graduate_elementary` int(11) DEFAULT NULL,
  `honors_received_elementary` text DEFAULT NULL,
  `secondary_school` varchar(255) DEFAULT NULL,
  `year_graduate_secondary` int(11) DEFAULT NULL,
  `honors_received_secondary` text DEFAULT NULL,
  `college_school` varchar(255) DEFAULT NULL,
  `year_graduate_college` int(11) DEFAULT NULL,
  `honors_received_college` text DEFAULT NULL,
  `about_yourself` text DEFAULT NULL,
  `need_scholarship` text DEFAULT NULL,
  `father_last_name` varchar(100) DEFAULT NULL,
  `father_given_name` varchar(100) DEFAULT NULL,
  `father_middle_name` varchar(100) DEFAULT NULL,
  `father_phone` varchar(15) DEFAULT NULL,
  `father_education` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `father_income` decimal(10,2) DEFAULT NULL,
  `mother_maiden_last_name` varchar(100) DEFAULT NULL,
  `mother_given_name` varchar(100) DEFAULT NULL,
  `mother_middle_name` varchar(100) DEFAULT NULL,
  `mother_phone` varchar(15) DEFAULT NULL,
  `mother_education` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `mother_income` decimal(10,2) DEFAULT NULL,
  `housing_status` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, 'useraccount01', '$2y$10$79QCeYHFnoNTOXgqT.jxe.A7SfubRz.Yp0RFy6wjmnuM9Va.Gyo2q', '2024-10-01 09:04:55', 'student'),
(8, 'useraccount02', '$2y$10$kK/DWNxdS4L.lv8HEplzNejkxW5v1F64XXMvPHl88y1qut64NRp4e', '2024-10-15 20:00:04', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `scholarship_applicants_data`
--
ALTER TABLE `scholarship_applicants_data`
  ADD PRIMARY KEY (`sf_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `scholarship_applicants_data`
--
ALTER TABLE `scholarship_applicants_data`
  MODIFY `sf_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
