-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 10, 2017 at 07:44 PM
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
-- Table structure for table `oc_bluedart_dockets`
--

CREATE TABLE `oc_bluedart_dockets` (
  `id` int(11) NOT NULL,
  `docket_no` varchar(30) NOT NULL COMMENT 'docket number',
  `order_id` varchar(63) NOT NULL COMMENT 'order number with this docket',
  `suborder_id` varchar(60) CHARACTER SET latin1 NOT NULL,
  `weight` varchar(50) NOT NULL,
  `height` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `length` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `unit` varchar(20) CHARACTER SET latin1 NOT NULL,
  `used` int(1) NOT NULL DEFAULT '0' COMMENT 'whether docket no used',
  `comments` varchar(100) DEFAULT NULL COMMENT 'comments',
  `post_data` text,
  `response_data` text,
  `pdf_data` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_bluedart_dockets`
--
ALTER TABLE `oc_bluedart_dockets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `docket_no` (`docket_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_bluedart_dockets`
--
ALTER TABLE `oc_bluedart_dockets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;