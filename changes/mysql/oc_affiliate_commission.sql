-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 21, 2019 at 04:48 PM
-- Server version: 5.7.23-0ubuntu0.18.04.1
-- PHP Version: 7.1.30-1+ubuntu18.04.1+deb.sury.org+1

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
-- Table structure for table `oc_affiliate_commission`
--

CREATE TABLE `oc_affiliate_commission` (
  `affiliate_commission_id` int(11) NOT NULL,
  `affiliate_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `order_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `commission_rate` tinyint(1) NOT NULL,
  `commission` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('PENDING','PAID') NOT NULL DEFAULT 'PENDING',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_affiliate_commission`
--
ALTER TABLE `oc_affiliate_commission`
  ADD PRIMARY KEY (`affiliate_commission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_affiliate_commission`
--
ALTER TABLE `oc_affiliate_commission`
  MODIFY `affiliate_commission_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
