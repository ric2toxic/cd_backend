-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 18, 2017 at 06:13 PM
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
-- Table structure for table `oc_category_images`
--

CREATE TABLE `oc_category_images` (
  `category_image_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image_height` varchar(6) CHARACTER SET utf8 NOT NULL,
  `image_width` varchar(6) CHARACTER SET utf8 NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sort_order` tinyint(4) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_category_images`
--
ALTER TABLE `oc_category_images`
  ADD PRIMARY KEY (`category_image_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_category_images`
--
ALTER TABLE `oc_category_images`
  MODIFY `category_image_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
