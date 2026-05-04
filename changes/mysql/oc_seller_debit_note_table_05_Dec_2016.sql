-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 05, 2016 at 02:40 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_old`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_seller_debit_note`
--

CREATE TABLE `oc_seller_debit_note` (
  `return_ids` varchar(60) CHARACTER SET utf8 NOT NULL,
  `order_id` int(11) NOT NULL,
  `suborder_id` varchar(32) CHARACTER SET utf8 NOT NULL,
  `seller_id` int(11) NOT NULL,
  `debit_note_prefix` varchar(32) CHARACTER SET utf8 NOT NULL,
  `debit_note_no` int(8) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_seller_debit_note`
--
ALTER TABLE `oc_seller_debit_note`
  ADD UNIQUE KEY `return_ids` (`return_ids`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
