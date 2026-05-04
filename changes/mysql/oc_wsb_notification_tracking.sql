-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 03, 2017 at 02:10 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

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
-- Table structure for table `oc_wsb_notification_tracking`
--

CREATE TABLE `oc_wsb_notification_tracking` (
  `notification_tracking_id` int(11) UNSIGNED NOT NULL,
  `notification_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime DEFAULT NULL,
  `message` text NOT NULL,
  `image` varchar(256) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_notification_tracking`
--
ALTER TABLE `oc_wsb_notification_tracking`
  ADD PRIMARY KEY (`notification_tracking_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_wsb_notification_tracking`
--
ALTER TABLE `oc_wsb_notification_tracking`
  MODIFY `notification_tracking_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
