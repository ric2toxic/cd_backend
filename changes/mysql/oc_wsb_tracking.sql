-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 02, 2016 at 04:03 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wholesalebox`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_wsb_tracking`
--

CREATE TABLE IF NOT EXISTS `oc_wsb_tracking` (
`id` int(11) NOT NULL,
  `sess_id` varchar(255) NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `utm_source` varchar(50) DEFAULT NULL,
  `matchtype` char(1) DEFAULT NULL,
  `network` varchar(50) DEFAULT NULL,
  `device` varchar(20) DEFAULT NULL,
  `creative` varchar(50) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `placement` varchar(255) DEFAULT NULL,
  `adposition` varchar(4) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `os` varchar(20) DEFAULT NULL,
  `campgain` varchar(50) DEFAULT NULL,
  `medium` varchar(50) DEFAULT NULL,
  `ip` varchar(150) NOT NULL,
  `tracking` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Track the traffic source for customers as per ad campgain';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_tracking`
--
ALTER TABLE `oc_wsb_tracking`
 ADD PRIMARY KEY (`id`) COMMENT 'Primary key';

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_wsb_tracking`
--
ALTER TABLE `oc_wsb_tracking`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
