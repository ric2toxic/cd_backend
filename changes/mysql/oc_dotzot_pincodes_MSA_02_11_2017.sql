-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 02, 2017 at 06:42 PM
-- Server version: 5.7.20-0ubuntu0.16.04.1
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
-- Table structure for table `oc_dotzot_pincodes`
--

CREATE TABLE `oc_dotzot_pincodes` (
  `id` int(11) NOT NULL,
  `product` varchar(25) CHARACTER SET utf8 NOT NULL,
  `pincode` varchar(10) CHARACTER SET utf8 NOT NULL,
  `city` varchar(50) CHARACTER SET utf8 NOT NULL,
  `state` varchar(50) CHARACTER SET utf8 NOT NULL,
  `region` varchar(50) CHARACTER SET utf8 NOT NULL,
  `prepaid` tinyint(1) NOT NULL,
  `cod` tinyint(1) NOT NULL,
  `reverse_pickup` tinyint(1) NOT NULL,
  `pickup` tinyint(1) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_dotzot_pincodes`
--
ALTER TABLE `oc_dotzot_pincodes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_dotzot_pincodes`
--
ALTER TABLE `oc_dotzot_pincodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
