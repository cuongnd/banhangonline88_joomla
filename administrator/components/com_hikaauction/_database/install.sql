CREATE TABLE IF NOT EXISTS `#__hikaauction_config` (
	`config_namekey` varchar(200) NOT NULL,
	`config_value` text NOT NULL,
	`config_default` text NOT NULL,
 	PRIMARY KEY (`config_namekey`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__hikaauction_queue` (
	`queue_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`queue_created` INT(11) NOT NULL DEFAULT 0,
	`queue_user_id` INT(10) UNSIGNED NOT NULL,
	`queue_product_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	`queue_type` varchar(255) NOT NULL DEFAULT '',
	`queue_data` varchar(1024) NOT NULL DEFAULT '',
 	PRIMARY KEY (`queue_id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__hikaauction_auction` (
	`auction_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`auction_created` INT(11) NOT NULL DEFAULT 0,
	`auction_amount` decimal(17,5) NOT NULL DEFAULT '0.00000',
	`auction_bidder_id` INT(10) UNSIGNED NOT NULL,
	`auction_product_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	`auction_status` INT(10) NOT NULL DEFAULT  0,
 	PRIMARY KEY (`auction_id`)
) DEFAULT CHARACTER SET utf8;

