-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2016 at 10:41 AM
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
-- Table structure for table `oc_product_status`
--

CREATE TABLE `oc_product_status` (
  `product_status_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_product_status`
--

INSERT INTO `oc_product_status` (`product_status_id`, `language_id`, `name`) VALUES
(0, 1, 'Disabled'),
(1, 1, 'Enabled'),
(2, 1, 'To Moderate'),
(3, 1, 'Disabled by Seller'),
(4, 1, 'Rejected');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_product_status`
--
ALTER TABLE `oc_product_status`
  ADD PRIMARY KEY (`product_status_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
