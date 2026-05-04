-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost:127.0.0.1
-- Generation Time: Apr 05, 2017 at 05:04 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
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
-- Table structure for table `oc_search_dictionary`
--

CREATE TABLE `oc_search_dictionary` (
  `id` int(11) NOT NULL,
  `word` varchar(150) NOT NULL,
  `synonyms` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_search_dictionary`
--
ALTER TABLE `oc_search_dictionary`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `oc_search_dictionary` ADD FULLTEXT KEY `synonyms` (`synonyms`);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
