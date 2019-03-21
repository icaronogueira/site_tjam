RENAME TABLE `#__activities_activities` TO `#__logman_activities`;

ALTER TABLE `#__logman_activities` CHANGE COLUMN `activities_activity_id` `logman_activity_id` int(11) unsigned NOT NULL AUTO_INCREMENT;