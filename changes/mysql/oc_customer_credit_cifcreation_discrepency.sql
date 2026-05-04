-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2019 at 11:45 AM
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
-- Table structure for table `oc_customer_credit_cifcreation_discrepency`
--

CREATE TABLE `oc_customer_credit_cifcreation_discrepency` (
  `id` int(11) NOT NULL,
  `retailer_id` int(11) NOT NULL,
  `retailer_name` varchar(50) DEFAULT NULL,
  `pan_no` varchar(20) DEFAULT NULL,
  `document_received_flag` varchar(20) DEFAULT NULL,
  `data_value_discrepency` varchar(50) DEFAULT NULL,
  `document_name_discrepency` varchar(100) DEFAULT NULL,
  `discrepency_reasons` varchar(100) DEFAULT NULL,
  `required_resolution` varchar(100) DEFAULT NULL,
  `discrepency_initiation_date` date DEFAULT NULL,
  `resolution_remarks` varchar(100) DEFAULT NULL,
  `current_status` varchar(100) DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `date_updated` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_credit_cifcreation_discrepency`
--
ALTER TABLE `oc_customer_credit_cifcreation_discrepency`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_credit_cifcreation_discrepency`
--
ALTER TABLE `oc_customer_credit_cifcreation_discrepency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
