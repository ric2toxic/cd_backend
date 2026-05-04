-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 16, 2019 at 04:20 PM
-- Server version: 5.7.27-0ubuntu0.16.04.1
-- PHP Version: 7.1.22-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `similar_seller_cluster_sellers`
--

CREATE TABLE `similar_seller_cluster_sellers` (
  `similar_seller_cluster_id` tinyint(255) UNSIGNED NOT NULL,
  `seller_id` int(255) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `similar_seller_cluster_sellers`
--
ALTER TABLE `similar_seller_cluster_sellers`
  ADD PRIMARY KEY (`similar_seller_cluster_id`,`seller_id`) USING BTREE;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `similar_seller_cluster_sellers`
--
ALTER TABLE `similar_seller_cluster_sellers`
  ADD CONSTRAINT `fk_similar_seller_cluster_id` FOREIGN KEY (`similar_seller_cluster_id`) REFERENCES `similar_seller_cluster` (`similar_seller_cluster_id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
