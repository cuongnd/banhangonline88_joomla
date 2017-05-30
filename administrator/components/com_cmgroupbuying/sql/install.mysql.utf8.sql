CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_aggregator_counter` (
  `ref_id` varchar(10) NOT NULL,
  `deal_id` int(11) NOT NULL,
  `view` int(11) NOT NULL,
  PRIMARY KEY (`ref_id`,`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_aggregator_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `xml_tree_header` text NOT NULL,
  `xml_tree_deals` text NOT NULL,
  `xml_tree_footer` text NOT NULL,
  `ref` varchar(10) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `map_icon` varchar(255) NOT NULL,
  `map_icon_shadow` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_configuration` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `currency_code` varchar(20) NOT NULL,
  `currency_prefix` varchar(20) NOT NULL,
  `currency_postfix` varchar(20) NOT NULL,
  `currency_decimals` tinyint(4) NOT NULL,
  `currency_dec_point` varchar(1) NOT NULL,
  `currency_thousands_sep` varchar(1) NOT NULL,
  `slideshow_switch_time` tinyint(4) NOT NULL,
  `slideshow_fade_time` tinyint(4) NOT NULL,
  `layout` varchar(255) NOT NULL,
  `mobile_template` varchar(100) NOT NULL,
  `mail_pay_buyer` tinyint(1) NOT NULL DEFAULT '1',
  `mail_pay_partner` tinyint(1) NOT NULL DEFAULT '1',
  `mail_tip_partner` tinyint(1) NOT NULL DEFAULT '1',
  `mail_void_buyer` tinyint(1) NOT NULL DEFAULT '1',
  `mail_void_partner` tinyint(1) NOT NULL DEFAULT '1',
  `mail_cash_buyer` tinyint(1) NOT NULL DEFAULT '1',
  `mail_approve_partner` tinyint(1) NOT NULL DEFAULT '1',
  `mail_pending_admin` tinyint(1) NOT NULL DEFAULT '1',
  `mail_approve_coupon_partner` tinyint(1) NOT NULL DEFAULT '1',
  `mail_pending_coupon_admin` tinyint(1) NOT NULL DEFAULT '1',
  `mail_cash_admin` tinyint(1) NOT NULL DEFAULT '1',
  `background_override` tinyint(1) NOT NULL DEFAULT '0',
  `facebook_comment` tinyint(1) NOT NULL DEFAULT '0',
  `facebook_comment_num_posts` tinyint(4) NOT NULL DEFAULT '10',
  `facebook_comment_width` mediumint(9) NOT NULL DEFAULT '650',
  `facebook_app_id` bigint(20) NOT NULL,
  `facebook_admin_user_id` varchar(255) NOT NULL,
  `disqus_comment` tinyint(1) NOT NULL DEFAULT '0',
  `disqus_shortname` varchar(100) NOT NULL,
  `disqus_multilanguage` tinyint(1) NOT NULL DEFAULT '0',
  `plg_cmdealarticle_template` text NOT NULL,
  `subscription_template` varchar(100) NOT NULL,
  `subscription_redirect` varchar(15) NOT NULL DEFAULT 'todaydeal',
  `subscription_cookie_lifetime` int(11) NOT NULL DEFAULT '24',
  `point_system` varchar(10) NOT NULL DEFAULT 'off',
  `deal_referral` tinyint(1) NOT NULL DEFAULT '0',
  `pay_with_point` tinyint(1) NOT NULL DEFAULT '0',
  `purchase_bonus` tinyint(1) NOT NULL DEFAULT '0',
  `exchange_rate` decimal(10,2) NOT NULL DEFAULT '1.00',
  `point_cookie_lifetime` int(11) NOT NULL DEFAULT '24',
  `pagination_limit` tinyint(4) NOT NULL,
  `deal_map_width` int(11) NOT NULL,
  `deal_map_height` int(11) NOT NULL,
  `deal_map_zoom` int(11) NOT NULL,
  `deal_map_latitude` float(10,6) NOT NULL,
  `deal_map_longitude` float(10,6) NOT NULL,
  `sh404sef_deal_alias` varchar(100) NOT NULL,
  `sh404sef_category_alias` varchar(100) NOT NULL,
  `sh404sef_partner_alias` varchar(100) NOT NULL,
  `sh404sef_free_coupon_alias` varchar(500) NOT NULL,
  `jomsocial_activity` tinyint(1) NOT NULL DEFAULT '0',
  `jomsocial_activity_title` varchar(255) NOT NULL,
  `jquery_loading` text NOT NULL,
  `partner_folder` varchar(100) NOT NULL,
  `geotargeting` varchar(100) NOT NULL,
  `maxmind_path` text NOT NULL,
  `ipinfodb_key` text NOT NULL,
  `geotargeting_cookie_lifetime` int(11) NOT NULL DEFAULT '24',
  `admin_email` varchar(100) NOT NULL,
  `deal_list_effect` varchar(50) NOT NULL,
  `deal_list_slideshow_timing` tinyint(4) NOT NULL,
  `buy_as_guest` tinyint(1) NOT NULL DEFAULT '0',
  `max_displayed_quantity` tinyint(4) NOT NULL DEFAULT '10',
  `tos` int(11) NOT NULL,
  `datetime_format` varchar(255) NOT NULL,
  `coupon_format` enum('html','pdf') NOT NULL DEFAULT 'html',
  `coupon_background` text NOT NULL,
  `coupon_elements` text NOT NULL,
  `qr_code_generator` varchar(50) NOT NULL,
  `payment_method_type` enum('hosted','direct') NOT NULL DEFAULT 'hosted',
  `direct_payment_method` varchar(20) NOT NULL,
  `payment_method_pretext` text NOT NULL,
  `payment_method_posttext` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `#__cmgroupbuying_configuration` (`id`, `currency_code`, `currency_prefix`, `currency_postfix`, `currency_decimals`, `currency_dec_point`, `currency_thousands_sep`, `slideshow_switch_time`, `slideshow_fade_time`, `layout`, `mobile_template`, `mail_pay_buyer`, `mail_pay_partner`, `mail_tip_partner`, `mail_void_buyer`, `mail_void_partner`, `mail_cash_buyer`, `mail_approve_partner`, `mail_pending_admin`, `mail_approve_coupon_partner`, `mail_pending_coupon_admin`, `mail_cash_admin`, `background_override`, `facebook_comment`, `facebook_comment_num_posts`, `facebook_comment_width`, `facebook_app_id`, `facebook_admin_user_id`, `disqus_comment`, `disqus_shortname`, `disqus_multilanguage`, `plg_cmdealarticle_template`, `subscription_template`, `subscription_redirect`, `subscription_cookie_lifetime`, `point_system`, `deal_referral`, `pay_with_point`, `purchase_bonus`, `exchange_rate`, `point_cookie_lifetime`, `pagination_limit`, `deal_map_width`, `deal_map_height`, `deal_map_zoom`, `deal_map_latitude`, `deal_map_longitude`, `sh404sef_deal_alias`, `sh404sef_category_alias`, `sh404sef_partner_alias`, `sh404sef_free_coupon_alias`, `jomsocial_activity`, `jomsocial_activity_title`, `jquery_loading`, `partner_folder`, `geotargeting`, `maxmind_path`, `ipinfodb_key`, `geotargeting_cookie_lifetime`, `admin_email`, `deal_list_effect`, `deal_list_slideshow_timing`, `buy_as_guest`, `max_displayed_quantity`, `tos`, `datetime_format`, `coupon_format`, `coupon_background`, `coupon_elements`, `qr_code_generator`, `payment_method_type`, `direct_payment_method`, `payment_method_pretext`, `payment_method_posttext`) VALUES
