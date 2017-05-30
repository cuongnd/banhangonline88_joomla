ALTER TABLE `#__cmgroupbuying_coupons` ADD PRIMARY KEY(`coupon_code`);
ALTER TABLE `#__cmgroupbuying_configuration` ADD `profile` VARCHAR( 50 ) NOT NULL DEFAULT 'blank';
ALTER TABLE  `#__cmgroupbuying_configuration` CHANGE  `pay_with_point`  `pay_with_point` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `point_system` VARCHAR( 10 ) NOT NULL DEFAULT  'off' AFTER  `subscription_redirect`;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `jomsocial_activity` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `jomsocial_activity_title` VARCHAR( 255 ) NOT NULL;