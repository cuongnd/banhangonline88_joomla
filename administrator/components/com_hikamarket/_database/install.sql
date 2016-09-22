CREATE TABLE IF NOT EXISTS `#__hikamarket_config` (
	`config_namekey` varchar(200) NOT NULL,
	`config_value` text NOT NULL,
	`config_default` text NOT NULL,
 	PRIMARY KEY (`config_namekey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikamarket_customer_vendor` (
	`customer_id` INT(10) NOT NULL,
	`vendor_id` INT(10) NOT NULL,
	PRIMARY KEY (`customer_id`,`vendor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikamarket_vendor` (
	`vendor_id` INT(10) NOT NULL AUTO_INCREMENT,
	`vendor_admin_id` INT(10) NOT NULL DEFAULT 0,
	`vendor_name` VARCHAR(255) NOT NULL,
	`vendor_alias` VARCHAR(255) NOT NULL DEFAULT '',
	`vendor_canonical` VARCHAR(255) NOT NULL DEFAULT '',
	`vendor_email` VARCHAR(255) NOT NULL,
	`vendor_published` tinyint(4) NOT NULL DEFAULT 0,
	`vendor_currency_id` INT(10) NOT NULL DEFAULT 0,
	`vendor_description` TEXT NOT NULL DEFAULT '',
	`vendor_access` TEXT NOT NULL DEFAULT '',
	`vendor_shippings` TEXT NOT NULL DEFAULT '',
	`vendor_params` TEXT NOT NULL DEFAULT '',
	`vendor_image` VARCHAR(255) NOT NULL DEFAULT '',
	`vendor_created` INT(11) NOT NULL DEFAULT 0,
	`vendor_modified` INT(11) NOT NULL DEFAULT 0,
	`vendor_template_id` VARCHAR(255) NOT NULL DEFAULT '',
	`vendor_address_company` TEXT NOT NULL,
	`vendor_address_street` TEXT NOT NULL,
	`vendor_address_street2` TEXT NOT NULL,
	`vendor_address_post_code` TEXT NOT NULL,
	`vendor_address_city` TEXT NOT NULL,
	`vendor_address_telephone` TEXT NOT NULL,
	`vendor_address_fax` TEXT NOT NULL,
	`vendor_address_state` TEXT NOT NULL,
	`vendor_address_country` TEXT NOT NULL,
	`vendor_address_vat` TEXT NOT NULL,
	`vendor_zone_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	`vendor_site_id` VARCHAR(255) NOT NULL DEFAULT '',
	`vendor_average_score` decimal(16,5) NOT NULL DEFAULT '0.00000',
	`vendor_total_vote` INT NOT NULL DEFAULT 0,
	`vendor_terms` TEXT NOT NULL DEFAULT '',
	PRIMARY KEY (`vendor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikamarket_fee` (
	`fee_id` INT(10) NOT NULL AUTO_INCREMENT,
	`fee_type` varchar(255) NOT NULL DEFAULT 'product',
	`fee_target_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`fee_currency_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`fee_value` decimal(16,5) NOT NULL DEFAULT '0.00000',
	`fee_fixed` decimal(16,5) NOT NULL DEFAULT '0.00000',
	`fee_percent` decimal(16,5) NOT NULL DEFAULT '0.00000',
	`fee_min_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`fee_min_price` decimal(16,5) NOT NULL DEFAULT '0.00000',
	`fee_group` int(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`fee_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hikamarket_customer_vendor` (
	`customer_id` INT(10) NOT NULL,
	`vendor_id` INT(10) NOT NULL,
	PRIMARY KEY (`customer_id`,`vendor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
