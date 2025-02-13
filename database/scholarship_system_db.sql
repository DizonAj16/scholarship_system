-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2024 at 02:49 AM
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
(19, '20241119021745276', 'Rented'),
(20, '20241120040336130', 'Owned'),
(21, '20241120130811036', 'Living with relatives'),
(22, '20241120130905956', 'Rented'),
(23, '20241120131404228', 'Rented'),
(24, '20241120131655964', 'Rented'),
(25, '20241120131737424', 'Owned'),
(26, '20241120131927870', 'Owned'),
(27, '20241120132416593', 'Living with relatives'),
(28, '20241120132515053', 'Living with relatives'),
(29, '20241120142018354', 'Rented'),
(30, '20241120142800480', 'Living with relatives'),
(31, '20241120154748446', 'Owned'),
(32, '20241120174919246', 'Living with relatives'),
(33, '20241120175158802', 'Living with relatives'),
(34, '20241120175248546', 'Owned'),
(36, '20241120180046949', 'Living with relatives'),
(37, '20241120180154920', 'Living with relatives'),
(40, '20241120192554180', 'Living with relatives'),
(41, '20241121190024540', 'Owned');

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
(19, '20241119021745276', 'Dizon', 'Arman ', 'Godoy', '09556455565', 'High School Graduate', 'Private Sector', 30000.00, 'Argonsola', 'Jessica', 'Atilano', '09552335871', 'College Graduate', 'Government', 20000.00),
(20, '20241120040336130', 'lastname', 'givenname', 'middlename', '09154542324', 'Postgraduate', 'Government', 99999999.99, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Overseas Employment', 99999999.99),
(21, '20241120130811036', 'lastname', 'givenname', 'middlename', '09154542324', 'College Graduate', 'Overseas Employment', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Contractual', 50000.00),
(22, '20241120130905956', 'lastname', 'givenname', 'middlename', '09154542324', 'College Graduate', 'Overseas Employment', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Contractual', 50000.00),
(23, '20241120131404228', 'lastname', 'givenname', 'middlename', '09154542324', 'College Graduate', 'Overseas Employment', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Contractual', 50000.00),
(24, '20241120131655964', 'lastname', 'givenname', 'middlename', '09154542324', 'College Graduate', 'Overseas Employment', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Contractual', 50000.00),
(25, '20241120131737424', 'lastname', 'givenname', 'middlename', '09154542324', 'College Graduate', 'Overseas Employment', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Contractual', 50000.00),
(26, '20241120131927870', 'lastname', 'givenname', 'middlename', '09154542324', 'College Graduate', 'Overseas Employment', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Contractual', 50000.00),
(27, '20241120132416593', 'lastname', 'givenname', 'middlename', '09154542324', 'College Graduate', 'Overseas Employment', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Contractual', 50000.00),
(28, '20241120132515053', 'lastname', 'givenname', 'middlename', '09154542324', 'College Graduate', 'Overseas Employment', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Graduate', 'Contractual', 50000.00),
(29, '20241120142018354', 'lastname', 'givenname', 'middlename', '09154542324', 'High School Undergraduate', 'Casual', 40000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Overseas Employment', 50000.00),
(30, '20241120142800480', 'lastname', 'givenname', 'middlename', '09154542324', 'High School Undergraduate', 'Casual', 40000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Overseas Employment', 50000.00),
(31, '20241120154748446', 'lastname', 'givenname', 'middlename', '09154542324', 'Postgraduate', 'Intern', 700000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Intern', 700000.00),
(32, '20241120174919246', 'lastname', 'givenname', 'middlename', '09154542324', 'No Formal Education', 'Government', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Contractual', 80000.00),
(33, '20241120175158802', 'lastname', 'givenname', 'middlename', '09154542324', 'No Formal Education', 'Government', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Contractual', 80000.00),
(34, '20241120175248546', 'lastname', 'givenname', 'middlename', '09154542324', 'No Formal Education', 'Government', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Contractual', 80000.00),
(36, '20241120180046949', 'lastname', 'givenname', 'middlename', '09154542324', 'No Formal Education', 'Government', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Contractual', 80000.00),
(37, '20241120180154920', 'lastname', 'givenname', 'middlename', '09154542324', 'No Formal Education', 'Government', 50000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Contractual', 80000.00),
(40, '20241120192554180', 'lastname', 'givenname', 'middlename', '09154542324', 'Postgraduate', 'Intern', 80000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'Postgraduate', 'Intern', 80000.00),
(41, '20241121190024540', 'lastname', 'givenname', 'middlename', '09154542324', 'Elementary Graduate', 'Self-Employed', 70000.00, 'lastname', 'givenname', 'middlename', '094515426544', 'College Undergraduate', 'Freelancer', 50000.00);

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_applications`
--

CREATE TABLE `scholarship_applications` (
  `application_id` varchar(20) NOT NULL,
  `date` datetime DEFAULT NULL,
  `semester` enum('1st sem','2nd sem','Summer') NOT NULL,
  `school_year` varchar(9) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `course` enum('BSIT','BSA','BSED','BSBA','BSHRM') NOT NULL,
  `yr_sec` varchar(15) NOT NULL,
  `major` varchar(100) NOT NULL,
  `cell_no` varchar(15) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `zip_code` int(11) NOT NULL,
  `present_address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sex` enum('male','female','others') NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age` int(11) NOT NULL,
  `place_of_birth` varchar(50) DEFAULT NULL,
  `civil_status` enum('single','married','widowed','divorced','separated') DEFAULT NULL,
  `religion` enum('roman catholic','islam','iglesia ni cristo','evangelical christian','a biblical church','others') DEFAULT NULL,
  `scholarship_grant` enum('academic scholarship','athletic scholarship','government scholarship','private scholarship','merit based','others') DEFAULT NULL,
  `disability` varchar(100) DEFAULT NULL,
  `indigenous_group` enum('igorot','lumad','moro','aeta','badjao','others','N/A') DEFAULT NULL,
  `reason_scholarship` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_applications`
--

INSERT INTO `scholarship_applications` (`application_id`, `date`, `semester`, `school_year`, `full_name`, `course`, `yr_sec`, `major`, `cell_no`, `permanent_address`, `zip_code`, `present_address`, `email`, `sex`, `date_of_birth`, `age`, `place_of_birth`, `civil_status`, `religion`, `scholarship_grant`, `disability`, `indigenous_group`, `reason_scholarship`, `user_id`, `status`) VALUES
('20241119021745276', '2024-11-19 09:14:00', '2nd sem', '2023-2024', 'Johnny Depp', 'BSED', '3A', 'INFORMATION TECHNOLOGY', '09158423449', 'Guiwan, Zamboanga City', 7000, 'Guiwan, Zamboanga City', 'dizon.arjecjose@gmail.com', 'male', '2004-04-16', 20, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq', 1, 'rejected'),
('20241120040336130', '2024-11-20 10:58:00', 'Summer', '2023-2024', 'John Doe', 'BSA', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'asd@email.com', 'male', '2001-04-16', 23, 'Zamboanga City', 'single', 'others', 'private scholarship', 'N/A', 'N/A', 'qwertyuiopasdfghjklzxcvbnm', 2, 'pending'),
('20241120130811036', '2024-11-20 20:06:00', '1st sem', '2023-2024', 'Arjec Jose Dizon', 'BSIT', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon923139@gmail.com', 'male', '2004-04-16', 20, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'dadasdqawdqwedqwaedqwaedqawedqwadeaqedqawdqawedqwedqawedqawedqwe', 1, 'pending'),
('20241120130905956', '2024-11-20 20:06:00', '1st sem', '2023-2024', 'Michael Johnson', 'BSHRM', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon923139@gmail.com', 'male', '2004-04-24', 20, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'dadasdqawdqwedqwaedqwaedqawedqwadeaqedqawdqawedqwedqawedqawedqwe', 1, 'pending'),
('20241120131404228', '2024-11-20 20:06:00', '1st sem', '2023-2024', 'Emily Davis', 'BSA', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon923139@gmail.com', 'male', '2004-05-01', 20, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'dadasdqawdqwedqwaedqwaedqawedqwadeaqedqawdqawedqwedqawedqawedqwe', 1, 'pending'),
('20241120131655964', '2024-11-20 20:06:00', '1st sem', '2023-2024', 'David Brown', 'BSED', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon923139@gmail.com', 'male', '1997-06-18', 27, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'dadasdqawdqwedqwaedqwaedqawedqwadeaqedqawdqawedqwedqawedqawedqwe', 1, 'pending'),
('20241120131737424', '2024-11-20 20:06:00', '1st sem', '2023-2024', 'Sarah Wilson', 'BSIT', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon923139@gmail.com', 'male', '1960-07-29', 64, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'dadasdqawdqwedqwaedqwaedqawedqwadeaqedqawdqawedqwedqawedqawedqwe', 1, 'pending'),
('20241120131927870', '2024-11-20 20:06:00', '1st sem', '2023-2024', 'James Taylor', 'BSIT', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon923139@gmail.com', 'male', '1960-08-06', 64, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'dadasdqawdqwedqwaedqwaedqawedqwadeaqedqawdqawedqwedqawedqawedqwe', 1, 'pending'),
('20241120132416593', '2024-11-20 20:06:00', '1st sem', '2023-2024', 'Jessica Martinez', 'BSIT', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon923139@gmail.com', 'male', '2001-04-16', 23, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'dadasdqawdqwedqwaedqwaedqawedqwadeaqedqawdqawedqwedqawedqawedqwe', 1, 'pending'),
('20241120132515053', '2024-11-20 01:12:00', '1st sem', '2023-2024', 'William Anderson', 'BSIT', '3A', 'ENGLISH', '09466456566', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon923139@gmail.com', 'male', '2001-05-05', 23, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'dadasdqawdqwedqwaedqwaedqawedqwadeaqedqawdqawedqwedqawedqawedqwe', 1, 'pending'),
('20241120142018354', '2024-11-20 21:16:00', '1st sem', '2023-2024', 'Amanda Moore', 'BSA', '2A', 'ACCOUNTANCY', '09486409573', 'Zamboanga City', 7000, 'Zamboanga City', 'eyyy@email.com', 'female', '2007-05-24', 17, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'adadadadadawdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', 1, 'pending'),
('20241120142800480', '2024-11-20 21:16:00', '1st sem', '2023-2024', 'Matthew White', 'BSA', '2A', 'ACCOUNTANCY', '09486409573', 'Zamboanga City', 7000, 'Zamboanga City', 'eyyy@email.com', 'female', '2007-05-25', 17, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'adadadadadawdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', 1, 'pending'),
('20241120154748446', '2024-11-20 22:44:00', 'Summer', '2023-2024', 'Nicole Young', 'BSBA', '1D', 'BUSINESS AD', '09548712364', 'Zamboanga City', 7000, 'Zamboanga City', 'nicol@email.com', 'female', '1998-12-04', 25, 'Zamboanga City', 'single', 'others', 'others', 'N/A', 'N/A', 'qw1qedwqerdawqrfwqa3erfewwwwwaq2qwqeqedqwaeq2e4q2weq2e32q13e2q13eq12e1q2', 1, 'pending'),
('20241120174919246', '2024-11-21 00:46:00', 'Summer', '2023-2024', 'Juan Carlos Reyes', 'BSHRM', '4F', 'HRM', '09154785417', 'Zamboanga City', 7000, 'Zamboanga City', 'elelelea@email.com', 'male', '2004-01-10', 20, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'rowhegifygwoseiiduopiegdsolizdfhopaswgdefcopsaiefgh\'pwseirfgdrpoihsfpodrigpsofhg[oWET', 1, 'pending'),
('20241120175158802', '2024-11-21 00:46:00', 'Summer', '2023-2024', 'Andres Javier Cruz', 'BSBA', '4G', 'HRM', '09154785417', 'Zamboanga City', 7000, 'Zamboanga City', 'elelelea@email.com', 'male', '2004-01-31', 20, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'rowhegifygwoseiiduopiegdsolizdfhopaswgdefcopsaiefgh\'pwseirfgdrpoihsfpodrigpsofhg[oWET', 1, 'pending'),
('20241120175248546', '2024-11-21 00:46:00', 'Summer', '2023-2024', 'Andres Javier Cruz', 'BSBA', '4G', 'HRM', '09154785417', 'Zamboanga City', 7000, 'Zamboanga City', 'elelelea@email.com', 'male', '2004-01-29', 20, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'rowhegifygwoseiiduopiegdsolizdfhopaswgdefcopsaiefgh\'pwseirfgdrpoihsfpodrigpsofhg[oWET', 1, 'pending'),
('20241120180046949', '2024-11-21 00:46:00', 'Summer', '2023-2024', 'Eduardo Rafael Lopez', 'BSHRM', '4G', 'HRM', '09154785417', 'Zamboanga City', 7000, 'Zamboanga City', 'elelelea@email.com', 'male', '1993-02-27', 31, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'rowhegifygwoseiiduopiegdsolizdfhopaswgdefcopsaiefgh\'pwseirfgdrpoihsfpodrigpsofhg[oWET', 1, 'pending'),
('20241120180154920', '2024-11-21 01:01:00', 'Summer', '2023-2024', 'Renato Rivera', 'BSHRM', '4G', 'HRM', '09154785417', 'Zamboanga City', 7000, 'Zamboanga City', 'elelelea@email.com', 'male', '1993-02-26', 31, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'rowhegifygwoseiiduopiegdsolizdfhopaswgdefcopsaiefgh\'pwseirfgdrpoihsfpodrigpsofhg[oWET', 1, 'pending'),
('20241120192554180', '2024-11-21 02:24:00', '1st sem', '2023-2024', 'Arjec Jose Dizon', 'BSIT', '3A', 'INFORMATION TECHNOLOGY', '09486409573', 'Zamboanga City', 7000, 'Zamboanga City', 'arjecdizon99@gmail.com', 'male', '2008-04-25', 16, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq', 3, 'approved'),
('20241121190024540', '2024-11-22 01:58:00', '1st sem', '2023-2024', 'Monkey D. Luffy', 'BSHRM', '1D', 'INFORMATION TECHNOLOGY', '09486409573', 'Zamboanga City', 7000, 'Zamboanga City', 'dizon.arjecjose@gmail.com', 'male', '1978-05-20', 46, 'Zamboanga City', 'single', 'roman catholic', 'academic scholarship', 'N/A', 'N/A', 'qwdwedrfawedaredwawedaewrdreqeqwedasfadasdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', 1, 'approved');

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
(21, '20241119021745276', 'Zamboanga Central School', '2016', '1', 'Zamboanga National Highschool West', '2022', '1', 'Zamboanga Peninsula Polytechnic State University', '2026', '1'),
(22, '20241120040336130', 'Harvard University', '2016', '1', 'New York University', '2020', '1', 'University of the Philippines', '2026', '1'),
(23, '20241120130811036', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(24, '20241120130905956', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(25, '20241120131404228', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(26, '20241120131655964', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(27, '20241120131737424', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(28, '20241120131927870', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(29, '20241120132416593', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(30, '20241120132515053', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(31, '20241120142018354', 'Harvard University', '2004', '1', 'New York University', '2004', '1', 'University of the Philippines', '2001', '1'),
(32, '20241120142800480', 'Harvard University', '2004', '1', 'New York University', '2004', '1', 'University of the Philippines', '2001', '1'),
(33, '20241120154748446', 'Harvard University', '2020', '1', 'New York University', '2020', '1', 'University of the Philippines', '2020', '1'),
(34, '20241120174919246', 'ZCS', '2004', '1', 'ZNHS-WEST', '2001', '1', 'ZPPSU', '2026', '1'),
(35, '20241120175158802', 'ZCS', '2004', '1', 'ZNHS-WEST', '2001', '1', 'ZPPSU', '2026', '1'),
(36, '20241120175248546', 'ZCS', '2004', '1', 'ZNHS-WEST', '2001', '1', 'ZPPSU', '2026', '1'),
(38, '20241120180046949', 'ZCS', '2004', '1', 'ZNHS-WEST', '2001', '1', 'ZPPSU', '2026', '1'),
(39, '20241120180154920', 'ZCS', '2004', '1', 'ZNHS-WEST', '2001', '1', 'ZPPSU', '2026', '1'),
(42, '20241120192554180', 'Harvard University', '2001', '1', 'New York University', '2007', '1', 'University of the Philippines', '2025', '1'),
(43, '20241121190024540', 'Harvard University', '2020', '1', 'ZNHS-WEST', '2020', '1', 'University of the Philippines', '2020', '1');

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
(3, 'useraccount02', '$2y$10$kK/DWNxdS4L.lv8HEplzNejkxW5v1F64XXMvPHl88y1qut64NRp4e', '2024-10-15 20:00:04', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `house_info`
--
ALTER TABLE `house_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `house_info_ibfk_1` (`application_id`);

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
-- AUTO_INCREMENT for table `house_info`
--
ALTER TABLE `house_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `parents_info`
--
ALTER TABLE `parents_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `schools_attended`
--
ALTER TABLE `schools_attended`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `house_info`
--
ALTER TABLE `house_info`
  ADD CONSTRAINT `house_info_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `scholarship_applications` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
