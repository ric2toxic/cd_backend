-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 01, 2018 at 02:36 PM
-- Server version: 5.7.23-0ubuntu0.16.04.1
-- PHP Version: 7.2.9-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_customer_wsb_credit`
--

CREATE TABLE `oc_customer_wsb_credit` (
  `customer_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `credit_limit` decimal(15,2) NOT NULL DEFAULT '0',
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_history` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_wsb_credit`
--
ALTER TABLE `oc_customer_wsb_credit`
  ADD PRIMARY KEY (`customer_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
