ALTER TABLE `#__jfbconnect_user_map` ADD COLUMN `params` TEXT;

CREATE TABLE IF NOT EXISTS `#__opengraph_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin` varchar(20) DEFAULT NULL,
  `system_name` varchar(20) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  `fb_built_in` tinyint(1) DEFAULT NULL,
  `can_disable` tinyint(1) DEFAULT NULL,
  `params` text,
  `published` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__opengraph_object` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin` varchar(15) DEFAULT NULL,
  `system_name` varchar(20) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `fb_built_in` int(1) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `params` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__opengraph_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action_id` int(11) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `url` text,
  `status` tinyint(2) DEFAULT NULL,
  `unique_key` varchar(32) DEFAULT NULL,
  `response` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__opengraph_action_object` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);