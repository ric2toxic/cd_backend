-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 02, 2017 at 03:00 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_group`
--

CREATE TABLE `oc_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_group`
--

INSERT INTO `oc_group` (`group_id`, `group_name`) VALUES
(1, 'COD'),
(2, 'Payment Gateway'),
(3, 'Direct Sales'),
(4, 'Buyer\'s Refund'),
(5, 'Suspense'),
(6, 'Employees'),
(7, 'Sales'),
(8, 'Bank Account'),
(9, 'Fixed Assets'),
(10, 'Sundry Debtors'),
(11, 'Sundry Creditors'),
(12, 'Expenses'),
(13, 'Incomes'),
(14, 'Investment'),
(15, 'Advances'),
(16, 'Duties & Taxes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_group`
--
ALTER TABLE `oc_group`
  ADD PRIMARY KEY (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_group`
--
ALTER TABLE `oc_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
