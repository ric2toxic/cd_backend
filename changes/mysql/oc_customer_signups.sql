-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 13, 2017 at 03:14 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

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
-- Table structure for table `oc_customer_signups`
--

CREATE TABLE `oc_customer_signups` (
  `signup_id` int(11) NOT NULL,
  `country_code` varchar(4) CHARACTER SET utf8 NOT NULL,
  `telephone` varchar(15) CHARACTER SET utf8 NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `session_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `ip` varchar(40) CHARACTER SET utf8 NOT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8 NOT NULL,
  `otp_page` enum('Sign up','Forgot password') NOT NULL,
  `otp` int(11) NOT NULL,
  `otp_verified` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_signups`
--
ALTER TABLE `oc_customer_signups`
  ADD PRIMARY KEY (`signup_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_signups`
--
ALTER TABLE `oc_customer_signups`
  MODIFY `signup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=314;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
