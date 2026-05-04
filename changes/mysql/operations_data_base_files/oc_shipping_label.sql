-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 16, 2016 at 09:13 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wholesalebox_operations`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_shipping_label`
--

CREATE TABLE `oc_shipping_label` (
  `shipping_label_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `suborder_id` varchar(20) NOT NULL,
  `from_warehouse_id` int(11) NOT NULL,
  `courier_name` varchar(64) NOT NULL,
  `tracking_no` varchar(64) NOT NULL,
  `weight` decimal(15,8) NOT NULL,
  `file_name` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table storing information about shipping labels generated';

--
-- Dumping data for table `oc_shipping_label`
--

INSERT INTO `oc_shipping_label` (`shipping_label_id`, `order_id`, `suborder_id`, `from_warehouse_id`, `courier_name`, `tracking_no`, `weight`, `file_name`) VALUES
(1, 6569, '6569', 1, 'gati', '588599727', '1.00000000', ''),
(7, 6397, '6397', 3, 'gati', '588599789', '7.00000000', '../download/shipping_label/19_Oct_16/20161006903_gati_588599789.pdf'),
(2, 6504, '6504', 1, 'gati', '588599734', '2.00000000', ''),
(8, 6498, '6498', 3, 'gati', '588599796', '7.00000000', '../download/shipping_label/19_Oct_16/20161006999_gati_588599796.pdf'),
(9, 6537, '6537', 3, 'gati', '588599802', '6.00000000', '../download/shipping_label/19_Oct_16/20161007038_gati_588599802.pdf'),
(3, 6329, '6329', 3, 'gati', '588599772', '7.00000000', '../download/shipping_label/19_Oct_16/20161006835_gati_588599772.pdf'),
(10, 6314, '6314', 3, 'gati', '588599819', '4.00000000', '../download/shipping_label/19_Oct_16/20161006822_gati_588599819.pdf'),
(11, 6533, '6533', 3, 'gati', '588599826', '4.00000000', '../download/shipping_label/19_Oct_16/20161007034_gati_588599826.pdf'),
(13, 6324, '6324', 3, 'gati', '588599840', '4.00000000', '../download/shipping_label/19_Oct_16/20161006830_gati_588599840.pdf'),
(14, 6582, '6582', 3, 'gati', '588599857', '5.00000000', '../download/shipping_label/19_Oct_16/20161007083_gati_588599857.pdf'),
(15, 5699, '5699', 1, 'gati', '588599864', '3.00000000', '../download/shipping_label/19_Oct_16/20160906264_gati_588599864.pdf'),
(16, 6583, '6583', 1, 'gati', '588599871', '6.50000000', '../download/shipping_label/19_Oct_16/20161007084_gati_588599871.pdf'),
(17, 6595, '6595', 1, 'gati', '588599888', '4.00000000', '../download/shipping_label/19_Oct_16/20161007096_gati_588599888.pdf'),
(18, 6445, '6445', 2, 'gati', '588599895', '7.00000000', '../download/shipping_label/20_Oct_16/20161006950_gati_588599895.pdf'),
(19, 6492, '6492', 2, 'gati', '588599901', '4.00000000', '../download/shipping_label/20_Oct_16/20161006993_gati_588599901.pdf'),
(20, 6530, '6530', 2, 'gati', '588599918', '8.00000000', '../download/shipping_label/20_Oct_16/20161007031_gati_588599918.pdf'),
(21, 6493, '6493', 2, 'gati', '588599925', '3.00000000', '../download/shipping_label/20_Oct_16/20161006994_gati_588599925.pdf'),
(22, 6567, '6567', 2, 'gati', '588599932', '4.00000000', '../download/shipping_label/20_Oct_16/20161007068_gati_588599932.pdf'),
(23, 6437, '6437', 2, 'gati', '588599949', '8.00000000', '../download/shipping_label/20_Oct_16/20161006942_gati_588599949.pdf'),
(24, 6522, '6522', 2, 'gati', '588599956', '8.00000000', '../download/shipping_label/20_Oct_16/20161007023_gati_588599956.pdf'),
(25, 6596, '6596', 2, 'gati', '588599963', '6.00000000', '../download/shipping_label/20_Oct_16/20161007097_gati_588599963.pdf'),
(26, 6576, '6576', 2, 'gati', '588599970', '5.00000000', '../download/shipping_label/20_Oct_16/20161007077_gati_588599970.pdf'),
(27, 6547, '6547', 2, 'gati', '588599987', '6.00000000', '../download/shipping_label/20_Oct_16/20161007048_gati_588599987.pdf'),
(28, 6528, '6528', 2, 'gati', '588599994', '5.00000000', '../download/shipping_label/20_Oct_16/20161007029_gati_588599994.pdf'),
(29, 6603, '6603', 3, 'gati', '588600003', '4.00000000', '../download/shipping_label/20_Oct_16/20161007104_gati_588600003.pdf'),
(30, 6547, '6547', 3, 'gati', '588600010', '2.00000000', '../download/shipping_label/20_Oct_16/20161007048_gati_588600010.pdf'),
(31, 6538, '6538', 3, 'gati', '588600027', '3.00000000', '../download/shipping_label/20_Oct_16/20161007039_gati_588600027.pdf'),
(32, 6323, '6323', 2, 'gati', '588600034', '4.00000000', '../download/shipping_label/20_Oct_16/20161006829_gati_588600034.pdf'),
(33, 6493, '6493', 1, 'gati', '588600041', '4.00000000', '../download/shipping_label/20_Oct_16/20161006994_gati_588600041.pdf'),
(34, 6593, '6593', 1, 'gati', '588600058', '4.50000000', '../download/shipping_label/20_Oct_16/20161007094_gati_588600058.pdf'),
(35, 6640, '6640', 2, 'gati', '588600065', '7.00000000', '../download/shipping_label/21_Oct_16/20161007139_gati_588600065.pdf'),
(36, 6494, '6494', 2, 'gati', '588600072', '5.00000000', '../download/shipping_label/21_Oct_16/20161006995_gati_588600072.pdf'),
(37, 6373, '6373', 2, 'gati', '588600089', '5.00000000', '../download/shipping_label/21_Oct_16/20161006879_gati_588600089.pdf'),
(38, 6575, '6575', 2, 'gati', '588600096', '7.00000000', '../download/shipping_label/21_Oct_16/20161007076_gati_588600096.pdf'),
(39, 6364, '6364', 3, 'gati', '588600102', '7.00000000', '../download/shipping_label/21_Oct_16/20161006870_gati_588600102.pdf'),
(40, 6600, '6600', 2, 'gati', '588600119', '6.00000000', '../download/shipping_label/21_Oct_16/20161007101_gati_588600119.pdf'),
(41, 6615, '6615', 1, 'gati', '588600126', '6.50000000', '../download/shipping_label/21_Oct_16/20161007116_gati_588600126.pdf'),
(42, 6519, '6519', 2, 'gati', '588600133', '4.00000000', '../download/shipping_label/22_Oct_16/20161007020_gati_588600133.pdf'),
(43, 6630, '6630', 2, 'gati', '588600140', '4.00000000', '../download/shipping_label/22_Oct_16/20161007129_gati_588600140.pdf'),
(44, 6630, '6630', 3, 'gati', '588600157', '5.00000000', '../download/shipping_label/22_Oct_16/20161007129_gati_588600157.pdf'),
(45, 6620, '6620', 1, 'gati', '588600164', '3.00000000', '../download/shipping_label/22_Oct_16/20161007121_gati_588600164.pdf'),
(46, 6644, '6644', 1, 'gati', '588600171', '7.00000000', '../download/shipping_label/22_Oct_16/20161007143_gati_588600171.pdf'),
(47, 6651, '6651', 1, 'gati', '588600188', '7.00000000', '../download/shipping_label/22_Oct_16/20161007150_gati_588600188.pdf'),
(48, 6659, '6659', 1, 'gati', '588600195', '3.00000000', '../download/shipping_label/22_Oct_16/20161007158_gati_588600195.pdf'),
(49, 5904, '5904', 2, 'gati', '588600201', '5.00000000', '../download/shipping_label/24_Oct_16/20160906468_gati_588600201.pdf'),
(50, 6288, '6288', 2, 'gati', '588600218', '5.00000000', '../download/shipping_label/24_Oct_16/20161006804_gati_588600218.pdf'),
(51, 6689, '6689', 3, 'gati', '588600225', '5.00000000', '../download/shipping_label/24_Oct_16/20161007188_gati_588600225.pdf'),
(52, 6678, '6678', 3, 'gati', '588600232', '3.00000000', '../download/shipping_label/24_Oct_16/20161007177_gati_588600232.pdf'),
(53, 6572, '6572', 3, 'gati', '588600249', '5.00000000', '../download/shipping_label/24_Oct_16/20161007073_gati_588600249.pdf'),
(54, 6547, '6547', 3, 'gati', '588600256', '5.00000000', '../download/shipping_label/24_Oct_16/20161007048_gati_588600256.pdf'),
(55, 6604, '6604', 1, 'gati', '588600263', '2.00000000', '../download/shipping_label/24_Oct_16/20161007105_gati_588600263.pdf'),
(56, 6674, '6674', 1, 'gati', '588600270', '4.00000000', '../download/shipping_label/24_Oct_16/20161007173_gati_588600270.pdf'),
(57, 6678, '6678', 2, 'gati', '588600287', '4.00000000', '../download/shipping_label/25_Oct_16/20161007177_gati_588600287.pdf'),
(58, 6690, '6690', 3, 'gati', '588600294', '5.00000000', '../download/shipping_label/25_Oct_16/20161007189_gati_588600294.pdf'),
(59, 6693, '6693', 3, 'gati', '588600300', '5.00000000', '../download/shipping_label/25_Oct_16/20161007192_gati_588600300.pdf'),
(60, 6698, '6698', 3, 'gati', '588600317', '5.00000000', '../download/shipping_label/25_Oct_16/20161007197_gati_588600317.pdf'),
(61, 6638, '6638', 3, 'gati', '588600324', '5.00000000', '../download/shipping_label/25_Oct_16/20161007137_gati_588600324.pdf'),
(62, 6650, '6650', 1, 'gati', '588600331', '7.80000000', '../download/shipping_label/25_Oct_16/20161007149_gati_588600331.pdf'),
(63, 6652, '6652', 1, 'gati', '588600348', '4.00000000', '../download/shipping_label/25_Oct_16/20161007151_gati_588600348.pdf'),
(64, 6498, '6498', 2, 'gati', '588600355', '6.00000000', '../download/shipping_label/26_Oct_16/20161006999_gati_588600355.pdf'),
(65, 6689, '6689', 2, 'gati', '588600362', '3.00000000', '../download/shipping_label/26_Oct_16/20161007188_gati_588600362.pdf'),
(66, 6742, '6742', 3, 'gati', '588600379', '4.00000000', '../download/shipping_label/26_Oct_16/20161007240_gati_588600379.pdf'),
(67, 6747, '6747', 3, 'gati', '588600386', '7.00000000', '../download/shipping_label/26_Oct_16/20161007245_gati_588600386.pdf'),
(68, 6722, '6722', 3, 'gati', '588600393', '5.00000000', '../download/shipping_label/26_Oct_16/20161007220_gati_588600393.pdf'),
(69, 6749, '6749', 1, 'gati', '588600409', '2.00000000', '../download/shipping_label/26_Oct_16/20161007247_gati_588600409.pdf'),
(70, 6745, '6745', 1, 'gati', '588600416', '6.00000000', '../download/shipping_label/26_Oct_16/20161007243_gati_588600416.pdf'),
(71, 6713, '6713', 1, 'gati', '588600423', '8.00000000', '../download/shipping_label/26_Oct_16/20161007212_gati_588600423.pdf'),
(72, 6668, '6668', 1, 'gati', '588600430', '7.50000000', '../download/shipping_label/26_Oct_16/20161007167_gati_588600430.pdf'),
(73, 6456, '6456', 1, 'gati', '588600447', '8.50000000', '../download/shipping_label/26_Oct_16/20161006961_gati_588600447.pdf'),
(74, 6741, '6741', 1, 'gati', '588600454', '8.00000000', '../download/shipping_label/26_Oct_16/20161007239_gati_588600454.pdf'),
(75, 6718, '6718', 3, 'gati', '588600461', '4.00000000', '../download/shipping_label/27_Oct_16/20161007217_gati_588600461.pdf'),
(76, 6705, '6705', 3, 'gati', '588600478', '4.00000000', '../download/shipping_label/27_Oct_16/20161007204_gati_588600478.pdf'),
(77, 6755, '6755', 3, 'gati', '588600485', '4.00000000', '../download/shipping_label/27_Oct_16/20161007253_gati_588600485.pdf'),
(78, 6760, '6760', 3, 'gati', '588600492', '5.00000000', '../download/shipping_label/27_Oct_16/20161007258_gati_588600492.pdf'),
(79, 6761, '6761', 1, 'gati', '588600508', '4.00000000', '../download/shipping_label/27_Oct_16/20161007259_gati_588600508.pdf'),
(80, 6763, '6763', 1, 'gati', '588600515', '3.50000000', '../download/shipping_label/29_Oct_16/20161007261_gati_588600515.pdf'),
(81, 6775, '6775', 1, 'gati', '588600522', '5.00000000', '../download/shipping_label/29_Oct_16/20161007273_gati_588600522.pdf'),
(82, 6785, '6785', 1, 'gati', '588600539', '5.30000000', '../download/shipping_label/03_Nov_16/20161007283_gati_588600539.pdf'),
(83, 6797, '6797', 1, 'gati', '588600546', '6.70000000', '../download/shipping_label/03_Nov_16/20161107295_gati_588600546.pdf'),
(84, 6808, '6808', 1, 'gati', '588600553', '6.50000000', '../download/shipping_label/04_Nov_16/20161107306_gati_588600553.pdf'),
(85, 6824, '6824', 1, 'gati', '588600560', '3.60000000', '../download/shipping_label/05_Nov_16/20161107322_gati_588600560.pdf'),
(86, 6855, '6855', 1, 'gati', '588600577', '3.50000000', 'MDhfTm92XzE2LzIwMTYxMTA3MzUzX2dhdGlfNTg4NjAwNTc3LnBkZg=='),
(87, 6822, '6822', 1, 'gati', '588600584', '5.50000000', 'MDlfTm92XzE2LzIwMTYxMTA3MzIwX2dhdGlfNTg4NjAwNTg0LnBkZg=='),
(88, 6865, '6865', 1, 'gati', '588600591', '8.00000000', 'MDlfTm92XzE2LzIwMTYxMTA3MzYzX2dhdGlfNTg4NjAwNTkxLnBkZg=='),
(89, 6865, '6865', 1, 'gati', '588600607', '7.00000000', 'MDlfTm92XzE2LzIwMTYxMTA3MzYzX2dhdGlfNTg4NjAwNjA3LnBkZg=='),
(90, 6865, '6865', 1, 'gati', '588600614', '7.00000000', 'MDlfTm92XzE2LzIwMTYxMTA3MzYzX2dhdGlfNTg4NjAwNjE0LnBkZg=='),
(91, 6865, '6865', 1, 'gati', '588600621', '7.00000000', 'MDlfTm92XzE2LzIwMTYxMTA3MzYzX2dhdGlfNTg4NjAwNjIxLnBkZg=='),
(92, 6890, '6890', 1, 'gati', '588600638', '7.00000000', 'MTBfTm92XzE2LzIwMTYxMTA3Mzg4X2dhdGlfNTg4NjAwNjM4LnBkZg=='),
(93, 6911, '6911', 1, 'gati', '588600645', '4.00000000', 'MTJfTm92XzE2LzIwMTYxMTA3NDA5X2dhdGlfNTg4NjAwNjQ1LnBkZg==');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_shipping_label`
--
ALTER TABLE `oc_shipping_label`
  ADD PRIMARY KEY (`shipping_label_id`),
  ADD KEY `order_id` (`order_id`,`suborder_id`,`from_warehouse_id`,`courier_name`,`tracking_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_shipping_label`
--
ALTER TABLE `oc_shipping_label`
  MODIFY `shipping_label_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
