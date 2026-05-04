-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 21, 2018 at 03:15 PM
-- Server version: 5.7.23-0ubuntu0.16.04.1
-- PHP Version: 7.2.9-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `oc_membership_level`
--

CREATE TABLE `oc_membership_level` (
  `membership_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `membership_fees` decimal(10,2) DEFAULT NULL,
  `purchase_range_starts` decimal(11,2) NOT NULL DEFAULT '0.00',
  `purchase_range_ends` decimal(11,2) NOT NULL DEFAULT '0.00',
  `target_for_free_factor` decimal(4,2) NOT NULL DEFAULT '0.00',
  `prepaid_discount` decimal(4,2) NOT NULL COMMENT 'Discount for Prepaid Orders',
  `cod_discount` decimal(4,2) NOT NULL COMMENT 'Discount for COD Orders',
  `credit_discount` decimal(4,2) NOT NULL COMMENT 'Discount for Credit type Orders',
  `priority_level` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1->Active, 0->Inactive',
  `date_added` datetime NOT NULL,
  `last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_membership_level`
--

INSERT INTO `oc_membership_level` (`membership_id`, `name`, `membership_fees`, `purchase_range_starts`, `purchase_range_ends`, `target_for_free_factor`, `prepaid_discount`, `cod_discount`, `credit_discount`, `priority_level`, `status`, `date_added`, `last_modified`) VALUES
(1, 'Silver', '2500.00', '0.00', '100000.00', '4.00', '4.00', '2.00', '2.00', 3, 1, '2018-09-07 00:00:00', '2018-09-07 15:16:52'),
(2, 'Gold', '8000.00', '100001.00', '300000.00', '2.00', '6.00', '4.00', '2.00', 2, 1, '2018-09-07 00:00:00', '2018-09-07 15:26:22'),
(3, 'Platinum', '12000.00', '300001.00', '100000000.00', '1.50', '8.00', '6.00', '4.00', 1, 1, '2018-09-07 00:00:00', '2018-09-07 15:26:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_membership_level`
--
ALTER TABLE `oc_membership_level`
  ADD PRIMARY KEY (`membership_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_membership_level`
--
ALTER TABLE `oc_membership_level`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
