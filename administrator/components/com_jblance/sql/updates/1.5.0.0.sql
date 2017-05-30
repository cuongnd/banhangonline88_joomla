CREATE TABLE IF NOT EXISTS `#__jblance_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_title` varchar(255) NOT NULL,
  `id_category` varchar(255) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `duration` int(11) NOT NULL DEFAULT '0',
  `instruction` text NOT NULL,
  `extras` text NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `attachment` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jblance_service_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `service_id` int(11) NOT NULL DEFAULT '0',
  `price` float NOT NULL DEFAULT '0',
  `duration` int(11) NOT NULL DEFAULT '0',
  `order_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(20) NOT NULL,
  `extras` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;