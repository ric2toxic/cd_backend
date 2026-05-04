-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 08, 2017 at 04:09 PM
-- Server version: 5.7.18-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_dev_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_ms_seller`
--

CREATE TABLE `oc_ms_seller` (
  `seller_id` int(11) NOT NULL,
  `nickname` varchar(32) NOT NULL DEFAULT '',
  `company` varchar(64) NOT NULL DEFAULT '',
  `website` varchar(2083) NOT NULL DEFAULT '',
  `seller_description` text NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `zone_id` int(11) NOT NULL DEFAULT '0',
  `avatar` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `paypal` varchar(255) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `seller_status` tinyint(4) NOT NULL,
  `seller_approved` tinyint(4) NOT NULL,
  `product_validation` tinyint(4) NOT NULL DEFAULT '1',
  `seller_group` int(11) NOT NULL DEFAULT '1',
  `commission_id` int(11) DEFAULT NULL,
  `address1` text NOT NULL,
  `address2` text,
  `pincode` varchar(6) NOT NULL,
  `city` varchar(50) NOT NULL,
  `pan` varchar(10) DEFAULT NULL,
  `tin` varchar(20) DEFAULT NULL,
  `tan` varchar(50) DEFAULT NULL,
  `non_returnable` tinyint(1) NOT NULL,
  `bank_ac_holder_name` varchar(100) NOT NULL,
  `bank_ac_number` varchar(20) NOT NULL,
  `retype_ac_number` varchar(20) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_state` varchar(64) NOT NULL,
  `bank_city` varchar(100) NOT NULL,
  `bank_branch` text,
  `email` varchar(50) NOT NULL,
  `alternate_email` varchar(50) NOT NULL,
  `landline_no` varchar(15) NOT NULL,
  `mobile_no` varchar(50) NOT NULL,
  `whatsapp_no` varchar(50) NOT NULL,
  `alternatemobile_no` varchar(50) NOT NULL,
  `theme` varchar(50) NOT NULL,
  `access_token` text,
  `vacation_mode` tinyint(4) NOT NULL,
  `cod_available` tinyint(1) NOT NULL DEFAULT '1',
  `sor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `pickup_city_code` varchar(2) DEFAULT NULL,
  `seller_agreement` tinyint(1) NOT NULL,
  `seller_agreement_acceptance_date` datetime DEFAULT NULL,
  `pickup_address` text,
  `pickup_city` varchar(50) DEFAULT NULL,
  `pickup_pincode` varchar(6) DEFAULT NULL,
  `pickup_zone_id` int(11) NOT NULL DEFAULT '0',
  `pickup_country_id` int(11) NOT NULL DEFAULT '0',
  `primary_contact_name` varchar(32) DEFAULT NULL,
  `primary_contact_no` varchar(15) DEFAULT NULL,
  `pickup_holder_name` varchar(32) DEFAULT NULL,
  `pickup_contact_no` varchar(15) DEFAULT NULL,
  `account_holder_name` varchar(32) DEFAULT NULL,
  `account_contact_no` varchar(15) DEFAULT NULL,
  `inventory_holder_name` varchar(32) DEFAULT NULL,
  `inventory_contact_no` varchar(15) DEFAULT NULL,
  `same_as_primary_pickup` tinyint(1) NOT NULL DEFAULT '0',
  `same_as_primary_account` tinyint(1) NOT NULL DEFAULT '0',
  `same_as_primary_inventory` tinyint(1) NOT NULL DEFAULT '0',
  `pan_image` varchar(256) DEFAULT NULL,
  `tin_image` varchar(256) DEFAULT NULL,
  `cancel_cheque_image` varchar(256) DEFAULT NULL,
  `same_as_primary_address` tinyint(1) NOT NULL DEFAULT '0',
  `tin_tax_type` varchar(5) DEFAULT '1',
  `app_only` tinyint(1) NOT NULL DEFAULT '0',
  `non_serviceable_areas` text,
  `seller_invoice_generate` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_ms_seller`
--
ALTER TABLE `oc_ms_seller`
  ADD PRIMARY KEY (`seller_id`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD KEY `seller_id` (`seller_id`,`nickname`,`seller_status`,`vacation_mode`,`cod_available`,`sor_enabled`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_ms_seller`
--
ALTER TABLE `oc_ms_seller`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
