-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2016 at 12:34 PM
-- Server version: 10.1.8-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_notification_message_type`
--

CREATE TABLE `oc_notification_message_type` (
  `message_type_id` int(10) NOT NULL,
  `Name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modify` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_notification_message_type`
--

INSERT INTO `oc_notification_message_type` (`message_type_id`, `Name`, `status`, `created`, `modify`) VALUES
(1, 'Text Message', 1, '2016-01-30 00:00:00', '0000-00-00 00:00:00'),
(2, 'Latest Design', 1, '2016-01-30 00:00:00', '0000-00-00 00:00:00'),
(3, 'Offer', 1, '2016-01-30 00:00:00', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_notification_message_type`
--
ALTER TABLE `oc_notification_message_type`
  ADD PRIMARY KEY (`message_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_notification_message_type`
--
ALTER TABLE `oc_notification_message_type`
  MODIFY `message_type_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
