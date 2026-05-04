-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 30, 2017 at 12:31 PM
-- Server version: 5.5.50-0ubuntu0.14.04.1-log
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
-- Table structure for table `oc_vat_input_rules`
--

CREATE TABLE IF NOT EXISTS `oc_vat_input_rules` (
  `rule_id` int(8) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `input_type` enum('input_available','no_input') NOT NULL DEFAULT 'no_input',
  `default_commission` decimal(8,4) NOT NULL DEFAULT '10.0000',
  `purchase_firm_name` varchar(64) NOT NULL,
  `purchase_firm_address1` varchar(64) NOT NULL,
  `purchase_firm_address2` varchar(64) NOT NULL,
  `purchase_firm_city` varchar(32) NOT NULL,
  `purchase_firm_pincode` mediumint(6) unsigned NOT NULL,
  `purchase_firm_tin_no` varchar(16) CHARACTER SET utf16 NOT NULL,
  `date_begin` date NULL,
  `date_end` date NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rule_id`),
  KEY `zone_id` (`zone_id`,`input_type`,`date_begin`,`date_end`,`status`,`default`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
