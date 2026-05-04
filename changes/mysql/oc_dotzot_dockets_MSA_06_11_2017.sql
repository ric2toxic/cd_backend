-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 06, 2017 at 05:07 PM
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
-- Table structure for table `oc_dotzot_dockets`
--

CREATE TABLE `oc_dotzot_dockets` (
  `id` int(11) NOT NULL COMMENT 'primary key',
  `docket_no` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT 'docket number',
  `order_id` varchar(63) CHARACTER SET utf8 NOT NULL COMMENT 'order number with this docket',
  `suborder_id` varchar(60) NOT NULL,
  `weight` varchar(50) CHARACTER SET utf8 NOT NULL,
  `height` varchar(20) DEFAULT NULL,
  `length` varchar(20) DEFAULT NULL,
  `unit` varchar(20) NOT NULL,
  `used` int(1) NOT NULL DEFAULT '0' COMMENT 'whether docket no used',
  `comments` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'comments',
  `cod_charge` varchar(20) DEFAULT NULL,
  `total_freight` varchar(20) DEFAULT NULL,
  `post_data` text,
  `response_data` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_dotzot_dockets`
--
ALTER TABLE `oc_dotzot_dockets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `docket_no` (`docket_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_dotzot_dockets`
--
ALTER TABLE `oc_dotzot_dockets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
