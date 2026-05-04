-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 18, 2019 at 03:57 PM
-- Server version: 5.7.27-0ubuntu0.16.04.1
-- PHP Version: 7.1.32-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wholesalebox1`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_order_review_reasons_list`
--

CREATE TABLE `oc_order_review_reasons_list` (
  `id` int(11) NOT NULL,
  `reasons` text NOT NULL,
  `type` enum('positive','negative') DEFAULT NULL,
  `show_comment` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_order_review_reasons_list`
--

INSERT INTO `oc_order_review_reasons_list` (`id`, `reasons`, `type`, `show_comment`, `status`) VALUES
(1, 'Product manufacturing quality', 'positive', 0, 1),
(2, 'Product pricing', 'positive', 0, 1),
(3, 'Order payment', 'positive', 0, 1),
(4, 'Product variety availability', 'positive', 0, 1),
(5, 'Sales support', 'positive', 0, 1),
(6, 'Order dispatch/delivery', 'positive', 0, 1),
(7, 'Shipping charges', 'positive', 0, 1),
(8, 'Product return policy', 'positive', 0, 1),
(9, 'Return/Refund', 'positive', 0, 1),
(10, 'Mobile app functionality', 'positive', 0, 1),
(11, 'Other', 'positive', 1, 1),
(12, 'Product manufacturing quality', 'negative', 0, 1),
(13, 'Product pricing', 'negative', 0, 1),
(14, 'Order payment', 'negative', 0, 1),
(15, 'Product variety availability', 'negative', 0, 1),
(16, 'Sales support', 'negative', 0, 1),
(17, 'Order dispatch/delivery', 'negative', 0, 1),
(18, 'Shipping charges', 'negative', 0, 1),
(19, 'Product return policy', 'negative', 0, 1),
(20, 'Return/Refund', 'negative', 0, 1),
(21, 'Mobile app functionality', 'negative', 0, 1),
(22, 'Other', 'negative', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_order_review_reasons_list`
--
ALTER TABLE `oc_order_review_reasons_list`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_order_review_reasons_list`
--
ALTER TABLE `oc_order_review_reasons_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
