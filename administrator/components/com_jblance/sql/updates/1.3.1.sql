ALTER TABLE `#__jblance_project` ADD (`is_private_invite` TINYINT(1) DEFAULT '0' NOT NULL);
ALTER TABLE `#__jblance_project` ADD (`invite_user_id` VARCHAR(500) DEFAULT '0' NOT NULL);

CREATE TABLE IF NOT EXISTS `#__jblance_favourite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor` int(11) NOT NULL DEFAULT '0',
  `target` int(11) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT 'profile',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;