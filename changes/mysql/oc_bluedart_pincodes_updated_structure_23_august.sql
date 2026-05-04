-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 26, 2019 at 12:09 PM
-- Server version: 5.7.27-0ubuntu0.16.04.1
-- PHP Version: 7.1.22-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `oc_bluedart_pincodes`
--

CREATE TABLE `oc_bluedart_pincodes` (
  `id` int(11) NOT NULL,
  `cscrcd` varchar(20) DEFAULT NULL,
  `pincode` int(11) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `bdel_loc` varchar(150) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `cod` varchar(20) DEFAULT NULL,
  `prepaid` varchar(20) DEFAULT NULL,
  `mode` varchar(25) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_bluedart_pincodes`
--
ALTER TABLE `oc_bluedart_pincodes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_bluedart_pincodes`
--
ALTER TABLE `oc_bluedart_pincodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
