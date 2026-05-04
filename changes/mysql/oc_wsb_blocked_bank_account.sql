-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: 10.139.48.57
-- Generation Time: Apr 29, 2019 at 01:37 PM
-- Server version: 5.7.20-log
-- PHP Version: 7.1.15-1+ubuntu16.04.1+deb.sury.org+2

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
-- Table structure for table `oc_wsb_blocked_bank_account`
--

CREATE TABLE `oc_wsb_blocked_bank_account` (
  `bank_ac_number` varchar(64) NOT NULL,
  `ifsc_code` varchar(11) NOT NULL,
  `bank_ac_holder_name` varchar(64) DEFAULT NULL,
  `emp_code` varchar(8) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_blocked_bank_account`
--
ALTER TABLE `oc_wsb_blocked_bank_account`
  ADD PRIMARY KEY (`bank_ac_number`,`ifsc_code`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
