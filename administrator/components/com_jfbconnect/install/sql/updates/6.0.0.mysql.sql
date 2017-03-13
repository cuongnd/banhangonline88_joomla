CREATE TABLE IF NOT EXISTS `#__jfbconnect_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(20) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `title` varchar(40) NOT NULL DEFAULT '',
  `description` text,
  `attribs` text,
  `published` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);