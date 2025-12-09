-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 10:02 PM
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
(1, 'Bill', 'Teacher', 100000.00),
(2, 'Frank', 'Custodian', 64000.00),
(3, 'Bubba', 'IT', 140000.00),
(5, 'Alex', 'Lawn Care', 40000.00);

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
(2026, 'students', 12.50);

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
(2026, 55.00, 'modified_total_direct_costs');

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
(1, 'Max', 'in-state'),
(2, 'Kate', 'out-of-state'),
(3, 'Beth', 'in-state');

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
(2026, 'out-of-state', 'spring', 14350.00, 980.00, 4.00);

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
(1, 'admin', 'admin@gmail.com', '$2y$10$yIombe0LKubToOuT4s8tYeF2LlmytmWt1auLN2wTN1pNWZFd6NSdq');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `budget_personnel`
--
ALTER TABLE `budget_personnel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `budget_students`
--
ALTER TABLE `budget_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `budget_travel`
--
ALTER TABLE `budget_travel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `faculty_staff`
--
ALTER TABLE `faculty_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `travel_profiles`
--
ALTER TABLE `travel_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
