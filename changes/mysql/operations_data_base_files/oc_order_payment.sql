-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 23, 2016 at 03:46 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

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
-- Table structure for table `oc_order_payment`
--

CREATE TABLE `oc_order_payment` (
  `payment_id` int(11) NOT NULL,
  `payment_type` enum('advance','full','refund','others') NOT NULL DEFAULT 'others',
  `order_id` int(11) NOT NULL,
  `merchant_txn_id` varchar(20) NOT NULL,
  `order_no` varchar(20) NOT NULL,
  `txn_status` varchar(64) NOT NULL,
  `payment_mode` varchar(64) NOT NULL,
  `amount` double NOT NULL,
  `txn_date_time` datetime NOT NULL,
  `date_added` datetime NOT NULL,
  `payment_gateway` enum('citrus','razorpay') DEFAULT NULL,
  `successfull` tinyint(1) NOT NULL,
  `reference` varchar(128) NOT NULL,
  `payment_link` varchar(100) NOT NULL,
  `json_format` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_order_payment`
--
ALTER TABLE `oc_order_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payment_type` (`payment_type`,`order_id`,`merchant_txn_id`,`order_no`,`txn_date_time`,`successfull`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_order_payment`
--
ALTER TABLE `oc_order_payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
