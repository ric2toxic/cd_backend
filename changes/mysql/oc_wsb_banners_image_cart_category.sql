-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 19, 2019 at 03:43 PM
-- Server version: 5.7.23-0ubuntu0.18.04.1
-- PHP Version: 7.1.27-1+ubuntu18.04.1+deb.sury.org+1

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
-- Table structure for table `oc_wsb_banners_image_cart_category`
--

CREATE TABLE `oc_wsb_banners_image_cart_category` (
  `banners_image_cart_category_id` int(11) NOT NULL,
  `banner_image_id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `positive_category` text NOT NULL,
  `negative_category` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_banners_image_cart_category`
--
ALTER TABLE `oc_wsb_banners_image_cart_category`
  ADD PRIMARY KEY (`banners_image_cart_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_wsb_banners_image_cart_category`
--
ALTER TABLE `oc_wsb_banners_image_cart_category`
  MODIFY `banners_image_cart_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
