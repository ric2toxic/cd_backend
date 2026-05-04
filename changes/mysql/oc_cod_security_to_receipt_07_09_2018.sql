-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 27, 2018 at 03:06 PM
-- Server version: 5.7.23-0ubuntu0.16.04.1
-- PHP Version: 7.1.20-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `oc_cod_security_to_receipt`
--

CREATE TABLE `oc_cod_security_to_receipt` (
  `id` int(11) NOT NULL,
  `master_id` int(11) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `table_id` int(11) NOT NULL,
  `credit_note_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_cod_security_to_receipt`
--
ALTER TABLE `oc_cod_security_to_receipt`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `master_id` (`master_id`,`table_name`,`table_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_cod_security_to_receipt`
--
ALTER TABLE `oc_cod_security_to_receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
