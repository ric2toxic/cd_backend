-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 21, 2017 at 07:39 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.13-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_dev_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_pickup_zone`
--

CREATE TABLE `oc_pickup_zone` (
  `pickup_zone_id` int(11) NOT NULL,
  `pickup_city_code` varchar(2) NOT NULL,
  `pickup_zone_name` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_pickup_zone`
--
ALTER TABLE `oc_pickup_zone`
  ADD PRIMARY KEY (`pickup_zone_id`),
  ADD KEY `pickup_city_code` (`pickup_city_code`);
ALTER TABLE `oc_pickup_zone` ADD FULLTEXT KEY `ull` (`pickup_zone_name`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
