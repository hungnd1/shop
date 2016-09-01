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

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_ads_id` int(11) NOT NULL,
  `site_id` int(10) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - banner (image, with web link)\n2 - open other app (su dung intent, voi target_url la app can mo, va cac thong in extra)',
  `image` varchar(255) DEFAULT NULL COMMENT 'banner image',
  `target_url` varchar(255) DEFAULT NULL COMMENT 'link to open khi click vao (web link hoac intent)',
  `extra` text COMMENT 'du lieu extra cua intent khi mo ung dung khac',
  `status` smallint(6) NOT NULL DEFAULT '10' COMMENT '10 - active\n0 - inactive',
  PRIMARY KEY (`id`),
  KEY `fk_ads_app_ads1_idx` (`app_ads_id`),
  KEY `fk_ads_site1_idx` (`site_id`),
  CONSTRAINT `fk_ads_app_ads1` FOREIGN KEY (`app_ads_id`) REFERENCES `app_ads` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ads_site1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_ads`
--

DROP TABLE IF EXISTS `app_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `app_name` varchar(45) DEFAULT NULL,
  `package_name` varchar(128) DEFAULT NULL,
  `app_key` varchar(45) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_app_ads_site1_idx` (`site_id`),
  CONSTRAINT `fk_app_ads_site1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_ads`
--

