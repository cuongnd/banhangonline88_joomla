ALTER TABLE `#__jblance_message` ADD (`type` varchar(50) NOT NULL);

ALTER TABLE `#__jblance_bid` ADD (`p_status` varchar(50) NOT NULL);
ALTER TABLE `#__jblance_bid` ADD (`p_percent` int(11) NOT NULL DEFAULT '0');
ALTER TABLE `#__jblance_bid` ADD (`p_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');
ALTER TABLE `#__jblance_bid` ADD (`p_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');
ALTER TABLE `#__jblance_bid` ADD (`p_ended` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');

ALTER TABLE `#__jblance_portfolio` ADD (`video_link` varchar(255) NOT NULL);

ALTER TABLE `#__jblance_project` ADD (`id_location` int(11) NOT NULL DEFAULT '0');

ALTER TABLE `#__jblance_user` ADD (`address` varchar(250) NOT NULL);
ALTER TABLE `#__jblance_user` ADD (`id_location` int(11) NOT NULL DEFAULT '0');
ALTER TABLE `#__jblance_user` ADD (`postcode` varchar(15) NOT NULL);
ALTER TABLE `#__jblance_user` ADD (`mobile` varchar(20) NOT NULL);

CREATE TABLE IF NOT EXISTS `#__jblance_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `alias` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `extension` varchar(50) NOT NULL,
  `default` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;