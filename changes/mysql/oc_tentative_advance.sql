-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 15, 2017 at 04:29 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
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
-- Table structure for table `oc_tentative_advance`
--

CREATE TABLE `oc_tentative_advance` (
  `tentative_advance_id` int(15) NOT NULL,
  `order_id` int(8) NOT NULL,
  `payment_mode` enum('cash','cheque','neft','payment_link','paytm','upi') CHARACTER SET utf8 NOT NULL,
  `cheque_no` text CHARACTER SET utf8,
  `collection_date` date DEFAULT NULL,
  `deposit_date` date DEFAULT NULL,
  `txn_id` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `txn_date` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `notes` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `order_payment_id` int(11) DEFAULT NULL,
  `msgadmin` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `bank_deposited` tinyint(1) NOT NULL DEFAULT '0',
  `bank_deposited_image` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `staff_id` int(10) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `staff_detail` text CHARACTER SET utf8,
  `confirm` tinyint(1) NOT NULL DEFAULT '0',
  `confirm_user` text CHARACTER SET utf8,
  `transaction_status` enum('will_collect','will_deposit','deposited','will_transact','transacted') CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_tentative_advance`
--
ALTER TABLE `oc_tentative_advance`
  ADD PRIMARY KEY (`tentative_advance_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_tentative_advance`
--
ALTER TABLE `oc_tentative_advance`
  MODIFY `tentative_advance_id` int(15) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



ALTER TABLE `oc_tentative_advance` ADD `transaction_status` ENUM('will_collect','will_deposit','deposited','will_transact','transacted') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER`is_transacted`;



UPDATE
  oc_tentative_advance
SET
  transaction_status =(
    IF(
      payment_mode = 'cash' OR payment_mode = 'cheque',
      IF(
        is_collected = 1 AND is_deposited = 1,
        'deposited',
        IF(
          is_collected = 1,
          'will_deposit',
          IF(
            is_deposited = 1,
            'deposited',
            IF(
              is_collected = 0,
              'will_collect',
              'will_collect'
            )
          )
        )
      ),
      IF(
        is_transacted = 1,
        'transacted',
        'will_transact'
      )
    )
  )


  ALTER TABLE `oc_tentative_advance` CHANGE `payment_mode` `payment_mode` ENUM('cash','cheque','neft','payment_link','paytm','upi','credit') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

  ALTER TABLE `oc_tentative_advance` CHANGE `transaction_status` `transaction_status` ENUM('will_collect','will_deposit','deposited','will_transact','transacted','on_credit') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

  ALTER TABLE `oc_tentative_advance` ADD `dated` DATE NULL DEFAULT NULL AFTER `txn_id`;

  ALTER TABLE `oc_tentative_advance` ADD `branch_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `bank_deposited_image`;