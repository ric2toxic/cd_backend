-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 09, 2017 at 02:28 PM
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
-- Table structure for table `oc_menu_admin`
--

CREATE TABLE `oc_menu_admin` (
  `id` int(11) NOT NULL,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `permission` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `link` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `icon` text CHARACTER SET utf8,
  `parent` int(11) NOT NULL DEFAULT '0',
  `sub_menu` tinyint(1) NOT NULL DEFAULT '0',
  `added_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  `menu_order` int(3) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_menu_admin`
--

INSERT INTO `oc_menu_admin` (`id`, `title`, `permission`, `link`, `icon`, `parent`, `sub_menu`, `added_date`, `is_deleted`, `menu_order`, `status`) VALUES
(1, 'Dashboard', 'common/dashboard', 'common/dashboard', 'fa fa-dashboard fa-fw', 0, 0, '2017-11-18 17:26:41', 0, 0, 1),
(2, 'Catalog', 'catalog/category', 'catalog/category', 'fa fa-tags fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 0, 1),
(3, 'Categories', 'catalog/category', 'catalog/category/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(4, 'Products', 'catalog/product', 'catalog/product/index', '', 2, 0, '2017-11-18 17:26:41', 0, 1, 1),
(5, 'User Questions', 'review/user_questions', 'review/user_questions/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(6, 'Menu', 'catalog/menu', 'catalog/menu/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(7, 'Recurring Profiles', 'catalog/recurring', 'catalog/recurring/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(8, 'Filters', 'catalog/filter', 'catalog/filter/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(9, 'Attributes', 'catalog/filter', 'catalog/filter', '', 2, 1, '2017-11-18 17:26:41', 0, 0, 1),
(10, 'Attributes', 'catalog/filter', 'catalog/filter/index', '', 9, 0, '2017-11-18 17:26:41', 0, 0, 1),
(11, 'Attribute Groups', 'catalog/attribute_group', 'catalog/attribute_group/index', '', 9, 0, '2017-11-18 17:26:41', 0, 0, 1),
(12, 'Options', 'catalog/option', 'catalog/option/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(13, 'Manufacturers', 'catalog/manufacturer', 'catalog/manufacturer/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(14, 'Downloads', 'catalog/download', 'catalog/download/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(15, 'Reviews', 'catalog/review', 'catalog/review', '', 2, 1, '2017-11-18 17:26:41', 0, 0, 1),
(16, 'Product Reviews', 'catalog/review', 'catalog/review/index', '', 15, 0, '2017-11-18 17:26:41', 0, 0, 1),
(17, 'Store Reviews', 'review/store_review', 'review/store_review/index', '', 15, 0, '2017-11-18 17:26:41', 0, 0, 1),
(18, 'Information', 'catalog/information', 'catalog/information/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(19, 'Deal of the day', 'catalog/deal_of_day', 'catalog/deal_of_day/index', '', 2, 0, '2017-11-18 17:26:41', 0, 0, 1),
(20, 'Extensions', 'extension/installer', 'extension/installer', 'fa fa-puzzle-piece fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 2, 1),
(21, 'Extension Installer', 'extension/installer', 'extension/installer/index', '', 20, 0, '2017-11-18 17:26:41', 0, 0, 1),
(22, 'Modifications', 'extension/modification', 'extension/modification/index', '', 20, 0, '2017-11-18 17:26:41', 0, 0, 1),
(23, 'Modules', 'extension/module', 'extension/module/index', '', 20, 0, '2017-11-18 17:26:41', 0, 0, 1),
(24, 'Shipping', 'extension/shipping', 'extension/shipping/index', '', 20, 0, '2017-11-18 17:26:41', 0, 0, 1),
(25, 'Payments', 'extension/payment', 'extension/payment/index', '', 20, 0, '2017-11-18 17:26:41', 0, 0, 1),
(26, 'Order Totals', 'extension/total', 'extension/total/index', '', 20, 0, '2017-11-18 17:26:41', 0, 0, 1),
(27, 'Feeds', 'extension/feed', 'extension/feed/index', '', 20, 0, '2017-11-18 17:26:41', 0, 0, 1),
(28, 'Anti-Fraud', 'extension/fraud', 'extension/fraud/index', '', 20, 0, '2017-11-18 17:26:41', 0, 0, 1),
(29, 'Operations', 'operations/order', 'operations/order', 'fa fa-life-ring', 0, 1, '2017-11-18 17:26:41', 0, 3, 1),
(30, 'Orders', 'operations/order', 'operations/order', '', 29, 1, '2017-11-18 17:26:41', 0, 0, 1),
(31, 'Pickup', 'operations/pickup', 'operations/pickup', '', 30, 0, '2017-11-18 17:26:41', 0, 1, 1),
(32, 'Sales', 'sale/order', 'sale/order', 'fa fa-shopping-cart fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 4, 1),
(33, 'Orders', 'sale/order', 'sale/order/index', '', 32, 0, '2017-11-18 17:26:41', 0, 0, 1),
(34, 'Recurring Orders', 'sale/recurring', 'sale/recurring/index', '', 32, 0, '2017-11-18 17:26:41', 0, 0, 1),
(35, 'Return', 'sale/return', 'sale/return/index', '', 32, 0, '2017-11-18 17:26:41', 0, 0, 1),
(36, 'Customers', 'sale/customer', 'sale/customer', '', 32, 1, '2017-11-18 17:26:41', 0, 0, 1),
(37, 'Customers', 'sale/customer', 'sale/customer/index', '', 36, 0, '2017-11-18 17:26:41', 0, 0, 1),
(38, 'Customers Credits', 'sale/customer_credits', 'sale/customer_credits/index', '', 36, 0, '2017-11-18 17:26:41', 0, 0, 1),
(39, 'Customers Groups', 'sale/customer_group', 'sale/customer_group/index', '', 36, 0, '2017-11-18 17:26:41', 0, 0, 1),
(40, 'Custom Fields', 'sale/custom_field', 'sale/custom_field/index', '', 36, 0, '2017-11-18 17:26:41', 0, 0, 1),
(41, 'Banned IP', 'sale/customer_ban_ip', 'sale/customer_ban_ip/index', '', 36, 0, '2017-11-18 17:26:41', 0, 0, 1),
(42, 'Gift Vouchers', 'sale/voucher', 'sale/voucher', '', 32, 1, '2017-11-18 17:26:41', 0, 0, 1),
(43, 'Gift Vouchers', 'sale/voucher', 'sale/voucher/index', '', 42, 0, '2017-11-18 17:26:41', 0, 0, 1),
(44, 'Voucher Themes', 'sale/voucher_theme', 'sale/voucher_theme/index', '', 42, 0, '2017-11-18 17:26:41', 0, 0, 1),
(45, 'Paypal', 'payment/pp_express', 'payment/pp_express', '', 32, 1, '2017-11-18 17:26:41', 0, 0, 1),
(46, 'Search', 'payment/pp_express', 'payment/pp_express/index', '', 45, 0, '2017-11-18 17:26:41', 0, 0, 1),
(47, 'Sellers', 'sellers/sellers', 'sellers/sellers', 'fa fa-users fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 5, 1),
(48, 'Sellers', 'sellers/sellers', 'sellers/sellers/index', '', 47, 0, '2017-11-18 17:26:41', 0, 0, 1),
(49, 'Mailler To Seller', 'sellers/mailler_sellers', 'sellers/mailler_sellers/index', '', 47, 0, '2017-11-18 17:26:41', 0, 0, 1),
(50, 'Seller Agreement', 'seller_agreement/seller_agreement', 'seller_agreement/seller_agreement/index', '', 47, 0, '2017-11-18 17:26:41', 0, 0, 1),
(51, 'Store Front', 'storefront/order', 'storefront/order', 'fa fa-university fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 6, 1),
(52, 'Store Front', 'storefront/order', 'storefront/order/index', '', 51, 0, '2017-11-18 17:26:41', 0, 0, 1),
(53, 'Customers', 'storefront/customer', 'storefront/customer/index', '', 51, 0, '2017-11-18 17:26:41', 0, 0, 1),
(54, 'Accounts', 'accounts/paymentsreport', 'accounts/paymentsreport', 'fa fa-files-o', 0, 1, '2017-11-18 17:26:41', 0, 7, 1),
(55, 'Reports', 'accounts/paymentsreport', 'accounts/paymentsreport', '', 54, 1, '2017-11-18 17:26:41', 0, 0, 1),
(56, 'Payments Report', 'accounts/paymentsreport', 'accounts/paymentsreport/index', '', 55, 0, '2017-11-18 17:26:41', 0, 0, 1),
(57, 'Purchase Reports', 'accounts/purchasereports', 'accounts/purchasereports/index', '', 55, 0, '2017-11-18 17:26:41', 0, 0, 1),
(58, 'Sales Reports', 'accounts/salesreports', 'accounts/salesreports/index', '', 55, 0, '2017-11-18 17:26:41', 0, 0, 1),
(59, 'Purchase Return', 'accounts/purchasereturnreports', 'accounts/purchasereturnreports/index', '', 55, 0, '2017-11-18 17:26:41', 0, 0, 1),
(60, 'Sale Return Report', 'accounts/salereturnreports', 'accounts/salereturnreports/index', '', 55, 0, '2017-11-18 17:26:41', 0, 0, 1),
(61, 'SOR Stock Report', 'accounts/sorstockreports', 'accounts/sorstockreports/index', '', 55, 0, '2017-11-18 17:26:41', 0, 0, 1),
(62, 'Inventory Reports', 'accounts/inventoryreports', 'accounts/inventoryreports/index', '', 55, 0, '2017-11-18 17:26:41', 0, 0, 1),
(63, 'Uploads', 'accounts/inventoryreports', 'accounts/inventoryreports', '', 54, 1, '2017-11-18 17:26:41', 0, 0, 1),
(64, 'Seller Order Status', 'accounts/inventoryreports', 'accounts/inventoryreports/index', '', 63, 0, '2017-11-18 17:26:41', 0, 0, 1),
(65, 'Pending Orders', 'accounts/pendingorder', 'accounts/pendingorder', '', 54, 0, '2017-11-18 17:26:41', 0, 0, 1),
(66, 'Sale Invoice', 'accounts/saleinvoice', 'accounts/saleinvoice', '', 54, 0, '2017-11-18 17:26:41', 0, 0, 1),
(67, 'Tally Reports', 'accounts/purchasereports', 'accounts/purchasereports', '', 54, 1, '2017-11-18 17:26:41', 0, 0, 1),
(68, 'Purchase Reports', 'accounts/purchasereports', 'accounts/purchasereports', '', 67, 0, '2017-11-18 17:26:41', 0, 0, 1),
(69, 'Purchase Return Reports', 'accounts/purchasereturnreports', 'accounts/purchasereturnreports', '', 67, 0, '2017-11-18 17:26:41', 0, 0, 1),
(70, 'Sales Reports', 'accounts/salesreports', 'accounts/salesreports', '', 67, 0, '2017-11-18 17:26:41', 0, 0, 1),
(71, 'Sale Return Report', 'accounts/salereturnreports', 'accounts/salereturnreports', '', 67, 0, '2017-11-18 17:26:41', 0, 0, 1),
(72, 'Advance Reports', 'accounts/advancevoucherreports', 'accounts/advancevoucherreports', '', 67, 0, '2017-11-18 17:26:41', 0, 0, 1),
(73, 'Order Pickup', 'pickuporders/pickuporders', 'pickuporders/pickuporders', 'fa fa-bar-chart-o fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 8, 1),
(74, 'Pending Sellers', 'pickuporders/pickuporders', 'pickuporders/pickuporders', '', 73, 0, '2017-11-18 17:26:41', 0, 0, 1),
(75, 'Digital Marketing', 'digital_marketing/custom_url', 'digital_marketing/custom_url', 'fa fa-share-alt fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 9, 1),
(76, 'Digital Marketing', 'digital_marketing/custom_url', 'digital_marketing/custom_url', '', 75, 0, '2017-11-18 17:26:41', 0, 0, 1),
(77, 'Marketing', 'marketing/marketing', 'marketing/marketing', 'fa fa-share-alt fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 10, 1),
(78, 'Marketing', 'marketing/marketing', 'marketing/marketing', '', 77, 0, '2017-11-18 17:26:41', 0, 0, 1),
(79, 'Affiliates', 'marketing/affiliate', 'marketing/affiliate', '', 77, 0, '2017-11-18 17:26:41', 0, 0, 1),
(80, 'Coupons', 'marketing/coupon', 'marketing/coupon', '', 77, 0, '2017-11-18 17:26:41', 0, 0, 1),
(81, 'Mail', 'marketing/contact', 'marketing/contact', '', 77, 0, '2017-11-18 17:26:41', 0, 0, 1),
(82, 'Templates', 'marketing/template', 'marketing/template', '', 77, 0, '2017-11-18 17:26:41', 0, 0, 1),
(83, 'System', 'setting/store', 'setting/store', 'fa fa-cog fa-fw', 0, 1, '2017-11-18 17:26:41', 0, 11, 1),
(84, 'Setting', 'setting/store', 'setting/store/index', '', 83, 0, '2017-11-18 17:26:41', 0, 0, 1),
(85, 'Design', 'design/layout', 'design/layout', '', 83, 1, '2017-11-18 17:26:41', 0, 0, 1),
(86, 'Layouts', 'design/layout', 'design/layout', '', 85, 0, '2017-11-18 17:26:41', 0, 0, 1),
(87, 'Banners', 'design/banner', 'design/banner', '', 85, 0, '2017-11-18 17:26:41', 0, 0, 1),
(88, 'Users', 'user/user', 'user/user', '', 83, 1, '2017-11-18 17:26:41', 0, 0, 1),
(89, 'Users', 'user/user', 'user/user/index', '', 88, 0, '2017-11-18 17:26:41', 0, 0, 1),
(90, 'User Groups', 'user/user_permission', 'user/user_permission', '', 88, 0, '2017-11-18 17:26:41', 0, 0, 1),
(91, 'API', 'user/api', 'user/api', '', 88, 0, '2017-11-18 17:26:41', 0, 0, 1),
(92, 'Localisation', 'localisation/location', 'localisation/location', '', 83, 1, '2017-11-18 17:26:41', 0, 0, 1),
(93, 'Store Locations', 'localisation/location', 'localisation/location', '', 92, 0, '2017-11-18 17:26:41', 0, 0, 1),
(94, 'Languages', 'localisation/language', 'localisation/language', '', 92, 0, '2017-11-18 17:26:41', 0, 0, 1),
(95, 'Currencies', 'localisation/currency', 'localisation/currency', '', 92, 0, '2017-11-18 17:26:41', 0, 0, 1),
(96, 'Stock Statuses', 'localisation/stock_status', 'localisation/stock_status', '', 92, 0, '2017-11-18 17:26:41', 0, 0, 1),
(97, 'Order Statuses', 'localisation/order_status', 'localisation/order_status', '', 92, 0, '2017-11-18 17:26:41', 0, 0, 1),
(98, 'Returns', 'localisation/return_action', 'localisation/return_action', '', 92, 1, '2017-11-18 17:26:41', 0, 0, 1),
(99, 'Tools', 'tool/upload', 'tool/upload', 'fa fa-wrench fa-fw', 0, 1, '2017-11-18 17:29:48', 0, 12, 1),
(100, 'Menu', 'setting/menu', 'setting/menu', '', 83, 0, '2017-11-18 18:03:59', 0, 0, 1),
(102, 'Testing', 'dashboard/testing', 'dashboard/testing', '', 0, 0, '2017-11-20 11:21:24', 1, 0, 0),
(103, 'Return Actions', 'localisation/return_action', 'localisation/return_action', '', 98, 0, '2017-11-20 12:57:41', 0, 0, 1),
(104, 'Return Reasons', 'localisation/return_reason', 'localisation/return_reason', '', 98, 0, '2017-11-20 13:09:29', 0, 0, 1),
(105, 'Countries', 'localisation/country', 'localisation/country', '', 92, 0, '2017-11-20 13:10:38', 0, 0, 1),
(106, 'Zones', 'localisation/zone', 'localisation/zone', '', 92, 0, '2017-11-20 13:11:28', 0, 0, 1),
(107, 'Geo Zones', 'localisation/geo_zone', 'localisation/geo_zone', '', 92, 0, '2017-11-20 13:12:00', 0, 0, 1),
(108, 'HSN Code', 'localisation/hsn_code', 'localisation/hsn_code', '', 92, 0, '2017-11-20 13:12:30', 0, 0, 1),
(109, 'Taxes', 'localisation/tax_class', 'localisation/tax_class', '', 92, 1, '2017-11-20 13:13:06', 0, 0, 1),
(110, 'Tax Classes', 'localisation/tax_class', 'localisation/tax_class', '', 109, 0, '2017-11-20 13:13:33', 0, 0, 1),
(111, 'Tax Rates', 'localisation/tax_rate', 'localisation/tax_rate', '', 109, 0, '2017-11-20 13:14:02', 0, 0, 1),
(112, 'Length Classes', 'localisation/length_class', 'localisation/length_class', '', 92, 0, '2017-11-20 13:14:53', 0, 0, 1),
(113, 'Weight Classes', 'localisation/weight_class', 'localisation/weight_class', '', 92, 0, '2017-11-20 13:15:25', 0, 0, 1),
(114, 'Uploads', 'tool/upload', 'tool/upload', '', 99, 0, '2017-11-20 13:17:21', 0, 0, 1),
(115, 'Backup / Restore', 'tool/backup', 'tool/backup', '', 99, 0, '2017-11-20 13:17:55', 0, 0, 1),
(116, 'Error Log', 'tool/error_log', 'tool/error_log', '', 99, 0, '2017-11-20 13:18:20', 0, 0, 1),
(117, 'Reports', 'report/sale_order', 'report/sale_order', 'fa fa-bar-chart-o fa-fw', 0, 1, '2017-11-20 13:19:31', 0, 13, 1),
(118, 'Sales', 'report/sale_order', 'report/sale_order', '', 117, 1, '2017-11-20 13:20:53', 0, 0, 1),
(119, 'Orders', 'report/sale_order', 'report/sale_order', '', 118, 0, '2017-11-20 13:21:22', 0, 0, 1),
(120, 'Sales Performance', 'report/performance', 'report/performance', '', 118, 0, '2017-11-20 13:22:23', 0, 0, 1),
(121, 'Tax', 'report/sale_tax', 'report/sale_tax', '', 118, 0, '2017-11-20 13:22:59', 0, 0, 1),
(122, 'Shipping', 'report/sale_shipping', 'report/sale_shipping', '', 118, 0, '2017-11-20 13:23:59', 0, 0, 1),
(123, 'Coupons', 'report/sale_coupon', 'report/sale_coupon', '', 118, 0, '2017-11-20 13:24:35', 0, 0, 1),
(124, 'Products', 'report/product_viewed', 'report/product_viewed', '', 117, 1, '2017-11-20 13:25:33', 0, 0, 1),
(125, 'Viewed', 'report/product_viewed', 'report/product_viewed', '', 124, 0, '2017-11-20 13:25:58', 0, 0, 1),
(126, 'Purchased', 'report/product_purchased', 'report/product_purchased', '', 124, 0, '2017-11-20 13:26:29', 0, 0, 1),
(127, 'Product Rating', 'report/product_rating', 'report/product_rating', '', 124, 0, '2017-11-20 13:26:54', 0, 0, 1),
(128, 'Returns', 'report/sale_return', 'report/sale_return', '', 118, 0, '2017-11-20 14:28:21', 0, 0, 1),
(129, 'Customers', 'report/index', 'report/index', '', 117, 1, '2017-11-20 14:29:44', 0, 0, 1),
(130, 'Customers Online', 'report/customer_online', 'report/customer_online', '', 129, 0, '2017-11-20 14:30:19', 0, 0, 1),
(131, 'Customers Activity', 'report/customer_activity', 'report/customer_activity', '', 129, 0, '2017-11-20 14:31:08', 0, 0, 1),
(132, 'Orders', 'report/customer_order', 'report/customer_order', '', 129, 0, '2017-11-20 14:31:48', 0, 0, 1),
(133, 'Reward Points', 'report/customer_reward', 'report/customer_reward', '', 129, 0, '2017-11-20 14:32:21', 0, 0, 1),
(134, 'Credit', 'report/customer_credit', 'report/customer_credit', '', 129, 0, '2017-11-20 14:32:53', 0, 0, 1),
(135, 'Seller Activity', 'report/seller_activity', 'report/seller_activity', '', 129, 0, '2017-11-20 14:34:25', 0, 0, 1),
(136, 'Abandoned', 'report/abandoned', 'report/abandoned', '', 117, 0, '2017-11-20 14:35:24', 0, 0, 1),
(137, 'Marketing', 'report/marketing', 'report/marketing', '', 117, 1, '2017-11-20 14:36:00', 0, 0, 1),
(138, 'Marketing', 'report/marketing', 'report/marketing', '', 137, 0, '2017-11-20 14:37:37', 0, 0, 1),
(139, 'Affiliate', 'report/affiliate', 'report/affiliate', '', 137, 0, '2017-11-20 14:38:05', 0, 0, 1),
(140, 'Affiliate Activity', 'report/affiliate_activity', 'report/affiliate_activity', '', 137, 0, '2017-11-20 14:38:25', 0, 0, 1),
(141, 'Analysis', 'report/analysis', 'report/analysis', '', 117, 1, '2017-11-20 14:39:47', 0, 0, 1),
(142, 'Productwise Margin', 'report/analysis', 'report/analysis/getProductWiseMargin', '', 141, 0, '2017-11-20 14:40:19', 0, 0, 1),
(143, 'Orderwise Margin', 'report/analysis', 'report/analysis/getOrderWiseMargin', '', 141, 0, '2017-11-20 14:41:04', 0, 0, 1),
(144, 'Customerwise Revenue', 'report/analysis', 'report/analysis/getCustomerWiseRevenue', '', 141, 0, '2017-11-20 14:41:28', 0, 0, 1),
(145, 'Sellerwise Revenue', 'report/analysis', 'report/analysis', '', 141, 0, '2017-11-20 14:41:57', 0, 0, 1),
(146, 'Productwise Returns', 'report/analysis', 'report/analysis/getProductWiseReturn', '', 141, 0, '2017-11-20 14:42:21', 0, 0, 1),
(147, 'Admin Info Log', 'report/admin_info_log', 'report/admin_info_log', '', 117, 0, '2017-11-20 14:42:56', 0, 0, 1),
(148, 'MultiMerch', 'module/multiseller', 'module/multiseller', 'fa fa-users fa-fw', 0, 1, '2017-11-20 14:44:12', 0, 14, 1),
(149, 'Settings', 'module/multiseller', 'module/multiseller', '', 148, 0, '2017-11-20 14:44:39', 0, 0, 1),
(150, 'Inventory', 'inventory/import', 'inventory/import', 'fa fa-pencil fa-fw', 0, 1, '2017-11-20 14:45:12', 0, 15, 1),
(151, 'Import', 'inventory/import', 'inventory/import', '', 150, 0, '2017-11-20 14:46:00', 0, 0, 1),
(152, 'Validate Inventory', 'inventory/validate', 'inventory/validate', '', 150, 0, '2017-11-20 14:46:22', 0, 0, 1),
(153, 'Bulk Image Upload', 'inventory/image', 'inventory/image', '', 150, 0, '2017-11-20 14:46:53', 0, 0, 1),
(154, 'Report', 'inventory/report', 'inventory/report', '', 150, 0, '2017-11-20 14:47:13', 0, 0, 1),
(155, 'Product To Moderate', 'inventory/producttomoderate', 'inventory/producttomoderate', '', 150, 0, '2017-11-20 14:47:39', 0, 0, 1),
(156, 'Wsb Purchase', 'wsb_purchase/import', 'wsb_purchase/import', 'fa fa-pencil fa-fw', 0, 1, '2017-11-20 14:48:18', 0, 16, 1),
(157, 'Import', 'wsb_purchase/import', 'wsb_purchase/import/import', '', 156, 0, '2017-11-20 14:49:00', 0, 0, 1),
(158, 'Reports', 'wsb_purchase/import', 'wsb_purchase/import', '', 156, 0, '2017-11-20 14:51:33', 0, 0, 1),
(159, 'Analysis', 'wsb_purchase/import', 'wsb_purchase/import/analysis', '', 156, 0, '2017-11-20 14:52:07', 0, 0, 1),
(160, 'Account Panel', 'account_panel/ledger', 'account_panel/ledger', 'fa fa-pencil fa-fw', 0, 1, '2017-11-20 14:53:15', 0, 17, 1),
(161, 'Ledger', 'account_panel/ledger', 'account_panel/ledger', '', 160, 1, '2017-11-20 14:54:11', 0, 0, 1),
(162, 'Creation', 'account_panel/ledger', 'account_panel/ledger', '', 161, 0, '2017-11-20 14:54:36', 0, 0, 1),
(163, 'Display', 'account_panel/ledger', 'account_panel/ledger/ledgerdisplay', '', 161, 0, '2017-11-20 14:55:13', 0, 0, 1),
(164, 'Detail', 'account_panel/ledger', 'account_panel/ledger/ledgerdetail', '', 161, 0, '2017-11-20 14:56:37', 0, 0, 1),
(165, 'Bank Receipt', 'account_panel/bankreceipt', 'account_panel/bankreceipt', '', 160, 0, '2017-11-20 14:57:30', 0, 0, 1),
(166, 'Bank Payment', 'account_panel/bankpayment', 'account_panel/bankpayment', '', 160, 0, '2017-11-20 14:58:04', 0, 0, 1),
(167, 'Tentative Advance', 'account_panel/tentativeadvance', 'account_panel/tentativeadvance', '', 160, 0, '2017-11-20 14:58:29', 0, 0, 1),
(168, 'Reporting', 'account_panel/orderpayment', 'account_panel/orderpayment', '', 160, 1, '2017-11-20 14:59:57', 0, 0, 1),
(169, 'Order Payment Report', 'account_panel/orderpayment', 'account_panel/orderpayment', '', 168, 0, '2017-11-20 15:00:27', 0, 0, 1),
(170, 'Logistic', 'logistic/gati', 'logistic/gati', 'fa fa-truck', 0, 1, '2017-11-20 15:01:13', 0, 18, 1),
(171, 'Gati Pincodes', 'logistic/gati', 'logistic/gati', '', 170, 0, '2017-11-20 15:01:37', 0, 0, 1),
(172, 'Dotzot Pincodes', 'logistic/dotzot', 'logistic/dotzot', '', 170, 0, '2017-11-20 15:01:58', 0, 0, 1),
(173, 'Jobs Opening', 'jobs/jobs_opening', 'jobs/jobs_opening', 'fa fa-briefcase fa-fw', 0, 0, '2017-11-20 15:02:24', 0, 19, 1),
(174, 'Dictionary', 'dictionary/search_dictionary', 'dictionary/search_dictionary', 'fa fa-bars fa-fw', 0, 0, '2017-11-20 15:02:47', 0, 20, 1),
(175, 'Popular Search', 'popularsearch/popularsearch', 'popularsearch/popularsearch', 'fa fa-fire fa-fw', 0, 0, '2017-11-20 15:03:17', 0, 21, 1),
(176, 'Preferences', 'preferences/preferences', 'preferences/preferences', 'fa fa-fire fa-fw', 0, 0, '2017-11-20 15:04:03', 0, 22, 1),
(177, 'App Dynamic Layout', 'app/dynamic_layout', 'app/dynamic_layout', 'fa fa-files-o', 0, 0, '2017-11-20 15:04:25', 0, 23, 1),
(178, 'Processing', 'operations/order', 'operations/order', '', 30, 1, '2017-11-30 16:51:09', 0, 0, 1),
(179, 'Warehouse', 'operations/order', 'operations/order', '', 30, 0, '2017-11-30 16:58:15', 0, 2, 1),
(180, 'Display', 'operations/order', 'operations/order', '', 30, 0, '2017-12-04 15:34:54', 0, 3, 1),
(181, 'Pending Orders', 'operations/order', 'operations/order', '', 178, 0, '2017-12-04 16:04:20', 0, 0, 1),
(182, 'Proceed', 'operations/order', 'operations/proceed_order', '', 178, 0, '2017-12-04 16:05:26', 0, 1, 1),
(183, 'Connect India Pincodes', 'logistic/connect_india', 'logistic/connect_india', '', 170, 0, '2017-12-07 18:58:42', 0, 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_menu_admin`
--
ALTER TABLE `oc_menu_admin`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_menu_admin`
--
ALTER TABLE `oc_menu_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
