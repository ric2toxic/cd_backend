-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2016 at 09:24 AM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wholesalebox_16`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_wsb_extension_to_store`
--

CREATE TABLE IF NOT EXISTS `oc_wsb_extension_to_store` (
  `extension_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_wsb_extension_to_store`
--

INSERT INTO `oc_wsb_extension_to_store` (`extension_id`, `store_id`) VALUES
(23, 0),
(437, 0),
(437, 2),
(441, 0),
(443, 0),
(452, 0),
(452, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_extension_to_store`
--
ALTER TABLE `oc_wsb_extension_to_store`
  ADD PRIMARY KEY (`extension_id`,`store_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
