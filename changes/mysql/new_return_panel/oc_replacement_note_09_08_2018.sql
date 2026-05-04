-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 14, 2018 at 05:07 PM
-- Server version: 5.7.23-0ubuntu0.16.04.1
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

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
-- Table structure for table `oc_replacement_note`
--

CREATE TABLE `oc_replacement_note` (
  `replacement_note_id` int(11) NOT NULL,
  `replacement_note_prefix` varchar(32) NOT NULL,
  `replacement_note_no` int(8) UNSIGNED NOT NULL,
  `replacement_note_amount` decimal(10,2) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `user` varchar(32) NOT NULL,
  `file_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_replacement_note`
--
ALTER TABLE `oc_replacement_note`
  ADD PRIMARY KEY (`replacement_note_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_replacement_note`
--
ALTER TABLE `oc_replacement_note`
  MODIFY `replacement_note_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
