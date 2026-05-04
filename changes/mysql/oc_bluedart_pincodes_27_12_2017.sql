-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 27, 2017 at 12:21 PM
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
-- Table structure for table `oc_bluedart_pincodes`
--

CREATE TABLE `oc_bluedart_pincodes` (
  `id` int(11) NOT NULL,
  `carea` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `cscrcd` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `pincode` int(11) DEFAULT NULL,
  `city` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `bdel_loc` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `state` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `region` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `cloctype` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `bembargo` int(3) DEFAULT NULL,
  `surfacecod` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `surfacepp` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `dstarcd` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `newzone` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `sfccodval` decimal(12,4) DEFAULT NULL,
  `sfcpreval` decimal(12,4) DEFAULT NULL,
  `statecode` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `cservflag` varchar(20) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
