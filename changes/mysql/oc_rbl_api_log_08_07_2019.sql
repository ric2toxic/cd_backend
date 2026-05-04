-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 08, 2019 at 12:07 PM
-- Server version: 5.7.26-0ubuntu0.16.04.1
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
-- Table structure for table `oc_rbl_api_log`
--

CREATE TABLE `oc_rbl_api_log` (
  `id` int(11) NOT NULL,
  `api_type` enum('cifStatus','orderPunch','lanDisbursement') DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `suborder_id` varchar(100) DEFAULT NULL,
  `cif_id` varchar(25) DEFAULT NULL,
  `dpd_count` int(3) DEFAULT NULL,
  `api_status` tinyint(1) DEFAULT NULL,
  `order_amount` decimal(12,2) DEFAULT NULL,
  `api_endpoint` varchar(255) DEFAULT NULL,
  `api_post_data` text,
  `api_response_data` text,
  `delivery_date` date DEFAULT NULL,
  `disbursal_request_date` datetime DEFAULT NULL,
  `disbursal_amount` decimal(12,2) DEFAULT NULL,
  `dpd_status` varchar(50) DEFAULT NULL,
  `limit_status` varchar(50) DEFAULT NULL,
  `lan_status` varchar(50) DEFAULT NULL,
  `disbursement_status` varchar(50) DEFAULT NULL,
  `updated_limit_available` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `status` enum('NEW','SUCCESS','FAILED') NOT NULL DEFAULT 'NEW'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_rbl_api_log`
--
ALTER TABLE `oc_rbl_api_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_rbl_api_log`
--
ALTER TABLE `oc_rbl_api_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
