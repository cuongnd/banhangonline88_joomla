CREATE TABLE IF NOT EXISTS `#__jblance_expiry_alert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscr_project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(12) NOT NULL,
  `sent_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;