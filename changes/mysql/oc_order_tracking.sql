-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 26, 2017 at 11:49 AM
-- Server version: 5.7.18-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

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
-- Table structure for table `oc_order_tracking`
--

CREATE TABLE `oc_order_tracking` (
  `tracking_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_no` varchar(20) CHARACTER SET utf8 NOT NULL,
  `suborder_id` varchar(20) CHARACTER SET utf8 NOT NULL,
  `tracking_no` varchar(20) CHARACTER SET utf8 NOT NULL,
  `shipping_company` varchar(55) CHARACTER SET utf8 NOT NULL,
  `reference_no` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `origin` varchar(64) CHARACTER SET utf8 NOT NULL,
  `destination` varchar(64) CHARACTER SET utf8 NOT NULL,
  `no_of_packages` int(11) NOT NULL,
  `assured_delivery_date` date NOT NULL,
  `delivery_date_time` datetime NOT NULL,
  `receivers_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `receivers_remarks` varchar(20) CHARACTER SET utf8 NOT NULL,
  `daily_status` text CHARACTER SET utf8 NOT NULL,
  `last_scrap_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_order_tracking`
--
ALTER TABLE `oc_order_tracking`
  ADD PRIMARY KEY (`tracking_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_order_tracking`
--
ALTER TABLE `oc_order_tracking`
  MODIFY `tracking_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
