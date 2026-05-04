-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 29, 2019 at 05:25 PM
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
-- Table structure for table `oc_customer_credit_disbursal_lan`
--

CREATE TABLE `oc_customer_credit_disbursal_lan` (
  `id` int(11) NOT NULL,
  `retailer_id` int(11) NOT NULL,
  `cif_id` int(11) DEFAULT NULL,
  `retailer_name` varchar(100) DEFAULT NULL,
  `firm_name` varchar(100) DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `privious_credit_limit` decimal(15,2) DEFAULT NULL,
  `invoice_amount` decimal(15,2) DEFAULT NULL,
  `disbursed_amount` decimal(15,2) DEFAULT NULL,
  `updated_credit_limit` decimal(15,2) DEFAULT NULL,
  `disbursal_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_credit_disbursal_lan`
--
ALTER TABLE `oc_customer_credit_disbursal_lan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_credit_disbursal_lan`
--
ALTER TABLE `oc_customer_credit_disbursal_lan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
