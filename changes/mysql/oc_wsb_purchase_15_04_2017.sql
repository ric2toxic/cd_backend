-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 15, 2017 at 11:13 AM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_wsb_purchase`
--

CREATE TABLE `oc_wsb_purchase` (
  `purchase_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `purchase_firm_id` int(11) NOT NULL,
  `invoice_no` varchar(64) CHARACTER SET utf8 NOT NULL,
  `invoice_date` date NOT NULL,
  `total_purchase_value` decimal(11,2) NOT NULL,
  `payment_done` tinyint(1) NOT NULL DEFAULT '1',
  `payment_release_invoice_date_gap` int(2) NOT NULL,
  `date_added` datetime NOT NULL,
  `user` varchar(255) CHARACTER SET utf8 NOT NULL,
  `seller_firm_meta` text CHARACTER SET utf8 NOT NULL,
  `purchase_firm_meta` text CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_purchase`
--
ALTER TABLE `oc_wsb_purchase`
  ADD PRIMARY KEY (`purchase_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_wsb_purchase`
--
ALTER TABLE `oc_wsb_purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
