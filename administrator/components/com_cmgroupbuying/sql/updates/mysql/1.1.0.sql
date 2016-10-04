ALTER TABLE `#__cmgroupbuying_deals` DROP `price`;
ALTER TABLE `#__cmgroupbuying_deals` DROP `original_price`;
ALTER TABLE `#__cmgroupbuying_orders` ADD `option_id` INT NOT NULL AFTER `deal_id`;
ALTER TABLE `#__cmgroupbuying_coupons` ADD `option_id` INT NOT NULL AFTER `deal_id`;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `deal_referral` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `subscription_redirect`;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `pay_with_point` TINYINT NOT NULL AFTER `deal_referral`;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `cookie_lifetime` INT NOT NULL DEFAULT '24' AFTER `deal_referral`;
ALTER TABLE `#__cmgroupbuying_orders` ADD `referrer` VARCHAR( 160 ) NOT NULL DEFAULT '';
ALTER TABLE `#__cmgroupbuying_orders` ADD `points` INT NOT NULL;
ALTER TABLE `#__cmgroupbuying_deals` ADD `map_latitude` FLOAT( 10, 6 ) NOT NULL AFTER `terms`;
ALTER TABLE `#__cmgroupbuying_deals` ADD `map_longtitude` FLOAT( 10, 6 ) NOT NULL AFTER `map_latitude`;
ALTER TABLE `#__cmgroupbuying_categories` ADD `map_icon` VARCHAR( 255 ) NOT NULL AFTER `description`;
ALTER TABLE `#__cmgroupbuying_categories` ADD `map_icon_shadow` VARCHAR( 255 ) NOT NULL AFTER `map_icon`;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `deal_map_width` INT NOT NULL;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `deal_map_height` INT NOT NULL;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `deal_map_zoom` INT NOT NULL;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `deal_map_latitude` FLOAT( 10, 6 ) NOT NULL;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `deal_map_longtitude` FLOAT( 10, 6 ) NOT NULL;
ALTER TABLE `#__cmgroupbuying_partners` CHANGE  `map_latitude`  `map_latitude` FLOAT( 10, 6 ) NOT NULL;
ALTER TABLE `#__cmgroupbuying_partners` CHANGE  `map_longtitude`  `map_longtitude` FLOAT( 10, 6 ) NOT NULL;
CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_deal_option` (
  `deal_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`deal_id`,`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
UPDATE `#__cmgroupbuying_coupons` SET option_id = 1;
UPDATE `#__cmgroupbuying_orders` SET option_id = 1;
