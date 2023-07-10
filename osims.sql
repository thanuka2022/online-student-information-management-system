-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 10, 2023 at 06:05 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `osims`
--

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

DROP TABLE IF EXISTS `guardians`;
CREATE TABLE IF NOT EXISTS `guardians` (
  `guardian_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `guardian_address` varchar(200) DEFAULT NULL,
  `relation` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`guardian_id`),
  KEY `student_id` (`student_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `guardians`
--

INSERT INTO `guardians` (`guardian_id`, `student_id`, `guardian_name`, `contact_no`, `guardian_address`, `relation`, `user_id`) VALUES
(9, 1, 'sewwwandi perera', '0770809456', 'no/15 amablangoda', 'Mother', NULL),
(8, 2, 'lavan silva', '0768069200', 'no/35 beach road ,matara', 'father', NULL),
(7, 4, 'kumara silva', '0770809456', 'no3 ambalangoda', 'Mother', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) DEFAULT NULL,
  `name_with_initial` varchar(50) DEFAULT NULL,
  `permanent_address` varchar(200) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `photo` varchar(100) DEFAULT NULL,
  `registered_date` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `full_name`, `name_with_initial`, `permanent_address`, `date_of_birth`, `gender`, `photo`, `registered_date`, `user_id`) VALUES
(1, 'thanuka kumara', 'm.t.k.de silva', 'no 19 beach road', '2023-07-12', 'Male', 'thanuka.jpg', '2023-07-20', NULL),
(4, 'channa silva', 's.s.kamal', 'ambalangoda', '2023-07-07', 'Male', 'face27.jpg', '2023-07-15', NULL),
(2, 'kanishkaa sdaruwan', 's.s.a.kaveesha', 'no/15 ,ambalangoda', '2023-07-13', 'Female', 'face26.jpg', '2023-07-12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `experience` int(10) DEFAULT NULL,
  `telephone_no` varchar(12) DEFAULT NULL,
  `photo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `created_at`, `token`, `email`, `name`, `subject`, `experience`, `telephone_no`, `photo`) VALUES
(1, 'admin', '1234', '2023-07-09 05:45:01', 'example_token', 'mtkdesilva@gmail.com', 'Thanuka kumara', 'mathematics', 8, '+94768069204', 'teacher.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
