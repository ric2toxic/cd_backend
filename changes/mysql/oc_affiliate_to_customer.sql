-- MySQL dump 10.13  Distrib 5.7.27, for Linux (x86_64)
--
-- Host: localhost    Database: wholesalebox
-- ------------------------------------------------------
-- Server version	5.7.27-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `oc_affiliate_to_customer`
--

DROP TABLE IF EXISTS `oc_affiliate_to_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oc_affiliate_to_customer` (
  `affiliate_to_customer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `referral_code` varchar(64) NOT NULL,
  `utm_campaign` varchar(45) DEFAULT '',
  `utm_medium` varchar(45) DEFAULT '',
  `utm_source` varchar(45) DEFAULT '',
  `utm_channel` varchar(45) DEFAULT '',
  `utm_term` varchar(45) DEFAULT '',
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`affiliate_to_customer_id`),
  UNIQUE KEY `index5` (`affiliate_id`,`customer_id`,`referral_code`),
  KEY `index2` (`affiliate_id`,`customer_id`),
  KEY `index3` (`affiliate_id`,`referral_code`),
  KEY `fk_oc_affiliate_to_customer_2_idx` (`customer_id`),
  CONSTRAINT `fk_oc_affiliate_to_customer_1` FOREIGN KEY (`affiliate_id`) REFERENCES `oc_affiliate` (`affiliate_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_oc_affiliate_to_customer_2` FOREIGN KEY (`customer_id`) REFERENCES `oc_customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-10-03 15:48:13
