-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 16, 2017 at 04:35 PM
-- Server version: 5.7.18-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

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
-- Table structure for table `oc_image_upload`
--

CREATE TABLE `oc_image_upload` (
  `upload_id` int(10) NOT NULL,
  `seller_id` int(10) NOT NULL,
  `user` varchar(255) CHARACTER SET utf8 NOT NULL,
  `date_added` date NOT NULL,
  `original_file` varchar(128) CHARACTER SET utf8 NOT NULL,
  `original_size` decimal(10,2) NOT NULL,
  `new_file` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `new_size` decimal(10,2) NOT NULL,
  `status` enum('uploaded','rejected') CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_image_upload`
--
ALTER TABLE `oc_image_upload`
  ADD PRIMARY KEY (`upload_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_image_upload`
--
ALTER TABLE `oc_image_upload`
  MODIFY `upload_id` int(10) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
