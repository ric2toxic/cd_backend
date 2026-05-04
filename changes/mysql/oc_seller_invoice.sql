-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 28, 2016 at 05:01 PM
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
-- Table structure for table `oc_seller_invoice`
--

CREATE TABLE `oc_seller_invoice` (
  `order_id` int(11) NOT NULL,
  `suborder_id` varchar(20) NOT NULL,
  `seller_id` int(8) NOT NULL,
  `seller_invoice_prefix` varchar(32) NOT NULL,
  `seller_invoice_no` int(8) NOT NULL,
  `seller_invoice_pdf` varchar(128) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_seller_invoice`
--
ALTER TABLE `oc_seller_invoice`
  ADD UNIQUE KEY `order_id` (`order_id`,`suborder_id`,`seller_id`);
ALTER TABLE `oc_seller_invoice` ADD FULLTEXT KEY `seller_invoice_prefix` (`seller_invoice_prefix`);
ALTER TABLE `oc_seller_invoice` ADD INDEX( `date_added`, `date_modified`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
