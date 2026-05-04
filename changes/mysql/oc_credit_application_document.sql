-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 09, 2018 at 01:06 PM
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
-- Table structure for table `oc_credit_application_document`
--

CREATE TABLE `oc_credit_application_document` (
  `id` int(11) NOT NULL,
  `credit_application_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `type` char(34) DEFAULT NULL,
  `name` set('pancard','aadhaar_card','photo','voter_id','driving_license','passport','electricity_bill','phone_landline_bill','registered_leave_license_agreement','maintenance_receipt','rental_agreement','six_months_bank_statement','last_quarter_vat_transactions','income_tax_returns','business_pan_no','vat_return','business_entity_address_proof','certificate_of_registration') NOT NULL,
  `document_number` varchar(32) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_credit_application_document`
--
ALTER TABLE `oc_credit_application_document`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_credit_application_document`
--
ALTER TABLE `oc_credit_application_document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
