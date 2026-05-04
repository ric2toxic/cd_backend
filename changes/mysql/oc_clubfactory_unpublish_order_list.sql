-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 24, 2019 at 12:28 PM
-- Server version: 5.7.25-0ubuntu0.16.04.2
-- PHP Version: 7.1.25-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox1`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_clubfactory_unpublish_order_list`
--

CREATE TABLE `oc_clubfactory_unpublish_order_list` (
  `id` int(11) NOT NULL,
  `order_number` varchar(100) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0=unpublish,1=Order_placed',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_clubfactory_unpublish_order_list`
--
ALTER TABLE `oc_clubfactory_unpublish_order_list`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_clubfactory_unpublish_order_list`
--
ALTER TABLE `oc_clubfactory_unpublish_order_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
