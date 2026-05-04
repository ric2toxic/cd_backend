-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2016 at 11:37 AM
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
-- Table structure for table `oc_wsb_job_applied`
--

CREATE TABLE `oc_wsb_job_applied` (
  `job_applied_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` int(11) NOT NULL,
  `resume` varchar(255) NOT NULL,
  `cover_letter` text NOT NULL,
  `ip` varchar(100) NOT NULL,
  `os` varchar(100) NOT NULL,
  `browser` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_job_applied`
--
ALTER TABLE `oc_wsb_job_applied`
  ADD PRIMARY KEY (`job_applied_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_wsb_job_applied`
--
ALTER TABLE `oc_wsb_job_applied`
  MODIFY `job_applied_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
