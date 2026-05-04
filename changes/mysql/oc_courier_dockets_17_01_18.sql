-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 17, 2018 at 11:43 AM
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
-- Table structure for table `oc_courier_dockets`
--

CREATE TABLE `oc_courier_dockets` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `suborder_id` varchar(64) DEFAULT NULL,
  `courier_partners_id` int(11) DEFAULT NULL,
  `docket_no` varchar(64) DEFAULT NULL,
  `payment_mode` varchar(64) DEFAULT NULL,
  `cod_amount` decimal(12,4) DEFAULT NULL COMMENT 'net_payble',
  `parcel_amount` decimal(12,4) DEFAULT NULL COMMENT 'order_total',
  `post_mode` enum('REST','SOAP') DEFAULT NULL,
  `post_type` enum('XML','JSON') DEFAULT NULL,
  `post_url` varchar(256) DEFAULT NULL,
  `post_values` text,
  `response_type` enum('XML','JSON') DEFAULT NULL,
  `response_value` text,
  `bluedart_pdf` longblob,
  `date_added` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_courier_dockets`
--
ALTER TABLE `oc_courier_dockets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `suborder_id` (`suborder_id`),
  ADD KEY `courier_partners_id` (`courier_partners_id`),
  ADD KEY `docket_no` (`docket_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_courier_dockets`
--
ALTER TABLE `oc_courier_dockets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
