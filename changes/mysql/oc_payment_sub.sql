-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 02, 2017 at 03:00 PM
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
-- Table structure for table `oc_payment_sub`
--

CREATE TABLE `oc_payment_sub` (
  `payment_sub_id` int(15) NOT NULL,
  `payment_id` int(15) NOT NULL,
  `ledger_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `imgg` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` int(10) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `user_detail` text CHARACTER SET utf8,
  `delete_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_payment_sub`
--
ALTER TABLE `oc_payment_sub`
  ADD PRIMARY KEY (`payment_sub_id`),
  ADD KEY `ledger_id` (`ledger_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_payment_sub`
--
ALTER TABLE `oc_payment_sub`
  MODIFY `payment_sub_id` int(15) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `oc_payment_sub`
--
ALTER TABLE `oc_payment_sub`
  ADD CONSTRAINT `FK_LedgerPaymentSub` FOREIGN KEY (`ledger_id`) REFERENCES `oc_ledger` (`ledger_id`),
  ADD CONSTRAINT `FK_PaymentPaymentSub` FOREIGN KEY (`payment_id`) REFERENCES `oc_payment` (`payment_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
