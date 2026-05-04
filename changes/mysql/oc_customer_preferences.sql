-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 05, 2017 at 06:32 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

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
-- Table structure for table `oc_customer_preferences`
--

CREATE TABLE `oc_customer_preferences` (
  `pref_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `rating` int(1) DEFAULT NULL,
  `min_price` int(10) DEFAULT NULL,
  `max_price` int(10) DEFAULT NULL,
  `pieces_count` int(6) DEFAULT NULL,
  `total_amount` int(6) DEFAULT NULL,
  `pref_order_history` text CHARACTER SET utf8,
  `pref_return_history` text CHARACTER SET utf8,
  `pref_shortlist` text CHARACTER SET utf8,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_preferences`
--
ALTER TABLE `oc_customer_preferences`
  ADD PRIMARY KEY (`pref_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_preferences`
--
ALTER TABLE `oc_customer_preferences`
  MODIFY `pref_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
