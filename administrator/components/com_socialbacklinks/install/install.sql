CREATE TABLE IF NOT EXISTS `#__socialbacklinks_configs` (
  `socialbacklinks_config_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`socialbacklinks_config_id`),
  UNIQUE KEY `section_name` (`section`,`name`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `#__socialbacklinks_errors` (
  `socialbacklinks_error_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `network` varchar(64) NOT NULL,
  `extension` varchar(64) NOT NULL,
  `item_id`	int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`socialbacklinks_error_id`),
  UNIQUE KEY `item_id_extension` (`network`,`extension`,`item_id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `#__socialbacklinks_histories` (
  `socialbacklinks_history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `network` varchar(64) NOT NULL,
  `extension` varchar(64) NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `result` tinyint(1) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`socialbacklinks_history_id`),
  KEY `extension_item_id_result` (`extension`,`item_id`,`result`)
) ENGINE=MyISAM ;
