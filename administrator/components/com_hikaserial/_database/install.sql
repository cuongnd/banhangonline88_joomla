CREATE TABLE IF NOT EXISTS `#__hikaserial_config` (
	`config_namekey` varchar(200) NOT NULL,
	`config_value` text NOT NULL,
	`config_default` text NOT NULL,
 	PRIMARY KEY (`config_namekey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikaserial_pack` (
	`pack_id` INT(10) NOT NULL AUTO_INCREMENT,
	`pack_name` VARCHAR(255) NOT NULL,
	`pack_data` VARCHAR(255) NOT NULL DEFAULT 'sql',
	`pack_generator` VARCHAR(255) NOT NULL DEFAULT '',
	`pack_published` INT(4) NOT NULL DEFAULT 0,
	`pack_vendor_id` INT(10) NOT NULL DEFAULT 0,
	`pack_manage_access` VARCHAR(255) NOT NULL DEFAULT 'all',
	`pack_params` TEXT NOT NULL DEFAULT '',
	`pack_description` TEXT NOT NULL DEFAULT '',
	PRIMARY KEY (`pack_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikaserial_serial` (
	`serial_id` INT(10) NOT NULL AUTO_INCREMENT,
	`serial_pack_id` INT(10) NOT NULL,
	`serial_data` TEXT NOT NULL,
	`serial_extradata` TEXT NOT NULL,
	`serial_status` VARCHAR(255) NOT NULL,
	`serial_assign_date` INT(10) NULL,
	`serial_order_id` INT(10) NULL,
	`serial_user_id` INT(10) NULL,
	`serial_order_product_id` INT(10) NULL,
	PRIMARY KEY (`serial_id`),
	KEY `serial_pack_id` (`serial_pack_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikaserial_product_pack` (
	`product_id` INT(10) NOT NULL,
	`pack_id` INT(10) NOT NULL,
	`quantity` INT(10) NOT NULL DEFAULT 1,
	PRIMARY KEY (`pack_id`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikaserial_generator` (
	`generator_id` INT(10) NOT NULL AUTO_INCREMENT,
	`generator_type` VARCHAR(255) NOT NULL,
	`generator_published` INT(4) NOT NULL DEFAULT 0,
	`generator_name` VARCHAR(255) NOT NULL,
	`generator_ordering` INT(10) NOT NULL DEFAULT 0,
	`generator_description` TEXT NOT NULL DEFAULT '',
	`generator_params` TEXT NOT NULL DEFAULT '',
	`generator_access` VARCHAR(255) NOT NULL DEFAULT 'all',
	PRIMARY KEY (`generator_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikaserial_consumer` (
	`consumer_id` INT(10) NOT NULL AUTO_INCREMENT,
	`consumer_type` VARCHAR(255) NOT NULL,
	`consumer_published` INT(4) NOT NULL DEFAULT 0,
	`consumer_name` VARCHAR(255) NOT NULL,
	`consumer_ordering` INT(10) NOT NULL DEFAULT 0,
	`consumer_description` TEXT NOT NULL DEFAULT '',
	`consumer_params` TEXT NOT NULL DEFAULT '',
	`consumer_access` VARCHAR(255) NOT NULL DEFAULT 'all',
	PRIMARY KEY (`consumer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikaserial_plugin` (
	`plugin_id` INT(10) NOT NULL AUTO_INCREMENT,
	`plugin_type` VARCHAR(255) NOT NULL,
	`plugin_published` INT(4) NOT NULL DEFAULT 0,
	`plugin_name` VARCHAR(255) NOT NULL,
	`plugin_ordering` INT(10) NOT NULL DEFAULT 0,
	`plugin_description` TEXT NOT NULL DEFAULT '',
	`plugin_params` TEXT NOT NULL DEFAULT '',
	`plugin_access` VARCHAR(255) NOT NULL DEFAULT 'all',
	PRIMARY KEY (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikaserial_history` (
	`history_id` INT(10) NOT NULL AUTO_INCREMENT,
	`history_serial_id` INT(10) unsigned NOT NULL DEFAULT '0',
	`history_created` INT(10) unsigned NOT NULL DEFAULT '0',
	`history_ip` VARCHAR(255) NOT NULL DEFAULT '',
	`history_new_status` VARCHAR(255) NOT NULL DEFAULT '',
	`history_type` VARCHAR(255) NOT NULL DEFAULT '',
	`history_data` TEXT NOT NULL DEFAULT '',
	`history_user_id` INT(10) unsigned DEFAULT '0',
	PRIMARY KEY (`history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
