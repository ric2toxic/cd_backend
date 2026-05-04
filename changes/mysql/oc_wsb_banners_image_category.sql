-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 05, 2017 at 06:31 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

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
-- Table structure for table `oc_wsb_banners_image_category`
--

CREATE TABLE `oc_wsb_banners_image_category` (
  `banners_image_category_id` int(11) NOT NULL,
  `banner_image_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_banners_image_category`
--
ALTER TABLE `oc_wsb_banners_image_category`
  ADD PRIMARY KEY (`banners_image_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_wsb_banners_image_category`
--
ALTER TABLE `oc_wsb_banners_image_category`
  MODIFY `banners_image_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=705;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
