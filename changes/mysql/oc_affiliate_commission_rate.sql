-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 26, 2019 at 06:24 PM
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
-- Table structure for table `oc_affiliate_commission_rate`
--

CREATE TABLE `oc_affiliate_commission_rate` (
  `commission_rate_id` int(11) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  `order_count_from` int(11) NOT NULL,
  `order_count_to` int(11) NOT NULL,
  `rate` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_affiliate_commission_rate`
--
ALTER TABLE `oc_affiliate_commission_rate`
  ADD PRIMARY KEY (`commission_rate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_affiliate_commission_rate`
--
ALTER TABLE `oc_affiliate_commission_rate`
  MODIFY `commission_rate_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
