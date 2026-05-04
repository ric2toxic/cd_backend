-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 04, 2018 at 12:09 PM
-- Server version: 5.7.23-0ubuntu0.16.04.1
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

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
-- Table structure for table `oc_credit_application`
--

CREATE TABLE `oc_credit_application` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `business_entity_type` enum('proprietorship','partnership','corporate') NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `pan_no` varchar(255) NOT NULL,
  `aadhaar_no` varchar(32) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `education` enum('less_than_graduate','graduate','post_graduate','diploma') NOT NULL,
  `phone_no` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `current_address` text NOT NULL,
  `current_pincode` varchar(6) NOT NULL,
  `current_city` varchar(255) NOT NULL,
  `current_state` varchar(255) NOT NULL,
  `current_landline_phone_no` varchar(32) NOT NULL,
  `current_resident_premises` enum('self_owned','rented','family_owned','leased') NOT NULL,
  `residing_date` date DEFAULT NULL,
  `permanent_address` text NOT NULL,
  `permanent_pincode` varchar(6) NOT NULL,
  `permanent_city` varchar(255) NOT NULL,
  `permanent_state` varchar(255) NOT NULL,
  `permanent_landline_phone_no` varchar(32) NOT NULL,
  `permanent_resident_premises` enum('self_owned','rented','family_owned','leased') NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `entity_name` varchar(255) NOT NULL,
  `partners` varchar(36) NOT NULL,
  `shop_establishment_number` varchar(255) NOT NULL,
  `business_pan_no` varchar(255) NOT NULL,
  `company_identification_number` varchar(255) NOT NULL,
  `trading_name` varchar(255) NOT NULL,
  `nature_of_business` enum('jewellery','garment','footwear','gift_store') DEFAULT NULL,
  `business_premises` enum('self_owned','rented','family_owned') DEFAULT NULL,
  `occupied_since` date DEFAULT NULL,
  `business_address` varchar(255) NOT NULL,
  `business_pincode` varchar(6) NOT NULL,
  `business_city` varchar(255) NOT NULL,
  `business_state` varchar(255) NOT NULL,
  `reg_office_address` text NOT NULL,
  `reg_office_pincode` varchar(6) NOT NULL,
  `reg_office_city` varchar(255) NOT NULL,
  `reg_office_state` varchar(255) NOT NULL,
  `other_business_entity_detail` text NOT NULL,
  `business_since` date DEFAULT NULL,
  `annual_turnover` varchar(255) NOT NULL,
  `litigation` text NOT NULL,
  `is_contact_person_same` tinyint(1) NOT NULL DEFAULT '0',
  `contact_person_first_name` varchar(255) NOT NULL,
  `contact_person_middle_name` varchar(255) NOT NULL,
  `contact_person_last_name` varchar(255) NOT NULL,
  `contact_person_designation` varchar(255) NOT NULL,
  `contact_person_relation_with_borrower` varchar(255) NOT NULL,
  `contact_person_email` varchar(255) NOT NULL,
  `contact_person_phone_no` varchar(32) NOT NULL,
  `declaration` int(1) NOT NULL DEFAULT '0',
  `draft` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_credit_application`
--
ALTER TABLE `oc_credit_application`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_credit_application`
--
ALTER TABLE `oc_credit_application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
