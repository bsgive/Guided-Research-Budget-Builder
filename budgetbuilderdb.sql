-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2025 at 01:58 AM
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
-- Database: `budgetbuilderdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `pi_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `start_year` int(11) DEFAULT NULL,
  `duration_years` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `pi_id`, `user_id`, `title`, `start_year`, `duration_years`, `created_at`) VALUES
(1, 2, 2, 'John Smith\'s 2026 Budget', 2026, 3, '2025-12-11 21:41:37'),
(2, 4, 3, 'Bart\'s 2025 Budget', 2025, 1, '2025-12-11 21:45:46'),
(3, 5, 4, 'Herb\'s Plan', 2027, 3, '2025-12-11 21:51:13'),
(4, 4, 5, 'Bubba Smith\'s 2028 Budget', 2028, 4, '2025-12-11 21:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `budget_personnel`
--

CREATE TABLE `budget_personnel` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) NOT NULL,
  `pi_id` int(11) NOT NULL,
  `effort_y1` int(11) DEFAULT 0,
  `effort_y2` int(11) DEFAULT 0,
  `effort_y3` int(11) DEFAULT 0,
  `effort_y4` int(11) DEFAULT 0,
  `effort_y5` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_personnel`
--

INSERT INTO `budget_personnel` (`id`, `budget_id`, `pi_id`, `effort_y1`, `effort_y2`, `effort_y3`, `effort_y4`, `effort_y5`) VALUES
(1, 1, 2, 10, 25, 30, 0, 0),
(2, 1, 3, 24, 40, 3, 0, 0),
(3, 2, 4, 10, 0, 0, 0, 0),
(4, 3, 5, 6, 25, 2, 0, 0),
(5, 4, 4, 3, 24, 18, 47, 0),
(6, 4, 6, 6, 4, 10, 24, 0);

-- --------------------------------------------------------

--
-- Table structure for table `budget_students`
--

CREATE TABLE `budget_students` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fte` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_students`
--

INSERT INTO `budget_students` (`id`, `budget_id`, `student_id`, `fte`) VALUES
(1, 1, 6, 15.00),
(2, 1, 4, 32.00),
(3, 2, 4, 15.00),
(4, 3, 3, 45.00),
(5, 4, 2, 15.00),
(6, 4, 6, 19.00);

-- --------------------------------------------------------

--
-- Table structure for table `budget_travel`
--

CREATE TABLE `budget_travel` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) NOT NULL,
  `travel_type_id` int(11) NOT NULL,
  `trips` int(11) NOT NULL,
  `days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_travel`
--

INSERT INTO `budget_travel` (`id`, `budget_id`, `travel_type_id`, `trips`, `days`) VALUES
(1, 1, 2, 3, 4),
(2, 1, 3, 1, 7),
(3, 2, 2, 3, 6),
(4, 3, 3, 2, 14),
(5, 4, 3, 20, 4);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_staff`
--

CREATE TABLE `faculty_staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_staff`
--

INSERT INTO `faculty_staff` (`id`, `name`, `position`, `base_salary`) VALUES
(1, 'Frank Smith', 'Custodian', 70000.00),
(2, 'John Black', 'Lawn Care', 45000.00),
(3, 'Carrie Understone', 'Professor', 89000.00),
(4, 'Billy Mole', 'Head of Atheltics', 120000.00),
(5, 'Kendrick Dot', 'Consultant ', 120000.00),
(6, 'Herbert Gunther', 'Dean of Admissions', 140000.00),
(7, 'Tim Smith', 'Lawn Care', 25000.00);

-- --------------------------------------------------------

--
-- Table structure for table `fringe_rates`
--