LOCK TABLES `app_ads` WRITE;
/*!40000 ALTER TABLE `app_ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_assignment`
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
  `site_id` int(11) NOT NULL,
  `display_name` varchar(200) NOT NULL,
  `ascii_name` varchar(200) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT 'type tuong ung voi cac loai content:\n1 - video\n2 - live\n3 - music\n4 - news\n',
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
  `admin_note` varchar(4000) DEFAULT NULL,
  `show_on_portal` smallint(1) DEFAULT '1',
  `show_on_client` smallint(1) DEFAULT '1',
  `is_content_service` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_vod_category_vod_category_idx` (`parent_id`),
  KEY `idx_name` (`display_name`),
  KEY `idx_name_ascii` (`ascii_name`),
  KEY `idx_desc` (`description`(255)),
  KEY `idx_order_no` (`order_number`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_path` (`path`),
  KEY `idx_level` (`level`),
  KEY `fk_category_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_category_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
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
  `pricing_id` int(11) DEFAULT NULL,
  `content` text COMMENT 'HTML content',
  `duration` int(11) DEFAULT '0' COMMENT 'thoi gian, tinh bang giay',
  `urls` text COMMENT 'json encoded array, link downoad (doi voi video, app). Doi voi app co the la link den apptota, googleplay, apple store hoac link download truc tiep',
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
  `is_free` tinyint(1) NOT NULL DEFAULT '0',
  `images` text COMMENT 'danh sach cac images, json encoded\n',
  `status` int(11) NOT NULL DEFAULT '10' COMMENT '0 - pending\n10 - active\n1 - waiting for trancoding\n2 - inactive\n3 - for test only\n4 - rejected vi nguyen nhan 1\n5 - rejected vi nguyen nhan 2\n6 - rejected vi nguyen nhan 3\n...',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `honor` int(11) DEFAULT '0' COMMENT '0 --> nothing\n1 --> featured\n2 --> hot\n3 --> especial',
  `approved_at` int(11) DEFAULT NULL,
  `admin_note` varchar(4000) DEFAULT NULL,
  `is_series` int(11) DEFAULT '0',
  `episode_count` int(11) DEFAULT '0',
  `episode_order` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) NOT NULL,
  `day_download` smallint(2) DEFAULT '1' COMMENT 'so ngay download ke tu khi mua ',
  `author` varchar(200) DEFAULT NULL,
  `director` varchar(200) DEFAULT NULL,
  `actor` varchar(200) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  `view_date` int(11) DEFAULT '0',
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
  KEY `idx_is_free` (`is_free`),
  KEY `fk_content_pricing1_idx` (`pricing_id`),
  CONSTRAINT `fk_content_pricing1` FOREIGN KEY (`pricing_id`) REFERENCES `pricing` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
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

DROP TABLE IF EXISTS `content_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_attribute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `content_type` smallint(6) NOT NULL DEFAULT '1' COMMENT 'vod',
  `data_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - string\n2 - int\n3 - double\n4 - array\n...',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='lich su keyword tim kiem vod';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_attribute`
--

LOCK TABLES `content_attribute` WRITE;
/*!40000 ALTER TABLE `content_attribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_attribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_attribute_value`
--

DROP TABLE IF EXISTS `content_attribute_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_attribute_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content_attribute_id` int(11) NOT NULL,
  `value` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_content_attribute_value_content1_idx` (`content_id`),
  KEY `fk_content_attribute_value_content_attribute1_idx` (`content_attribute_id`),
  CONSTRAINT `fk_content_attribute_value_content1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_attribute_value_content_attribute1` FOREIGN KEY (`content_attribute_id`) REFERENCES `content_attribute` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='lich su keyword tim kiem vod';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_attribute_value`
--

LOCK TABLES `content_attribute_value` WRITE;
/*!40000 ALTER TABLE `content_attribute_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_attribute_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_category_asm`
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
  KEY `fk_vod_category_asset_mapping_vod_asset1_idx` (`content_id`),
  KEY `fk_vod_category_asset_mapping_vod_category1_idx` (`category_id`),
  CONSTRAINT `fk_vod_category_asset_mapping_vod_asset1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vod_category_asset_mapping_vod_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
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

DROP TABLE IF EXISTS `content_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `content_id` int(11) NOT NULL,
  `subscriber_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` varchar(4000) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10' COMMENT '0 - pending\n10 - active\n1 - rejected\n',
  `like` smallint(6) DEFAULT '0' COMMENT '0 - not set\n1 - like\n-1 - dislike',
  `site_id` int(10) NOT NULL,
  `admin_note` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vod_rating_vod_asset1` (`content_id`),
  KEY `fk_vod_rating_subscriber1` (`subscriber_id`),
  KEY `idx_rating` (`rating`),
  KEY `idx_create_date` (`created_at`),
  KEY `fk_content_feedback_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_content_feedback_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vod_rating_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vod_rating_vod_asset1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='cham diem VOD (1 star ... 5 stars)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_feedback`
--

LOCK TABLES `content_feedback` WRITE;
/*!40000 ALTER TABLE `content_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_keyword`
--

DROP TABLE IF EXISTS `content_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `subscriber_id` int(11) DEFAULT NULL,
  `keyword` varchar(200) NOT NULL,
  `hit_count` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vod_search_history_subscriber1` (`subscriber_id`),
  KEY `fk_content_keyword_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_content_keyword_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vod_search_history_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='lich su keyword tim kiem vod';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_keyword`
--

LOCK TABLES `content_keyword` WRITE;
/*!40000 ALTER TABLE `content_keyword` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_keyword` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_log`
--

DROP TABLE IF EXISTS `content_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10' COMMENT '10 - success\n0 - fail',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '1 - Xem qua goi cuoc (Chia se doanh thu)\n2 - Xem qua mua le (Khong chia se doanh thu)\n',
  `description` text,
  `user_agent` varchar(255) DEFAULT NULL,
  `site_id` int(10) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `content_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_content_log_service_provider1_idx` (`site_id`),
  KEY `fk_content_log_content1_idx` (`content_id`),
  KEY `fk_content_log_user_id_idx` (`user_id`),
  CONSTRAINT `fk_content_log_content1_idx` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_log_user_id_idx` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='bang log nay se lon rat nhanh';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_log`
--

LOCK TABLES `content_log` WRITE;
/*!40000 ALTER TABLE `content_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_profile`
--

DROP TABLE IF EXISTS `content_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - raw file\n2- stream\n',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `bitrate` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `quality` int(11) DEFAULT '1' COMMENT '1 - low\n2 - normal\n3 - high\n4 - HD\n5 - FHD\n10 - 4k',
  `progress` double DEFAULT '0' COMMENT 'Transcoding progress',
  `sub_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vod_rating_vod_asset1` (`content_id`),
  KEY `idx_rating` (`name`),
  KEY `idx_create_date` (`created_at`),
  CONSTRAINT `fk_vod_rating_vod_asset10` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COMMENT='cham diem VOD (1 star ... 5 stars)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_profile`
--

LOCK TABLES `content_profile` WRITE;
/*!40000 ALTER TABLE `content_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_site_asm`
--

DROP TABLE IF EXISTS `content_site_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_site_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_content_service_provider_asm_content1_idx` (`content_id`),
  KEY `fk_content_service_provider_asm_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_content_service_provider_asm_content1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_service_provider_asm_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_site_asm`
--

LOCK TABLES `content_site_asm` WRITE;
/*!40000 ALTER TABLE `content_site_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_site_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_view_log`
--

DROP TABLE IF EXISTS `content_view_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_view_log` (
  `id` bigint(20) NOT NULL,
  `subscriber_id` int(11) DEFAULT NULL,
  `content_id` int(11) NOT NULL,
  `msisdn` varchar(20) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10' COMMENT '10 - success\n0 - fail',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '1 - Xem qua goi cuoc (Chia se doanh thu)\n2 - Xem qua mua le (Khong chia se doanh thu)\n',
  `description` text,
  `user_agent` varchar(255) DEFAULT NULL,
  `channel` smallint(6) DEFAULT NULL COMMENT 'sms, wap, web, android app, ios app...',
  `site_id` int(10) NOT NULL,
  `started_at` int(11) DEFAULT NULL,
  `stopped_at` int(11) DEFAULT NULL,
  `view_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscriber_activity_log_subscriber1` (`subscriber_id`),
  KEY `client_ip` (`ip_address`),
  KEY `fk_content_view_log_service_provider1_idx` (`site_id`),
  KEY `fk_content_view_log_content1_idx` (`content_id`),
  CONSTRAINT `fk_content_view_log_content1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_view_log_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_activity_log_subscriber10` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='bang log nay se lon rat nhanh';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_view_log`
--

LOCK TABLES `content_view_log` WRITE;
/*!40000 ALTER TABLE `content_view_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_view_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dealer`
--

DROP TABLE IF EXISTS `dealer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dealer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `description` text,
  `status` int(11) NOT NULL DEFAULT '10' COMMENT '10 - active, \n0 - inactive, \n3 - testing',
  `updated_at` int(11) DEFAULT NULL,
  `user_admin_id` int(11) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `vnp_cpname` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_content_provider_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_content_provider_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dealer`
--

LOCK TABLES `dealer` WRITE;
/*!40000 ALTER TABLE `dealer` DISABLE KEYS */;
/*!40000 ALTER TABLE `dealer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dealer_subscriber_asm`
--

DROP TABLE IF EXISTS `dealer_subscriber_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dealer_subscriber_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dealer_id` int(10) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `site_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_table1_dealer1_idx` (`dealer_id`),
  KEY `fk_table1_subscriber1_idx` (`subscriber_id`),
  KEY `fk_dealer_subscriber_asm_site1_idx` (`site_id`),
  CONSTRAINT `fk_table1_dealer1` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_dealer_subscriber_asm_site1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dealer_subscriber_asm`
--

LOCK TABLES `dealer_subscriber_asm` WRITE;
/*!40000 ALTER TABLE `dealer_subscriber_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `dealer_subscriber_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device`
--

DROP TABLE IF EXISTS `device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(45) NOT NULL COMMENT 'Mac, serial...',
  `device_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - Smartbox V2',
  `device_firmware` varchar(100) DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device`
--

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;
/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_program`
--

DROP TABLE IF EXISTS `live_program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_program` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) NOT NULL COMMENT 'id cua kenh (content voi type la live)',
  `content_id` int(11) DEFAULT NULL COMMENT 'id cua program (recorded program luu trong bang content voi type = live program)',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - not yet recorded\n1 - recorded (not yet available)\n10 - recorded & available\n-1 - deleted',
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `started_at` int(11) DEFAULT NULL,
  `ended_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_live_program_content1_idx` (`channel_id`),
  KEY `fk_live_program_content2_idx` (`content_id`),
  CONSTRAINT `fk_live_program_content1` FOREIGN KEY (`channel_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_live_program_content2` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_program`
--

LOCK TABLES `live_program` WRITE;
/*!40000 ALTER TABLE `live_program` DISABLE KEYS */;
/*!40000 ALTER TABLE `live_program` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pricing`
--

DROP TABLE IF EXISTS `pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `price_coin` double DEFAULT NULL COMMENT 'gia mua = tien ao\ngia mua = sms',
  `price_sms` double DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - service\n2 - content\n',
  `watching_period` int(11) NOT NULL DEFAULT '72' COMMENT '(hours)\nthoi gian duoc xem phim khi mua le\n-1: unlimited',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_price_level_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_price_level_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pricing`
--

LOCK TABLES `pricing` WRITE;
/*!40000 ALTER TABLE `pricing` DISABLE KEYS */;
/*!40000 ALTER TABLE `pricing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_monthly_dealer_revenue`
--

DROP TABLE IF EXISTS `report_monthly_dealer_revenue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_monthly_dealer_revenue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `dealer_id` int(10) NOT NULL,
  `revenue` double DEFAULT NULL,
  `revenue_percent` double DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `report_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_report_cp_revenue_service_provider1_idx` (`site_id`),
  KEY `fk_report_cp_revenue_content_provider1_idx` (`dealer_id`),
  CONSTRAINT `fk_report_cp_revenue_content_provider10` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_report_cp_revenue_service_provider10` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_monthly_dealer_revenue`
--

LOCK TABLES `report_monthly_dealer_revenue` WRITE;
/*!40000 ALTER TABLE `report_monthly_dealer_revenue` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_monthly_dealer_revenue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_monthly_dealer_revenue_detail`
--

DROP TABLE IF EXISTS `report_monthly_dealer_revenue_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_monthly_dealer_revenue_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL,
  `site_id` int(10) NOT NULL,
  `dealer_id` int(10) NOT NULL,
  `view_count` int(11) DEFAULT NULL,
  `view_percent` double DEFAULT NULL,
  `revenue` double DEFAULT NULL,
  `revenue_percent` double DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `report_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_report_cp_revenue_subscriber1_idx` (`subscriber_id`),
  KEY `fk_report_cp_revenue_service_provider1_idx` (`site_id`),
  KEY `fk_report_cp_revenue_content_provider1_idx` (`dealer_id`),
  CONSTRAINT `fk_report_cp_revenue_content_provider1` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_report_cp_revenue_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_report_cp_revenue_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_monthly_dealer_revenue_detail`
--

LOCK TABLES `report_monthly_dealer_revenue_detail` WRITE;
/*!40000 ALTER TABLE `report_monthly_dealer_revenue_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_monthly_dealer_revenue_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_revenues_content`
--

DROP TABLE IF EXISTS `report_revenues_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_revenues_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_date` datetime DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `content_revenues` double DEFAULT '0',
  `register_revenues` double DEFAULT '0',
  `renew_revenues` double DEFAULT '0',
  `total_revenues` double DEFAULT '0',
  `buy_content_number` int(11) DEFAULT '0',
  `renew_number` int(11) DEFAULT '0',
  `register_number` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_revenues_content`
--

LOCK TABLES `report_revenues_content` WRITE;
/*!40000 ALTER TABLE `report_revenues_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_revenues_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_revenues_dealer`
--

DROP TABLE IF EXISTS `report_revenues_dealer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_revenues_dealer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_date` datetime DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `dealer_id` int(11) DEFAULT NULL,
  `total_revenues` double DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_revenues_dealer`
--

LOCK TABLES `report_revenues_dealer` WRITE;
/*!40000 ALTER TABLE `report_revenues_dealer` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_revenues_dealer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_revenues_service`
--

DROP TABLE IF EXISTS `report_revenues_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_revenues_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_date` datetime DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `renew_revenues` double DEFAULT '0',
  `register_revenues` double DEFAULT '0',
  `total_revenues` double DEFAULT '0',
  `register_number` int(11) DEFAULT '0',
  `renew_number` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_revenues_service`
--

LOCK TABLES `report_revenues_service` WRITE;
/*!40000 ALTER TABLE `report_revenues_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_revenues_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_view_category`
--

DROP TABLE IF EXISTS `report_view_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_view_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_date` datetime DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `view_count` int(11) DEFAULT '0',
  `download_count` int(11) DEFAULT '0',
  `buy_revenues` double DEFAULT '0',
  `type` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_view_category`
--

LOCK TABLES `report_view_category` WRITE;
/*!40000 ALTER TABLE `report_view_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_view_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `name` varchar(32) NOT NULL,
  `pricing_id` int(11) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text,
  `status` smallint(6) NOT NULL DEFAULT '10' COMMENT '10 - active',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `free_download_count` int(11) DEFAULT '0',
  `free_duration` int(10) DEFAULT '0' COMMENT 'Thoi gian xem phim mien phi 3G (tinh theo giay)',
  `free_view_count` int(11) DEFAULT '-1' COMMENT '-1: unlimited',
  `free_gift_count` int(11) DEFAULT '0',
  `period` int(11) DEFAULT NULL COMMENT 'chu ky cuoc (ngay)',
  `auto_renew` smallint(6) DEFAULT NULL,
  `free_days` int(11) DEFAULT '0' COMMENT 'So ngay dc mien phi',
  `max_daily_retry` int(11) DEFAULT '3',
  `max_day_failure_before_cancel` int(11) DEFAULT '35' COMMENT 'So ngay gia han that bai lien tuc toi da truoc khi huy',
  `admin_note` varchar(255) DEFAULT NULL,
  `day_register_again` smallint(2) DEFAULT '0' COMMENT 'so ngay toi thieu dang ky lai duoc mien phi cuoc ',
  `root_service_id` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_is_active` (`status`),
  KEY `idx_name` (`display_name`),
  KEY `idx_desc` (`description`(255)),
  KEY `fk_service_service_provider1_idx` (`site_id`),
  KEY `fk_service_pricing1_idx` (`pricing_id`),
  CONSTRAINT `fk_service_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_pricing1` FOREIGN KEY (`pricing_id`) REFERENCES `pricing` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='service là gói dịch vụ mà khách hàng được sử dụng,  ví dụ:\r\nMP\nMP7\nMp30';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_category_asm`
--

DROP TABLE IF EXISTS `service_category_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_category_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_service_category_asm_category1_idx` (`category_id`),
  KEY `fk_service_category_asm_service1_idx` (`service_id`),
  CONSTRAINT `fk_service_category_asm_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_category_asm_service1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2496 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_category_asm`
--

LOCK TABLES `service_category_asm` WRITE;
/*!40000 ALTER TABLE `service_category_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_category_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_group`
--

DROP TABLE IF EXISTS `service_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `display_name` varchar(200) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `type` int(10) NOT NULL,
  `site_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_service_group_site1_idx` (`site_id`),
  CONSTRAINT `fk_service_group_site1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_group`
--

LOCK TABLES `service_group` WRITE;
/*!40000 ALTER TABLE `service_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_group_asm`
--

DROP TABLE IF EXISTS `service_group_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_group_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `service_group_id` int(11) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_service_group_asm_service1_idx` (`service_id`),
  KEY `fk_service_group_asm_service_group1_idx` (`service_group_id`),
  CONSTRAINT `fk_service_group_asm_service1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_group_asm_service_group1` FOREIGN KEY (`service_group_id`) REFERENCES `service_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_group_asm`
--

LOCK TABLES `service_group_asm` WRITE;
/*!40000 ALTER TABLE `service_group_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_group_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site`
--

DROP TABLE IF EXISTS `site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text,
  `status` int(11) NOT NULL DEFAULT '10' COMMENT '10 - active, \n0 - inactive, \n3 - testing',
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - noi dia\n2 - quoc te',
  `website` varchar(255) DEFAULT NULL,
  `cp_revernue_percent` double NOT NULL DEFAULT '0',
  `user_admin_id` int(11) DEFAULT NULL,
  `service_brand_name` varchar(126) DEFAULT NULL,
  `service_sms_number` varchar(126) DEFAULT NULL,
  `free_video_count` int(11) NOT NULL DEFAULT '10' COMMENT 'so luong video mien phi duoc xem trong 1 chu ky free_video_cycle',
  `free_video_cycle` int(11) NOT NULL DEFAULT '7',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site`
--

LOCK TABLES `site` WRITE;
/*!40000 ALTER TABLE `site` DISABLE KEYS */;
/*!40000 ALTER TABLE `site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_api_credential`
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
  `package_name` varchar(200) DEFAULT NULL,
  `certificate_fingerprint` varchar(1024) DEFAULT NULL,
  `bundle_id` varchar(200) DEFAULT NULL,
  `appstore_id` varchar(200) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_service_provider_api_credential_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_service_provider_api_credential_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_api_credential`
--

LOCK TABLES `site_api_credential` WRITE;
/*!40000 ALTER TABLE `site_api_credential` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_api_credential` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_streaming_server_asm`
--

DROP TABLE IF EXISTS `site_streaming_server_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_streaming_server_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `streaming_server_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_site_streaming_server_asm_site1_idx` (`site_id`),
  KEY `fk_site_streaming_server_asm_streaming_server1_idx` (`streaming_server_id`),
  CONSTRAINT `fk_site_streaming_server_asm_site1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_site_streaming_server_asm_streaming_server1` FOREIGN KEY (`streaming_server_id`) REFERENCES `streaming_server` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_streaming_server_asm`
--

LOCK TABLES `site_streaming_server_asm` WRITE;
/*!40000 ALTER TABLE `site_streaming_server_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_streaming_server_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_message`
--

DROP TABLE IF EXISTS `sms_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) DEFAULT NULL,
  `sms_template_id` int(11) DEFAULT NULL,
  `msisdn` varchar(20) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - MO\n2 - MT\n',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `source` varchar(20) DEFAULT NULL,
  `destination` varchar(20) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `received_at` int(11) DEFAULT NULL,
  `sent_at` int(11) DEFAULT NULL,
  `mo_id` int(11) DEFAULT NULL,
  `mt_status` varchar(500) DEFAULT NULL,
  `mo_status` varchar(200) DEFAULT NULL,
  `site_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sms_message_subscriber` (`subscriber_id`),
  KEY `fk_sms_message_mo_id` (`mo_id`),
  KEY `fk_sms_message_type` (`type`),
  KEY `fk_sms_message_sms_template1_idx` (`sms_template_id`),
  KEY `fk_sms_message_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_sms_message_mo_id1` FOREIGN KEY (`mo_id`) REFERENCES `sms_message` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sms_message_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sms_message_sms_template1` FOREIGN KEY (`sms_template_id`) REFERENCES `sms_mt_template_content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sms_message_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_message`
--

LOCK TABLES `sms_message` WRITE;
/*!40000 ALTER TABLE `sms_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_mo_syntax`
--

DROP TABLE IF EXISTS `sms_mo_syntax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_mo_syntax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syntax` varchar(45) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - Dang ky\n2 - Huy\n3 - tang',
  `service_id` int(11) DEFAULT NULL,
  `site_id` int(10) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `admin_note` varchar(255) DEFAULT NULL,
  `event` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_sms_mo_syntax_service1_idx` (`service_id`),
  KEY `fk_sms_mo_syntax_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_sms_mo_syntax_service1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sms_mo_syntax_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_mo_syntax`
--

LOCK TABLES `sms_mo_syntax` WRITE;
/*!40000 ALTER TABLE `sms_mo_syntax` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_mo_syntax` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_mt_template`
--

DROP TABLE IF EXISTS `sms_mt_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_mt_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_name` varchar(255) NOT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `params` varchar(255) DEFAULT NULL COMMENT 'json encoded array - list of avaiable params\n{''service_name'',''service_expiry_date''}',
  `status` int(11) DEFAULT NULL,
  `content` varchar(4000) DEFAULT NULL,
  `type` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_name_UNIQUE` (`code_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_mt_template`
--

LOCK TABLES `sms_mt_template` WRITE;
/*!40000 ALTER TABLE `sms_mt_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_mt_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_mt_template_content`
--

DROP TABLE IF EXISTS `sms_mt_template_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_mt_template_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_name` varchar(255) NOT NULL,
  `content` varchar(4000) NOT NULL,
  `type` smallint(6) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `site_id` int(10) NOT NULL,
  `sms_mo_syntax_id` int(11) DEFAULT NULL,
  `sms_mt_template_id` int(11) NOT NULL,
  `service_id` int(10) DEFAULT NULL,
  `web_content` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sms_mt_template_service_provider1_idx` (`site_id`),
  KEY `fk_sms_mt_template_sms_mo_syntax1_idx` (`sms_mo_syntax_id`),
  KEY `fk_sms_mt_template_content_sms_mt_template1_idx` (`sms_mt_template_id`),
  KEY `fk_service_idx` (`service_id`),
  CONSTRAINT `fk_service_idx` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
  CONSTRAINT `fk_sms_mt_template_content_sms_mt_template1` FOREIGN KEY (`sms_mt_template_id`) REFERENCES `sms_mt_template` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sms_mt_template_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sms_mt_template_sms_mo_syntax1` FOREIGN KEY (`sms_mo_syntax_id`) REFERENCES `sms_mo_syntax` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='chua mau MT tra ve, ung voi moi MO\nMO/MT co de chua tham so `param` hoac `param`, trong do `param` la tham so co trong MO, `param` la tham so dinh nghia san trong he thong, vi du ngay het han, gia cuoc, chu ky....';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_mt_template_content`
--

LOCK TABLES `sms_mt_template_content` WRITE;
/*!40000 ALTER TABLE `sms_mt_template_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_mt_template_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `streaming_server`
--

DROP TABLE IF EXISTS `streaming_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streaming_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `host` varchar(64) DEFAULT NULL COMMENT 'ip/domain',
  `url_regex` varchar(255) DEFAULT NULL COMMENT 'regular expression de tao stream url',
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - vod\n2 - live\n...',
  `percent` double NOT NULL DEFAULT '100' COMMENT 'xac suat sd server nay (phân tải)',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streaming_server`
--

LOCK TABLES `streaming_server` WRITE;
/*!40000 ALTER TABLE `streaming_server` DISABLE KEYS */;
/*!40000 ALTER TABLE `streaming_server` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber`
--

DROP TABLE IF EXISTS `subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `authen_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - username(sdt)/pass\n2 - auto MAC login',
  `msisdn` varchar(45) NOT NULL COMMENT 'so dien thoai',
  `username` varchar(100) DEFAULT NULL COMMENT 'ban dau de mac dinh la so dien thoai',
  `balance` int(11) NOT NULL DEFAULT '0' COMMENT 'so du tien ao',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '10 - active',
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(200) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `last_login_at` int(11) DEFAULT NULL,
  `last_login_session` int(11) DEFAULT NULL,
  `birthday` int(11) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL COMMENT '1 - male, 0 - female',
  `avatar_url` varchar(255) DEFAULT NULL,
  `skype_id` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `client_type` int(11) DEFAULT NULL COMMENT '1 - wap, \n2 - android, \n3 - iOS\n4 - wp',
  `using_promotion` int(11) DEFAULT '0',
  `auto_renew` tinyint(1) DEFAULT '1',
  `verification_code` varchar(32) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscriber_subscriber_session1_idx` (`last_login_session`),
  KEY `email` (`email`),
  KEY `fk_subscriber_service_provider1_idx` (`site_id`),
  KEY `idx_msisdn` (`msisdn`),
  CONSTRAINT `fk_subscriber_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1008 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber`
--

LOCK TABLES `subscriber` WRITE;
/*!40000 ALTER TABLE `subscriber` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_activity`
--

DROP TABLE IF EXISTS `subscriber_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_activity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL,
  `msisdn` varchar(20) DEFAULT NULL,
  `action` int(10) DEFAULT NULL COMMENT '1 - login\n2 - logout\n3 - xem\n4 - download\n5 - gift\n6 - mua service\n7 - chu dong huy service\n8 - bi provider huy service\n9 - gia han service\n...',
  `params` mediumtext,
  `created_at` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10' COMMENT '10 - success\n0 - fail',
  `target_id` int(11) DEFAULT NULL,
  `target_type` smallint(6) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `description` text,
  `user_agent` varchar(255) DEFAULT NULL,
  `channel` smallint(6) DEFAULT NULL COMMENT 'sms, wap, web, android app, ios app...',
  `site_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscriber_activity_log_subscriber1` (`subscriber_id`),
  KEY `client_ip` (`ip_address`),
  KEY `fk_subscriber_activity_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_subscriber_activity_log_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_activity_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='bang log nay se lon rat nhanh';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_activity`
--

LOCK TABLES `subscriber_activity` WRITE;
/*!40000 ALTER TABLE `subscriber_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_content_asm`
--

DROP TABLE IF EXISTS `subscriber_content_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_content_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `msisdn` varchar(20) DEFAULT NULL,
  `description` text,
  `activated_at` int(11) NOT NULL COMMENT 'thoi diem dau tien dung dich vu\n',
  `expired_at` int(11) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10' COMMENT '10 - active\n0 - expired',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `purchase_type` int(2) NOT NULL DEFAULT '1' COMMENT '- 1 : mua de xem\n- 2 : download\n- 3 : mua de tang\n- 4 : duoc tang\n',
  `subscriber2_id` int(11) DEFAULT NULL COMMENT 'nguoi tang hoac nguoi duoc tang',
  `site_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_service_subscriber_mapping_subscriber1` (`subscriber_id`),
  KEY `idx_is_active` (`status`),
  KEY `idx_active_date` (`activated_at`),
  KEY `idx_expire_date` (`expired_at`),
  KEY `fk_vod_subscriber_mapping_vod_asset1` (`content_id`),
  KEY `fk_subscriber_video_asm_subscriber1_idx` (`subscriber2_id`),
  KEY `fk_subscriber_content_asm_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_service_subscriber_mapping_subscriber10` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_content_asm_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_content_asm_subscriber1` FOREIGN KEY (`subscriber2_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vod_subscriber_mapping_vod_asset1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Moi lan subscriber mua  phim le, mua episodes de xem se tao ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_content_asm`
--

LOCK TABLES `subscriber_content_asm` WRITE;
/*!40000 ALTER TABLE `subscriber_content_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_content_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_device_asm`
--

DROP TABLE IF EXISTS `subscriber_device_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_device_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `decscription` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscriber_device_asm_subscriber1_idx` (`subscriber_id`),
  KEY `fk_subscriber_device_asm_device1_idx` (`device_id`),
  CONSTRAINT `fk_subscriber_device_asm_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_device_asm_device1` FOREIGN KEY (`device_id`) REFERENCES `device` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_device_asm`
--

LOCK TABLES `subscriber_device_asm` WRITE;
/*!40000 ALTER TABLE `subscriber_device_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_device_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_favorite`
--

DROP TABLE IF EXISTS `subscriber_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_favorite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `site_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vod_subscriber_favorite_subscriber1` (`subscriber_id`),
  KEY `fk_vod_subscriber_favorite_vod_asset1` (`content_id`),
  KEY `idx_create_date` (`created_at`),
  KEY `fk_subscriber_favorite_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_subscriber_favorite_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vod_subscriber_favorite_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vod_subscriber_favorite_vod_asset1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_favorite`
--

LOCK TABLES `subscriber_favorite` WRITE;
/*!40000 ALTER TABLE `subscriber_favorite` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_favorite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_feedback`
--

DROP TABLE IF EXISTS `subscriber_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL,
  `content` varchar(5000) NOT NULL,
  `title` varchar(500) DEFAULT NULL,
  `create_date` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `status_log` mediumtext,
  `is_responsed` tinyint(1) NOT NULL DEFAULT '0',
  `response_date` datetime DEFAULT NULL,
  `response_user_id` bigint(11) DEFAULT NULL,
  `response_detail` varchar(5000) DEFAULT NULL,
  `site_id` int(10) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscriber_feedback_subscriber1` (`subscriber_id`),
  KEY `idx_create_date` (`create_date`),
  KEY `idx_is_responsed` (`is_responsed`),
  KEY `idx_response_date` (`response_date`),
  KEY `fk_subscriber_feedback_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_subscriber_feedback_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_feedback_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_feedback`
--

LOCK TABLES `subscriber_feedback` WRITE;
/*!40000 ALTER TABLE `subscriber_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_service_asm`
--

DROP TABLE IF EXISTS `subscriber_service_asm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_service_asm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `site_id` int(10) NOT NULL,
  `msisdn` varchar(20) DEFAULT NULL,
  `service_name` int(11) DEFAULT NULL,
  `description` text,
  `activated_at` int(11) NOT NULL COMMENT 'thoi diem dau tien dung dich vu\n',
  `renewed_at` int(11) DEFAULT NULL COMMENT 'thoi diem gia han thanh cong cuoi cung',
  `expired_at` int(11) NOT NULL,
  `last_renew_fail_at` int(11) DEFAULT NULL,
  `renew_fail_count` int(11) DEFAULT '0',
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '10:inactive,\n0:active,\n2:pending,\n3:restore\n4: deleted',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `pending_date` int(11) DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT '0' COMMENT 'trang thai hien tai: so luot da xem',
  `download_count` int(11) NOT NULL DEFAULT '0' COMMENT 'trang thai hien tai: so luot da download',
  `gift_count` int(11) NOT NULL DEFAULT '0' COMMENT 'trang thai hien tai: so luot da tang',
  `watching_time` int(10) NOT NULL DEFAULT '0',
  `subscriber2_id` int(11) DEFAULT NULL COMMENT 'id cua nguoi tang hoac nguoi dc tang',
  `transaction_id` int(11) DEFAULT NULL,
  `cancel_transaction_id` int(11) DEFAULT NULL,
  `last_renew_transaction_id` int(11) DEFAULT NULL,
  `canceled_at` smallint(2) DEFAULT '0' COMMENT 'ngay huy goi cuoc',
  `auto_renew` smallint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_service_subscriber_mapping_service1` (`service_id`),
  KEY `fk_service_subscriber_mapping_subscriber1` (`subscriber_id`),
  KEY `idx_is_active` (`status`),
  KEY `idx_active_date` (`activated_at`),
  KEY `idx_expire_date` (`expired_at`),
  KEY `modify_date` (`updated_at`),
  KEY `fk_subscriber_service_asm_subscriber1_idx` (`subscriber2_id`),
  KEY `fk_subscriber_service_asm_subscriber_transaction1_idx` (`transaction_id`),
  KEY `fk_subscriber_service_asm_subscriber_transaction2_idx` (`cancel_transaction_id`),
  KEY `fk_subscriber_service_asm_subscriber_transaction3_idx` (`last_renew_transaction_id`),
  KEY `fk_subscriber_service_asm_service_provider1_idx` (`site_id`),
  CONSTRAINT `fk_service_subscriber_mapping_service1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_subscriber_mapping_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_service_asm_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_service_asm_subscriber1` FOREIGN KEY (`subscriber2_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_service_asm_subscriber_transaction1` FOREIGN KEY (`transaction_id`) REFERENCES `subscriber_transaction` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_service_asm_subscriber_transaction2` FOREIGN KEY (`cancel_transaction_id`) REFERENCES `subscriber_transaction` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_service_asm_subscriber_transaction3` FOREIGN KEY (`last_renew_transaction_id`) REFERENCES `subscriber_transaction` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Moi lan subscriber mua 1 service (PHIM, PHIM7, PHIM30) se ta';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_service_asm`
--

LOCK TABLES `subscriber_service_asm` WRITE;
/*!40000 ALTER TABLE `subscriber_service_asm` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_service_asm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_token`
--

DROP TABLE IF EXISTS `subscriber_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL,
  `msisdn` varchar(20) DEFAULT NULL,
  `token` varchar(100) NOT NULL,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - wifi password\n2 - access token\n',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `expired_at` int(11) DEFAULT NULL,
  `cookies` varchar(1000) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `channel` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscriber_session_subscriber1` (`subscriber_id`),
  KEY `idx_session_id` (`token`),
  KEY `idx_is_active` (`status`),
  KEY `idx_create_time` (`created_at`),
  KEY `idx_expire_time` (`expired_at`),
  CONSTRAINT `fk_subscriber_session_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='wifi password hoac access token khi dang nhap vao client';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_token`
--

LOCK TABLES `subscriber_token` WRITE;
/*!40000 ALTER TABLE `subscriber_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_transaction`
--

DROP TABLE IF EXISTS `subscriber_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL,
  `msisdn` varchar(20) DEFAULT NULL,
  `payment_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - thanh toan tra truoc = tien ao\n2 - thanh toan tra truoc = sms\n3 - nap tiep/thanh toan tra sau',
  `type` smallint(6) DEFAULT NULL COMMENT '1 : mua moi\n2 : gia han\n3 : subscriber chu dong huy\n4 : bi provider huy, \n5: pending, \n6: restore\n7 : mua dich vu\n8 : mua de xem\n9 : mua de download\n10 : mua de tang\n11: tang goi cuoc\n100: nap tien qua sms',
  `service_id` int(11) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `transaction_time` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `status` int(2) NOT NULL COMMENT '10 : success\n0 : fail\n',
  `shortcode` varchar(45) DEFAULT NULL COMMENT 'đầu số nhắn tin',
  `description` varchar(200) DEFAULT NULL COMMENT 'mô tả nguyên nhân vì sao giao dịch lỗi\n',
  `cost` double(10,2) DEFAULT '0.00',
  `channel` smallint(6) DEFAULT NULL COMMENT 'Kenh thuc hien giao dich: WAP, SMS',
  `event_id` varchar(10) DEFAULT NULL COMMENT 'Ma phan biet cac nhom noi dung...',
  `error_code` varchar(20) DEFAULT NULL,
  `subscriber_activity_id` bigint(20) DEFAULT NULL,
  `subscriber_service_asm_id` int(11) DEFAULT NULL,
  `site_id` int(10) NOT NULL,
  `dealer_id` int(11) DEFAULT NULL,
  `application` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subscriber_transaction_service1` (`service_id`),
  KEY `fk_subscriber_transaction_vod_asset1` (`content_id`),
  KEY `fk_subscriber_transaction_subscriber1` (`subscriber_id`),
  KEY `idx_create_date` (`created_at`),
  KEY `idx_status` (`status`),
  KEY `idx_purchase_type` (`type`),
  KEY `channel_type` (`channel`),
  KEY `fk_subscriber_transaction_subscriber_activity1_idx` (`subscriber_activity_id`),
  KEY `fk_subscriber_transaction_subscriber_service_asm1_idx` (`subscriber_service_asm_id`),
  KEY `fk_subscriber_transaction_service_provider1_idx` (`site_id`),
  KEY `fk_subscriber_transaction_content_provider1_idx` (`dealer_id`),
  CONSTRAINT `fk_subscriber_transaction_content_provider1` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`),
  CONSTRAINT `fk_subscriber_transaction_service1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_transaction_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_transaction_subscriber1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_transaction_subscriber_activity1` FOREIGN KEY (`subscriber_activity_id`) REFERENCES `subscriber_activity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscriber_transaction_vod_asset1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 COMMENT='luu lai toan bo transaction cua subscriber';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_transaction`
--

LOCK TABLES `subscriber_transaction` WRITE;
/*!40000 ALTER TABLE `subscriber_transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sum_content`
--

DROP TABLE IF EXISTS `sum_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sum_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `active_count` int(10) DEFAULT '0' COMMENT 'Đã duyệt',
  `inactive_count` int(10) DEFAULT '0' COMMENT 'Chờ duyệt',
  `reject_count` int(10) DEFAULT '0' COMMENT 'Từ chối',
  `delete_count` int(10) DEFAULT '0' COMMENT 'Xóa',
  `content_purchase_count` int(11) DEFAULT '0',
  `type` smallint(6) DEFAULT '1' COMMENT '1 - video\n2 - live\n3 - music\n4 - news',
  `report_date` date DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sum_content_reportdate_index` (`report_date`),
  KEY `sum_content_fk_site_idx` (`site_id`),
  KEY `sum_content_fk_content_provider_idx` (`dealer_id`),
  CONSTRAINT `sum_content_fk_content_provider` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sum_content_fk_service_provider` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Thống kê số lượng nội dung theo ngày, theo loại, theo CP';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sum_content`
--

LOCK TABLES `sum_content` WRITE;
/*!40000 ALTER TABLE `sum_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `sum_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sum_content_download`
--

DROP TABLE IF EXISTS `sum_content_download`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sum_content_download` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `download_count` int(10) DEFAULT '0',
  `amount` double(10,2) DEFAULT '0.00',
  `is_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: Free\n1: Not free',
  `type` smallint(6) DEFAULT '1' COMMENT '1 - video\n2 - live\n3 - music\n4 - news',
  `report_date` date DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sum_content_download_index2` (`content_id`),
  KEY `sum_content_download_index3` (`site_id`),
  KEY `sum_content_download_index4` (`dealer_id`),
  KEY `sum_content_download_index5` (`type`),
  KEY `sum_content_download_index6` (`report_date`),
  KEY `sum_content_download_index7` (`is_free`),
  CONSTRAINT `sum_content_download_pk1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sum_content_download_pk2` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sum_content_download_pk3` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bảng thống kê content download';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sum_content_download`
--

LOCK TABLES `sum_content_download` WRITE;
/*!40000 ALTER TABLE `sum_content_download` DISABLE KEYS */;
/*!40000 ALTER TABLE `sum_content_download` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sum_content_upload`
--

DROP TABLE IF EXISTS `sum_content_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sum_content_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `upload_count` int(10) DEFAULT '0' COMMENT 'Số lượng video upload',
  `type` smallint(6) DEFAULT '1' COMMENT '1 - video\n2 - live\n3 - music\n4 - news',
  `report_date` date DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sum_content_upload_idx2` (`report_date`),
  KEY `sum_content_upload_idx3` (`site_id`),
  KEY `sum_content_upload_idx4` (`dealer_id`),
  CONSTRAINT `sum_content_upload_pk1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sum_content_upload_pk2` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Thống kê số lượng nội dung upload theo ngày, theo loại, theo CP';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sum_content_upload`
--

LOCK TABLES `sum_content_upload` WRITE;
/*!40000 ALTER TABLE `sum_content_upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `sum_content_upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sum_content_view`
--

DROP TABLE IF EXISTS `sum_content_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sum_content_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `view_count` int(10) DEFAULT '0',
  `amount` double(10,2) DEFAULT '0.00',
  `is_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: Free\n1: Not free',
  `type` smallint(6) DEFAULT '1' COMMENT '1 - video\n2 - live\n3 - music\n4 - news',
  `report_date` date DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sum_content_view_index2` (`content_id`),
  KEY `sum_content_view_index3` (`site_id`),
  KEY `sum_content_view_index4` (`dealer_id`),
  KEY `sum_content_view_index5` (`type`),
  KEY `sum_content_view_index6` (`report_date`),
  KEY `sum_content_view_index7` (`is_free`),
  CONSTRAINT `pk_sum_content_view_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `pk_sum_content_view_2` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `pk_sum_content_view_3` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Bảng thống kê content view';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sum_content_view`
--

LOCK TABLES `sum_content_view` WRITE;
/*!40000 ALTER TABLE `sum_content_view` DISABLE KEYS */;
/*!40000 ALTER TABLE `sum_content_view` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sum_service`
--

DROP TABLE IF EXISTS `sum_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sum_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `status` int(2) DEFAULT '10' COMMENT '10: success\n0: false',
  `subscriber_count` int(11) DEFAULT '0',
  `register_count_success` int(11) DEFAULT '0',
  `register_count_false` int(11) DEFAULT '0',
  `renew_count` int(11) DEFAULT '0',
  `user_cancel_count` int(11) DEFAULT '0',
  `provider_cancel_count` int(11) DEFAULT '0',
  `report_date` date NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sum_service_idx` (`status`),
  KEY `sum_service_pk1_idx` (`service_id`),
  KEY `sum_service_pk2_idx` (`site_id`),
  KEY `sum_service_pk3_idx` (`report_date`),
  CONSTRAINT `sum_service_pk1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sum_service_pk2` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Bảng thống kê gói cước: Số lượng thuê bao lũy kế, số lượng hủy, số lượng gia hạn, số lượng đăng ký (th công, th bại)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sum_service`
--

LOCK TABLES `sum_service` WRITE;
/*!40000 ALTER TABLE `sum_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `sum_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sum_service_amount`
--

DROP TABLE IF EXISTS `sum_service_amount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sum_service_amount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  `type` smallint(6) DEFAULT NULL COMMENT '1: register\n2: renew\n3: download\n4: user_cancel\n5: provider_cancel\n6: retry\n7: content_purchase\n...',
  `amount` double(10,2) DEFAULT '0.00' COMMENT 'Doanh thu ',
  `report_date` date DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sum_service_amount_idx1` (`type`),
  KEY `sum_service_amount_idx2` (`report_date`),
  KEY `sum_service_amount_pk1_idx` (`service_id`),
  KEY `sum_service_amount_pk2_idx` (`site_id`),
  CONSTRAINT `sum_service_amount_pk1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sum_service_amount_pk2` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Bảng thống kê doanh thu';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sum_service_amount`
--

LOCK TABLES `sum_service_amount` WRITE;
/*!40000 ALTER TABLE `sum_service_amount` DISABLE KEYS */;
/*!40000 ALTER TABLE `sum_service_amount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sum_view_partner`
--

DROP TABLE IF EXISTS `sum_view_partner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sum_view_partner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `view_count` int(11) DEFAULT '0',
  `report_date` date DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sum_view_partner_subscriber_idx` (`subscriber_id`),
  KEY `fk_sum_view_partner_site_idx` (`site_id`),
  KEY `fk_sum_view_partner_content_provider_idx` (`dealer_id`),
  KEY `sum_view_partner_view_count_idx` (`view_count`),
  KEY `sum_view_partner_report_date_idx` (`report_date`),
  CONSTRAINT `fk_sum_view_partner_content_provider` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sum_view_partner_service_provider` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sum_view_partner_subscriber` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Báo cáo phân chia doanh thu cho CP';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sum_view_partner`
--

LOCK TABLES `sum_view_partner` WRITE;
/*!40000 ALTER TABLE `sum_view_partner` DISABLE KEYS */;
/*!40000 ALTER TABLE `sum_view_partner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

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
  KEY `fk_user_service_provider1_idx` (`site_id`),
  KEY `fk_user_content_provider1_idx` (`dealer_id`),
  KEY `fk_user_user1_idx` (`parent_id`),
  CONSTRAINT `fk_user_content_provider1` FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_service_provider1` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_user1` FOREIGN KEY (`parent_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
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
