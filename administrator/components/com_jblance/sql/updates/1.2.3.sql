ALTER TABLE `#__jblance_budget` ADD (`project_type` varchar(32) NOT NULL DEFAULT 'COM_JBLANCE_FIXED');

CREATE TABLE IF NOT EXISTS `#__jblance_duration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `duration_from` float NOT NULL DEFAULT '0',
  `duration_from_type` set('days','weeks','months','years') NOT NULL DEFAULT 'days',
  `duration_to` float NOT NULL DEFAULT '0',
  `duration_to_type` set('days','weeks','months','years') NOT NULL DEFAULT 'days',
  `less_great` varchar(3) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `#__jblance_project` ADD (`project_type` varchar(32) NOT NULL DEFAULT 'COM_JBLANCE_FIXED');
ALTER TABLE `#__jblance_project` ADD (`project_duration` int(11) NOT NULL DEFAULT '0');
ALTER TABLE `#__jblance_project` ADD (`commitment` varchar(100) NOT NULL);
ALTER TABLE `#__jblance_project` ADD (`params` longtext NOT NULL);

ALTER TABLE `#__jblance_escrow` ADD (`pay_for` int(11) NOT NULL DEFAULT '0');
