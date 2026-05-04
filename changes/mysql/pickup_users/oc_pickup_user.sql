-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 23, 2017 at 11:13 AM
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
-- Table structure for table `oc_pickup_user`
--

CREATE TABLE `oc_pickup_user` (
  `pickup_user_id` int(11) NOT NULL,
  `username` varchar(12) NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `mobile_no` varchar(10) DEFAULT NULL,
  `alternate_mobile_no` varchar(10) DEFAULT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `access_token` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0: disabled, 1:active',
  `default_pickup_city_code` varchar(2) NOT NULL,
  `pickup_zone_ids` varchar(32) NOT NULL,
  `imei_code` varchar(20) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_pickup_user`
--
ALTER TABLE `oc_pickup_user`
  ADD PRIMARY KEY (`pickup_user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `mobile_no` (`mobile_no`),
  ADD UNIQUE KEY `alternate_mobile_no` (`alternate_mobile_no`),
  ADD KEY `status` (`status`,`default_pickup_city_code`,`pickup_zone_ids`);
ALTER TABLE `oc_pickup_user` ADD FULLTEXT KEY `firstname` (`firstname`,`lastname`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_pickup_user`
--
ALTER TABLE `oc_pickup_user`
  MODIFY `pickup_user_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
