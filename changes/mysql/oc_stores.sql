-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 11, 2017 at 07:06 PM
-- Server version: 5.5.50-0ubuntu0.14.04.1-log
-- PHP Version: 5.5.9-1ubuntu4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wholesalebox`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_stores`
--

CREATE TABLE IF NOT EXISTS `oc_stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `telephone` varchar(11) CHARACTER SET utf8 NOT NULL,
  `mobile` varchar(10) CHARACTER SET utf8 NOT NULL,
  `contact_person_name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `store_lat` varchar(50) CHARACTER SET utf8 NOT NULL,
  `store_lng` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `store_link` text NOT NULL, 
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
