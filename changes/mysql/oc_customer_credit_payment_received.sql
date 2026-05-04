-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 30, 2019 at 12:29 PM
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
-- Table structure for table `oc_customer_credit_payment_received`
--

CREATE TABLE `oc_customer_credit_payment_received` (
  `id` int(11) NOT NULL,
  `cif_id` int(11) DEFAULT NULL,
  `retailer_id` int(11) NOT NULL,
  `retailer_name` varchar(100) DEFAULT NULL,
  `firm_name` varchar(100) DEFAULT NULL,
  `lan` decimal(15,2) DEFAULT NULL,
  `lan_amount` decimal(15,2) DEFAULT NULL,
  `payment_amount` decimal(15,2) DEFAULT NULL,
  `payment_ref_number` varchar(50) DEFAULT NULL,
  `updated_limit` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_credit_payment_received`
--
ALTER TABLE `oc_customer_credit_payment_received`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_credit_payment_received`
--
ALTER TABLE `oc_customer_credit_payment_received`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
