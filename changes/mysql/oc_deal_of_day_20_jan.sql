-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2016 at 07:15 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wsb`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_deal_of_day`
--

CREATE TABLE `oc_deal_of_day` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `order_amount` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_deal_of_day`
--

INSERT INTO `oc_deal_of_day` (`id`, `seller_id`, `order_amount`, `discount`, `start_date`, `end_date`, `status`, `created`, `modified`) VALUES
(1, 16, 10000, 5, '2016-01-18 14:18:00', '2016-01-18 14:18:00', 1, '2016-01-18 10:01:55', '0000-00-00 00:00:00'),
(2, 16, 15000, 7, '2016-01-18 14:18:00', '2016-01-18 14:18:00', 1, '2016-01-18 10:01:55', '0000-00-00 00:00:00'),
(3, 16, 20000, 10, '2016-01-18 14:19:00', '2016-01-18 14:19:00', 0, '2016-01-18 10:01:55', '0000-00-00 00:00:00'),
(4, 16, 25000, 15, '2016-01-18 14:19:00', '2016-01-18 14:19:00', 1, '2016-01-18 10:01:55', '0000-00-00 00:00:00'),
(5, 16, 10000, 5, '2016-01-18 18:59:00', '2016-01-18 18:59:00', 1, '2016-01-18 14:29:16', '0000-00-00 00:00:00'),
(6, 16, 5000, 5, '2016-01-19 15:19:00', '2016-01-19 15:19:00', 1, '2016-01-19 10:50:03', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_deal_of_day`
--
ALTER TABLE `oc_deal_of_day`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_deal_of_day`
--
ALTER TABLE `oc_deal_of_day`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
