CREATE DATABASE  IF NOT EXISTS `wholesalebox` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `wholesalebox`;
-- MySQL dump 10.13  Distrib 5.5.55, for debian-linux-gnu (i686)
--
-- Host: 127.0.0.1    Database: wholesalebox
-- ------------------------------------------------------
-- Server version	5.5.55-0ubuntu0.14.04.1

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
-- Table structure for table `oc_return_action_reasons`
--

DROP TABLE IF EXISTS `oc_return_action_reasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oc_return_action_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_reason_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language_id` int(11) DEFAULT '0',
  `parent_action_ids` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oc_return_action_reasons`
--

LOCK TABLES `oc_return_action_reasons` WRITE;
/*!40000 ALTER TABLE `oc_return_action_reasons` DISABLE KEYS */;
INSERT INTO `oc_return_action_reasons` VALUES (1,'Delay in Return Request ( Return Request not Received on Time)',1,'8,7',1),(2,'Return Request Rejected from Client Side',1,'2',1),(3,'Reverse Pickup arranged but deferred by client',1,'7,8',1),(4,'Short Pieces Received from client',1,'7,8',1),(5,'Not received same goods as per request',1,'7,8',1),(6,'Others',1,'7,8',1);
/*!40000 ALTER TABLE `oc_return_action_reasons` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-25 13:16:28
