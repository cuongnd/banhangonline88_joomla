ALTER TABLE `#__jblance_service` ADD (`create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');
ALTER TABLE `#__jblance_service` ADD (`modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');
ALTER TABLE `#__jblance_service` ADD (`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');
ALTER TABLE `#__jblance_service` ADD (`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');
ALTER TABLE `#__jblance_service` ADD (`disapprove_reason` text);
ALTER TABLE `#__jblance_service` ADD (`hits` int(10) unsigned NOT NULL DEFAULT '0');
ALTER TABLE `#__jblance_service` ADD (`available` tinyint(1) NOT NULL DEFAULT '1');

ALTER TABLE `#__jblance_rating` ADD (`type` varchar(50) NOT NULL DEFAULT 'COM_JBLANCE_PROJECT');