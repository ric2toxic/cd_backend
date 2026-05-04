-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2019 at 06:51 PM
-- Server version: 5.7.25-0ubuntu0.16.04.2
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
-- Table structure for table `oc_truxcargo_pincodes`
--

CREATE TABLE `oc_truxcargo_pincodes` (
  `id` int(11) NOT NULL,
  `pincode` int(10) DEFAULT NULL,
  `product_type` varchar(100) DEFAULT NULL,
  `center` varchar(100) DEFAULT NULL,
  `facility_type` varchar(10) DEFAULT NULL,
  `weight_limit` varchar(50) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_truxcargo_pincodes`
--
ALTER TABLE `oc_truxcargo_pincodes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_truxcargo_pincodes`
--
ALTER TABLE `oc_truxcargo_pincodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
