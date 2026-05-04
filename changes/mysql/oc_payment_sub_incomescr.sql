-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 03, 2017 at 11:27 AM
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
-- Table structure for table `oc_payment_sub_incomescr`
--

CREATE TABLE `oc_payment_sub_incomescr` (
  `payment_sub_incomescr_id` int(15) NOT NULL,
  `payment_id` int(15) NOT NULL,
  `payment_sub_id` int(15) NOT NULL,
  `dated` date NOT NULL,
  `ledger_id` int(8) NOT NULL,
  `incomes` decimal(15,2) NOT NULL,
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
-- Indexes for table `oc_payment_sub_incomescr`
--
ALTER TABLE `oc_payment_sub_incomescr`
  ADD PRIMARY KEY (`payment_sub_incomescr_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_payment_sub_incomescr`
--
ALTER TABLE `oc_payment_sub_incomescr`
  MODIFY `payment_sub_incomescr_id` int(15) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
