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
-- Table structure for table `oc_receipt_sub_chargesdr`
--

CREATE TABLE `oc_receipt_sub_chargesdr` (
  `oc_receipt_sub_chargesdr_id` int(15) NOT NULL,
  `receipt_id` int(15) NOT NULL,
  `receipt_sub_id` int(15) NOT NULL,
  `oc_receipt_sub_csv_id` int(15) NOT NULL,
  `dated` date NOT NULL,
  `ledger_id` int(8) NOT NULL,
  `charges` decimal(15,2) NOT NULL,
  `order_no` varchar(55) CHARACTER SET utf8 NOT NULL,
  `ref` varchar(255) CHARACTER SET utf8 NOT NULL,
  `order_payment_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `delete_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_receipt_sub_chargesdr`
--
ALTER TABLE `oc_receipt_sub_chargesdr`
  ADD PRIMARY KEY (`oc_receipt_sub_chargesdr_id`),
  ADD KEY `ledger_id` (`ledger_id`),
  ADD KEY `receipt_id` (`receipt_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_receipt_sub_chargesdr`
--
ALTER TABLE `oc_receipt_sub_chargesdr`
  MODIFY `oc_receipt_sub_chargesdr_id` int(15) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `oc_receipt_sub_chargesdr`
--
ALTER TABLE `oc_receipt_sub_chargesdr`
  ADD CONSTRAINT `FK_LedgerReceiptSubChangesDr` FOREIGN KEY (`ledger_id`) REFERENCES `oc_ledger` (`ledger_id`),
  ADD CONSTRAINT `FK_ReceiptReceiptSubChangesDr` FOREIGN KEY (`receipt_id`) REFERENCES `oc_receipt` (`receipt_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
