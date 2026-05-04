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
-- Table structure for table `oc_payment_sub_csv`
--

CREATE TABLE `oc_payment_sub_csv` (
  `oc_payment_sub_csv_id` int(15) NOT NULL,
  `payment_id` int(15) NOT NULL,
  `payment_sub_id` int(15) NOT NULL,
  `dated` date NOT NULL,
  `ledger_id` int(8) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `order_no` varchar(55) CHARACTER SET utf8 NOT NULL,
  `ref` varchar(255) CHARACTER SET utf8 NOT NULL,
  `order_payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `delete_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_payment_sub_csv`
--
ALTER TABLE `oc_payment_sub_csv`
  ADD PRIMARY KEY (`oc_payment_sub_csv_id`),
  ADD KEY `ledger_id` (`ledger_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_payment_sub_csv`
--
ALTER TABLE `oc_payment_sub_csv`
  MODIFY `oc_payment_sub_csv_id` int(15) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `oc_payment_sub_csv`
--
ALTER TABLE `oc_payment_sub_csv`
  ADD CONSTRAINT `FK_LedgerPaymentSubCSV` FOREIGN KEY (`ledger_id`) REFERENCES `oc_ledger` (`ledger_id`),
  ADD CONSTRAINT `FK_PaymentPaymentSubCSV` FOREIGN KEY (`payment_id`) REFERENCES `oc_payment` (`payment_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
