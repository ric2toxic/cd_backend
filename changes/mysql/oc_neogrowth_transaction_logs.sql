-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 10, 2018 at 04:45 PM
-- Server version: 5.7.22-0ubuntu0.16.04.1
-- PHP Version: 7.0.30-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_neogrowth_transaction_logs`
--

CREATE TABLE `oc_neogrowth_transaction_logs` (
  `log_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `request` text,
  `response` text,
  `order_no` varchar(16) DEFAULT NULL,
  `request_type` enum('Purchased','Delivered','Cancelled','Return','GetOrderStatus','GetOTBL') NOT NULL,
  `transaction_amount` decimal(10,4) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `transaction_id` varchar(24) DEFAULT NULL,
  `buyer_registration_number` varchar(64) DEFAULT NULL,
  `status` enum('NEW','COMPLETE','SUCCESS','FAILED') NOT NULL DEFAULT 'NEW',
  `message` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Indexes for table `oc_neogrowth_transaction_logs`
--
ALTER TABLE `oc_neogrowth_transaction_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_neogrowth_transaction_logs`
--
ALTER TABLE `oc_neogrowth_transaction_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
