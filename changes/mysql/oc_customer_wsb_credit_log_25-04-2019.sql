-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 25, 2019 at 07:35 PM
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
-- Table structure for table `oc_customer_wsb_credit_log`
--

CREATE TABLE `oc_customer_wsb_credit_log` (
  `log_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `status` enum('DISABLED','ENABLED','BLOCKED','REJECTED','ON_HOLD','PENDING_APPROVAL') NOT NULL DEFAULT 'DISABLED',
  `credit_limit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `nach_schedule_crontab` varchar(64) DEFAULT NULL,
  `days_before_nach_start` int(5) NOT NULL DEFAULT '4' COMMENT 'After these days of order delivery NACH will be started ',
  `schedule_days_for_nach` int(5) NOT NULL DEFAULT '20' COMMENT 'Default Working days to complete NACH against Order at customer level',
  `auto_nach_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `comment` text,
  `date_added` datetime NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(64) DEFAULT NULL,
  `user_agent` text NOT NULL,
  `ip_address` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_wsb_credit_log`
--
ALTER TABLE `oc_customer_wsb_credit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `customer_id` (`customer_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_wsb_credit_log`
--
ALTER TABLE `oc_customer_wsb_credit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
