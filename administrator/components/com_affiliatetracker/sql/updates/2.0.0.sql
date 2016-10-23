ALTER TABLE  `#__affiliate_tracker_accounts` ADD `variable_comissions` text NOT NULL ;
ALTER TABLE  `#__affiliate_tracker_accounts` ADD `refer_url` varchar(255) NOT NULL ;
ALTER TABLE  `#__affiliate_tracker_accounts` ADD `parent_id` int(11) NOT NULL ;

CREATE TABLE IF NOT EXISTS `#__affiliate_tracker_marketing_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `html_code` text NOT NULL,
  `publish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;
