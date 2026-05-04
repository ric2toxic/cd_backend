-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 28, 2019 at 10:59 AM
-- Server version: 5.7.27-0ubuntu0.16.04.1
-- PHP Version: 7.1.32-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `oc_wsb_route_profiles`
--

CREATE TABLE `oc_wsb_route_profiles` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `path` varchar(250) NOT NULL,
  `route` varchar(250) NOT NULL,
  `get_params` JSON NOT NULL,
  `post_params` JSON NOT NULL,
  `response_time_in_ms` mediumint(8) UNSIGNED NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_route_profiles`
--
ALTER TABLE `oc_wsb_route_profiles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_wsb_route_profiles`
--
ALTER TABLE `oc_wsb_route_profiles`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
