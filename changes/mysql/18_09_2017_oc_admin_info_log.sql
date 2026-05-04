-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 18, 2017 at 02:39 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_dev_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_admin_info_log`
--

CREATE TABLE `oc_admin_info_log` (
  `field_type` varchar(20) DEFAULT NULL,
  `field_value` varchar(100) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `file_location` text,
  `ip` varchar(20) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` varchar(20) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
