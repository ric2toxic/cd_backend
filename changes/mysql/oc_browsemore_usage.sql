-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 21, 2016 at 05:13 PM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.17

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
-- Table structure for table `oc_browsemore_usage`
--

CREATE TABLE IF NOT EXISTS `oc_browsemore_usage` (
  `usage_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL COMMENT 'unique session id',
  `customer_id` int(11) NOT NULL COMMENT 'if customer signs up or logs in',
  `order_id` int(11) NOT NULL COMMENT 'if existing session is converted into order',
  `category_id` int(11) NOT NULL COMMENT 'category on which browse more is clicked',
  `no_clicks` int(11) NOT NULL COMMENT 'number of clicks done in the session for the category',
  PRIMARY KEY (`usage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='User behaviour about how many designs he browses generally in one sessio' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
