-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 02, 2018 at 05:35 PM
-- Server version: 5.7.21-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

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
-- Table structure for table `oc_gati_dockets`
--

CREATE TABLE `oc_gati_dockets` (
  `id` int(11) NOT NULL COMMENT 'Primary key',
  `docket_no` varchar(30) NOT NULL COMMENT 'docket number',
  `order_no` varchar(32) DEFAULT NULL COMMENT 'order number with this docket',
  `weight` decimal(7,2) DEFAULT NULL,
  `used` int(1) NOT NULL DEFAULT '0' COMMENT 'whether docket no used',
  `is_success` tinyint(1) NOT NULL DEFAULT '0',
  `comments` varchar(100) DEFAULT NULL COMMENT 'comments',
  `post_data` text CHARACTER SET latin1,
  `response_data` text CHARACTER SET latin1
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_gati_dockets`
--
ALTER TABLE `oc_gati_dockets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_gati_dockets`
--
ALTER TABLE `oc_gati_dockets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
