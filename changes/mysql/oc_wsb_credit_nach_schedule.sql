-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2019 at 09:51 PM
-- Server version: 5.7.24-0ubuntu0.16.04.1
-- PHP Version: 7.2.12-1+ubuntu16.04.1+deb.sury.org+1

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
-- Table structure for table `oc_wsb_credit_nach_schedule`
--

CREATE TABLE `oc_wsb_credit_nach_schedule` (
  `nach_schedule_id` int(11) NOT NULL,
  `suborder_id` varchar(20) NOT NULL,
  `nach_debit_date` date NOT NULL,
  `nach_debit_amount` decimal(10,2) NOT NULL,
  `date_added` date NOT NULL,
  `date_modified` date NOT NULL,
  `deffered_by_customer` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_wsb_credit_nach_schedule`
--
ALTER TABLE `oc_wsb_credit_nach_schedule`
  ADD PRIMARY KEY (`nach_schedule_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_wsb_credit_nach_schedule`
--
ALTER TABLE `oc_wsb_credit_nach_schedule`
  MODIFY `nach_schedule_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


ALTER TABLE `oc_wsb_credit_nach_schedule` CHANGE `status` `status` ENUM('NOT_DONE','BANK_SUCCESS','BANK_FAILURE','BANK_PENDING','SHIFT_SCHEDULE','SUDDEN_BANK_HOLIDAY') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'NOT_DONE';

ALTER TABLE `oc_wsb_credit_nach_schedule` ADD `customer_nach_details_id` INT(11) NULL DEFAULT NULL AFTER `status`;

ALTER TABLE `oc_wsb_credit_nach_schedule` ADD CONSTRAINT `fk_customer_nach_details_id` FOREIGN KEY (`customer_nach_details_id`) REFERENCES `oc_customer_nach_details`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;