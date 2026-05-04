-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 08, 2018 at 12:32 PM
-- Server version: 5.7.22-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

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
-- Table structure for table `oc_return_shipment_backto_customer`
--

CREATE TABLE `oc_return_shipment_backto_customer` (
  `shipping_id` int(11) NOT NULL,
  `courier_company` varchar(255) NOT NULL,
  `tracking_no` varchar(255) NOT NULL COMMENT 'NuvoEx Docket/AWB',
  `shipping_slip` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `equivalent_status` enum('Generated','Picked_Up','In_Transit','Problem','Delivered','New') NOT NULL DEFAULT 'Generated',
  `package_description` varchar(255) DEFAULT NULL,
  `qty` int(5) DEFAULT NULL,
  `return_reason` varchar(255) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` varchar(10) DEFAULT NULL,
  `vendor_code` varchar(50) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `request_param` text,
  `remarks` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_return_shipment_backto_customer`
--
ALTER TABLE `oc_return_shipment_backto_customer`
  ADD PRIMARY KEY (`shipping_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_return_shipment_backto_customer`
--
ALTER TABLE `oc_return_shipment_backto_customer`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
