-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 09, 2017 at 02:37 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_dev_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_admin_product_change_log`
--

CREATE TABLE `oc_admin_product_change_log` (
  `product_id` int(11) NOT NULL,
  `field_name` enum('price','quantity','seller_tax','commission','tax_class_id','weight','title','description','model','sku','piece_in_set','set_description','exclusive','custom_rating','seller_change') NOT NULL,
  `old_value` text NOT NULL,
  `new_value` text NOT NULL,
  `date_added` datetime NOT NULL,
  `user` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
