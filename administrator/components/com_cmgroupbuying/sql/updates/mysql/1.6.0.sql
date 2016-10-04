ALTER TABLE  `#__cmgroupbuying_configuration` CHANGE  `deal_map_latitude`  `deal_map_latitude` FLOAT( 10, 6 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` CHANGE  `deal_map_longtitude`  `deal_map_longtitude` FLOAT( 10, 6 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `deal_list_effect` VARCHAR( 50 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `deal_list_slideshow_timing` TINYINT NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `purchase_bonus` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `pay_with_point`;
ALTER TABLE  `#__cmgroupbuying_deals` ADD  `shipping_cost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0' AFTER  `coupon_elements`;
ALTER TABLE  `#__cmgroupbuying_orders` ADD  `shipping_cost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0' AFTER  `quantity`;
CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_free_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `discount` varchar(50) NOT NULL,
  `short_description` varchar(1000) NOT NULL,
  `description` text NOT NULL,
  `mobile_description` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `image_path_1` varchar(255) NOT NULL,
  `image_path_2` varchar(255) NOT NULL,
  `image_path_3` varchar(255) NOT NULL,
  `image_path_4` varchar(255) NOT NULL,
  `image_path_5` varchar(255) NOT NULL,
  `mobile_image_path` varchar(255) NOT NULL,
  `background_image` varchar(255) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `coupon_path` varchar(1000) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `view` int(11) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deal_alias` (`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_free_coupon_location` (
  `coupon_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`coupon_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `sh404sef_free_coupon_alias` VARCHAR( 500 ) NOT NULL AFTER  `sh404sef_partner_alias`;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `mail_approve_coupon_partner` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `mail_pending_admin`;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `mail_pending_coupon_admin` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `mail_approve_coupon_partner`;
INSERT INTO  `#__cmgroupbuying_mail_templates` (
`id` ,
`name` ,
`subject` ,
`body`
)
VALUES (
NULL ,  'approve_coupon_partner',  'Your pending free coupon is approved',  '<p>Dear {partner_name},</p>
<p>You free coupon - {coupon_name} - has just been approved by Administrator.</p>
<p><strong>(This is a sample template for demonstration)</strong></p>'
);
INSERT INTO  `#__cmgroupbuying_mail_templates` (
`id` ,
`name` ,
`subject` ,
`body`
)
VALUES (
NULL ,  'pending_coupon_admin',  'A new deal has just been submitted',  '<p>Dear Administrator,</p>
<p>A new free coupon - {coupon_name} - has just been submitted by {partner_name}.</p>
<p><strong>(This is a sample template for demonstration)</strong></p>'
);
ALTER TABLE  `#__cmgroupbuying_configuration` CHANGE  `deal_map_longtitude`  `deal_map_longitude` FLOAT( 10, 6 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_deals` CHANGE  `map_longtitude`  `map_longitude` FLOAT( 10, 6 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_partners` CHANGE  `map_longtitude`  `map_longitude` FLOAT( 10, 6 ) NOT NULL;