CREATE TABLE `fringe_rates` (
  `year` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `rate_percent` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fringe_rates`
--

INSERT INTO `fringe_rates` (`year`, `category`, `rate_percent`) VALUES
(2025, 'faculty', 30.00),
(2025, 'staff', 28.00),
(2025, 'students', 12.00),
(2026, 'faculty', 31.00),
(2026, 'staff', 29.00),
(2026, 'students', 12.50),
(2027, 'faculty', 31.15),
(2027, 'staff', 29.15),
(2027, 'students', 12.56),
(2028, 'faculty', 31.31),
(2028, 'staff', 29.30),
(2028, 'students', 12.62),
(2029, 'faculty', 31.46),
(2029, 'staff', 29.45),
(2029, 'students', 12.68),
(2030, 'faculty', 31.62),
(2030, 'staff', 29.60),
(2030, 'students', 12.74),
(2031, 'faculty', 31.78),
(2031, 'staff', 29.75),
(2031, 'students', 12.81),
(2032, 'faculty', 31.94),
(2032, 'staff', 29.90),
(2032, 'students', 12.87),
(2033, 'faculty', 32.10),
(2033, 'staff', 30.05),
(2033, 'students', 12.93),
(2034, 'faculty', 32.26),
(2034, 'staff', 30.20),
(2034, 'students', 12.99),
(2035, 'faculty', 32.41),
(2035, 'staff', 30.35),
(2035, 'students', 13.06);

-- --------------------------------------------------------

--
-- Table structure for table `f_and_a_rates`
--

CREATE TABLE `f_and_a_rates` (
  `year` int(11) DEFAULT NULL,
  `rate_percent` decimal(5,2) DEFAULT NULL,
  `cost_base` enum('total_direct_costs','modified_total_direct_costs') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `f_and_a_rates`
--

INSERT INTO `f_and_a_rates` (`year`, `rate_percent`, `cost_base`) VALUES
(2025, 54.00, 'modified_total_direct_costs'),
(2026, 55.00, 'modified_total_direct_costs'),
(2027, 55.55, 'modified_total_direct_costs'),
(2028, 56.11, 'modified_total_direct_costs'),
(2029, 56.67, 'modified_total_direct_costs'),
(2030, 57.24, 'modified_total_direct_costs'),
(2031, 57.81, 'modified_total_direct_costs'),
(2032, 58.39, 'modified_total_direct_costs'),
(2033, 58.98, 'modified_total_direct_costs'),
(2034, 59.57, 'modified_total_direct_costs'),
(2035, 60.17, 'modified_total_direct_costs');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `sid` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `residency_status` enum('in-state','out-of-state') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`sid`, `name`, `residency_status`) VALUES
(1, 'Vicki Smalls', 'in-state'),
(2, 'Veronica Large', 'out-of-state'),
(3, 'Bill Withers', 'in-state'),
(4, 'Bart Johnson', 'in-state'),
(5, 'Andre Humpreys', 'out-of-state'),
(6, 'Ada George', 'in-state');

-- --------------------------------------------------------

--
-- Table structure for table `travel_profiles`
--

CREATE TABLE `travel_profiles` (
  `id` int(11) NOT NULL,
  `type` enum('domestic','international') DEFAULT NULL,
  `per_diem` decimal(10,2) DEFAULT NULL,
  `airfare_estimate` decimal(10,2) DEFAULT NULL,
  `lodging_cap` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_profiles`
--

INSERT INTO `travel_profiles` (`id`, `type`, `per_diem`, `airfare_estimate`, `lodging_cap`) VALUES
(2, 'domestic', 90.00, 450.00, 200.00),
(3, 'international', 120.00, 1200.00, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `tuition_fees`
--

CREATE TABLE `tuition_fees` (
  `year` int(11) DEFAULT NULL,
  `residency_status` enum('in-state','out-of-state') DEFAULT NULL,
  `semester` enum('fall','spring','summer') DEFAULT NULL,
  `tuition_amount` decimal(10,2) DEFAULT NULL,
  `fees` decimal(10,2) DEFAULT NULL,
  `projected_increase_percent` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuition_fees`
--

INSERT INTO `tuition_fees` (`year`, `residency_status`, `semester`, `tuition_amount`, `fees`, `projected_increase_percent`) VALUES
(2025, 'in-state', 'fall', 5200.00, 850.00, 3.00),
(2025, 'in-state', 'spring', 5200.00, 850.00, 3.00),
(2025, 'out-of-state', 'fall', 13800.00, 950.00, 4.00),
(2025, 'out-of-state', 'spring', 13800.00, 950.00, 4.00),
(2026, 'in-state', 'fall', 5360.00, 880.00, 3.00),
(2026, 'in-state', 'spring', 5360.00, 880.00, 3.00),
(2026, 'out-of-state', 'fall', 14350.00, 980.00, 4.00),
(2026, 'out-of-state', 'spring', 14350.00, 980.00, 4.00),
(2027, 'in-state', 'fall', 5520.80, 906.40, 3.00),
(2027, 'in-state', 'spring', 5520.80, 906.40, 3.00),
(2027, 'out-of-state', 'fall', 14780.50, 1009.40, 3.00),
(2027, 'out-of-state', 'spring', 14780.50, 1009.40, 3.00),
(2028, 'in-state', 'fall', 5686.42, 933.59, 3.00),
(2028, 'in-state', 'spring', 5686.42, 933.59, 3.00),
(2028, 'out-of-state', 'fall', 15223.92, 1039.68, 3.00),
(2028, 'out-of-state', 'spring', 15223.92, 1039.68, 3.00),
(2029, 'in-state', 'fall', 5857.01, 961.59, 3.00),
(2029, 'in-state', 'spring', 5857.01, 961.59, 3.00),
(2029, 'out-of-state', 'fall', 15680.64, 1070.87, 3.00),
(2029, 'out-of-state', 'spring', 15680.64, 1070.87, 3.00),
(2030, 'in-state', 'fall', 6032.72, 990.44, 3.00),
(2030, 'in-state', 'spring', 6032.72, 990.44, 3.00),
(2030, 'out-of-state', 'fall', 16151.06, 1102.00, 3.00),
(2030, 'out-of-state', 'spring', 16151.06, 1102.00, 3.00),
(2031, 'in-state', 'fall', 6213.70, 1020.15, 3.00),
(2031, 'in-state', 'spring', 6213.70, 1020.15, 3.00),
(2031, 'out-of-state', 'fall', 16635.60, 1135.06, 3.00),
(2031, 'out-of-state', 'spring', 16635.60, 1135.06, 3.00),
(2032, 'in-state', 'fall', 6400.11, 1050.75, 3.00),
(2032, 'in-state', 'spring', 6400.11, 1050.75, 3.00),
(2032, 'out-of-state', 'fall', 17134.67, 1169.11, 3.00),
(2032, 'out-of-state', 'spring', 17134.67, 1169.11, 3.00),
(2033, 'in-state', 'fall', 6592.11, 1082.27, 3.00),
(2033, 'in-state', 'spring', 6592.11, 1082.27, 3.00),
(2033, 'out-of-state', 'fall', 17648.71, 1204.18, 3.00),
(2033, 'out-of-state', 'spring', 17648.71, 1204.18, 3.00),
(2034, 'in-state', 'fall', 6789.87, 1114.74, 3.00),
(2034, 'in-state', 'spring', 6789.87, 1114.74, 3.00),
(2034, 'out-of-state', 'fall', 18178.17, 1240.30, 3.00),
(2034, 'out-of-state', 'spring', 18178.17, 1240.30, 3.00),
(2035, 'in-state', 'fall', 6993.57, 1148.18, 3.00),
(2035, 'in-state', 'spring', 6993.57, 1148.18, 3.00),
(2035, 'out-of-state', 'fall', 18723.51, 1277.51, 3.00),
(2035, 'out-of-state', 'spring', 18723.51, 1277.51, 3.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$giNNkRw7t.jY4IpiLNkKp.hzXwY3uuLEP6RTVHPMbkfJA3vSBnMm.'),
(2, 'John Smith', 'johnsmith@gmail.com', '$2y$10$/Io.hyhg0KYEO0jClgkd2eiDs8gY27Qu/H2Y/6ZFhWdNJkkW/6fCm'),
(3, 'Bart Johnson', 'bartjohnson@gmail.com', '$2y$10$bU9rbVCYALmXpbg/jrvjnOxp0dhdGlyPy3XMazn4ZL3fdGFvgfeTy'),
(4, 'Herbert Humphries', 'herberthumpries@gmail.com', '$2y$10$lvOqajdjSOhQxuXxYLI19.EgICzzMzHHYwO89HPTuGlJD1Av0QJ9y'),
(5, 'Bubba Smith', 'bubbasmith@gmail.com', '$2y$10$pIQvh6nbHu92vKW1dDLFSeqUGpHHkU8VhHXcx8O7V6fBsJbaUA2Hi'),
(6, 'Ben Givens', 'ben.s.givens@gmail.com', '$2y$10$gl4F9B9eMyKz7wtHznWTQO63dJXl9qhNOqBaKqdCXZX3Hhi05CUg6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `budget_personnel`
--
ALTER TABLE `budget_personnel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`),
  ADD KEY `pi_id` (`pi_id`);

--
-- Indexes for table `budget_students`
--
ALTER TABLE `budget_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `budget_travel`
--
ALTER TABLE `budget_travel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`),
  ADD KEY `travel_type_id` (`travel_type_id`);

--
-- Indexes for table `faculty_staff`
--
ALTER TABLE `faculty_staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `travel_profiles`
--
ALTER TABLE `travel_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `budget_personnel`
--
ALTER TABLE `budget_personnel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `budget_students`
--
ALTER TABLE `budget_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `budget_travel`
--
ALTER TABLE `budget_travel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `faculty_staff`
--
ALTER TABLE `faculty_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `travel_profiles`
--
ALTER TABLE `travel_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budget_personnel`
--
ALTER TABLE `budget_personnel`
  ADD CONSTRAINT `budget_personnel_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_personnel_ibfk_2` FOREIGN KEY (`pi_id`) REFERENCES `faculty_staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_students`
--
ALTER TABLE `budget_students`
  ADD CONSTRAINT `budget_students_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_students_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`sid`) ON DELETE CASCADE;

--
-- Constraints for table `budget_travel`
--
ALTER TABLE `budget_travel`
  ADD CONSTRAINT `budget_travel_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_travel_ibfk_2` FOREIGN KEY (`travel_type_id`) REFERENCES `travel_profiles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
