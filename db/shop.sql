-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tvod2
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu0.14.04.1

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
-- Table structure for table `ads`
--




DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `fk_auth_assignment_user1_idx` (`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_auth_assignment_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_assignment`
--

LOCK TABLES `auth_assignment` WRITE;
/*!40000 ALTER TABLE `auth_assignment` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `acc_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_item`
--

LOCK TABLES `auth_item` WRITE;
/*!40000 ALTER TABLE `auth_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_item_child`
--

LOCK TABLES `auth_item_child` WRITE;
/*!40000 ALTER TABLE `auth_item_child` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_item_child` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_rule`
--

LOCK TABLES `auth_rule` WRITE;
/*!40000 ALTER TABLE `auth_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(200) NOT NULL,
  `ascii_name` varchar(200) DEFAULT NULL,
  `description` text,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '10 - active\n0 - inactive\n3 - for test only',
  `order_number` int(11) NOT NULL DEFAULT '0' COMMENT 'dung de sap xep category theo thu tu xac dinh, order chi dc so sanh khi cac category co cung level',
  `parent_id` int(11) DEFAULT NULL,
  `path` varchar(200) DEFAULT NULL COMMENT 'chua duong dan tu root den node nay trong category tree, vi du: 1/3/18/4, voi 4 la id cua category hien tai',
  `level` int(11) DEFAULT NULL COMMENT '0 - root\n1 - category cap 2\n2 - category cap 3\n...',
  `child_count` int(11) DEFAULT NULL,
  `images` varchar(500) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vod_category_vod_category_idx` (`parent_id`),
  KEY `idx_name` (`display_name`),
  KEY `idx_name_ascii` (`ascii_name`),
  KEY `idx_desc` (`description`(255)),
  KEY `idx_order_no` (`order_number`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_path` (`path`),
  KEY `idx_level` (`level`),
  CONSTRAINT `fk_vod_category_vod_category` FOREIGN KEY (`parent_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(200) NOT NULL,
  `code` varchar(20) NOT NULL COMMENT 'ma de mua noi dung (qua SMS)',
  `ascii_name` varchar(200) DEFAULT NULL COMMENT 'string khong dau cua display_name',
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - video\n2 - live\n3 - music\n4 - news\n11 - music\n12 - clip\n13 - radio\n14 - karaoke\n15 - live programm (recorded)\n21 - near live\n100 - app\n',
  `tags` varchar(500) DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `description` text,
  `content` text COMMENT 'HTML content',
  `version_code` int(11) DEFAULT NULL,
  `version` varchar(64) DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT '0',
  `download_count` int(11) DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `dislike_count` int(11) NOT NULL DEFAULT '0',
  `rating` double(11,2) NOT NULL DEFAULT '0.00',
  `rating_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `favorite_count` int(11) NOT NULL DEFAULT '0',
  `images` text COMMENT 'danh sach cac images, json encoded\n',
  `status` int(11) NOT NULL DEFAULT '10' COMMENT '0 - pending\n10 - active\n1 - waiting for trancoding\n2 - inactive\n3 - for test only\n4 - rejected vi nguyen nhan 1\n5 - rejected vi nguyen nhan 2\n6 - rejected vi nguyen nhan 3\n...',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `honor` int(11) DEFAULT '0' COMMENT '0 --> nothing\n1 --> featured\n2 --> hot\n3 --> especial',
  `approved_at` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT '0',
  `price` int(11) DEFAULT '0',
  `price_promotion` int(11) DEFAULT NULL,
  `highlight` text,
  `condition` text,
  `expired_at` int(11) DEFAULT '1' COMMENT 'so ngay download ke tu khi mua ',
  `address` varchar(500) DEFAULT NULL,
  `title_short` varchar(500) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`),
  KEY `idx_name` (`display_name`),
  KEY `idx_tags` (`tags`(255)),
  KEY `idx_short_desc` (`short_description`(255)),
  KEY `idx_desc` (`description`(255)),
  KEY `idx_view_count` (`view_count`),
  KEY `idx_like_count` (`like_count`),
  KEY `idx_dislike_count` (`dislike_count`),
  KEY `idx_rating` (`rating`),
  KEY `idx_rating_count` (`rating_count`),
  KEY `idx_comment_count` (`comment_count`),
  KEY `idx_favorite_count` (`favorite_count`),
  KEY `idx_is_deleted` (`status`),
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='TODO: thong tin ve cac thuoc tinh nhu dao dien, tac gia, ca ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_attribute`
--


--
-- Table structure for table `content_attribute_value`
--



DROP TABLE IF EXISTS `content_category_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_category_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_category_asm`
--

LOCK TABLES `content_category_asm` WRITE;
/*!40000 ALTER TABLE `content_category_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_category_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_feedback`
--




DROP TABLE IF EXISTS `site_api_credential`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_api_credential` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `client_name` varchar(200) NOT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - web client (can co secret key cho server va apikey)\n2 - android client (can co api key, packagename va certificate fingerprint\n3 - ios\n4 - windows phone',
  `client_api_key` varchar(128) NOT NULL COMMENT 'dung cho tat cac moi client',
  `client_secret` varchar(128) DEFAULT NULL COMMENT 'dung cho web, ios, windows',
  `description` varchar(1024) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '10' COMMENT '10 - active, \n0 - suspended, \n...',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_api_credential`
--

LOCK TABLES `site_api_credential` WRITE;
/*!40000 ALTER TABLE `site_api_credential` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_api_credential` ENABLE KEYS */;
UNLOCK TABLES;




DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role` smallint(6) NOT NULL DEFAULT '10',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - Admin\n2 - SP\n3 - dealer',
  `site_id` int(10) DEFAULT NULL,
  `dealer_id` int(10) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT 'ID cua accout me',
  `fullname` varchar(255) DEFAULT NULL,
  `user_ref_id` int(11) DEFAULT NULL,
  `access_login_token` varchar(255) DEFAULT NULL,
  `phone_number` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='quan ly cac site (tvod viet nam, tvod nga, tvod sec...)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activity`
--

DROP TABLE IF EXISTS `user_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `action` varchar(126) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL COMMENT 'id cua doi tuong tac dong\n(phim, user...)',
  `target_type` smallint(6) DEFAULT NULL COMMENT '1 - user\n2 - cat\n3 - content\n4 - subscriber\n5 - ...',
  `created_at` int(11) DEFAULT NULL,
  `description` text,
  `status` varchar(255) DEFAULT NULL,
  `site_id` int(10) DEFAULT NULL,
  `dealer_id` int(10) DEFAULT NULL,
  `request_detail` varchar(256) DEFAULT NULL,
  `request_params` text,
  PRIMARY KEY (`id`),
  KEY `fk_user_activity_user1_idx` (`user_id`),
  KEY `fk_user_activity_service_provider1_idx` (`site_id`),
  KEY `fk_user_activity_content_provider1_idx` (`dealer_id`),
  CONSTRAINT `fk_user_activity_content_provider1` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_activity_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_activity_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activity`
--

LOCK TABLES `user_activity` WRITE;
/*!40000 ALTER TABLE `user_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activity` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-03-04  9:11:39
