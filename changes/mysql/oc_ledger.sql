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
-- Table structure for table `oc_ledger`
--

CREATE TABLE `oc_ledger` (
  `ledger_id` int(11) NOT NULL,
  `ledger_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `group_id` int(11) NOT NULL,
  `emp_code` varchar(50) DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `drcr` text CHARACTER SET utf8 NOT NULL,
  `user_id` int(10) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `user_detail` text CHARACTER SET utf8,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_ledger`
--

INSERT INTO `oc_ledger` (`ledger_id`, `ledger_name`, `group_id`, `emp_code`, `opening_balance`, `drcr`, `user_id`, `date_created`, `date_modified`, `user_detail`, `status`) VALUES
(1, 'COD Fedex', 1, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(2, 'COD Gati KWE', 1, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(3, 'COD Gati Ltd', 1, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(4, 'Citrus', 2, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(5, 'Paytm', 2, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(6, 'RazorPay', 2, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(7, 'Citrus Charges', 12, NULL, '0.00', '', 0, NULL, NULL, NULL, 0),
(8, 'Paytm Charges', 12, NULL, '0.00', '', 0, NULL, NULL, NULL, 0),
(9, 'RazorPay Charges', 12, NULL, '0.00', '', 0, NULL, NULL, NULL, 0),
(10, 'Direct Sales', 3, NULL, '0.00', '', 0, NULL, NULL, NULL, 0),
(11, 'Buyer\'s Refund', 4, NULL, '0.00', '', 0, NULL, NULL, NULL, 0),
(12, 'Other Charges', 7, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(13, 'Other Incomes', 7, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(14, 'Salary Expenses', 12, NULL, '0.00', '', 0, NULL, NULL, NULL, 1),
(15, 'Round Off', 5, NULL, '0.00', '', 0, NULL, NULL, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_ledger`
--
ALTER TABLE `oc_ledger`
  ADD PRIMARY KEY (`ledger_id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_ledger`
--
ALTER TABLE `oc_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `oc_ledger`
--
ALTER TABLE `oc_ledger`
  ADD CONSTRAINT `FK_GroupLedger` FOREIGN KEY (`group_id`) REFERENCES `oc_group` (`group_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
