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
-- Table structure for table `oc_master_customer_membership`
--

CREATE TABLE `oc_master_customer_membership` (
  `id` int(11) NOT NULL,
  `master_id` int(11) NOT NULL,
  `membership_id` int(11) NOT NULL,
  `membership_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `membership_fees` decimal(10,2) NOT NULL,
  `prepaid_discount` decimal(4,2) NOT NULL DEFAULT '0.00',
  `cod_discount` decimal(4,2) NOT NULL DEFAULT '0.00',
  `credit_discount` decimal(4,2) NOT NULL DEFAULT '0.00',
  `purchase_value` decimal(11,2) DEFAULT '0.00',
  `return_value` decimal(11,2) NOT NULL DEFAULT '0.00',
  `purchase_return_months` int(2) NOT NULL DEFAULT '3',
  `target_for_free_factor` decimal(4,2) NOT NULL,
  `target_amount_for_free` decimal(10,2) DEFAULT NULL,
  `membership_purchase_type` enum('PURCHASE','FREE_TARGET_ACHIEVE') CHARACTER SET latin1 NOT NULL DEFAULT 'PURCHASE',
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_master_customer_membership`
--
ALTER TABLE `oc_master_customer_membership`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_master_customer_membership`
--
ALTER TABLE `oc_master_customer_membership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
