-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 30, 2017 at 12:16 PM
-- Server version: 5.7.18-0ubuntu0.16.04.1
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
-- Table structure for table `oc_order_edit_history`
--

CREATE TABLE `oc_order_edit_history` (
  `edit_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `suborder_id` varchar(20) CHARACTER SET utf8 NOT NULL,
  `order_product_id` int(11) NOT NULL,
  `edit_type` enum('ADD_PRODUCT','DELETE_PRODUCT','UPDATE_PIECES','UPDATE_SETS','UPDATE_PRICE','UPDATE_SET_DESC','SHIPPING_RATE','SHIPPING_CODE','SHIPPING_ADDRESS','PAYMENT_ADDRESS','PAYMENT_CODE') CHARACTER SET utf8 NOT NULL,
  `old_value` varchar(255) CHARACTER SET utf8 NOT NULL,
  `new_value` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_order_edit_history`
--
ALTER TABLE `oc_order_edit_history`
  ADD PRIMARY KEY (`edit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_order_edit_history`
--
ALTER TABLE `oc_order_edit_history`
  MODIFY `edit_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
