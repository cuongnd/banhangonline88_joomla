CREATE TABLE IF NOT EXISTS `#__jfbconnect_user_map` (
	`id` int unsigned NOT NULL auto_increment,
	`j_user_id` INT NOT NULL,
	`fb_user_id` BIGINT NOT NULL,
  `created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

# 6.0;
CREATE TABLE IF NOT EXISTS `#__jfbconnect_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(20) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `title` varchar(40) NOT NULL DEFAULT '',
  `description` text,
  `attribs` text,
  `published` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

# 4.3;
CREATE TABLE IF NOT EXISTS `#__opengraph_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin` varchar(20) DEFAULT NULL,
  `system_name` varchar(20) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  `fb_built_in` tinyint(1) DEFAULT NULL,
  `can_disable` tinyint(1) DEFAULT NULL,
  `params` text,
  `published` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__opengraph_object` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin` varchar(15) DEFAULT NULL,
  `system_name` varchar(20) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `fb_built_in` int(1) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `params` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__opengraph_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action_id` int(11) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `url` text,
  `status` tinyint(2) DEFAULT NULL,
  `unique_key` varchar(32) DEFAULT NULL,
  `response` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__opengraph_action_object` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

# 4.2 Not adding the unique to `setting` because we add it later with the install scripts, which can cause a duplicate key
# Not a big deal having 2, but cleaner to only have one.
# In a future version, add it by default ;
CREATE TABLE IF NOT EXISTS `#__jfbconnect_config` (
	`id` int unsigned NOT NULL auto_increment,
	`setting` VARCHAR(50) NOT NULL,
	`value` TEXT,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

# 4.1 Create new Requests tables ;
CREATE TABLE IF NOT EXISTS `#__jfbconnect_request` (
	`id` INT unsigned NOT NULL auto_increment,
	`published` TINYINT NOT NULL,
	`title` VARCHAR(50) NOT NULL,
	`message` VARCHAR(250) NOT NULL,
	`destination_url` VARCHAR(200) NOT NULL,
	`thanks_url` VARCHAR(200) NOT NULL,
	`breakout_canvas` TINYINT NOT NULL,
    `created` DATETIME NOT NULL,
	`modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__jfbconnect_notification` (
	`id` INT unsigned NOT NULL auto_increment,
	`fb_request_id` BIGINT NOT NULL,
	`fb_user_to` BIGINT NOT NULL,
	`fb_user_from` BIGINT NOT NULL,
	`jfbc_request_id` INT NOT NULL,
	`status` TINYINT NOT NULL,
	`created` DATETIME NOT NULL,
	`modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

# 4.1 Remove unused keys ;
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_update_status_msg";
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_perm_status_update";
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_perm_email";
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_perm_profile_data";

# 4.0 Remove unused keys ;
DELETE FROM `#__jfbconnect_config` WHERE `setting` = "facebook_api_key";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_comment_max_num" WHERE `setting` = "social_comment_max_num";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_comment_width" WHERE `setting` = "social_comment_width";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_comment_color_scheme" WHERE `setting` = "social_comment_color_scheme";

UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_comment_max_num" WHERE `setting` = "social_k2_comment_max_num";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_comment_width" WHERE `setting` = "social_k2_comment_width";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_comment_color_scheme" WHERE `setting` = "social_k2_comment_color_scheme";

UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_layout_style" WHERE `setting` = "social_like_layout_style";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_show_faces" WHERE `setting` = "social_like_show_faces";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_show_send_button" WHERE `setting` = "social_like_show_send_button";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_width" WHERE `setting` = "social_like_width";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_verb_to_display" WHERE `setting` = "social_like_verb_to_display";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_font" WHERE `setting` = "social_like_font";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_color_scheme" WHERE `setting` = "social_like_color_scheme";
UPDATE `#__jfbconnect_config` SET `setting` = "social_article_like_show_extra_social_buttons" WHERE `setting` = "social_like_show_extra_social_buttons";

UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_layout_style" WHERE `setting` = "social_k2_like_layout_style";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_show_faces" WHERE `setting` = "social_k2_like_show_faces";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_show_send_button" WHERE `setting` = "social_k2_like_show_send_button";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_width" WHERE `setting` = "social_k2_like_width";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_verb_to_display" WHERE `setting` = "social_k2_like_verb_to_display";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_font" WHERE `setting` = "social_k2_like_font";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_color_scheme" WHERE `setting` = "social_k2_like_color_scheme";
UPDATE `#__jfbconnect_config` SET `setting` = "social_k2_item_like_show_extra_social_buttons" WHERE `setting` = "social_k2_like_show_extra_social_buttons";