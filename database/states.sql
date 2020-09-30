-- MySQL dump 10.13  Distrib 5.5.60, for Linux (x86_64)
--
-- Host: eyeforsecurity.cwpbvx46kdfc.us-east-2.rds.amazonaws.com    Database: cssi
-- ------------------------------------------------------
-- Server version	5.5.5-10.0.24-MariaDB

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
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL DEFAULT '',
  `title` varchar(50) DEFAULT NULL,
  `extra_cost` decimal(10,2) unsigned DEFAULT NULL,
  `mvr_cost` decimal(10,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (1,'AL','Alabama',1.00,10.00),(2,'AK','Alaska',0.00,10.00),(3,'AZ','Arizona',0.00,8.00),(4,'AR','Arkansas',0.00,13.00),(5,'CA','California',0.00,2.00),(6,'CO','Colorado',2.00,2.20),(7,'CT','Connecticut',0.00,18.00),(8,'DE','Deleware',0.00,25.00),(9,'DC','District of Columbia',0.00,13.00),(10,'FL','Florida',14.00,8.02),(11,'GA','Georgia',0.00,6.00),(12,'HI','Hawaii',5.00,23.00),(13,'ID','Idaho',0.00,9.00),(14,'IL','Illinois',10.00,12.00),(15,'IN','Indiana',16.32,7.50),(16,'IA','Iowa',0.00,8.50),(17,'KS','Kansas',20.00,13.70),(18,'KY','Kentucky',20.00,5.50),(19,'LA','Louisiana',0.00,18.00),(20,'ME','Maine',31.00,7.00),(21,'MD','Maryland',0.00,12.00),(22,'MA','Massachusetts',0.00,8.00),(23,'MI','Michigan',10.00,11.00),(24,'MN','Minnesota',0.00,5.00),(25,'MS','Mississippi',5.00,14.00),(26,'MO','Missouri',0.00,5.80),(27,'MT','Montana',14.00,7.37),(28,'NE','Nebraska',0.00,3.00),(29,'NV','Nevada',0.00,8.00),(30,'NH','New Hampshire',0.00,13.00),(31,'NJ','New Jersey',0.00,12.00),(32,'NM','New Mexico',0.00,6.50),(33,'NY','New York',65.00,7.00),(34,'NC','North Carolina',0.00,10.00),(35,'ND','North Dakota',0.00,3.00),(36,'OH','Ohio',0.00,5.00),(37,'OK','Oklahoma',0.00,27.50),(38,'OR','Oregon',0.00,10.00),(39,'PA','Pennsylvania',0.00,12.00),(40,'RI','Rhode Island',0.00,20.00),(41,'SC','South Carolina',25.00,7.25),(42,'SD','South Dakota',20.00,5.00),(43,'TN','Tennessee',29.00,7.50),(44,'TX','Texas',4.00,6.50),(45,'UT','Utah',10.00,9.00),(46,'VT','Vermont',30.00,18.00),(47,'VA','Virginia',0.00,8.00),(48,'WA','Washington',0.00,13.00),(49,'WV','West Virginia',NULL,12.50),(50,'WI','Wisconsin',0.00,7.00),(51,'WY','Wyoming',0.00,5.00);
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-06-23 16:45:57
