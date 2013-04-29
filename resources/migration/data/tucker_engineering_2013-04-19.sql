# Sequel Pro dump
# Version 1630
# http://code.google.com/p/sequel-pro
#
# Host: 127.0.0.1 (MySQL 5.1.49)
# Database: tucker_engineering
# Generation Time: 2013-04-19 14:14:33 -0500
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table assets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `assets`;

CREATE TABLE `assets` (
  `asset_id` int(11) DEFAULT NULL,
  `filename` varchar(32) DEFAULT NULL,
  `date_added` int(11) DEFAULT NULL,
  `date_updated` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table checksums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `checksums`;

CREATE TABLE `checksums` (
  `id` int(11) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table client_contact
# ------------------------------------------------------------

DROP TABLE IF EXISTS `client_contact`;

CREATE TABLE `client_contact` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `info` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clients`;

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `search_name` varchar(64) DEFAULT NULL,
  `title` varchar(64) DEFAULT NULL,
  `date_added` int(16) DEFAULT NULL,
  `date_updated` int(16) DEFAULT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` varchar(128) DEFAULT NULL,
  `note` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` (`name`,`value`,`note`)
VALUES
	('application.date_format','F jS \\a\\t g:ia',NULL),
	('application.extensions.location','../resources/extensions/',NULL),
	('application.search.excerpt.lines','3',NULL),
	('application.search.excerpt.words','7',NULL),
	('clients.requester.relationships.1','owner',NULL),
	('clients.requester.relationships.2','buyer',NULL),
	('clients.requester.relationships.3','tenant',NULL),
	('clients.requester.relationships.4','agent',NULL),
	('clients.requester.relationships.5','executor',NULL),
	('clients.requester.relationships.6','lender',NULL),
	('clients.requester.relationships.7','contractor',NULL),
	('company.locations.1.branch_name','Austin',NULL),
	('company.locations.1.default_area_code','512',NULL),
	('company.locations.1.localization','US',NULL),
	('company.locations.1.map_center.latitude','30.2678',NULL),
	('company.locations.1.map_center.longitude','97.7426',NULL),
	('company.locations.1.property_id','0',NULL),
	('company.name','Tucker Engineering, Inc.',NULL),
	('jobs.types.1.color','#00FF00',NULL),
	('jobs.types.1.name','Inspection',NULL),
	('jobs.types.1.time_required','60','Units are minutes'),
	('jobs.types.2.color','#00FFFF',NULL),
	('jobs.types.2.name','Certification',NULL),
	('jobs.types.2.time_required','20','Units are minutes '),
	('jobs.types.3.color','#0000FF',NULL),
	('jobs.types.3.name','Design',NULL),
	('jobs.types.3.time_required','20','Units are minutes'),
	('jobs.types.4.color','#cccccc',NULL),
	('jobs.types.4.name','Unavailable',NULL),
	('mail.invoice.subject','Your invoice for job #{job_id} at {location}',NULL),
	('mail.new_user.subject','An account has been created for you',NULL),
	('meta.property.foundation.1','Slab',NULL),
	('meta.property.foundation.2','Pier and Beam',NULL),
	('meta.property.foundation.3','Combination',NULL),
	('meta.property.types.1','Single Family',NULL),
	('meta.property.types.2','Duplex',NULL),
	('meta.property.types.3','Fourplex',NULL),
	('meta.property.types.4','Commercial',NULL),
	('tender.overbalance_name','Overbalance Payment','This name is reserved'),
	('tender.types.1.aggregation','+',NULL),
	('tender.types.1.name','cash',NULL),
	('tender.types.2.aggregation','+',NULL),
	('tender.types.2.name','check',NULL),
	('tender.types.3.aggregation','+',NULL),
	('tender.types.3.name','card',NULL),
	('tender.types.4.aggregation','-',NULL),
	('tender.types.4.name','refund',NULL),
	('time.format','12h','Options are 12h or 24h'),
	('users.default_permissions','/ 2|/admin 0','separate permission from function by space and functions by |');

/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table invoices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `invoices`;

CREATE TABLE `invoices` (
  `invoice_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `date_added` int(11) DEFAULT NULL,
  `date_sent` int(11) DEFAULT NULL,
  UNIQUE KEY `UNIQUE` (`invoice_id`,`client_id`,`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `condition` varchar(32) DEFAULT NULL,
  `note` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `UNIQUE` (`name`,`condition`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` (`item_id`,`name`,`cost`,`condition`,`note`)
VALUES
	(0,'Travel Fee',0.00,'GENERIC','Autocomplete travel fee'),
	(1,'Travel Fee',125.00,'68616','Bastrop county'),
	(2,'Travel Fee',150.00,'78636','Johnson City'),
	(3,'Travel Fee',100.00,'73301','Dripping Springs'),
	(4,'Travel Fee',125.00,'76116','Bastrop county'),
	(5,'Travel Fee',150.00,'76522','Copperas Cove'),
	(6,'Travel Fee',50.00,'76527','Georgetown'),
	(7,'Travel Fee',50.00,'76530','Georgetown'),
	(8,'Travel Fee',50.00,'76537','Georgetown'),
	(9,'Travel Fee',175.00,'76541','Killeen'),
	(10,'Travel Fee',125.00,'76542','Burnet county'),
	(11,'Travel Fee',175.00,'76544','Fort Hood'),
	(12,'Travel Fee',125.00,'76549','Burnet county'),
	(13,'Travel Fee',150.00,'76550','Lampasas'),
	(14,'Travel Fee',75.00,'76571','Salado'),
	(15,'Travel Fee',75.00,'76574','Taylor'),
	(16,'Travel Fee',175.00,'78130','New Braunfels'),
	(17,'Travel Fee',175.00,'78131','New Braunfels'),
	(18,'Travel Fee',175.00,'78132','New Braunfels'),
	(19,'Travel Fee',175.00,'78135','New Braunfels'),
	(20,'Travel Fee',100.00,'78218','Dripping Springs'),
	(21,'Travel Fee',50.00,'78268','Georgetown'),
	(22,'Travel Fee',125.00,'78600','Burnet county'),
	(23,'Travel Fee',125.00,'78602','Bastrop County'),
	(24,'Travel Fee',125.00,'78605','Burnet county'),
	(25,'Travel Fee',125.00,'78608','Burnet county'),
	(26,'Travel Fee',75.00,'78610','Buda'),
	(27,'Travel Fee',125.00,'78611','Burnet county'),
	(28,'Travel Fee',75.00,'78612','Cedar Creek'),
	(29,'Travel Fee',125.00,'78616','Bastrop county'),
	(30,'Travel Fee',100.00,'78619','Dripping Springs'),
	(31,'Travel Fee',100.00,'78620','Dripping Springs'),
	(32,'Travel Fee',75.00,'78621','Elgin'),
	(33,'Travel Fee',50.00,'78624','Georgetown'),
	(34,'Travel Fee',50.00,'78626','Georgetown'),
	(35,'Travel Fee',50.00,'78627','Georgetown'),
	(36,'Travel Fee',50.00,'78628','Georgetown'),
	(37,'Travel Fee',50.00,'78630','Georgetown'),
	(38,'Travel Fee',50.00,'78634','Georgetown'),
	(40,'Travel Fee',125.00,'78639','Burnet county'),
	(41,'Travel Fee',75.00,'78640','Kyle'),
	(42,'Travel Fee',50.00,'78641','Leander'),
	(43,'Travel Fee',75.00,'78642','Liberty Hill'),
	(44,'Travel Fee',125.00,'78644','Lockhart'),
	(45,'Travel Fee',50.00,'78645','Jonestown'),
	(46,'Travel Fee',50.00,'78646','Leander'),
	(47,'Travel Fee',75.00,'78650','Elgin'),
	(48,'Travel Fee',50.00,'78651','Coupland'),
	(49,'Travel Fee',75.00,'78652','San Leanna'),
	(50,'Travel Fee',125.00,'78654','Marble Falls'),
	(51,'Travel Fee',125.00,'78657','Marble Falls'),
	(52,'Travel Fee',125.00,'78659','Bastrop County'),
	(53,'Travel Fee',75.00,'78662','Liberty Hill'),
	(54,'Travel Fee',50.00,'78664','Georgetown'),
	(55,'Travel Fee',150.00,'78666','San Marcos'),
	(56,'Travel Fee',150.00,'78667','San Marcos'),
	(57,'Travel Fee',100.00,'78669','Spicewood'),
	(58,'Travel Fee',125.00,'78672','Burnet county'),
	(59,'Travel Fee',150.00,'78676','Wimberley'),
	(60,'Travel Fee',125.00,'78717','Bastrop county'),
	(61,'Travel Fee',100.00,'78720','Dripping Springs'),
	(62,'Travel Fee',125.00,'78721','Bastrop county'),
	(63,'Travel Fee',125.00,'78725','Burnet county'),
	(64,'Travel Fee',75.00,'78734','Lakeway'),
	(65,'Travel Fee',100.00,'78736','Dripping Springs'),
	(66,'Travel Fee',100.00,'78737','Dripping Springs'),
	(67,'Travel Fee',75.00,'78738','Lakeway'),
	(68,'Travel Fee',75.00,'78748','San Leanna'),
	(69,'Travel Fee',125.00,'78752','Burnet county'),
	(70,'Travel Fee',150.00,'78942','Giddings'),
	(71,'Travel Fee',125.00,'78947','Lexington'),
	(72,'Travel Fee',125.00,'78953','Bastrop County'),
	(73,'Travel Fee',50.00,'79628','Georgetown'),
	(74,'Structural Inspection',450.00,NULL,NULL),
	(75,'Written Report',100.00,NULL,NULL),
	(76,'Letter',100.00,NULL,NULL),
	(77,'Certification',150.00,NULL,NULL),
	(78,'Pier and Beam Surcharge',100.00,'Pier and Beam',NULL),
	(79,'Duplex Surcharge',25.00,'Duplex',NULL),
	(80,'Fourplex Surcharge',50.00,'Fourplex',NULL),
	(81,'On site consultation',150.00,NULL,NULL);

/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table job_assets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `job_assets`;

CREATE TABLE `job_assets` (
  `id` int(11) NOT NULL DEFAULT '0',
  `type` varchar(16) NOT NULL DEFAULT '',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`type`,`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `requester_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `requester_relationship` varchar(64) DEFAULT NULL,
  `date_billed` int(16) DEFAULT NULL,
  `date_added` int(16) DEFAULT NULL,
  `date_updated` int(16) DEFAULT NULL,
  PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ledger
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ledger`;

CREATE TABLE `ledger` (
  `ledger_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `item` varchar(32) DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `date_added` int(11) DEFAULT NULL,
  `date_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table listeners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `listeners`;

CREATE TABLE `listeners` (
  `event` varchar(32) NOT NULL,
  `package` varchar(64) NOT NULL,
  `callback` varchar(64) NOT NULL,
  UNIQUE KEY `UNIQUE` (`event`,`package`,`callback`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `listeners` WRITE;
/*!40000 ALTER TABLE `listeners` DISABLE KEYS */;
INSERT INTO `listeners` (`event`,`package`,`callback`)
VALUES
	('client.commit.after','History','committed'),
	('client.commit.after','Search','commit_handler'),
	('client.dirty','Search','commit_handler'),
	('job.commit.after','History','committed'),
	('job.commit.after','Search','commit_handler'),
	('job.dirty','Search','commit_handler'),
	('property.commit.after','History','committed'),
	('property.commit.after','Search','commit_handler'),
	('property.dirty','Search','commit_handler');

/*!40000 ALTER TABLE `listeners` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `log`;

CREATE TABLE `log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(64) DEFAULT NULL,
  `message` text,
  `source` text,
  `date` int(16) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `meta`;

CREATE TABLE `meta` (
  `id` int(11) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `value` varchar(64) DEFAULT NULL,
  UNIQUE KEY `UNIQUE` (`id`,`type`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` text,
  `date_added` int(11) DEFAULT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table notifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `importance` varchar(8) DEFAULT NULL,
  `header` varchar(64) DEFAULT NULL,
  `body` varchar(256) DEFAULT NULL,
  `link` varchar(512) DEFAULT NULL,
  `redirect` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`notification_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL DEFAULT '0',
  `client_id` int(11) DEFAULT NULL,
  `tender` varchar(32) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `date_added` int(11) DEFAULT NULL,
  `date_posted` int(11) DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `user_id` int(11) DEFAULT NULL,
  `function` varchar(128) DEFAULT NULL,
  `permissions` int(3) DEFAULT NULL,
  UNIQUE KEY `UNIQUE` (`user_id`,`function`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`user_id`,`function`,`permissions`)
VALUES
	(0,'/',2),
	(1,'/',3),
	(0,'/admin',0),
	(2,'/admin',0),
	(2,'/',2);

/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table properties
# ------------------------------------------------------------

DROP TABLE IF EXISTS `properties`;

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL AUTO_INCREMENT,
  `search_text` varchar(64) DEFAULT NULL,
  `street_number` varchar(32) DEFAULT NULL,
  `route` varchar(64) DEFAULT NULL,
  `subpremise` varchar(64) DEFAULT NULL,
  `locality` varchar(64) DEFAULT NULL,
  `administrative_area_level_1` varchar(64) DEFAULT NULL,
  `administrative_area_level_2` varchar(64) DEFAULT NULL,
  `postal_code` varchar(16) DEFAULT NULL,
  `neighborhood` varchar(64) DEFAULT NULL,
  `latitude` varchar(32) DEFAULT NULL,
  `longitude` varchar(32) DEFAULT NULL,
  `date_added` int(16) DEFAULT NULL,
  `date_updated` int(16) DEFAULT NULL,
  PRIMARY KEY (`property_id`),
  KEY `UNIQUE` (`street_number`,`route`,`subpremise`,`locality`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table search
# ------------------------------------------------------------

DROP TABLE IF EXISTS `search`;

CREATE TABLE `search` (
  `id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `link` varchar(128) DEFAULT NULL,
  `keywords` text NOT NULL,
  `date_added` int(11) DEFAULT NULL,
  `date_updated` int(11) DEFAULT NULL,
  UNIQUE KEY `UNIQUE` (`id`,`type`),
  FULLTEXT KEY `FULLTEXT` (`keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` mediumtext NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` (`session_id`,`ip_address`,`user_agent`,`last_activity`,`user_data`)
VALUES
	('ff88c4a4defbf59ba20c5b809b2ed9d3','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:20.0) Gecko/20100101 Firefox/20.0',1366414597,'a:3:{s:9:\"user_data\";s:0:\"\";s:7:\"user_id\";s:1:\"1\";s:9:\"last_page\";s:22:\"admin/database/migrate\";}');

/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table time_distance
# ------------------------------------------------------------

DROP TABLE IF EXISTS `time_distance`;

CREATE TABLE `time_distance` (
  `adress_id_1` int(11) DEFAULT NULL,
  `address_id_2` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `distance` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `date_added` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`user_id`,`email`,`password`,`name`,`office_id`,`date_added`)
VALUES
	('0',NULL,NULL,'Io',NULL,NULL),
	('1','saven.matt@gmail.com','$2a$08$RFro6Um7yCrtNVCqPOwzYOK/pwrvDXmnWrrmb/SNs34jMvT5rPhti','Matthew Solum',1,NULL),
	('2','jlm.526@gmail.com','$2a$08$LNsYnaAjStxpM5U4BoVcHuXdytMNv86Wm//RDJd7LpDbfATgFLt.S','Jackie McCauley',1,1360307369);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;





/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
