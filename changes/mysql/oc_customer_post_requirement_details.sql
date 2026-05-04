-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 06, 2017 at 07:54 PM
-- Server version: 5.7.20-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `new_wholesalebox`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_customer_post_requirement_details`
--

CREATE TABLE `oc_customer_post_requirement_details` (
  `customer_requirement_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `required_days` varchar(55) NOT NULL,
  `price_to` decimal(10,4) NOT NULL,
  `price_from` decimal(10,4) NOT NULL,
  `description` text NOT NULL,
  `refrenece_link` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_post_requirement_details`
--
ALTER TABLE `oc_customer_post_requirement_details`
  ADD PRIMARY KEY (`customer_requirement_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_post_requirement_details`
--
ALTER TABLE `oc_customer_post_requirement_details`
  MODIFY `customer_requirement_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
