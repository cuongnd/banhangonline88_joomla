ALTER TABLE `#__cmgroupbuying_configuration` ADD `datetime_format` VARCHAR( 255 ) NOT NULL ,
ADD `coupon_background` TEXT NOT NULL ,
ADD `coupon_elements` TEXT NOT NULL;

ALTER TABLE `#__cmgroupbuying_configuration` DROP `profile`;
ALTER TABLE `#__cmgroupbuying_deals` ADD `coupon_expiration` VARCHAR( 255 ) NOT NULL AFTER `tipped_date`;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_user_profile` (
  `id` tinyint(11) NOT NULL AUTO_INCREMENT,
  `profile` varchar(50) NOT NULL DEFAULT 'blank',
  `profile_name_value` VARCHAR( 255 ) NOT NULL ,
  `profile_firstname_value` VARCHAR( 255 ) NOT NULL ,
  `profile_lastname_value` VARCHAR( 255 ) NOT NULL ,
  `profile_address_value` VARCHAR( 255 ) NOT NULL ,
  `profile_city_value` VARCHAR( 255 ) NOT NULL ,
  `profile_state_value` VARCHAR( 255 ) NOT NULL ,
  `profile_zip_value` VARCHAR( 255 ) NOT NULL ,
  `profile_phone_value` VARCHAR( 255 ) NOT NULL ,
  `profile_name_attribute` ENUM( 'hidden', 'optional', 'required' ) NOT NULL ,
  `profile_firstname_attribute` ENUM( 'hidden', 'optional', 'required' ) NOT NULL ,
  `profile_lastname_attribute` ENUM( 'hidden', 'optional', 'required' ) NOT NULL ,
  `profile_address_attribute` ENUM( 'hidden', 'optional', 'required' ) NOT NULL ,
  `profile_city_attribute` ENUM( 'hidden', 'optional', 'required' ) NOT NULL ,
  `profile_state_attribute` ENUM( 'hidden', 'optional', 'required' ) NOT NULL ,
  `profile_zip_attribute` ENUM( 'hidden', 'optional', 'required' ) NOT NULL ,
  `profile_phone_attribute` ENUM( 'hidden', 'optional', 'required' ) NOT NULL ,
  `optional_text` VARCHAR( 255 ) NOT NULL ,
  `required_text` VARCHAR( 255 ) NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;