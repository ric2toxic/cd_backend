-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 28, 2019 at 11:10 AM
-- Server version: 5.7.24-0ubuntu0.16.04.1
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
-- Table structure for table `oc_customer_nach_details`
--

CREATE TABLE `oc_customer_nach_details` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `account_name` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `account_no` varchar(64) CHARACTER SET utf8 NOT NULL,
  `ifsc_code` varchar(11) CHARACTER SET utf8 NOT NULL,
  `umrn_no` varchar(64) NOT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=>Currently default NACH Account',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=>Active and available to make default NACH Account'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_nach_details`
--
ALTER TABLE `oc_customer_nach_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_nach_details`
--
ALTER TABLE `oc_customer_nach_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
