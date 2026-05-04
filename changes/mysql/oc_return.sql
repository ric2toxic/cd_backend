-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 24, 2017 at 02:19 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
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
-- Table structure for table `oc_return`
--

CREATE TABLE `oc_return` (
  `return_id` int(11) NOT NULL,
  `order_product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `return_reason_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `return_action_id` int(11) NOT NULL,
  `internal_note` text NOT NULL,
  `payment_method` enum('credit_note','refund','not_decided') NOT NULL DEFAULT 'not_decided',
  `date_added` datetime NOT NULL,
  `user` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `return_pickup_address_id` int(11) DEFAULT NULL,
  `debit_note_id` int(11) NOT NULL DEFAULT '0',
  `master_return_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_return`
--
ALTER TABLE `oc_return`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `order_product_id` (`order_product_id`,`return_reason_id`,`return_action_id`,`payment_method`,`date_added`,`user`,`debit_note_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_return`
--
ALTER TABLE `oc_return`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
