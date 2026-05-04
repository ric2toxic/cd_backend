-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 02, 2017 at 03:01 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_order_payment`
--

CREATE TABLE `oc_order_payment` (
  `payment_id` int(11) NOT NULL,
  `payment_type` enum('advance','full','refund','others','bounce_back') NOT NULL DEFAULT 'others',
  `order_id` int(11) NOT NULL,
  `merchant_txn_id` varchar(128) NOT NULL,
  `order_no` varchar(20) NOT NULL,
  `txn_status` varchar(64) NOT NULL,
  `payment_mode` varchar(64) NOT NULL,
  `amount` double NOT NULL,
  `txn_date_time` datetime NOT NULL,
  `date_added` datetime NOT NULL,
  `payment_gateway` enum('citrus','razorpay','bank_transfer','cash','paytm') DEFAULT NULL,
  `bank_transfer_mode` enum('instant','cheque_deposited','cheque_success','cheque_failed','not_applicable') DEFAULT 'not_applicable',
  `successfull` tinyint(1) NOT NULL,
  `reference` varchar(128) NOT NULL,
  `payment_link` varchar(100) NOT NULL,
  `json_format` text NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `sales_staff_id` int(11) NOT NULL,
  `rec_pay_tablename` enum('receipt','payment') DEFAULT NULL,
  `rec_pay_id` int(11) NOT NULL DEFAULT '0',
  `rec_pay_sub_id` int(11) NOT NULL DEFAULT '0',
  `delete_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_order_payment`
--
ALTER TABLE `oc_order_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payment_type` (`payment_type`,`order_id`,`merchant_txn_id`,`order_no`,`txn_status`,`payment_mode`,`amount`,`txn_date_time`,`date_added`,`payment_gateway`,`successfull`,`user_id`);

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
