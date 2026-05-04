-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 08, 2017 at 12:55 PM
-- Server version: 5.7.20-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

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
-- Table structure for table `oc_warehouse_address`
--

CREATE TABLE `oc_warehouse_address` (
  `warehouse_id` int(11) NOT NULL,
  `warehouse_name` varchar(64) NOT NULL,
  `gstin` varchar(64) DEFAULT NULL,
  `address_1` varchar(64) NOT NULL,
  `address_2` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `telephone` varchar(64) NOT NULL,
  `gati_vendor_code` varchar(8) NOT NULL,
  `nuvoex_vendor_code` varchar(100) DEFAULT NULL,
  `bluedart_vendor_code` varchar(64) DEFAULT NULL,
  `bluedart_surface_customer_code` int(11) DEFAULT NULL,
  `bluedart_apex_customer_code` int(11) DEFAULT NULL,
  `bluedart_origin_area` varchar(64) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table storing the warehouse addresses generally needed in a FROM section of shipping label';

--
-- Dumping data for table `oc_warehouse_address`
--

INSERT INTO `oc_warehouse_address` (`warehouse_id`, `warehouse_name`, `gstin`, `address_1`, `address_2`, `city`, `postcode`, `zone_id`, `country_id`, `telephone`, `gati_vendor_code`, `nuvoex_vendor_code`, `bluedart_vendor_code`, `bluedart_surface_customer_code`, `bluedart_apex_customer_code`, `bluedart_origin_area`, `email`, `status`) VALUES
(1, 'WholesaleBox Internet Pvt Ltd (JP)', '08AABCW7022Q1ZU', 'B-1, Crystal Mall', 'Banipark', 'Jaipur', '302016', 1501, 99, '(+91) 141 4049163', 'JAICWC', 'NUVO-JP1', 'WHOL11', 337654, 335296, 'JAI', 'operations@wholesalebox.in', 1),
(2, 'WholesaleBox Internet Pvt Ltd (001_ST)', NULL, 'B-1, Crystal Mall', 'Banipark', 'Jaipur', '302016', 1501, 99, '(+91) 141 4049163', 'SRT', NULL, NULL, NULL, NULL, NULL, 'operations@wholesalebox.in', 0),
(3, 'WholesaleBox Internet Pvt Ltd (002_ST)', NULL, 'B-1, Crystal Mall', 'Banipark', 'Jaipur', '302016', 1501, 99, '(+91) 141 4049163', 'SRT1', NULL, NULL, NULL, NULL, NULL, 'operations@wholesalebox.in', 0),
(4, 'Shree Kurti', NULL, 'WZ-72, Main Road', 'Todapur', 'Delhi', '110012', 1483, 99, '9810013086', '', NULL, NULL, NULL, NULL, NULL, 'operations@wholesalebox.in', 0),
(5, 'WholesaleBox Internet Pvt Ltd (ST)', NULL, 'G-19, Raghuvir Textile Mall', 'Patiya, Behind D.R. World Mall', 'Surat', '395010', 1485, 99, '(+91) 141 4049163', 'SRT2', NULL, NULL, NULL, NULL, NULL, 'operations@wholesalebox.in', 0),
(6, 'WholesaleBox Internet Pvt Ltd (ST)', '24AABCW7022Q1Z0', 'A 2009,10', 'Millennium Textile Market, Ring Road', 'Surat', '395002', 1485, 99, '(+91) 141 4049163', 'SRT2', 'NUVO-ST1', 'SUR111', 243154, 243154, 'SUR', 'operations@wholesalebox.in', 1),
(7, 'WholesaleBox Internet Pvt Ltd (DL)', '07AABCW7022Q1ZW', 'F-45, Old Double Storey, Block-F', 'Amar Colony Market, Lajpat Nagar 4', 'New Delhi', '110024', 1483, 99, '(+91) 011 - 4101 0196', 'DEL', 'NUVO-DL1', 'WS1317', 463481, 463610, 'DEL', 'operations@wholesalebox.in', 1),
(8, 'WholesaleBox Internet Pvt Ltd (MU)', '27AABCW7022Q1ZU', '30, Bhoomi Plaza, Ground floor', 'Matkar Marg, Masjid Gali, Dadar West', 'Mumbai', '400028', 1493, 99, '(+91) 97394 88453', 'WHBOM', 'NUVO-MU1', 'MUM111', 388780, 388791, 'MUM', 'operations@wholesalebox.in', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_warehouse_address`
--
ALTER TABLE `oc_warehouse_address`
  ADD PRIMARY KEY (`warehouse_id`),
  ADD KEY `status` (`status`);
ALTER TABLE `oc_warehouse_address` ADD FULLTEXT KEY `warehouse_name` (`warehouse_name`,`city`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
