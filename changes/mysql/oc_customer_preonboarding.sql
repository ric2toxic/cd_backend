-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 18, 2019 at 05:46 PM
-- Server version: 5.7.25-0ubuntu0.16.04.2
-- PHP Version: 7.1.22-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `oc_customer_preonboarding`
--

CREATE TABLE `oc_customer_preonboarding` (
  `id` int(11) NOT NULL,
  `retailer_id` int(11) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `pan_no` varchar(15) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `residence_address` varchar(255) NOT NULL,
  `residence_pincode` int(11) NOT NULL,
  `email_id` varchar(255) NOT NULL,
  `firm_name` varchar(255) NOT NULL,
  `office_address` varchar(255) NOT NULL,
  `office_pincode` int(11) NOT NULL,
  `annual_turnover` decimal(10,2) NOT NULL,
  `retailer_business_vintage` varchar(255) NOT NULL,
  `date_of_first_order` date NOT NULL,
  `M1` decimal(10,2) NOT NULL,
  `M2` decimal(10,2) NOT NULL,
  `M3` decimal(10,2) NOT NULL,
  `M4` decimal(10,2) NOT NULL,
  `M5` decimal(10,2) NOT NULL,
  `M6` decimal(10,2) NOT NULL,
  `M7` decimal(10,2) NOT NULL,
  `M8` decimal(10,2) NOT NULL,
  `M9` decimal(10,2) NOT NULL,
  `M10` decimal(10,2) NOT NULL,
  `M11` decimal(10,2) NOT NULL,
  `M12` decimal(10,2) NOT NULL,
  `M13` decimal(10,2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_preonboarding`
--
ALTER TABLE `oc_customer_preonboarding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `retailer_id` (`retailer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_preonboarding`
--
ALTER TABLE `oc_customer_preonboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
