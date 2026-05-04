-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost:127.0.0.1
-- Generation Time: Mar 27, 2017 at 01:12 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

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
-- Table structure for table `oc_mailer_to_sellers_log`
--

CREATE TABLE `oc_mailer_to_sellers_log` (
  `mail_id` int(11) NOT NULL,
  `emails` mediumtext CHARACTER SET utf8,
  `attachments` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `subject` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `message` mediumtext CHARACTER SET utf8,
  `user` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `date_sent` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_mailer_to_sellers_log`
--
ALTER TABLE `oc_mailer_to_sellers_log`
  ADD PRIMARY KEY (`mail_id`),
  ADD KEY `date_sent` (`date_sent`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_mailer_to_sellers_log`
--
ALTER TABLE `oc_mailer_to_sellers_log`
  MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
