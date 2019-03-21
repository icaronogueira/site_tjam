CREATE TABLE IF NOT EXISTS `#__logman_activities` (
	`logman_activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`uuid` VARCHAR(36) NOT NULL DEFAULT '' UNIQUE,
	`application` VARCHAR(10) NOT NULL DEFAULT '',
	`type` VARCHAR(3) NOT NULL DEFAULT '',
	`package` VARCHAR(50) NOT NULL DEFAULT '',
	`name` VARCHAR(50) NOT NULL DEFAULT '',
	`action` VARCHAR(50) NOT NULL DEFAULT '',
	`row` varchar(2048) NOT NULL DEFAULT '',
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`status` varchar(100) NOT NULL,
	`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(11) NOT NULL DEFAULT '0',
	`ip` varchar(45) NOT NULL DEFAULT '',
	`metadata` text NOT NULL,
	PRIMARY KEY(`logman_activity_id`),
	KEY `package` (`package`),
    KEY `name` (`name`),
    KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__activities_resources` (
  `activities_resource_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `package` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `resource_id` varchar(2048) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `created_on` datetime NOT NULL,
  `data` longtext NOT NULL COMMENT '@Filter("json")',
  PRIMARY KEY (`activities_resource_id`),
  KEY `idx_package` (`package`),
  KEY `idx_name` (`name`),
  KEY `idx_package-name` (`package`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__logman_routes` (
  `logman_route_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `row` varchar(2048) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `package` varchar(255) DEFAULT NULL,
  `page` bigint(20) DEFAULT NULL,
  `path` varchar(2048) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`logman_route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__logman_impressions` (
  `logman_impression_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `row` varchar(2048) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `package` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `referrer` varchar(2048) DEFAULT NULL,
  `internal` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `url` varchar(2048) DEFAULT NULL,
  `session_hash` varchar(32) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`logman_impression_id`),
  KEY `idx:name-package` (`name`,`package`),
  KEY `idx:session_hash` (`session_hash`),
  KEY `idx:internal` (`internal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__logman_synchronization` (
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__logman_activities_impressions` (
  `logman_activity_id` bigint(20) unsigned NOT NULL,
  `logman_impression_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`logman_activity_id`,`logman_impression_id`),
  KEY `logman_impression_id` (`logman_impression_id`),
  CONSTRAINT `logman_activities_impressions_ibfk_1` FOREIGN KEY (`logman_activity_id`) REFERENCES `#__logman_activities` (`logman_activity_id`) ON DELETE CASCADE,
  CONSTRAINT `logman_activities_impressions_ibfk_2` FOREIGN KEY (`logman_impression_id`) REFERENCES `#__logman_impressions` (`logman_impression_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

