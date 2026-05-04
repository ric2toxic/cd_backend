-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 09, 2018 at 03:43 PM
-- Server version: 5.7.23-0ubuntu0.16.04.1
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_react_structure`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_credit_partners`
--

CREATE TABLE `oc_credit_partners` (
  `credit_partner_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_credit_partners`
--

INSERT INTO `oc_credit_partners` (`credit_partner_id`, `name`, `email`, `telephone`, `created`) VALUES
(1, 'ePayLater', 'epaylater@email.com', '', '2018-08-06 10:53:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_credit_partners`
--
ALTER TABLE `oc_credit_partners`
  ADD PRIMARY KEY (`credit_partner_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_credit_partners`
--
ALTER TABLE `oc_credit_partners`
  MODIFY `credit_partner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
