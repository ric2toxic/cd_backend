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
-- Table structure for table `similar_seller_cluster`
--

CREATE TABLE `similar_seller_cluster` (
  `similar_seller_cluster_id` tinyint(255) UNSIGNED NOT NULL,
  `cluster_name` varchar(255) NOT NULL,
  `category_id` smallint(255) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `similar_seller_cluster`
--
ALTER TABLE `similar_seller_cluster`
  ADD PRIMARY KEY (`similar_seller_cluster_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `similar_seller_cluster`
--
ALTER TABLE `similar_seller_cluster`
  MODIFY `similar_seller_cluster_id` tinyint(255) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
