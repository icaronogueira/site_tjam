ALTER TABLE `#__logman_activities` ADD `row_uuid` varchar(36) DEFAULT NULL AFTER `row`;

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