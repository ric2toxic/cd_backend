-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 31, 2019 at 03:16 PM
-- Server version: 5.7.27-0ubuntu0.16.04.1
-- PHP Version: 7.1.30-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wholesalebox1`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_order_review`
--

CREATE TABLE `oc_order_review` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT '0',
  `comment` text,
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_order_review`
--
ALTER TABLE `oc_order_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_order_review`
--
ALTER TABLE `oc_order_review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;