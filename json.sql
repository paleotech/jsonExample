-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: json_example
-- ------------------------------------------------------
-- Server version	5.1.73-log

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
-- Table structure for table `json_access_log`
--

DROP TABLE IF EXISTS `json_access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_access_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data_accessed` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `admin_user` tinyint(1) NOT NULL DEFAULT '0',
  `log_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=181464 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Table structure for table `json_case`
--

DROP TABLE IF EXISTS `json_case`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `date_of_service` date NOT NULL,
  `server_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `local_timestamp` timestamp NULL DEFAULT NULL,
  `case_type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=896 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_case_type`
--

DROP TABLE IF EXISTS `json_case_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_case_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `varname` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `display_name` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_data`
--

DROP TABLE IF EXISTS `json_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL,
  `data` varbinary(2048) NOT NULL,
  `user_id` int(11) NOT NULL,
  `server_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `local_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `local_timestamp` (`local_timestamp`),
  KEY `encounter_id` (`encounter_id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7284 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_encounter`
--

DROP TABLE IF EXISTS `json_encounter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_encounter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `server_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `local_timestamp` timestamp NULL DEFAULT NULL,
  `type` int(11) NOT NULL,
  `date_of_service` date DEFAULT '0000-00-00',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `provider` int(11) DEFAULT NULL,
  `is_migrated` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `date_of_service` (`date_of_service`)
) ENGINE=MyISAM AUTO_INCREMENT=1745 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_encounter_for_case_type`
--

DROP TABLE IF EXISTS `json_encounter_for_case_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_encounter_for_case_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encounter_type_id` int(11) NOT NULL,
  `case_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `encounter_type_id` (`encounter_type_id`),
  KEY `case_type_id` (`case_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_encounter_type`
--

DROP TABLE IF EXISTS `json_encounter_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_encounter_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `varname` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `display_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ordinal` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_encounter_update_log`
--

DROP TABLE IF EXISTS `json_encounter_update_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_encounter_update_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` varbinary(65) NOT NULL,
  `provider_id` varbinary(65) NOT NULL,
  `scheduled_time` varbinary(97) NOT NULL,
  `location_index` varbinary(65) NOT NULL,
  `surgeon` varbinary(128) DEFAULT NULL,
  `height` varbinary(65) DEFAULT NULL,
  `weight` varbinary(65) DEFAULT NULL,
  `pulse` varbinary(65) DEFAULT NULL,
  `systolic` varbinary(65) DEFAULT NULL,
  `diastolic` varbinary(65) DEFAULT NULL,
  `asa_class_index` varbinary(64) DEFAULT NULL,
  `account_number` varbinary(65) DEFAULT NULL,
  `procedure_other_text` varbinary(2048) DEFAULT NULL,
  `procedure_index` varbinary(64) DEFAULT NULL,
  `diagnosis_other_text` varbinary(2048) DEFAULT NULL,
  `diagnosis_index` varbinary(64) DEFAULT NULL,
  `encounter_created` timestamp NULL DEFAULT NULL,
  `encounter_creator` int(11) NOT NULL,
  `encounter_updated` timestamp NULL DEFAULT NULL,
  `encounter_updater` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `provider_id` (`provider_id`),
  KEY `scheduled_time` (`scheduled_time`),
  KEY `location_index` (`location_index`)
) ENGINE=MyISAM AUTO_INCREMENT=1872 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_location`
--

DROP TABLE IF EXISTS `json_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_property`
--

DROP TABLE IF EXISTS `json_property`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `display_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=272 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_signature`
--

DROP TABLE IF EXISTS `json_signature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_signature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encounter_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `upload_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` blob NOT NULL,
  `provider_name` varbinary(97) DEFAULT NULL,
  `is_preop` tinyint(4) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=150 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_user`
--

DROP TABLE IF EXISTS `json_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `passhash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `role_index` int(11) NOT NULL,
  `default_location` int(11) NOT NULL DEFAULT '1',
  `createdDateTime` datetime NOT NULL,
  `lastLoginDateTime` datetime NOT NULL,
  `disabledDateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=233 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `json_user_at_location`
--

DROP TABLE IF EXISTS `json_user_at_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `json_user_at_location` (
  `location_id` int(11) NOT NULL DEFAULT '0' COMMENT 'COMMENT "CONSTRAINT FOREIGN KEY (locationId) REFERENCES location(id)"',
  `user_id` int(11) DEFAULT NULL COMMENT 'COMMENT "CONSTRAINT FOREIGN KEY (userId) REFERENCES users(id)"',
  `status` enum('Active','Inactive') COLLATE utf8_unicode_ci NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `locationId` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-27 19:32:01
