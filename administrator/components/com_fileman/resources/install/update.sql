# 3.0.0-beta1
ALTER TABLE `#__files_containers` ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__fileman_attachments` (
  `fileman_attachment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `files_container_id` bigint(20) NOT NULL,
  `uuid` char(36) NOT NULL DEFAULT '',
  `path` varchar(2000) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `created_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` bigint(20) NOT NULL,
  `modified_on` datetime NOT NULL,
  `locked_by` bigint(20) NOT NULL,
  `locked_on` datetime NOT NULL,
  PRIMARY KEY (`fileman_attachment_id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `files_container_id` (`files_container_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__fileman_attachments_relations` (
  `fileman_attachment_id` bigint(20) unsigned NOT NULL,
  `table` varchar(255) NOT NULL DEFAULT '',
  `row` bigint(20) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`fileman_attachment_id`,`table`,`row`),
  CONSTRAINT `#__fileman_attachments_relaions_ibfk_1` FOREIGN KEY (`fileman_attachment_id`) REFERENCES `#__fileman_attachments` (`fileman_attachment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# 3.1.0-RC1
DROP TABLE IF EXISTS `#__files_thumbnails`;

# 3.1.2
CREATE TABLE IF NOT EXISTS `#__fileman_file_contents` (
  `fileman_content_id` SERIAL,
  `container` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(512) NOT NULL DEFAULT '',
  `contents` longtext,
  PRIMARY KEY (`fileman_content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;