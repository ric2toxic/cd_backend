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
-- Table structure for table `oc_receipt`
--

CREATE TABLE `oc_receipt` (
  `receipt_id` int(15) NOT NULL,
  `dated` date DEFAULT NULL,
  `ledger_id` int(8) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `mode` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `reference` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `no_of_order` int(5) NOT NULL DEFAULT '0',
  `narration` text CHARACTER SET utf8,
  `user_id` int(10) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `user_detail` text CHARACTER SET utf8,
  `confirm` tinyint(1) NOT NULL DEFAULT '0',
  `confirm_user` text CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_receipt`
--
ALTER TABLE `oc_receipt`
  ADD PRIMARY KEY (`receipt_id`),
  ADD KEY `ledger_id` (`ledger_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_receipt`
--
ALTER TABLE `oc_receipt`
  MODIFY `receipt_id` int(15) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `oc_receipt`
--
ALTER TABLE `oc_receipt`
  ADD CONSTRAINT `FK_LedgerReceipt` FOREIGN KEY (`ledger_id`) REFERENCES `oc_ledger` (`ledger_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
