-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2025 at 05:38 PM
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
-- Database: `hostel_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `contact`, `user_id`) VALUES
(4, 'mama', '0764538580', 28);

-- --------------------------------------------------------

--
-- Table structure for table `complaint`
--

CREATE TABLE `complaint` (
  `complaint_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `warden_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint`
--

INSERT INTO `complaint` (`complaint_id`, `date`, `status`, `remarks`, `description`, `type`, `student_id`, `warden_id`) VALUES
(14, '2025-06-27', 'Pending', 'idapan', 'weded', 'Plumbing', 15, NULL),
(15, '2025-06-27', 'Resolved', 'supiri', 'no current', 'Electricity', 15, NULL),
(17, '2025-06-27', 'Resolved', 'onn dunna', 'weda ne pako watura deepan', 'Plumbing', 15, NULL),
(18, '2025-06-28', 'Resolved', 'ponnayek uba', 'Apirisidui', 'Cleaning', 15, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `penalty` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `status`, `penalty`, `amount`, `payment_date`, `student_id`, `admin_id`) VALUES
(9, 'Approved', 999.00, 300.00, '2025-06-27', 15, NULL),
(10, 'Rejected', 159.00, 1000.00, '2025-06-27', 15, NULL),
(11, 'Pending', 200.00, 1200.00, '2025-06-27', 15, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_number` int(11) NOT NULL,
  `ac_nonac` varchar(11) DEFAULT NULL,
  `occupied_count` int(11) DEFAULT 0,
  `capacity` int(11) NOT NULL,
  `room_type` enum('Single','Double','Multiple') NOT NULL,
  `warden_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_number`, `ac_nonac`, `occupied_count`, `capacity`, `room_type`, `warden_id`) VALUES
(101, 'AC', 0, 2, 'Double', NULL),
(102, 'ac', 0, 1, 'Single', NULL),
(104, 'Non-AC', 0, 1, 'Single', NULL),
(105, 'AC', 1, 2, 'Double', NULL),
(106, 'Non-AC', 0, 5, 'Multiple', NULL),
(107, 'AC', 0, 1, 'Single', NULL),
(108, 'Non-AC', 0, 2, 'Double', NULL),
(109, 'AC', 0, 3, 'Multiple', NULL),
(110, 'AC', 2, 2, 'Double', 9),
(111, 'AC', 0, 1, 'Single', NULL),
(112, 'Non-AC', 0, 2, 'Double', NULL),
(113, 'AC', 0, 4, 'Multiple', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `duration_of_stay` int(11) DEFAULT NULL,
  `room_number` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `name`, `contact_info`, `duration_of_stay`, `room_number`, `user_id`) VALUES
(15, 'Thisada', '0764538580', 12, 110, 35);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('student','warden','admin') NOT NULL,
  `username` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `password`, `role`, `username`) VALUES
(28, '$2y$10$SrBxT1utzpOGphFBWJA1i.ykmi.AKvYNekX.wocW8jm1GyiieLVEO', 'admin', 'admin1'),
(33, '$2y$10$qsMhuUE1tMZXKRaf.pEWgeUVcB.fFZcEh57.5CXsiQok.iIrrAg8K', 'warden', 'warden1'),
(35, '$2y$10$3QggbnMpxj486UemUv2hYOzf8MjVbeBxjtYTB/EsRSCMLOjQav1Ea', 'student', 'www'),
(36, '$2y$10$WDCfxTa3uKubUHkjp4EieO4qyxNmut0vqGNA5U2Oi/59Mbu1ExW86', 'warden', 'warden2');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_log`
--

CREATE TABLE `visitor_log` (
  `visitor_id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `visit_datetime` datetime NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor_log`
--

INSERT INTO `visitor_log` (`visitor_id`, `visitor_name`, `visit_datetime`, `purpose`, `student_id`) VALUES
(4, 'Nayanaka', '2025-05-31 10:59:00', 'Drinking ,Fighiting', 15),
(5, 'Gawrawa', '2025-05-31 00:00:00', 'Eating', 15);

-- --------------------------------------------------------

--
-- Table structure for table `warden`
--

CREATE TABLE `warden` (
  `warden_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `assigned_hostel` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warden`
--

INSERT INTO `warden` (`warden_id`, `name`, `contact_info`, `assigned_hostel`, `user_id`) VALUES
(8, 'G.D.P.D RANASINGHE', '0764538580', 'Block J', 33),
(9, 'Ashan', '0887876580', 'block M', 36);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `complaint`
--
ALTER TABLE `complaint`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `warden_id` (`warden_id`),
  ADD KEY `complaint_ibfk_1` (`student_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `payment_ibfk_1` (`student_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_number`),
  ADD KEY `room_ibfk_1` (`warden_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `room_number` (`room_number`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `visitor_log`
--
ALTER TABLE `visitor_log`
  ADD PRIMARY KEY (`visitor_id`),
  ADD KEY `visitor_log_ibfk_1` (`student_id`);

--
-- Indexes for table `warden`
--
ALTER TABLE `warden`
  ADD PRIMARY KEY (`warden_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `complaint`
--
ALTER TABLE `complaint`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `visitor_log`
--
ALTER TABLE `visitor_log`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `warden`
--
ALTER TABLE `warden`
  MODIFY `warden_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaint`
--
ALTER TABLE `complaint`
  ADD CONSTRAINT `complaint_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaint_ibfk_2` FOREIGN KEY (`warden_id`) REFERENCES `warden` (`warden_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`warden_id`) REFERENCES `warden` (`warden_id`) ON DELETE SET NULL;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`room_number`) REFERENCES `room` (`room_number`);

--
-- Constraints for table `visitor_log`
--
ALTER TABLE `visitor_log`
  ADD CONSTRAINT `visitor_log_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
