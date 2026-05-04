-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 07, 2015 at 01:53 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wholesalebox`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_abandoned_cart`
--

CREATE TABLE IF NOT EXISTS `oc_abandoned_cart` (
`id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_mobile` varchar(20) CHARACTER SET utf8 NOT NULL,
  `cart` text CHARACTER SET utf8 NOT NULL,
  `user_ip` varchar(50) CHARACTER SET utf8 NOT NULL,
  `user_os` varchar(50) CHARACTER SET utf8 NOT NULL,
  `user_browser` varchar(100) CHARACTER SET utf8 NOT NULL,
  `mail_sent` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_abandoned_cart`
--
ALTER TABLE `oc_abandoned_cart`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_abandoned_cart`
--
ALTER TABLE `oc_abandoned_cart`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
