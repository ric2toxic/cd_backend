-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2017 at 12:19 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
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
-- Table structure for table `oc_suborder_backup`
--

CREATE TABLE `oc_suborder_backup` (
  `order_id` int(11) NOT NULL,
  `suborder_id` varchar(20) NOT NULL,
  `invoice_no` int(11) NOT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_prefix` varchar(26) NOT NULL,
  `shipping_method` varchar(128) NOT NULL,
  `shipping_code` varchar(128) NOT NULL,
  `total` decimal(15,4) NOT NULL,
  `order_status_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `cform_submit` enum('no_submit','will_submit','submitted','') NOT NULL DEFAULT 'no_submit',
  `cst_with_cform` decimal(15,4) DEFAULT NULL,
  `refundable_cform` decimal(15,4) DEFAULT NULL,
  `refund_status` enum('refunded','not_refunded','not_applicable','') NOT NULL DEFAULT 'not_applicable',
  `courier_partner` varchar(100) DEFAULT NULL,
  `tracking_no` varchar(64) DEFAULT NULL,
  `shipping_charge` decimal(15,2) DEFAULT NULL,
  `custom_totals` text,
  `gst` tinyint(1) DEFAULT '1',
  `backup_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_suborder_backup`
--
ALTER TABLE `oc_suborder_backup`
  ADD UNIQUE KEY `order_id` (`order_id`,`suborder_id`),
  ADD KEY `invoice_prefix` (`invoice_prefix`,`total`,`order_status_id`,`date_added`,`cform_submit`,`courier_partner`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
