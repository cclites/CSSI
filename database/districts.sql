

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `districts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `state_code` varchar(2) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state_code` (`state_code`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES (1,'AL','Middle District Court'),(2,'AL','Northern District Court'),(3,'AL','Southern District Court'),(4,'AR','Eastern District Court'),(5,'AR','Western District Court'),(6,'CA','Central District Court'),(7,'CA','Eastern District Court'),(8,'CA','Northern District Court'),(9,'CA','Southern District Court'),(10,'FL','Middle District Court'),(11,'FL','Northern District Court'),(12,'FL','Southern District Court'),(13,'GA','Middle District Court'),(14,'GA','Northern District Court'),(15,'GA','Southern District Court'),(16,'IL','Central District Court'),(17,'IL','Northern District Court'),(18,'IL','Southern District Court'),(19,'IN','Northern District Court'),(20,'IN','Southern District Court'),(21,'IA','Northern District Court'),(22,'IA','Southern District Court'),(23,'KY','Eastern District Court'),(24,'KY','Western District Court'),(25,'LA','Eastern District Court'),(26,'LA','Middle District Court'),(27,'LA','Western District Court'),(28,'MI','Eastern District Court'),(29,'MI','Western District Court'),(30,'MS','Northern District Court'),(31,'MS','Southern District Court'),(32,'MO','Eastern District Court'),(33,'MO','Western District Court'),(34,'NY','Eastern District Court'),(35,'NY','Northern District Court'),(36,'NY','Southern District Court'),(37,'NY','Western District Court'),(38,'NC','Eastern District Court'),(39,'NC','Middle District Court'),(40,'NC','Western District Court'),(41,'OH','Northern District Court'),(42,'OH','Southern District Court'),(43,'OK','Eastern District Court'),(44,'OK','Northern District Court'),(45,'OK','Western District Court'),(46,'PA','Eastern District Court'),(47,'PA','Middle District Court'),(48,'PA','Western District Court'),(49,'TN','Eastern District Court'),(50,'TN','Middle District Court'),(51,'TN','Western District Court'),(52,'TX','Eastern District Court'),(53,'TX','Northern District Court'),(54,'TX','Southern District Court'),(55,'TX','Western District Court'),(56,'VA','Eastern District Court'),(57,'VA','Western District Court'),(58,'WA','Eastern District Court'),(59,'WA','Western District Court'),(60,'WV','Northern District Court'),(61,'WV','Southern District Court'),(62,'WI','Eastern District Court'),(63,'WI','Western District Court');
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-05 15:56:55
