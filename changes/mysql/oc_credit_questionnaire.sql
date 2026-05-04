-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 18, 2019 at 12:38 PM
-- Server version: 5.7.23-0ubuntu0.18.04.1
-- PHP Version: 7.1.20-1+ubuntu18.04.1+deb.sury.org+1

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
-- Table structure for table `oc_credit_questionnaire`
--

CREATE TABLE `oc_credit_questionnaire` (
  `credit_questionnaire_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `options` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_credit_questionnaire`
--

INSERT INTO `oc_credit_questionnaire` (`credit_questionnaire_id`, `question`, `options`, `status`) VALUES
(14, 'What category of garments you are dealing in', 'Ladies,Kids,Gents', 1),
(15, 'Monthly turnover.', '', 1),
(16, '% of Cash Sales.', '', 1),
(17, 'Are you registered under GST and since when', '', 1),
(18, 'Location of Shop', 'Stand alone in residentialneighborhood,Stand alone in market,Shop in Market,Shop in mall,Commercial Complex', 1),
(19, 'shop owned or rented', 'owned,rented', 1),
(20, 'Do you have Swiping Machine, Which one', '', 1),
(21, 'Are you accepting wallet payments', 'Paytm,Mobikwik,Phone Pay', 1),
(22, 'How long you are in same business.', '', 1),
(23, 'How long the shop is opened at current location', '', 1),
(24, 'What size of your Shop (SqftxSqft).', '', 1),
(25, 'Have you taken loan or OD or CC limit from the bank before. If yes then Amount of loan or limit', '', 1),
(26, 'What are the your current sourcing locations', '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_credit_questionnaire`
--
ALTER TABLE `oc_credit_questionnaire`
  ADD PRIMARY KEY (`credit_questionnaire_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_credit_questionnaire`
--
ALTER TABLE `oc_credit_questionnaire`
  MODIFY `credit_questionnaire_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
