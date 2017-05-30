ALTER TABLE `#__jblance_escrow` ADD (`type` varchar(50) NOT NULL);
ALTER TABLE `#__jblance_service_order` ADD (`p_status` varchar(50) NOT NULL);
ALTER TABLE `#__jblance_service_order` ADD (`p_percent` int(11) NOT NULL DEFAULT '0');
ALTER TABLE `#__jblance_service_order` ADD (`p_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');
ALTER TABLE `#__jblance_service_order` ADD (`p_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');
ALTER TABLE `#__jblance_service_order` ADD (`p_ended` datetime NOT NULL DEFAULT '0000-00-00 00:00:00');