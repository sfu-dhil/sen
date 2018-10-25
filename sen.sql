-- MySQL dump 10.13  Distrib 5.7.23, for osx10.11 (x86_64)
--
-- Host: localhost    Database: sen
-- ------------------------------------------------------
-- Server version	5.7.23

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
-- Table structure for table `blog_page`
--

DROP TABLE IF EXISTS `blog_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `include_comments` tinyint(1) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` longtext COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `searchable` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F4DA3AB0A76ED395` (`user_id`),
  FULLTEXT KEY `blog_page_content` (`title`,`searchable`),
  CONSTRAINT `FK_F4DA3AB0A76ED395` FOREIGN KEY (`user_id`) REFERENCES `nines_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_page`
--
-- ORDER BY:  `id`

LOCK TABLES `blog_page` WRITE;
/*!40000 ALTER TABLE `blog_page` DISABLE KEYS */;
INSERT INTO `blog_page` (`id`, `user_id`, `weight`, `public`, `include_comments`, `title`, `excerpt`, `content`, `searchable`, `created`, `updated`) VALUES (1,1,0,0,0,'Hello draft.','I am draft excerpt.','I am an excerpt and I like drafts.','I am an excerpt and I like drafts.','2018-10-10 14:12:11','2018-10-10 14:12:11');
INSERT INTO `blog_page` (`id`, `user_id`, `weight`, `public`, `include_comments`, `title`, `excerpt`, `content`, `searchable`, `created`, `updated`) VALUES (2,1,0,1,0,'Hello world.','I am published excerpt.','I am an excerpt and I like publishing.','I am an excerpt and I like publishing.','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `blog_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_post`
--

DROP TABLE IF EXISTS `blog_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `include_comments` tinyint(1) NOT NULL,
  `excerpt` longtext COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `searchable` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BA5AE01D12469DE2` (`category_id`),
  KEY `IDX_BA5AE01D6BF700BD` (`status_id`),
  KEY `IDX_BA5AE01DA76ED395` (`user_id`),
  FULLTEXT KEY `blog_post_content` (`title`,`searchable`),
  CONSTRAINT `FK_BA5AE01D12469DE2` FOREIGN KEY (`category_id`) REFERENCES `blog_post_category` (`id`),
  CONSTRAINT `FK_BA5AE01D6BF700BD` FOREIGN KEY (`status_id`) REFERENCES `blog_post_status` (`id`),
  CONSTRAINT `FK_BA5AE01DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `nines_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_post`
--
-- ORDER BY:  `id`

