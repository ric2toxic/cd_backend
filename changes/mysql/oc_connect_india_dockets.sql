-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 13, 2017 at 08:01 PM
-- Server version: 5.7.20-0ubuntu0.16.04.1
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
-- Table structure for table `oc_connect_india_dockets`
--

CREATE TABLE `oc_connect_india_dockets` (
  `id` int(11) NOT NULL COMMENT 'primary key',
  `docket_no` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `suborder_id` varchar(64) DEFAULT NULL,
  `weight` decimal(11,4) DEFAULT NULL,
  `height` decimal(11,4) DEFAULT NULL,
  `length` decimal(11,4) DEFAULT NULL,
  `width` decimal(11,4) DEFAULT NULL,
  `unit` varchar(64) DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `comments` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `post_data` text,
  `response_data` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_connect_india_dockets`
--
ALTER TABLE `oc_connect_india_dockets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_connect_india_dockets`
--
ALTER TABLE `oc_connect_india_dockets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
