-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 09, 2017 at 02:27 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
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
-- Table structure for table `oc_seller_updates`
--

CREATE TABLE `oc_seller_updates` (
  `update_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `update_group` enum('profile','business','agreement') NOT NULL,
  `update_type` varchar(32) CHARACTER SET utf8 NOT NULL,
  `new_value` varchar(256) CHARACTER SET utf8 NOT NULL,
  `date_added` datetime NOT NULL,
  `verification_status` enum('not_required','pending','approved','cancelled','admin_updated','change_by_seller','attached_tin_address') CHARACTER SET utf8 NOT NULL,
  `verified_by` varchar(24) CHARACTER SET utf8 NOT NULL,
  `previous_value` varchar(256) CHARACTER SET utf8 NOT NULL,
  `additional_details` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_seller_updates`
--
ALTER TABLE `oc_seller_updates`
  ADD PRIMARY KEY (`update_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_seller_updates`
--
ALTER TABLE `oc_seller_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