LOCK TABLES `blog_post` WRITE;
/*!40000 ALTER TABLE `blog_post` DISABLE KEYS */;
INSERT INTO `blog_post` (`id`, `category_id`, `status_id`, `user_id`, `title`, `include_comments`, `excerpt`, `content`, `searchable`, `created`, `updated`) VALUES (1,1,1,1,'Hello draft.',0,'I am draft excerpt.','I am an excerpt and I like drafts.','I am an excerpt and I like drafts.','2018-10-10 14:12:11','2018-10-10 14:12:11');
INSERT INTO `blog_post` (`id`, `category_id`, `status_id`, `user_id`, `title`, `include_comments`, `excerpt`, `content`, `searchable`, `created`, `updated`) VALUES (2,1,1,1,'Hello world.',0,'I am published excerpt.','I am an excerpt and I like publishing.','I am an excerpt and I like publishing.','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `blog_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_post_category`
--

DROP TABLE IF EXISTS `blog_post_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_post_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_CA275A0C5E237E06` (`name`),
  FULLTEXT KEY `IDX_CA275A0CEA750E8` (`label`),
  FULLTEXT KEY `IDX_CA275A0C6DE44026` (`description`),
  FULLTEXT KEY `IDX_CA275A0CEA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_post_category`
--
-- ORDER BY:  `id`

LOCK TABLES `blog_post_category` WRITE;
/*!40000 ALTER TABLE `blog_post_category` DISABLE KEYS */;
INSERT INTO `blog_post_category` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (1,'announcement','Announcement','Stuff happened.','2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `blog_post_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_post_status`
--

DROP TABLE IF EXISTS `blog_post_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_post_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `public` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_92121D875E237E06` (`name`),
  FULLTEXT KEY `IDX_92121D87EA750E8` (`label`),
  FULLTEXT KEY `IDX_92121D876DE44026` (`description`),
  FULLTEXT KEY `IDX_92121D87EA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_post_status`
--
-- ORDER BY:  `id`

LOCK TABLES `blog_post_status` WRITE;
/*!40000 ALTER TABLE `blog_post_status` DISABLE KEYS */;
INSERT INTO `blog_post_status` (`id`, `name`, `label`, `description`, `public`, `created`, `updated`) VALUES (1,'draft','Draft','Drafty',0,'2018-10-10 14:12:10','2018-10-10 14:12:10');
INSERT INTO `blog_post_status` (`id`, `name`, `label`, `description`, `public`, `created`, `updated`) VALUES (2,'published','Published','Public',1,'2018-10-10 14:12:10','2018-10-10 14:12:10');
/*!40000 ALTER TABLE `blog_post_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `city`
--
-- ORDER BY:  `id`

LOCK TABLES `city` WRITE;
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` (`id`, `name`, `created`, `updated`) VALUES (1,'Abbeville','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `city` (`id`, `name`, `created`, `updated`) VALUES (2,'Saint Bernard Parish','2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_id` int(11) NOT NULL,
  `fullname` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `follow_up` tinyint(1) NOT NULL,
  `entity` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9474526C6BF700BD` (`status_id`),
  FULLTEXT KEY `comment_ft_idx` (`fullname`,`content`),
  CONSTRAINT `FK_9474526C6BF700BD` FOREIGN KEY (`status_id`) REFERENCES `comment_status` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--
-- ORDER BY:  `id`

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` (`id`, `status_id`, `fullname`, `email`, `follow_up`, `entity`, `content`, `created`, `updated`) VALUES (1,1,'Bobby','bob@example.com',0,'Nines\\BlogBundle\\Entity\\Page:2','Comment 1','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_note`
--

DROP TABLE IF EXISTS `comment_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E98B58F8A76ED395` (`user_id`),
  KEY `IDX_E98B58F8F8697D13` (`comment_id`),
  FULLTEXT KEY `commentnote_ft_idx` (`content`),
  CONSTRAINT `FK_E98B58F8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `nines_user` (`id`),
  CONSTRAINT `FK_E98B58F8F8697D13` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_note`
--
-- ORDER BY:  `id`

LOCK TABLES `comment_note` WRITE;
/*!40000 ALTER TABLE `comment_note` DISABLE KEYS */;
INSERT INTO `comment_note` (`id`, `user_id`, `comment_id`, `content`, `created`, `updated`) VALUES (1,1,1,'This is a note.','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `comment_note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_status`
--

DROP TABLE IF EXISTS `comment_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B1133D0E5E237E06` (`name`),
  FULLTEXT KEY `IDX_B1133D0EEA750E8` (`label`),
  FULLTEXT KEY `IDX_B1133D0E6DE44026` (`description`),
  FULLTEXT KEY `IDX_B1133D0EEA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_status`
--
-- ORDER BY:  `id`

LOCK TABLES `comment_status` WRITE;
/*!40000 ALTER TABLE `comment_status` DISABLE KEYS */;
INSERT INTO `comment_status` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (1,'submitted','Submitted','The comment has been submitted, but not yet vetted.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `comment_status` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (2,'unpublished','Unpublished','Comment has not been approved for publication.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `comment_status` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (3,'published','Published','Comment has been approved for publication.','2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `comment_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element`
--

DROP TABLE IF EXISTS `element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `uri` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_41405E39841CB121` (`uri`),
  FULLTEXT KEY `IDX_41405E39EA750E8` (`label`),
  FULLTEXT KEY `IDX_41405E396DE44026` (`description`),
  FULLTEXT KEY `IDX_41405E39EA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element`
--
-- ORDER BY:  `id`

LOCK TABLES `element` WRITE;
/*!40000 ALTER TABLE `element` DISABLE KEYS */;
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (1,'dc_contributor','Contributor','An entity responsible for making contributions to the resource.','http://purl.org/dc/elements/1.1/contributor','Examples of a Contributor include a person, an organization, or a service. Typically, the name of a Contributor should be used to indicate the entity.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (2,'dc_coverage','Coverage','The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant.','http://purl.org/dc/elements/1.1/coverage','Spatial topic and spatial applicability may be a named place or a location specified by its geographic coordinates. Temporal topic may be a named period, date, or date range. A jurisdiction may be a named administrative entity or a geographic place to which the resource applies. Recommended best practice is to use a controlled vocabulary such as the Thesaurus of Geographic Names [TGN]. Where appropriate, named places or time periods can be used in preference to numeric identifiers such as sets of coordinates or date ranges.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (3,'dc_creator','Creator','An entity primarily responsible for making the resource.','http://purl.org/dc/elements/1.1/creator','Examples of a Creator include a person, an organization, or a service. Typically, the name of a Creator should be used to indicate the entity.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (4,'dc_date','Date','A point or period of time associated with an event in the lifecycle of the resource.','http://purl.org/dc/elements/1.1/date','Date may be used to express temporal information at any level of granularity. Recommended best practice is to use an encoding scheme, such as the W3CDTF profile of ISO 8601 [W3CDTF].','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (5,'dc_description','Description','An account of the resource.','http://purl.org/dc/elements/1.1/description','Description may include but is not limited to: an abstract, a table of contents, a graphical representation, or a free-text account of the resource.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (6,'dc_format','Format','The file format, physical medium, or dimensions of the resource.','http://purl.org/dc/elements/1.1/format','Examples of dimensions include size and duration. Recommended best practice is to use a controlled vocabulary such as the list of Internet Media Types [MIME].','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (7,'dc_identifier','Identifier','An unambiguous reference to the resource within a given context.','http://purl.org/dc/elements/1.1/identifier','Recommended best practice is to identify the resource by means of a string conforming to a formal identification system.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (8,'dc_language','Language','A language of the resource.','http://purl.org/dc/elements/1.1/language','Recommended best practice is to use a controlled vocabulary such as RFC 4646 [RFC4646].','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (9,'dc_publisher','Publisher','An entity responsible for making the resource available.','http://purl.org/dc/elements/1.1/publisher','Examples of a Publisher include a person, an organization, or a service. Typically, the name of a Publisher should be used to indicate the entity.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (10,'dc_relation','Relation','A related resource.','http://purl.org/dc/elements/1.1/relation','Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (11,'dc_rights','Rights','Information about rights held in and over the resource.','http://purl.org/dc/elements/1.1/rights','Typically, rights information includes a statement about various property rights associated with the resource, including intellectual property rights.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (12,'dc_source','Source','A related resource from which the described resource is derived.','http://purl.org/dc/elements/1.1/source','The described resource may be derived from the related resource in whole or in part. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (13,'dc_subject','Subject','The topic of the resource.','http://purl.org/dc/elements/1.1/subject','','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (14,'dc_title','Title','A name given to the resource.','http://purl.org/dc/elements/1.1/title','Typically, a Title will be a name by which the resource is formally known.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `element` (`id`, `name`, `label`, `description`, `uri`, `comment`, `created`, `updated`) VALUES (15,'dc_type','Type','The nature or genre of the resource.','http://purl.org/dc/elements/1.1/type','Recommended best practice is to use a controlled vocabulary such as the DCMI Type Vocabulary [DCMITYPE]. To describe the file format, physical medium, or dimensions of the resource, use the Format element.','2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `element` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `written_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3BAE0AA712469DE2` (`category_id`),
  KEY `IDX_3BAE0AA764D218E` (`location_id`),
  CONSTRAINT `FK_3BAE0AA712469DE2` FOREIGN KEY (`category_id`) REFERENCES `event_category` (`id`),
  CONSTRAINT `FK_3BAE0AA764D218E` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--
-- ORDER BY:  `id`

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` (`id`, `category_id`, `location_id`, `written_date`, `date`, `note`, `created`, `updated`) VALUES (1,1,1,'21 Feb 1792','1792-01-21','Seen original.','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_category`
--

DROP TABLE IF EXISTS `event_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_40A0F0115E237E06` (`name`),
  FULLTEXT KEY `IDX_40A0F011EA750E8` (`label`),
  FULLTEXT KEY `IDX_40A0F0116DE44026` (`description`),
  FULLTEXT KEY `IDX_40A0F011EA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_category`
--
-- ORDER BY:  `id`

LOCK TABLES `event_category` WRITE;
/*!40000 ALTER TABLE `event_category` DISABLE KEYS */;
INSERT INTO `event_category` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (1,'baptism','Baptism','Baptism is a rite of admission into Christianity.','2018-10-10 14:12:09','2018-10-10 14:12:09');
INSERT INTO `event_category` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (2,'manumission','Manumission','Manumission, or affranchisement, is the act of an owner freeing his or her slaves.','2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `event_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_person`
--

DROP TABLE IF EXISTS `event_person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_person` (
  `event_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`,`person_id`),
  KEY `IDX_645A62471F7E88B` (`event_id`),
  KEY `IDX_645A624217BBB47` (`person_id`),
  CONSTRAINT `FK_645A624217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_645A62471F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_person`
--
-- ORDER BY:  `event_id`,`person_id`

LOCK TABLES `event_person` WRITE;
/*!40000 ALTER TABLE `event_person` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ledger`
--

DROP TABLE IF EXISTS `ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ledger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notary_id` int(11) NOT NULL,
  `volume` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C07BA4BCACC994D3` (`notary_id`),
  CONSTRAINT `FK_C07BA4BCACC994D3` FOREIGN KEY (`notary_id`) REFERENCES `notary` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ledger`
--
-- ORDER BY:  `id`

LOCK TABLES `ledger` WRITE;
/*!40000 ALTER TABLE `ledger` DISABLE KEYS */;
INSERT INTO `ledger` (`id`, `notary_id`, `volume`, `year`, `created`, `updated`) VALUES (1,1,'9; 10',1794,'2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `ledger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5E9E89CB12469DE2` (`category_id`),
  CONSTRAINT `FK_5E9E89CB12469DE2` FOREIGN KEY (`category_id`) REFERENCES `location_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--
-- ORDER BY:  `id`

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` (`id`, `category_id`, `name`, `created`, `updated`) VALUES (1,1,'Saint Barnabas Church','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_category`
--

DROP TABLE IF EXISTS `location_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D7193B235E237E06` (`name`),
  FULLTEXT KEY `IDX_D7193B23EA750E8` (`label`),
  FULLTEXT KEY `IDX_D7193B236DE44026` (`description`),
  FULLTEXT KEY `IDX_D7193B23EA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_category`
--
-- ORDER BY:  `id`

LOCK TABLES `location_category` WRITE;
/*!40000 ALTER TABLE `location_category` DISABLE KEYS */;
INSERT INTO `location_category` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (1,'church','Church',NULL,'2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `location_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nines_user`
--

DROP TABLE IF EXISTS `nines_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nines_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5BA994A192FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_5BA994A1A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_5BA994A1C05FB297` (`confirmation_token`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nines_user`
--
-- ORDER BY:  `id`

LOCK TABLES `nines_user` WRITE;
/*!40000 ALTER TABLE `nines_user` DISABLE KEYS */;
INSERT INTO `nines_user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`, `fullname`, `institution`, `data`) VALUES (1,'admin@example.com','admin@example.com','admin@example.com','admin@example.com',1,NULL,'$2y$13$eWRKikKdcbP98PFE3FLLDekTKviDwj9m5UkJdhBJg2vmlCaEQhrtW',NULL,NULL,NULL,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}','Admin user',NULL,'a:0:{}');
INSERT INTO `nines_user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`, `fullname`, `institution`, `data`) VALUES (2,'user@example.com','user@example.com','user@example.com','user@example.com',1,NULL,'$2y$13$GliJPuxqIeqAdrhEV/ZRfuLIjxphFa7NPnRPbcEd4UK8MutMON1lS',NULL,NULL,NULL,'a:0:{}','Unprivileged user',NULL,'a:0:{}');
/*!40000 ALTER TABLE `nines_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notary`
--

DROP TABLE IF EXISTS `notary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notary`
--
-- ORDER BY:  `id`

LOCK TABLES `notary` WRITE;
/*!40000 ALTER TABLE `notary` DISABLE KEYS */;
INSERT INTO `notary` (`id`, `name`, `created`, `updated`) VALUES (1,'Billy Terwilliger','2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `notary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `birth_place_id` int(11) DEFAULT NULL,
  `race_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `native` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sex` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_34DCD176B4BB6BBC` (`birth_place_id`),
  KEY `IDX_34DCD1766E59D40D` (`race_id`),
  CONSTRAINT `FK_34DCD1766E59D40D` FOREIGN KEY (`race_id`) REFERENCES `race` (`id`),
  CONSTRAINT `FK_34DCD176B4BB6BBC` FOREIGN KEY (`birth_place_id`) REFERENCES `city` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--
-- ORDER BY:  `id`

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
INSERT INTO `person` (`id`, `birth_place_id`, `race_id`, `first_name`, `last_name`, `alias`, `native`, `occupation`, `sex`, `birth_date`, `birth_status`, `status`, `created`, `updated`) VALUES (1,1,1,'Emery','Ville','a:2:{i:0;s:2:\"Em\";i:1;s:2:\"EV\";}','Attakapas','1775 soldier','M','1760/01/02',NULL,'free','2018-10-10 14:12:11','2018-10-10 14:12:11');
INSERT INTO `person` (`id`, `birth_place_id`, `race_id`, `first_name`, `last_name`, `alias`, `native`, `occupation`, `sex`, `birth_date`, `birth_status`, `status`, `created`, `updated`) VALUES (2,1,1,'Savanah','Kansas','a:0:{}',NULL,'1776 busness person','F','1761/02/03',NULL,'free','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `race`
--

DROP TABLE IF EXISTS `race`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `race` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_DA6FBBAF5E237E06` (`name`),
  FULLTEXT KEY `IDX_DA6FBBAFEA750E8` (`label`),
  FULLTEXT KEY `IDX_DA6FBBAF6DE44026` (`description`),
  FULLTEXT KEY `IDX_DA6FBBAFEA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `race`
--
-- ORDER BY:  `id`

LOCK TABLES `race` WRITE;
/*!40000 ALTER TABLE `race` DISABLE KEYS */;
INSERT INTO `race` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (1,'indian','Indian',NULL,'2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `race` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relationship`
--

DROP TABLE IF EXISTS `relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relationship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `start_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_200444A012469DE2` (`category_id`),
  KEY `IDX_200444A0217BBB47` (`person_id`),
  KEY `IDX_200444A03256915B` (`relation_id`),
  CONSTRAINT `FK_200444A012469DE2` FOREIGN KEY (`category_id`) REFERENCES `relationship_category` (`id`),
  CONSTRAINT `FK_200444A0217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_200444A03256915B` FOREIGN KEY (`relation_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relationship`
--
-- ORDER BY:  `id`

LOCK TABLES `relationship` WRITE;
/*!40000 ALTER TABLE `relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relationship_category`
--

DROP TABLE IF EXISTS `relationship_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relationship_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_921C337F5E237E06` (`name`),
  FULLTEXT KEY `IDX_921C337FEA750E8` (`label`),
  FULLTEXT KEY `IDX_921C337F6DE44026` (`description`),
  FULLTEXT KEY `IDX_921C337FEA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relationship_category`
--
-- ORDER BY:  `id`

LOCK TABLES `relationship_category` WRITE;
/*!40000 ALTER TABLE `relationship_category` DISABLE KEYS */;
INSERT INTO `relationship_category` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (1,'rel','Rel',NULL,'2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `relationship_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `residence`
--

DROP TABLE IF EXISTS `residence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `residence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3275823217BBB47` (`person_id`),
  KEY `IDX_32758238BAC62AF` (`city_id`),
  CONSTRAINT `FK_3275823217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_32758238BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `residence`
--
-- ORDER BY:  `id`

LOCK TABLES `residence` WRITE;
/*!40000 ALTER TABLE `residence` DISABLE KEYS */;
INSERT INTO `residence` (`id`, `person_id`, `city_id`, `date`, `created`, `updated`) VALUES (1,1,1,'1780','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `residence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_category`
--

DROP TABLE IF EXISTS `transaction_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_483E30A95E237E06` (`name`),
  FULLTEXT KEY `IDX_483E30A9EA750E8` (`label`),
  FULLTEXT KEY `IDX_483E30A96DE44026` (`description`),
  FULLTEXT KEY `IDX_483E30A9EA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_category`
--
-- ORDER BY:  `id`

LOCK TABLES `transaction_category` WRITE;
/*!40000 ALTER TABLE `transaction_category` DISABLE KEYS */;
INSERT INTO `transaction_category` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (1,'sale-property','Sale of property',NULL,'2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `transaction_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_party_id` int(11) NOT NULL,
  `second_party_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `ledger_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `page` int(11) NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_party_note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conjunction` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `second_party_note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EAA81A4C95242202` (`first_party_id`),
  KEY `IDX_EAA81A4C12284F3C` (`second_party_id`),
  KEY `IDX_EAA81A4C12469DE2` (`category_id`),
  KEY `IDX_EAA81A4CA7B913DD` (`ledger_id`),
  CONSTRAINT `FK_EAA81A4C12284F3C` FOREIGN KEY (`second_party_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_EAA81A4C12469DE2` FOREIGN KEY (`category_id`) REFERENCES `transaction_category` (`id`),
  CONSTRAINT `FK_EAA81A4C95242202` FOREIGN KEY (`first_party_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_EAA81A4CA7B913DD` FOREIGN KEY (`ledger_id`) REFERENCES `ledger` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--
-- ORDER BY:  `id`

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` (`id`, `first_party_id`, `second_party_id`, `category_id`, `ledger_id`, `date`, `page`, `notes`, `first_party_note`, `conjunction`, `second_party_note`, `created`, `updated`) VALUES (1,1,2,1,1,'1790-04-20',27,NULL,'and wife','to','and children','2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `witness`
--

DROP TABLE IF EXISTS `witness`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `witness` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `person_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CFF1AA0E12469DE2` (`category_id`),
  KEY `IDX_CFF1AA0E217BBB47` (`person_id`),
  KEY `IDX_CFF1AA0E71F7E88B` (`event_id`),
  CONSTRAINT `FK_CFF1AA0E12469DE2` FOREIGN KEY (`category_id`) REFERENCES `witness_category` (`id`),
  CONSTRAINT `FK_CFF1AA0E217BBB47` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_CFF1AA0E71F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `witness`
--
-- ORDER BY:  `id`

LOCK TABLES `witness` WRITE;
/*!40000 ALTER TABLE `witness` DISABLE KEYS */;
INSERT INTO `witness` (`id`, `category_id`, `person_id`, `event_id`, `created`, `updated`) VALUES (1,1,1,1,'2018-10-10 14:12:11','2018-10-10 14:12:11');
/*!40000 ALTER TABLE `witness` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `witness_category`
--

DROP TABLE IF EXISTS `witness_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `witness_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_F974E17B5E237E06` (`name`),
  FULLTEXT KEY `IDX_F974E17BEA750E8` (`label`),
  FULLTEXT KEY `IDX_F974E17B6DE44026` (`description`),
  FULLTEXT KEY `IDX_F974E17BEA750E86DE44026` (`label`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `witness_category`
--
-- ORDER BY:  `id`

LOCK TABLES `witness_category` WRITE;
/*!40000 ALTER TABLE `witness_category` DISABLE KEYS */;
INSERT INTO `witness_category` (`id`, `name`, `label`, `description`, `created`, `updated`) VALUES (1,'wedding','Wedding',NULL,'2018-10-10 14:12:09','2018-10-10 14:12:09');
/*!40000 ALTER TABLE `witness_category` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-10-17 11:54:03
