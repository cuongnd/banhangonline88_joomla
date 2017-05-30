ALTER TABLE `#__cmgroupbuying_deals` CHANGE `coupon_elements` `coupon_elements` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `coupon_format` ENUM( 'html', 'pdf' ) NOT NULL DEFAULT 'html' AFTER `datetime_format`;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `qr_code_generator` VARCHAR( 50 ) NOT NULL AFTER `coupon_elements`;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `short_description` text NOT NULL,
  `image` text NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `#__cmgroupbuying_deals` ADD `product_id` INT NOT NULL DEFAULT '0' AFTER `category_id`;
ALTER TABLE `#__cmgroupbuying_free_coupons` ADD `featured` TINYINT( 1 ) NOT NULL AFTER `approved`;

ALTER TABLE `#__cmgroupbuying_management` ADD `partner_view_free_coupon_list` TINYINT( 1 ) NOT NULL AFTER `partner_edit_profile` ,
ADD `partner_submit_new_free_coupon` TINYINT( 1 ) NOT NULL AFTER `partner_view_free_coupon_list`;

ALTER TABLE `#__cmgroupbuying_free_coupons` ADD `category_id` INT NOT NULL DEFAULT '0' AFTER `alias`;

ALTER TABLE `#__cmgroupbuying_deals` ADD `metakey` TEXT NOT NULL AFTER `coupon_expiration`, ADD `metadesc` TEXT NOT NULL AFTER `metakey`;

ALTER TABLE `#__cmgroupbuying_free_coupons` ADD `metakey` TEXT NOT NULL AFTER `view`, ADD `metadesc` TEXT NOT NULL AFTER `metakey`;