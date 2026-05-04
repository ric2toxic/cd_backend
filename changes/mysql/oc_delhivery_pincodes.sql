-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 11, 2019 at 11:55 AM
-- Server version: 5.7.25-0ubuntu0.16.04.2
-- PHP Version: 7.1.22-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `oc_delhivery_pincodes`
--

CREATE TABLE `oc_delhivery_pincodes` (
  `id` int(11) NOT NULL,
  `pincode` int(11) DEFAULT NULL,
  `prepaid` tinyint(1) DEFAULT NULL,
  `pickup` tinyint(1) DEFAULT NULL,
  `repl` tinyint(1) DEFAULT NULL,
  `cod` tinyint(1) DEFAULT NULL,
  `dispatch_center` varchar(100) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `state_code` varchar(10) DEFAULT NULL,
  `sort_code` varchar(64) DEFAULT NULL,
  `value_capping` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_delhivery_pincodes`
--
ALTER TABLE `oc_delhivery_pincodes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_delhivery_pincodes`
--
ALTER TABLE `oc_delhivery_pincodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
