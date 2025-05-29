-- MySQL dump 10.13  Distrib 5.7.28, for macos10.14 (x86_64)
--
-- Host: localhost    Database: officefinder
-- ------------------------------------------------------
-- Server version	5.7.28

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
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `org_unit` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Corresponds to official HR Org number, eg: 5000XXX',
  `bc_org_unit` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bc_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `building` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `room` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `academic` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Flag to indicate if department is academic',
  `suppress` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Suppress displaying this department? Typically used to hide official SAP orgs with no appointments and children.',
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `uid` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'UID corresponding to related IDM user',
  `uidlastupdated` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rgt` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lft` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_unit` (`org_unit`),
  KEY `name` (`name`(255)),
  KEY `parent_id` (`parent_id`),
  KEY `academic` (`academic`),
  KEY `suppress` (`suppress`),
  KEY `bc_org_unit` (`bc_org_unit`),
  KEY `bc_name` (`bc_name`(255))
) ENGINE=InnoDB AUTO_INCREMENT=6131 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Seed this for things to work --
INSERT INTO `departments` (id, name,org_unit,bc_org_unit,bc_name,building,room,city,state,postal_code,address,phone,fax,email,website,academic,suppress,parent_id,sort_order,uid,uidlastupdated,dateupdated,rgt,lft,`level`) VALUES
	(1, 'University of Nebraska–Lincoln','50000003',NULL,NULL,NULL,NULL,'Lincoln','NE','68588','1400 R Street',NULL,NULL,NULL,'https://www.unl.edu/',0,0,4643,1,NULL,NULL,'2023-10-26 16:27:50',NULL,NULL,NULL),
	(4643, 'University of Nebraska Office of the President','50000001',NULL,NULL,'VARH',NULL,'Lincoln','NE','68583','3835 Holdrege Street','402-472-2111',NULL,NULL,'https://www.nebraska.edu/',0,0,NULL,15,NULL,'erasmussen2','2022-08-19 15:07:36',NULL,NULL,NULL);

--
-- Table structure for table `department_aliases`
--

DROP TABLE IF EXISTS `department_aliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_aliases` (
  `department_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`department_id`,`name`),
  CONSTRAINT `FK_DEPT_ALIAS_DEPT` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department_permissions`
--

DROP TABLE IF EXISTS `department_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_permissions` (
  `department_id` int(11) NOT NULL,
  `uid` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`department_id`,`uid`),
  CONSTRAINT `FK_DEPT_PERM_DEPT` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `telecom_unidaslt_to_departments`
--

DROP TABLE IF EXISTS `telecom_unidaslt_to_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telecom_unidaslt_to_departments` (
  `lMaster_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`lMaster_id`,`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

DROP TABLE IF EXISTS `person_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_info` (
  `uid` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `avatar_updated_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `person_info_avatar_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_info_avatar_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `status` ENUM('queued','working','finished','error') NOT NULL DEFAULT 'queued',
  `file` LONGTEXT COLLATE utf8_unicode_ci DEFAULT NULL,
  `square_x` int(11) NOT NULL,
  `square_y` int(11) NOT NULL,
  `square_size` int(11) NOT NULL,
  `error` LONGTEXT COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-02-27 15:45:39
