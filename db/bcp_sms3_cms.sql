-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2024 at 11:43 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bcp_sms3_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `bcp_sms3_patients`
--

CREATE TABLE `bcp_sms3_patients` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `student_number` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `s_gender` enum('Male','Female','Other','Prefer_not_to_say') NOT NULL,
  `age` int(11) NOT NULL,
  `year_level` enum('shs','1st_year','2nd_year','3rd_year','4th_year') NOT NULL,
  `conditions` text NOT NULL,
  `treatment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bcp_sms3_patients`
--

INSERT INTO `bcp_sms3_patients` (`id`, `fullname`, `student_number`, `contact`, `s_gender`, `age`, `year_level`, `conditions`, `treatment`, `created_at`) VALUES
(10, 'Patrick Nobleza', '21011518', 'patsandesu@gmail.com', '', 22, '4th_year', 'Heart Attack', 'CPR', '2024-10-09 07:43:18'),
(31, 'Doc Jane Ann Hernandez', '21015518', 'patsandesu@gmail.com', '', 45, '4th_year', 'Kunware may sakit', 'Kiss', '2024-10-09 08:44:32'),
(32, 'Patrick Nobleza', '21015118', 'patsandesu@gmail.com', '', 23, 'shs', 'Heart Attack', 'CPR', '2024-10-09 08:48:27'),
(33, 'Patrick Nobleza', '45454', 'patsandesu@gmail.com', '', 22, 'shs', 'Heart Attack', 'CPR', '2024-10-09 09:00:14'),
(34, 'Patrick Nobleza', '21015518', 'patsandesu@gmail.com', '', 22, '4th_year', 'Heart Attack', 'CPR', '2024-10-09 09:22:32');

-- --------------------------------------------------------

--
-- Table structure for table `bcp_sms3_supplies`
--

CREATE TABLE `bcp_sms3_supplies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit` enum('pieces','boxes','liters') NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `reorder_level` int(11) NOT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `supplier_contact` varchar(255) DEFAULT NULL,
  `deliver_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `patient_view`
-- (See below for the actual view)
--
CREATE TABLE `patient_view` (
);

-- --------------------------------------------------------

--
-- Structure for view `patient_view`
--
DROP TABLE IF EXISTS `patient_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `patient_view`  AS SELECT `bcp_sms3_patients`.`id` AS `id`, `bcp_sms3_patients`.`fullname` AS `fullname`, `bcp_sms3_patients`.`student_number` AS `student_number`, `bcp_sms3_patients`.`contact` AS `contact`, `bcp_sms3_patients`.`gender` AS `gender`, `bcp_sms3_patients`.`age` AS `age`, `bcp_sms3_patients`.`year_level` AS `year_level`, `bcp_sms3_patients`.`conditions` AS `conditions`, `bcp_sms3_patients`.`treatment` AS `treatment`, `bcp_sms3_patients`.`note` AS `note`, date_format(`bcp_sms3_patients`.`created_at`,'%h:%i %p') AS `formatted_created_at` FROM `bcp_sms3_patients` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bcp_sms3_patients`
--
ALTER TABLE `bcp_sms3_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bcp_sms3_supplies`
--
ALTER TABLE `bcp_sms3_supplies`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bcp_sms3_patients`
--
ALTER TABLE `bcp_sms3_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `bcp_sms3_supplies`
--
ALTER TABLE `bcp_sms3_supplies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
