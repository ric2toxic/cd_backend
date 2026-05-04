-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2017 at 12:18 PM
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
-- Table structure for table `oc_order_product_backup`
--

CREATE TABLE `oc_order_product_backup` (
  `order_product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `suborder_id` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `seller_invoice_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `model` varchar(64) NOT NULL,
  `quantity` int(4) NOT NULL,
  `piece_in_set` int(8) NOT NULL,
  `price_per_piece` decimal(15,4) NOT NULL,
  `discount_per_piece` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `discount_breakup` text,
  `weight_per_piece` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `output_tax_rates` varchar(16) DEFAULT NULL,
  `reward` int(8) NOT NULL,
  `comment` text NOT NULL,
  `seller_sku` varchar(64) NOT NULL,
  `transfer_price_per_piece` decimal(15,2) NOT NULL,
  `seller_input_tax` decimal(15,4) DEFAULT NULL,
  `seller_cst` tinyint(1) DEFAULT '0',
  `store_sales` enum('NO','BLR','DL','JP','ST') NOT NULL DEFAULT 'NO',
  `store_pickup` tinyint(1) NOT NULL DEFAULT '0',
  `customer_comment` text,
  `edit_type` enum('YES','REJECTED_WRONG_PRODUCT','REJECTED_SELLER_DAMAGE','REJECTED_WSB_DAMAGE','DAMAGE_BY_COURIER_COMPANY','SELLER_APPROVED','SELLER_PARTIAL','SELLER_NOT_SUPPLIED','SELLER_LATER_DISPATCH') NOT NULL DEFAULT 'YES',
  `edit_history` text,
  `hsn_code` varchar(8) DEFAULT NULL,
  `backup_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_order_product_backup`
--
ALTER TABLE `oc_order_product_backup`
  ADD PRIMARY KEY (`order_product_id`),
  ADD KEY `order_id` (`order_id`,`suborder_id`,`product_id`,`model`,`piece_in_set`,`output_tax_rates`,`seller_sku`,`seller_input_tax`,`seller_cst`,`store_sales`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_order_product_backup`
--
ALTER TABLE `oc_order_product_backup`
  MODIFY `order_product_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
