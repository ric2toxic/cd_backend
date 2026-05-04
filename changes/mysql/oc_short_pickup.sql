-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 26, 2016 at 11:28 AM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.16

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
-- Table structure for table `oc_short_pickup`
--

CREATE TABLE IF NOT EXISTS `oc_short_pickup` (
  `order_product_id` int(11) NOT NULL,
  `pieces_not_given` int(8) NOT NULL COMMENT 'Number of pieces for this product in this order not given by the seller',
  `comment` text NOT NULL COMMENT 'Comments about short supply such as what sizes are not given etc.',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`order_product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Short supply given by the sellers for an order';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
