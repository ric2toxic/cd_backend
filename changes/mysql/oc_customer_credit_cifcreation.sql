-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 02, 2019 at 07:36 PM
-- Server version: 5.7.27-0ubuntu0.16.04.1
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
-- Table structure for table `oc_customer_credit_cifcreation`
--

CREATE TABLE `oc_customer_credit_cifcreation` (
  `id` int(11) NOT NULL,
  `retailer_id` int(11) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `title` varchar(10) DEFAULT NULL,
  `first_name` varchar(25) DEFAULT NULL,
  `middle_name` varchar(25) DEFAULT NULL,
  `last_name` varchar(25) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mother_maiden_name` varchar(100) DEFAULT NULL,
  `community` varchar(100) DEFAULT NULL,
  `marital_status` varchar(20) DEFAULT NULL,
  `gross_income` decimal(15,4) DEFAULT NULL,
  `refer_pan_no` varchar(20) DEFAULT NULL,
  `refer_address_type` varchar(100) DEFAULT NULL,
  `refer_address_line1` varchar(100) DEFAULT NULL,
  `refer_address_line2` varchar(100) DEFAULT NULL,
  `refer_address_line3` varchar(100) DEFAULT NULL,
  `refer_city` varchar(50) DEFAULT NULL,
  `refer_state` varchar(50) DEFAULT NULL,
  `refer_postcode` varchar(20) DEFAULT NULL,
  `refer_phone` varchar(20) DEFAULT NULL,
  `corporate_name` varchar(100) DEFAULT NULL,
  `incorporation_date` date DEFAULT NULL,
  `entity_pan_no` varchar(20) DEFAULT NULL,
  `annual_turnover` decimal(15,4) DEFAULT NULL,
  `entity_gst_no` varchar(25) DEFAULT NULL,
  `entity_address_type` varchar(100) DEFAULT NULL,
  `entity_address_line1` varchar(100) DEFAULT NULL,
  `entity_address_line2` varchar(100) DEFAULT NULL,
  `entity_address_line3` varchar(100) DEFAULT NULL,
  `entity_city` varchar(50) DEFAULT NULL,
  `entity_state` varchar(50) DEFAULT NULL,
  `entity_country` varchar(50) DEFAULT NULL,
  `entity_postcode` varchar(20) DEFAULT NULL,
  `entity_phone` varchar(20) DEFAULT NULL,
  `entity_email_id` varchar(100) DEFAULT NULL,
  `consent_details` varchar(255) DEFAULT NULL,
  `request_ref_number` varchar(50) DEFAULT NULL,
  `applied_on` date DEFAULT NULL,
  `tc_acceptance_flag` varchar(20) DEFAULT NULL,
  `cif_creation_status` enum('Pending','SentForCifCreation','DiscrepencyStatus','CifCreated') DEFAULT 'Pending',
  `date_added` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_credit_cifcreation`
--
ALTER TABLE `oc_customer_credit_cifcreation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `retailer_id` (`retailer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_credit_cifcreation`
--
ALTER TABLE `oc_customer_credit_cifcreation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
