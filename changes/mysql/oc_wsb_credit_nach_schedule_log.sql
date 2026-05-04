-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 23, 2019 at 06:45 PM
-- Server version: 5.7.25-0ubuntu0.16.04.2
-- PHP Version: 7.2.12-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `oc_wsb_credit_nach_schedule_log`
--

CREATE TABLE IF NOT EXISTS `oc_wsb_credit_nach_schedule_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `nach_schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(64) DEFAULT NULL,
  `field_name` enum('nach_debit_amount','nach_debit_date','deffered_by_customer','status') DEFAULT NULL,
  `ref_url` varchar(255) DEFAULT NULL,
  `old_value` text,
  `new_value` text,
  `comment` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