(1, 'USD', '$', '', 2, '.', ',', 2, 1, 'dailydeal_groupon', 'cm_mobile', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 30, 400, 0, '', 0, '', 1, '', '', 'todaydeal', 48, 'off', 1, 1, 1, 1.00, 12, 6, 930, 600, 10, 0.000000, 0.000000, '', '', '', '', 0, '{buyer_name} bought {deal_name}', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', 'merchants', 'off', '', '', 48, 'youremail@domain.com', 'slideshow', 1, 1, 15, 141, 'j F Y, g:i a', 'html', '0', '', 'php_qr_code', 'hosted', '', '', '');

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_coupons` (
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `deal_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_code` varchar(100) NOT NULL,
  `expired_date` datetime NOT NULL,
  `coupon_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`coupon_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_deals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `short_description` varchar(1000) NOT NULL,
  `description` text NOT NULL,
  `mobile_description` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `highlights` text NOT NULL,
  `terms` text NOT NULL,
  `map_latitude` float(10,6) NOT NULL,
  `map_longitude` float(10,6) NOT NULL,
  `image_path_1` varchar(255) NOT NULL,
  `image_path_2` varchar(255) NOT NULL,
  `image_path_3` varchar(255) NOT NULL,
  `image_path_4` varchar(255) NOT NULL,
  `image_path_5` varchar(255) NOT NULL,
  `mobile_image_path` varchar(255) NOT NULL,
  `background_image` varchar(255) NOT NULL,
  `min_bought` smallint(6) NOT NULL,
  `max_bought` smallint(6) NOT NULL,
  `max_coupon` smallint(6) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `coupon_path` varchar(1000) NOT NULL,
  `coupon_elements` text,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `commission_rate` decimal(10,2) NOT NULL,
  `advance_payment` tinyint(1) NOT NULL DEFAULT '0',
  `voided` tinyint(1) NOT NULL,
  `tipped` tinyint(1) NOT NULL,
  `tipped_date` datetime NOT NULL,
  `coupon_expiration` varchar(255) NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deal_alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_deal_location` (
  `deal_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`deal_id`,`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_deal_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `advance_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_free_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
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
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `deal_alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_free_coupon_location` (
  `coupon_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`coupon_id`,`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `geotargeting_name` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_mail_templates` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

INSERT INTO `#__cmgroupbuying_mail_templates` (`id`, `name`, `subject`, `body`) VALUES
(1, 'pay_buyer', 'Your payment is completed', '<p>Dear {buyer_last_name},</p>\r\n<p>You just finished your payment on our website on {order_paid_date}. Your order value is {order_value} and the order ID is {order_id}. Please click the following link to view your order detail:</p>\r\n<p>{order_link}</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(2, 'pay_partner', 'Your deal is bought', '<p>Dear {partner_name},</p>\r\n<p>Your deal -  {deal_name} - was just bought on {order_paid_date}, the order id is {order_id}.</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(3, 'coupon_for_buyer', 'Your coupon for {deal_name}', '<p>Dear {buyer_last_name},</p>\r\n<p>This is the link your coupon for {deal_name} deal that you bought on {order_paid_date}. Now you can print the coupon and bring it to {partner_name} to exchange it for product or service.</p>\r\n<p>{coupon_link}</p>\r\n<p>Thank you very much! </p>\r\n<p><strong>(This is a sample template for demonstration)</strong> </p>'),
(4, 'coupon_for_friend', 'Your coupon for {deal_name}', '<p>Dear {friend_full_name},</p>\r\n<p>This is the link your coupon for {deal_name} deal that {buyer_first_name} {buyer_last_name} ({buyer_email}) bought for you on {order_paid_date}. Now you can print the coupon and bring it to {partner_name} to exchange it for product or service.</p>\r\n<p>{coupon_link}</p>\r\n<p>Thank you very much! </p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(5, 'void_buyer', '{deal_name} is voided', '<p>Dear {buyer_last_name},</p>\r\n<p>{deal_name} deal that you bought on our website on {order_paid_date} (order ID {order_id}) is voided. We will send the refund to you.</p>\r\n<p>You can view your order detail here: {order_link}</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(6, 'void_partner', '{deal_name} is voided', '<p>Dear {partner_name}</p>\r\n<p>Your deal - {deal_name} - on our website is voided. We will send refunds to the buyers.</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(7, 'late_pay_buyer', 'Your payment is late', '<p>Dear {buyer_last_name},</p>\r\n<p>You made a payment for order ID {order_id} on our website.</p>\r\n<p>Unfortunately, you made your payment when your order had been expired, so now your payment is invalid.</p>\r\n<p>We''re so sorry for that. We will send your money back.</p>\r\n<p>For more information about your order, you can visit {order_link}</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(8, 'tip_partner', '{deal_name} is tipped', '<p>Dear {partner_name},</p>\r\n<p>Your deal - {deal_name} - has just been tipped on {deal_tipped_date}.</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(9, 'cash_buyer', 'You made an order on www.website.com', '<p>Dear {buyer_last_name},</p>\r\n<p>You just made an order on our website. Your order value is {order_value} and the order ID is {order_id}.</p>\r\n<p>Please send your cash to our bank account before {order_expired_date}: 1234567890 ABC bank.</p>\r\n<p>Thank you very much! </p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(10, 'approve_partner', 'Your pending deal is approved', '<p>Dear {partner_name},</p>\r\n<p>You deal - {deal_name} - has just been approved by Administrator.</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>\r\n<p><strong> </strong></p>'),
(11, 'pending_admin', 'A new deal has just been submitted', '<p>Dear Administrator,</p>\r\n<p>A new deal - {deal_name} - has just been submitted by {partner_name}.</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(12, 'approve_coupon_partner', 'Your pending free coupon is approved', '<p>Dear {partner_name},</p>\r\n<p>You free coupon - {coupon_name} - has just been approved by Administrator.</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(13, 'pending_coupon_admin', 'A new deal has just been submitted', '<p>Dear Administrator,</p>\r\n<p>A new free coupon - {coupon_name} - has just been submitted by {partner_name}.</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>'),
(14, 'cash_admin', 'A new cash order has just been made', '<p>Dear Administrator,</p>\r\n<p>A new cash order has just been made by. Order ID: {order_id}.</p>\r\n<p><strong>(This is a sample template for demonstration)</strong></p>');

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_management` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `partner_welcome` text NOT NULL,
  `partner_footer` text NOT NULL,
  `partner_view_deal_list` tinyint(1) NOT NULL,
  `partner_submit_new_deal` tinyint(1) NOT NULL,
  `partner_check_coupon_status` tinyint(1) NOT NULL,
  `partner_change_coupon_status` tinyint(1) NOT NULL,
  `partner_view_coupon_list` tinyint(1) NOT NULL,
  `partner_view_buyer_info` tinyint(1) NOT NULL,
  `partner_view_commission_report` tinyint(1) NOT NULL,
  `partner_edit_profile` tinyint(1) NOT NULL,
  `partner_view_free_coupon_list` tinyint(1) NOT NULL,
  `partner_submit_new_free_coupon` tinyint(1) NOT NULL,
  `staff_access_level` int(11) NOT NULL,
  `staff_welcome` text NOT NULL,
  `staff_footer` text NOT NULL,
  `staff_change_order_paid` tinyint(1) NOT NULL,
  `staff_change_order_unpaid` tinyint(1) NOT NULL,
  `staff_change_user_info` tinyint(1) NOT NULL,
  `staff_view_coupon` tinyint(1) NOT NULL,
  `staff_send_coupon` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `#__cmgroupbuying_management` (`id`, `partner_welcome`, `partner_footer`, `partner_view_deal_list`, `partner_submit_new_deal`, `partner_check_coupon_status`, `partner_change_coupon_status`, `partner_view_coupon_list`, `partner_view_buyer_info`, `partner_view_commission_report`, `partner_edit_profile`, `partner_view_free_coupon_list`, `partner_submit_new_free_coupon`, `staff_access_level`, `staff_welcome`, `staff_footer`, `staff_change_order_paid`, `staff_change_order_unpaid`, `staff_change_user_info`, `staff_view_coupon`, `staff_send_coupon`) VALUES
(1, '<p><strong>Welcome message</strong></p>', '<p>Footer</p>', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 7, '<p>Welcome message</p>', '<p>Footer</p>', 1, 1, 1, 1, 1);

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` decimal(10,2) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `buyer_info` varchar(1000) NOT NULL,
  `friend_info` varchar(255) NOT NULL,
  `payment_id` varchar(100) NOT NULL,
  `payment_name` varchar(255) NOT NULL,
  `transaction_info` text NOT NULL,
  `created_date` datetime NOT NULL,
  `expired_date` datetime NOT NULL,
  `paid_date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `referrer` varchar(160) NOT NULL DEFAULT '',
  `points` decimal(10,2) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `deal_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `token` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `website` varchar(255) NOT NULL,
  `about` varchar(1000) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `map_zoom_level` tinyint(4) NOT NULL,
  `location1` text NOT NULL,
  `location2` text NOT NULL,
  `location3` text NOT NULL,
  `location4` text NOT NULL,
  `location5` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8_unicode_ci NOT NULL,
  `image` text COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `metakey` text COLLATE utf8_unicode_ci NOT NULL,
  `metadesc` text COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_user_profile` (
  `id` tinyint(11) NOT NULL AUTO_INCREMENT,
  `profile` varchar(50) NOT NULL DEFAULT 'blank',
  `profile_name_value` varchar(255) NOT NULL,
  `profile_firstname_value` varchar(255) NOT NULL,
  `profile_lastname_value` varchar(255) NOT NULL,
  `profile_address_value` varchar(255) NOT NULL,
  `profile_city_value` varchar(255) NOT NULL,
  `profile_state_value` varchar(255) NOT NULL,
  `profile_zip_value` varchar(255) NOT NULL,
  `profile_phone_value` varchar(255) NOT NULL,
  `profile_name_attribute` enum('hidden','optional','required') NOT NULL,
  `profile_firstname_attribute` enum('hidden','optional','required') NOT NULL,
  `profile_lastname_attribute` enum('hidden','optional','required') NOT NULL,
  `profile_address_attribute` enum('hidden','optional','required') NOT NULL,
  `profile_city_attribute` enum('hidden','optional','required') NOT NULL,
  `profile_state_attribute` enum('hidden','optional','required') NOT NULL,
  `profile_zip_attribute` enum('hidden','optional','required') NOT NULL,
  `profile_phone_attribute` enum('hidden','optional','required') NOT NULL,
  `optional_text` varchar(255) NOT NULL,
  `required_text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `#__cmgroupbuying_user_profile` (`id`, `profile`, `profile_name_value`, `profile_firstname_value`, `profile_lastname_value`, `profile_address_value`, `profile_city_value`, `profile_state_value`, `profile_zip_value`, `profile_phone_value`, `profile_name_attribute`, `profile_firstname_attribute`, `profile_lastname_attribute`, `profile_address_attribute`, `profile_city_attribute`, `profile_state_attribute`, `profile_zip_attribute`, `profile_phone_attribute`, `optional_text`, `required_text`) VALUES
(1, 'blank', 'name', 'firstname', 'lastname', 'cb_address', '', '', '', '', 'required', 'optional', 'optional', 'optional', 'optional', 'optional', 'optional', 'optional', ' (Optional)', ' *');
