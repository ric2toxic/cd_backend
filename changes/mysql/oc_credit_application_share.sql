-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 09, 2018 at 03:42 PM
-- Server version: 5.7.23-0ubuntu0.16.04.1
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_react_structure`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_credit_application_share`
--

CREATE TABLE `oc_credit_application_share` (
  `credit_application_share_id` int(15) NOT NULL,
  `credit_application_id` int(11) NOT NULL,
  `credit_partner_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `credit_response_application_id` varchar(20) NOT NULL,
  `credit_response_applicant_id` varchar(20) NOT NULL,
  `requested_through` enum('API','CSV') NOT NULL DEFAULT 'API',
  `kyc_done` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_credit_application_share`
--
ALTER TABLE `oc_credit_application_share`
  ADD PRIMARY KEY (`credit_application_share_id`),
  ADD KEY `credit_application_id` (`credit_application_id`,`credit_response_application_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_credit_application_share`
--
ALTER TABLE `oc_credit_application_share`
  MODIFY `credit_application_share_id` int(15) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
