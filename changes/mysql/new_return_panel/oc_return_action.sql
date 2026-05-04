-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 23, 2018 at 01:55 PM
-- Server version: 5.7.22-0ubuntu0.16.04.1
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

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
-- Table structure for table `oc_return_action`
--

CREATE TABLE `oc_return_action` (
  `return_action_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT '1',
  `name` varchar(64) NOT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  `post_actions` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_return_action`
--

INSERT INTO `oc_return_action` (`return_action_id`, `language_id`, `name`, `class_name`, `post_actions`, `status`) VALUES
(101, 1, 'Pending', 'ActionPending', '102,104,103,107,105', 1),
(102, 1, 'Return Request Accepted', 'ActionReturnRequestAccepeted', '104,105,109,110,111', 1),
(103, 1, 'Replacement Request Accepted', 'ActionReplacementRequestAccepted', '105,109,110,111', 1),
(104, 1, 'Return Request Rejected', 'ActionReturnRequestRejected', NULL, 1),
(105, 1, 'Self Shipment', 'ActionSelfShipment', '108,109,110,111', 1),
(106, 1, 'Reverse Shipment Generated', 'ActionReverseShipmentGenerated', '112,113,109,110,111,108', 0),
(107, 1, 'Replacement Request Rejected', 'ActionReplacementRequestRejected', NULL, 1),
(108, 1, 'Return Goods Rejected', 'ActionReturnGoodsRejected', NULL, 1),
(109, 1, 'Goods Received', 'ActionGoodsReceived', '108,121,117', 1),
(110, 1, 'Goods Received (More)', 'ActionExtraGoodsReceived', '108,121,117', 1),
(111, 1, 'Goods Received (Less)', 'ActionShortGoodsReceived', '108,121,117', 1),
(112, 1, 'Goods Picked Up', 'ActionGoodsPickedup', '109,110,111,113', 1),
(113, 1, 'Shipment Lost by Courier', 'ActionShipmentLostbyCourier', NULL, 1),
(114, 1, 'DN Generate for Logistic Company', 'ActionDnForLogistic', NULL, 0),
(115, 1, 'CN for Client', 'ActionCnForClient', '116,117,118,119,134', 0),
(116, 1, 'Refunded Initiated', 'ActionRefundInitiated', '117,118,119,134', 1),
(117, 1, 'DN Generated for Seller', 'ActionDnForSeller', '121,122', 1),
(118, 1, 'Goods Taken on WSB Books and Relisted', 'ActionGoodsTakenOnWsbAndRelisted', NULL, 1),
(119, 1, 'Loss Booked by WSB', 'ActionLossBookedByWSB', NULL, 1),
(120, 1, 'CN Cancelled', 'ActionCnCancelled', '129', 0),
(121, 1, 'Goods Given to Pickup Boy', 'ActionGoodsGivenPickupBoy', '124,122', 1),
(122, 1, 'Goods Handed Over to Seller', 'ActionGoodsHandedOverSeller', '125,126,132,133,134', 1),
(123, 1, 'Cancelled DN For Seller', 'ActionDnCancelled', '117,118,119', 0),
(124, 1, 'Goods Damaged by Pickup Boy during Return', 'ActionDamageByPickupDuringReturn', '118,119', 1),
(125, 1, 'Seller Accepts Return/Replacement', 'ActionSellerAcceptsReturn', '117,128,132,133,134', 1),
(126, 1, 'Seller Rejected Accepting Returns', 'ActionSellerRejectedAcceptsReturn', '127', 1),
(127, 1, 'Issue Resolved with Seller', 'ActionIssueResolvedWithSeller', '117,126,125,133,134', 1),
(128, 1, 'Replacement Given By Seller', 'ActionReplacementGivenBySeller', '137', 1),
(129, 1, 'Goods Hold by WSB waiting for Customer to Pickup', 'ActionGoodsHoldWaitCustPickup', '131,134', 1),
(130, 1, 'Shipment Back to Customer', 'ActionGoodsBackToCustomer', '113,137', 0),
(8, 1, 'Returned Goods Rejected', 'ActionPending', NULL, 0),
(7, 1, 'Return Request Rejected', 'ActionPending', NULL, 0),
(6, 1, 'Replacement Reserved', 'ActionPending', NULL, 0),
(5, 1, 'Replacement Sent', 'ActionPending', NULL, 0),
(4, 1, 'Credit Issued', 'ActionPending', NULL, 0),
(3, 1, 'Amount Refunded', 'ActionPending', NULL, 0),
(2, 1, 'Returned Goods Received', 'ActionPending', NULL, 0),
(1, 1, 'Return Approved - Awaiting Products', 'ActionPending', NULL, 0),
(9, 1, 'Rejected Returns Reserved', 'ActionPending', NULL, 0),
(10, 1, 'Rejected Returns Resent', 'ActionPending', NULL, 0),
(11, 1, 'Cancelled', 'ActionPending', NULL, 0),
(12, 1, 'Replacement not available', 'ActionPending', NULL, 0),
(13, 1, 'Good to generate Debit Note', 'ActionPending', NULL, 0),
(14, 1, 'VAT Purchase Returned under GST', 'ActionPending', NULL, 0),
(15, 1, 'Parcel Lost during Return by Courier', 'ActionPending', NULL, 0),
(132, 1, 'Goods Deliver to Seller and waiting for Replacement', 'ActionDeliverToSellerWaitingReplacement', '128,133', 1),
(133, 1, 'Replacement Not Available', 'ActionReplacementNotAvailable', NULL, 1),
(134, 1, 'Return Complete', 'ActionReturnComplete', NULL, 1),
(131, 1, 'Customer Picked Items', 'ActionCustomerPickedItems', '137', 1),
(135, 1, 'Cancelled By Customer', 'ActionCancelledByCustomer', NULL, 0),
(136, 1, 'Custom DN Cancelled', 'ActionCustomDnCancelled', '118,119', 0),
(137, 1, 'Replacement Complete', 'ActionReplacementComplete', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_return_action`
--
ALTER TABLE `oc_return_action`
  ADD PRIMARY KEY (`return_action_id`,`language_id`),
  ADD KEY `class_name` (`class_name`),
  ADD KEY `post_actions` (`post_actions`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_return_action`
--
ALTER TABLE `oc_return_action`
  MODIFY `return_action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
