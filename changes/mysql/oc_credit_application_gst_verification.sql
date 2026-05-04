-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 04, 2019 at 08:00 PM
-- Server version: 5.7.23-0ubuntu0.18.04.1
-- PHP Version: 7.1.20-1+ubuntu18.04.1+deb.sury.org+1

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
-- Table structure for table `oc_credit_application_gst_verification`
--

CREATE TABLE `oc_credit_application_gst_verification` (
  `gst_verification_id` int(11) NOT NULL,
  `credit_application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gst_type` enum('composition','regular') NOT NULL,
  `gst_active` int(11) NOT NULL,
  `last_filling_date` datetime NOT NULL,
  `registration_date` datetime NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_credit_application_gst_verification`
--
ALTER TABLE `oc_credit_application_gst_verification`
  ADD PRIMARY KEY (`gst_verification_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_credit_application_gst_verification`
--
ALTER TABLE `oc_credit_application_gst_verification`
  MODIFY `gst_verification_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
