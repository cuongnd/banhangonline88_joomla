CREATE TABLE IF NOT EXISTS `#__support_activities` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_user` INT( 11 ) NOT NULL ,
	`ip_address` VARCHAR( 25 ) NOT NULL ,
	`client` VARCHAR( 10 ) NOT NULL ,
	`section` VARCHAR( 25 ) NOT NULL ,
	`action` VARCHAR( 25 ) NOT NULL ,
	`id_table` INT( 11 ) NOT NULL ,
	`date_created` DATETIME NOT NULL ,
	`link` VARCHAR( 250 ) NOT NULL ,
	`is_support` TINYINT(1) NOT NULL default '0' ,
	`id_client` INT( 11 ) NOT NULL
);
CREATE TABLE IF NOT EXISTS `#__support_addon` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
	`sname` VARCHAR( 50 ) NOT NULL ,
	`lname` VARCHAR( 100 ) NOT NULL ,
	`description` LONGTEXT NOT NULL ,
	`iscore` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
	`version` VARCHAR( 10 ) NOT NULL ,
	`execution` TINYINT( 2 ) DEFAULT '0' NOT NULL ,
	`menu` VARCHAR( 50 ) NOT NULL ,
	`date` DATE default NULL ,
	`publish` TINYINT( 1 ) DEFAULT '1' NOT NULL,
	PRIMARY KEY ( `id` )
);
CREATE TABLE IF NOT EXISTS `#__support_addon_contract` (
	`percentage` int(11) NOT NULL,
	`notify` tinyint(1) NOT NULL default '1'
);
CREATE TABLE IF NOT EXISTS `#__support_activity_rate` (
	`id` int(11) NOT NULL auto_increment,
	`description` varchar(50) NOT NULL default '',
	`multiplier` double(2,1) NOT NULL default '1.0',
	`isdefault` tinyint(1) NOT NULL default '0',
	`published` tinyint(1) NOT NULL default '1',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_activity_type` (
	`id` int(11) NOT NULL auto_increment,
	`description` varchar(50) NOT NULL default '',
	`isdefault` tinyint(1) NOT NULL default '0',
	`published` tinyint(1) NOT NULL default '1',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_announce` (
	`id` int(11) NOT NULL auto_increment,
	`id_workgroup` int(11) NOT NULL default '0',
	`id_client` int(11) NOT NULL default '0',
	`date` date NOT NULL default '0000-00-00',
	`introtext` mediumtext NOT NULL,
	`bodytext` longtext,
	`frontpage` tinyint(1) NOT NULL default '0',
	`urgent` tinyint(1) NOT NULL default '0',
	`sent` tinyint(1) NOT NULL default '0',
	`slug` VARCHAR( 200 ) NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_announce_mail` (
	`id` int(11) NOT NULL auto_increment,
	`email` varchar(150) NOT NULL default '',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_bookmark` (
	`id` int(11) NOT NULL auto_increment,
	`id_user` int(11) NOT NULL default '0',
	`id_bookmark` int(11) NOT NULL default '0',
	`source` enum('T','K') NOT NULL default 'T',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_category` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(100) NOT NULL default '',
	`show` tinyint(1) NOT NULL default '0',
	`id_workgroup` int(11) NOT NULL default '0',
	`parent` int(11) NOT NULL default '0',
	`kb` tinyint(1) NOT NULL default '0',
	`tickets` tinyint(1) NOT NULL default '0',
	`downloads` tinyint(1) NOT NULL default '0',
	`discussions` tinyint(1) NOT NULL default '0',
	`bugtracker` tinyint(1) NOT NULL default '0',
	`glossary` tinyint(1) NOT NULL default '0',
	`level` INT( 10 ) NOT NULL DEFAULT '1',
	`slug` VARCHAR( 100 ) NULL,
	`ordering` int(11) NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_client` (
	`id` int(11) NOT NULL auto_increment,
	`date_created` date NOT NULL default '0000-00-00',
	`clientname` varchar(100) NOT NULL default '',
	`address` varchar(150) default NULL,
	`zipcode` varchar(15) default NULL,
	`city` varchar(50) default NULL,
	`state` varchar(100) default NULL,
	`country` varchar(50) default NULL,
	`phone` varchar(25) default NULL,
	`fax` varchar(25) default NULL,
	`mobile` varchar(25) default NULL,
	`email` varchar(100) NOT NULL default '',
	`contactname` varchar(100) NOT NULL default '',
	`website` varchar(100) default NULL,
	`description` longtext,
	`travel_time` double(14,2) NOT NULL default '0.00',
	`rate` double(14,2) NOT NULL default '0.00',
	`manager` tinyint(1) NOT NULL default '0',
	`block` tinyint(1) NOT NULL default '0',
	`client_mail_notify` text,
	`logo` varchar(250) default NULL,
	`clientid` VARCHAR( 10 ) NOT NULL,
	`taxnumber` VARCHAR( 15 ) NOT NULL,
	`approval` tinyint( 1 ) NOT NULL default '0',
	`slug` VARCHAR( 100 ) NULL,
	`autoassign` int(11) NOT NULL default '0',
	`app_announcements` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_bugtracker` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_discussions` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_glossary` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_trouble` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_downloads` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_kb` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_faq` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_ticket` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`overtime` DECIMAL(14, 2) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_client_docs` (
	`id` int(11) NOT NULL auto_increment,
	`id_client` int(11) NOT NULL default '0',
	`date_created` date NOT NULL default '0000-00-00',
	`description` longtext NOT NULL,
	`filename` varchar(200) NOT NULL default '',
	`available` tinyint( 1 ) NOT NULL default '1',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_client_info` (
	`id` int(11) NOT NULL auto_increment,
	`id_client` int(11) NOT NULL default '0',
	`date` date NOT NULL default '0000-00-00',
	`subject` varchar(100) NOT NULL default '',
	`message` text NOT NULL,
	`published` tinyint(1) NOT NULL default '1',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_client_users` (
	`id_client` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`id` int(11) NOT NULL auto_increment,
	`manager` tinyint(1) NOT NULL default '0',
	`default` tinyint NOT NULL default '0',
	`create_ticket` tinyint NOT NULL default '0',
	PRIMARY KEY  (`id`),
	INDEX `idx_id_user` (`id_user`)
);
CREATE TABLE IF NOT EXISTS `#__support_client_wk` (
	`id_workgroup` int(11) NOT NULL default '0',
	`id_client` int(11) NOT NULL default '0',
	`app_announcements` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_bugtracker` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_discussions` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_glossary` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_trouble` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_downloads` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_kb` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_faq` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	`app_ticket` TINYINT( 1 ) NOT NULL DEFAULT '1' ,
	PRIMARY KEY  (`id_workgroup`,`id_client`)
);
CREATE TABLE IF NOT EXISTS `#__support_components` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(100) NOT NULL default '',
	`description` text NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_config` (
	`id` tinyint(1) NOT NULL default '1',
	`update_version` varchar(50) NOT NULL default '',
	`support_version` varchar(50) NOT NULL default '',
	`date_short` varchar(255) NOT NULL default '',
	`date_long` varchar(255) NOT NULL default '',
	`docspath` varchar(150) NOT NULL default '',
	`extensions` text,
	`maxAllowed` varchar(50) NOT NULL default '',
	`public_attach` tinyint(1) NOT NULL default '0',
	`attachs_num` tinyint(2) NOT NULL default '1',
	`receive_mail` tinyint(1) NOT NULL default '0',
	`ac_active` tinyint(1) NOT NULL default '0',
	`ac_days` tinyint(3) NOT NULL default '30',
	`rating` varchar(25) NOT NULL default 'star',
	`kb_popinfo` tinyint(1) NOT NULL default '0',
	`kb_recent` tinyint(1) NOT NULL default '0',
	`kb_moderate` tinyint(1) NOT NULL default '0',
	`notify_rate` tinyint(1) NOT NULL default '1',
	`less_rate` tinyint(1) NOT NULL default '2',
	`unregister` tinyint(1) NOT NULL default '0',
	`links` tinyint(1) NOT NULL default '1',
	`week_start` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`faq_kb_nhits` INT( 5 ) NOT NULL DEFAULT '0',
	`faq_kb_hits` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`faq_kb_manual` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`readmail_create_user` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`password_settings` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`license_agreement` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`company_info` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`create_client` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`rf_phone` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`rf_fax` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`rf_mobile` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`rf_address1` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`rf_address2` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`rf_zipcode` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`rf_location` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`rf_country` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`rf_city` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`users_registration` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`default_source` varchar(1) NOT NULL default '0',
	`rss` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`minutes` TINYINT( 2 ) NOT NULL DEFAULT '15',
	`users_login` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`use_uncategorized` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`client_change_status` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`support_change_status` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`register_user_change_status` TINYINT(1) NOT NULL DEFAULT 0,
	`support_only_show_assign` TINYINT(1) NOT NULL DEFAULT 0,
	`support_workgroup_only` TINYINT(1) NOT NULL DEFAULT 0,
	`duedate_algoritm` TINYINT(1) NOT NULL DEFAULT 0,
	`duedate_holidays` TINYINT(1) NOT NULL DEFAULT 1,
	`duedate_vacations` TINYINT(1) NOT NULL DEFAULT 1,
	`duedate_firstday` TINYINT(1) NOT NULL DEFAULT 1,
	`duedate_schedule` TINYINT(1) NOT NULL DEFAULT 1,
	`duedate_firstday_minimum` TINYINT(1) NOT NULL DEFAULT 0,
	`duedate_hoursday` TINYINT(1) NOT NULL DEFAULT 8,
	`id_schedule_default` INT(11) NOT NULL DEFAULT 0,
	`mail_queue` TINYINT(1) NOT NULL DEFAULT 0,
	`extra_email_notification` TINYINT(1) NOT NULL DEFAULT 0,
	`use_avatars` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`ticket_view` VARCHAR( 5 ) NOT NULL DEFAULT 'list',
	`show_dashboard_support` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`show_dashboard_customer` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`anonymous_tickets` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`post_comments_in_wall` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`use_jomsocial_avatars` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`post_kb_creation_in_wall` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`google_adwords` varchar(50) default NULL,
	`integrate_mtree` TINYINT(1) NOT NULL DEFAULT '0',
	`integrate_sobi` TINYINT(1) NOT NULL DEFAULT '0',
	`discussions_moderated` TINYINT(1) default 1,
	`integrate_artofuser` TINYINT(1) NOT NULL DEFAULT '0',
	`use_cb_avatars` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`mobile_interface` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`offset` TINYINT(3) NOT NULL DEFAULT '0',
	`js_post_question_wall` TINYINT(1) NOT NULL DEFAULT '0',
	`js_post_answer_wall` TINYINT(1) NOT NULL DEFAULT '0',
	`js_post_votes_wall` TINYINT(1) NOT NULL DEFAULT '0',
	`js_answer_selected_wall` TINYINT(1) NOT NULL DEFAULT '0',
	`js_post_bugtracker_wall` TINYINT(1) NOT NULL DEFAULT '0',
	`profile_required` TINYINT(1) NOT NULL DEFAULT '0',
	`stopspam` TINYINT(1) NOT NULL DEFAULT '0',
	`hide_powered` TINYINT(1) NOT NULL DEFAULT '0',
	`common_ticket_views` TINYINT(1) NOT NULL DEFAULT '0',
	`currency` varchar(10) default '&euro;',
	`use_merge` TINYINT(1) NOT NULL DEFAULT '0',
	`use_as_reply` TINYINT(1) NOT NULL DEFAULT '0',
	`use_parent` TINYINT(1) NOT NULL DEFAULT '0',
	`use_travel` TINYINT(1) NOT NULL DEFAULT '0',
	`use_type` TINYINT(1) NOT NULL DEFAULT '0',
	`integrate_jbolo` TINYINT(1) NOT NULL DEFAULT '0',
	`integrate_digistore` TINYINT(1) NOT NULL DEFAULT '0',
	`kb_approvement` tinyint(1) NOT NULL default '0',
	`ticket_ignore_letter` text,
	`bbb_url` varchar(50) default NULL,
	`bbb_apikey` varchar(50) default NULL,
	`bug_status` varchar(1) default 'P',
	`show_login_form` TINYINT(1) NOT NULL DEFAULT '1',
	`show_login_details` TINYINT(1) NOT NULL DEFAULT '1',
	`theme_icon` VARCHAR(25) DEFAULT 'default',
	`customfields_search` tinyint(1) default '0',
	`tickets_numbers` tinyint(1) default '1',
	`kbsocial` tinyint(1) default '1',
	`system_log` tinyint(1) default '1',
	`sms_assign` tinyint(1) default '1',
	`sms_username` VARCHAR(25) default NULL,
	`sms_password` VARCHAR(25) default NULL,
	`sms_gateway` VARCHAR(25) default NULL,
	`use_jomwall_avatars` tinyint(1) default '0',
	`use_department_groups` tinyint(1) default '0',
	`download_notification` tinyint(1) default '0',
	`faq_single_page` tinyint(1) default '0',
	`discussions_anonymous` tinyint(1) NOT NULL default '0',
	`downloads_badges` tinyint(1) NOT NULL default '1',
	`departments_template` VARCHAR(50) default 'departments_list',
	`screenr_account` VARCHAR(250) default '',
	`screenr_api_id` VARCHAR(250) default '',
	`jquery_source` VARCHAR(25) default 'google',
	`kb_enable_rating` tinyint(1) default '1',
	`kb_enable_comments` tinyint(1) default '1',
	`digistore_domains` tinyint(1) default '1',
	`github_username` VARCHAR(50) default '',
	`github_password` VARCHAR(50) default '',
	`show_kb_frontpage` tinyint(1) default '1',
	`include_bootstrap` tinyint(1) default '1',
	`dateonly_format` varchar(50) default '%d/%m/%Y',
	`manual_times` tinyint(1) default '0',
	`editor` varchar(10) NOT NULL default 'builtin',
	`kb_number_chars` varchar(3) NOT NULL default '30',
	`kb_number_columns` varchar(2) NOT NULL default '3',
	`use_eshop_suite_avatars` tinyint(1) default '0',
	`date_country_code` varchar(25) NOT NULL default '',
	`tickets_per_department` TINYINT(1) NOT NULL DEFAULT '0',
	UNIQUE KEY `id` (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_contract` (
	`id` int(11) NOT NULL auto_increment,
	`id_contract` int(11) NOT NULL default '0',
	`id_client` int(11) NOT NULL default '0',
	`contract_number` varchar(200) default NULL,
	`creation_date` date NOT NULL default '0000-00-00',
	`date_start` date NOT NULL default '0000-00-00',
	`date_end` date default NULL,
	`unit` enum('Y','M','D','H','T') NOT NULL default 'Y',
	`value` int(11) NOT NULL default '0',
	`actual_value` DECIMAL( 14, 2 ) NOT NULL default '0',
	`status` enum('A','I','C') NOT NULL default 'I',
	`remarks` longtext,
	`id_user` INT( 11 ) NOT NULL,
	`notified` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`overdue` DECIMAL( 14, 2 ) NOT NULL DEFAULT '0',
	`overdue_update` DECIMAL(14,2) NOT NULL DEFAULT '0.00',
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_contract_comp` (
	`id_contract` int(11) NOT NULL default '0',
	`id_component` int(11) NOT NULL default '0'
);
CREATE TABLE IF NOT EXISTS `#__support_contract_fields` (
	`id` int(11) NOT NULL auto_increment,
	`id_field` int(11) NOT NULL,
	`ordering` int(11) NOT NULL,
	`required` tinyint(1) NOT NULL default '0',
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_contract_files` (
	`id` int(11) NOT NULL auto_increment,
	`id_contract` int(11) NOT NULL default '0',
	`filename` varchar(100) NOT NULL default '',
	`date` date NOT NULL default '0000-00-00',
	`description` longtext,
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_contract_template` (
	`id` int(11) NOT NULL auto_increment,
	`id_priority` int(11) NOT NULL default '0',
	`name` varchar(100) NOT NULL default '',
	`description` TEXT NOT NULL DEFAULT '',
	`unit` enum('Y','M','D','H','T') NOT NULL default 'Y',
	`val` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_country` (
	`startip` varchar(10) NOT NULL default '',
	`endip` varchar(10) NOT NULL default '',
	`countrycode` varchar(2) NOT NULL DEFAULT '',
	`countryname` varchar(100) NOT NULL DEFAULT '',
	UNIQUE KEY `idxBoth` (`startip`,`endip`),
    KEY `idxStart` (`startip`),
    KEY `idxEnd` (`endip`)
);
CREATE TABLE IF NOT EXISTS `#__support_contract_fields_values` (
	`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
	`id_contract` int( 11 ) NOT NULL default '0',
	`id_field` int( 11 ) NOT NULL default '0',
	`value` varchar( 200 ) NOT NULL default '',
	PRIMARY KEY ( `id` )
);
CREATE TABLE IF NOT EXISTS `#__support_custom_fields` (
	`id` int(11) NOT NULL auto_increment,
	`caption` TEXT NOT NULL DEFAULT '',
	`fname` varchar(50) NOT NULL default '',
	`ftype` enum('text', 'select', 'radio', 'checkbox', 'textarea', 'htmleditor', 'dbselect', 'date', 'country', 'state', 'note') NOT NULL default 'text',
	`value` text default NULL,
	`size` int(3) default NULL,
	`maxlength` int(4) NOT NULL default '0',
	`cftype` VARCHAR( 1 ) NOT NULL DEFAULT 'W',
	`tooltip` TEXT NULL,
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_department_groups` (
  `id_department` INT( 11 ) NOT NULL ,
  `id_group` INT( 11 ) NOT NULL ,
  PRIMARY KEY (  `id_department` ,  `id_group` )
);
CREATE TABLE IF NOT EXISTS `#__support_discussions` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_user` INT( 11 ) NOT NULL ,
	`id_workgroup` INT( 11 ) NOT NULL DEFAULT '0',
	`id_category` INT( 11 ) NOT NULL DEFAULT '0',
	`date_created` DATETIME NOT NULL ,
	`title` VARCHAR( 100 ) NOT NULL ,
	`content` TEXT NOT NULL ,
	`published` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`status` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`converted` INT( 11 ) NOT NULL DEFAULT '0',
	`tags` VARCHAR( 250 ) NULL,
	`views` INT( 11 ) NOT NULL DEFAULT '0',
	`votes` INT( 11 ) NOT NULL DEFAULT '0',
	`slug` VARCHAR( 100 ) NULL
);
CREATE TABLE IF NOT EXISTS `#__support_discussions_messages` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_discussion` INT( 11 ) NOT NULL ,
	`id_user` INT( 11 ) NOT NULL ,
	`is_support` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`date_created` DATETIME NOT NULL ,
	`content` TEXT NOT NULL ,
	`published` TINYINT NOT NULL DEFAULT '0',
	`votes` INT( 11 ) NOT NULL DEFAULT '0'
);
CREATE TABLE IF NOT EXISTS `#__support_discussions_subscribe` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_discussion` INT( 11 ) NOT NULL ,
	`id_user` INT( 11 ) NOT NULL
);
CREATE TABLE IF NOT EXISTS `#__support_discussions_votes` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_user` INT( 11 ) NOT NULL ,
	`id_discussion` INT( 11 ) NOT NULL ,
	`id_message` INT( 11 ) NOT NULL ,
	`date_created` DATETIME NOT NULL ,
	`vote` INT( 11 ) NOT NULL DEFAULT '0'
);
CREATE TABLE IF NOT EXISTS `#__support_dl` (
	`id` int(11) NOT NULL auto_increment,
	`id_category` int(11) NOT NULL default '0',
	`pname` varchar(100) NOT NULL default '',
	`description` text NOT NULL,
	`ordering` int(4) NOT NULL default '0',
	`url` varchar(250) default NULL,
	`plataform` varchar(250) NOT NULL default '',
	`date` date NOT NULL default '0000-00-00',
	`hits` int(11) NOT NULL default '0',
	`id_license` int(11) NOT NULL default '0',
	`groupid` varchar(100) default '',
	`features` text NOT NULL,
	`requirements` text NOT NULL,
	`limitations` varchar(255) NOT NULL default '',
	`published` int(10) NOT NULL default '0',
	`expired` date NOT NULL default '0000-00-00',
	`updated` date NOT NULL default '0000-00-00',
	`offline` tinyint(1) NOT NULL default '0',
	`image` varchar(100) NOT NULL default '',
	`evaluation` varchar(200) NOT NULL default '',
	`download_version` tinyint(1) NOT NULL default '1',
	`download_previous` tinyint( 1 ) NOT NULL default '0',
	`template_file` VARCHAR( 100 ) NOT NULL ,
	`registered_only` TINYINT( 1 ) DEFAULT 0 NOT NULL ,
	`image_view` VARCHAR( 200 ) NOT NULL ,
	`slug` VARCHAR( 100 ) NULL,
	PRIMARY KEY  (`id`),
	KEY `pname` (`pname`(40)),
	KEY `id_category` (`id_category`)
);
CREATE TABLE IF NOT EXISTS `#__support_dl_access` (
	`id` int(11) NOT NULL auto_increment,
	`id_download` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`isactive` tinyint(1) NOT NULL default '0',
	`serialno` varchar(200) NOT NULL default '',
	`servicefrom` date NOT NULL default '0000-00-00',
	`serviceuntil` date NOT NULL default '0000-00-00',
	PRIMARY KEY  (`id`),
	KEY `id_download` (`id_download`),
	KEY `id_version` (`id_user`)
);
CREATE TABLE IF NOT EXISTS `#__support_dl_category` (
	`id` int(11) NOT NULL auto_increment,
	`cname` varchar(100) NOT NULL default '',
	`description` text,
	`ordering` int(4) NOT NULL default '0',
	`published` tinyint(1) NOT NULL default '0',
	`parent` int(11) NOT NULL default '0',
	`image` VARCHAR( 100 ) NULL DEFAULT NULL,
	`level` INT( 10 ) NOT NULL DEFAULT '1',
	`slug` VARCHAR( 100 ) NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_dl_group` (
	`id` int(11) NOT NULL auto_increment,
	`gname` varchar(50) NOT NULL default '',
	`description` varchar(250) NOT NULL default '',
	`unregister` tinyint(1) NOT NULL default '0',
	`isdefault` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_dl_license` (
	`id` int(11) NOT NULL auto_increment,
	`title` varchar(100) NOT NULL default '',
	`description` longtext NOT NULL,
	`slug` VARCHAR( 100 ) NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_dl_notify` (
	`id` int(11) NOT NULL auto_increment,
	`id_download` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`date` date NOT NULL default '0000-00-00',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_dl_stats` (
	`id` int(11) NOT NULL auto_increment,
	`id_version` int(11) NOT NULL default '0',
	`id_download` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL,
	`ipaddress` varchar(25) NOT NULL default '0',
	`dldate` datetime NOT NULL default '0000-00-00',
	PRIMARY KEY  (`id`),
	KEY `id_version` (`id_version`),
	KEY `id_download` (`id_download`)
);
CREATE TABLE IF NOT EXISTS `#__support_dl_users` (
	`id_group` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id_group`,`id_user`)
);
CREATE TABLE IF NOT EXISTS `#__support_dl_version` (
	`id` int(11) NOT NULL auto_increment,
	`id_download` int(11) NOT NULL default '0',
	`version` varchar(10) NOT NULL default '',
	`date` date NOT NULL default '0000-00-00',
	`description` text NOT NULL,
	`filename` varchar(200) NOT NULL default '',
	`filename_original` VARCHAR( 200 ) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `id_download` (`id_download`)
);
CREATE TABLE IF NOT EXISTS `#__support_export` (
	`id` int(11) NOT NULL auto_increment,
	`export_type` text NOT NULL,
	`export_date` datetime default '0000-00-00 00:00:00',
	`export_author` text NOT NULL,
	`export_options` text NOT NULL,
	`profile_name` text NOT NULL,
	`profile_tmpl` longtext NOT NULL,
	`fileformat` varchar(3) NOT NULL default '',
	`num_records` int(11) NOT NULL default '0',
	`hits` int(11) NOT NULL default '0',
	`export_data` longtext NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_export_profile` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(100) NOT NULL default '',
	`description` text NOT NULL,
	`isdefault` tinyint(1) NOT NULL default '0',
	`billableonly` tinyint(1) NOT NULL default '0',
	`export_tmpl` longtext NOT NULL,
	`export_type` VARCHAR( 10 ) NOT NULL,
	`auto_save` tinyint(1) NOT NULL default '1',
	`filter_statusid` VARCHAR(11) NOT NULL default '0',
	`filter_wkid` VARCHAR(11) NOT NULL default '0',
	`filter_clientid` VARCHAR(11) NOT NULL default '0',
	`filter_userid` VARCHAR(11) NOT NULL default '0',
	`update_exported` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_field_value` (
	`id_field` int(11) NOT NULL default '0',
	`id_ticket` int(11) NOT NULL default '0',
	`newfield` text default NULL,
	PRIMARY KEY  (`id_field`,`id_ticket`),
	INDEX `idx_id_ticket` (`id_ticket`)
);
CREATE TABLE IF NOT EXISTS `#__support_file` (
	`id_file` int(11) NOT NULL auto_increment,
	`id` int(11) NOT NULL default '0',
	`id_reply` int(11) NOT NULL default '0',
	`date` DATETIME NOT NULL,
	`id_user` int(11) NOT NULL default '0',
	`source` enum('T','K','B') NOT NULL default 'T',
	`filename` varchar(255) NOT NULL default '',
	`public` tinyint(1) NOT NULL default '1',
	`description` varchar(255) default NULL,
	PRIMARY KEY  (`id_file`)
);
CREATE TABLE IF NOT EXISTS `#__support_form` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(55) NOT NULL default '',
	`description` MEDIUMTEXT NULL default '',
	`redirect` varchar(255) NOT NULL default '',
	`show` tinyint(1) NOT NULL default '0',
	`layout` text NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_form_action` (
	`id` int(11) NOT NULL auto_increment,
	`id_form` int(11) NOT NULL default '0',
	`type` enum('email','db','show','include','user') NOT NULL default 'email',
	`value` varchar(100) NOT NULL default '',
	`layout` text NOT NULL,
	`published` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_form_field` (
	`id` int(11) NOT NULL auto_increment,
	`id_form` int(11) NOT NULL default '0',
	`caption` varchar(250) NOT NULL default '',
	`name` varchar(50) NOT NULL default '',
	`type` enum('text','select','radio','password','checkbox','textarea','htmlarea') NOT NULL default 'text',
	`value` varchar(250) default NULL,
	`order` int(2) NOT NULL default '1',
	`size` int(3) default NULL,
	`maxlength` int(4) default NULL,
	`required` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_glossary` (
	`id` int(11) NOT NULL auto_increment,
	`id_category` int(11) NOT NULL default '0',
	`term` varchar(50) NOT NULL default '',
	`description` text NOT NULL,
	`published` tinyint(1) NOT NULL default '1',
	`anonymous_access` TINYINT( 1 ) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_kb` (
	`id` int(11) NOT NULL auto_increment,
	`kbcode` varchar(15) default NULL,
	`id_user` int(11) NOT NULL default '0',
	`kbtitle` varchar(150) NOT NULL default '',
	`content` longtext NOT NULL,
	`keywords` varchar(250) NOT NULL default '',
	`publish` tinyint(1) NOT NULL default '0',
	`views` int(4) NOT NULL default '0',
	`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
	`date_updated` datetime NOT NULL default '0000-00-00 00:00:00',
	`anonymous_access` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`faq` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`approved` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`slug` VARCHAR( 150 ) NULL,
	`ordering` int(11) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `id_user` (`id_user`)
);
CREATE TABLE IF NOT EXISTS `#__support_kb_category` (
	`id_category` int(11) NOT NULL default '0',
	`id_kb` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id_category`,`id_kb`)
);
CREATE TABLE IF NOT EXISTS `#__support_kb_comment` (
	`id` int(11) NOT NULL auto_increment,
	`id_workgroup` int(11) default '0',
	`id_user` int(11) default NULL,
	`id_kb` int(11) NOT NULL default '0',
	`itemid` int(11) NOT NULL default '0',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	`comment` mediumtext NOT NULL,
	`publish` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`),
	KEY `id_user` (`id_user`,`id_kb`)
);
CREATE TABLE IF NOT EXISTS `#__support_log` (
	`id` int(11) NOT NULL auto_increment,
	`id_ticket` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`date_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`log` varchar(250) NOT NULL default '',
	`log_reserved` varchar(250) NOT NULL default '',
	`id_status` int(11) NOT NULL default '0',
	`field` varchar(20) not null default '',
	`value` int(11) not null default '0',
	`time_elapse` int(11) not null default '0',
	`image` VARCHAR( 25 ) NULL DEFAULT NULL,
	INDEX `idx_log_report` ( `id_ticket`, `date_time`, `time_elapse`, `field`, `value` ),
	INDEX `idx_log_ticket` (`id_ticket`),
	INDEX `idx_log_user` (`id_user`),
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_options` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_workgroup` INT( 11 ) NOT NULL ,
	`name` VARCHAR( 100 ) NOT NULL ,
	`description` VARCHAR( 200 ) NOT NULL ,
	`image` VARCHAR( 200 ) NOT NULL ,
	`link` VARCHAR( 200 ) NOT NULL
);
CREATE TABLE IF NOT EXISTS `#__support_mail_fetch` (
	`id` int(11) NOT NULL auto_increment,
	`id_workgroup` int(11) NOT NULL default '0',
	`email` varchar(100) NOT NULL default '',
	`server` varchar(100) NOT NULL default '',
	`port` varchar(5) NOT NULL default '110',
	`username` varchar(100) NOT NULL default '',
	`password` varchar(100) NOT NULL default '',
	`type` varchar(4) NOT NULL default 'pop',
	`remove` tinyint(1) NOT NULL default '1',
	`extra_info` varchar(100) NOT NULL default '',
	`queue` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`id_status` INT( 11 ) NOT NULL DEFAULT '0',
	`id_category` INT( 11 ) NOT NULL DEFAULT '0',
	`label` varchar(25) NOT NULL default 'INBOX',
	`notls` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`thrash` VARCHAR( 25 ) NOT NULL DEFAULT 'TRASH',
	`ssl` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`published` TINYINT( 1 ) NOT NULL DEFAULT '1',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_mail_log` (
	`id` int(11) NOT NULL auto_increment,
	`id_mail_fetch` int(11) NOT NULL default '0',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	`email` varchar(150) NOT NULL default '',
	`log` varchar(250) NOT NULL default '',
	`emailid` varchar(250) NOT NULL default '',
	INDEX `idx_emailid` ( `emailid` ( 150 ) ),
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_mail_queue` (
	`id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`usermail` VARCHAR( 200 ) NOT NULL ,
	`subject` VARCHAR( 200 ) NOT NULL ,
	`body` LONGTEXT NOT NULL ,
	`wkmail` VARCHAR( 200 ) NOT NULL ,
	`wkmail_name` VARCHAR( 200 ) NOT NULL ,
	`date_created` DATETIME NOT NULL ,
	`cc` TEXT NULL ,
	`bcc` TEXT NULL
);
CREATE TABLE IF NOT EXISTS `#__support_note` (
	`id` int(11) NOT NULL auto_increment,
	`id_ticket` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`date_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`note` longtext NOT NULL,
	`show` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_permission` (
	`id` int(11) NOT NULL auto_increment,
	`id_user` int(11) NOT NULL default '0',
	`id_workgroup` int(11) NOT NULL default '0',
	`id_schedule` int(11) NOT NULL default '0',
	`assign_only` tinyint(1) NOT NULL default '0',
	`manager` tinyint(1) NOT NULL default '0',
	`assign_report_users` varchar(255) not null default '',
	`can_delete` tinyint(1) NOT NULL default '0',
	`bugtracker` tinyint(1) NOT NULL default '1',
	`level` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`,`id_user`,`id_workgroup`)
);
CREATE TABLE IF NOT EXISTS `#__support_permission_category` (
	`id` int(11) NOT NULL auto_increment,
	`id_user` int(11) NOT NULL default '0',
	`id_workgroup` int(11) NOT NULL default '0',
	`id_category` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_priority` (
	`id` int(11) NOT NULL auto_increment,
	`description` varchar(100) NOT NULL default '',
	`show` tinyint(1) NOT NULL default '0',
	`timevalue` int(11) NOT NULL default '0',
	`timeunit` enum('D','H') NOT NULL default 'H',
	`isdefault` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_rate` (
	`id` int(11) NOT NULL auto_increment,
	`id_table` int(11) NOT NULL default '0',
	`source` enum('T','K') NOT NULL default 'T',
	`rate` tinyint(1) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`date` date NOT NULL default '0000-00-00',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_reply` (
	`id` int(11) NOT NULL auto_increment,
	`subject` varchar(100) NOT NULL default '',
	`answer` text NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_reports` (
	`id` int(11) NOT NULL auto_increment,
	`title` varchar(100) NOT NULL,
	`description` text NOT NULL,
	`f_workgroup` int(11) NOT NULL,
	`f_category` int(11) NOT NULL,
	`f_client` int(11) NOT NULL,
	`f_user` int(11) NOT NULL,
	`f_year` varchar(4) NOT NULL,
	`f_month` varchar(2) NOT NULL,
	`f_priority` int(11) NOT NULL,
	`f_status` int(11) NOT NULL,
	`f_source` int(11) NOT NULL,
	`f_staff` int(11) NOT NULL,
	`groupby` varchar(10) NOT NULL,
	`groupby2` varchar(10) NOT NULL,
	`report_type` varchar(1) NOT NULL,
	`chart_type` varchar(10) NOT NULL,
	`sf_workgroup` tinyint(1) NOT NULL default '0',
	`sf_category` tinyint(1) NOT NULL default '0',
	`sf_priority` tinyint(1) NOT NULL default '0',
	`sf_client` tinyint(1) NOT NULL default '0',
	`sf_user` tinyint(1) NOT NULL default '0',
	`sf_staff` tinyint(1) NOT NULL default '0',
	`sf_year` tinyint(1) NOT NULL default '0',
	`sf_month` tinyint(1) NOT NULL default '0',
	`sf_status` tinyint(1) NOT NULL default '0',
	`sf_source` tinyint(1) NOT NULL default '0',
	`chart_width` int(3) NOT NULL default '300',
	`chart_height` int(3) NOT NULL default '300',
	`layout` tinyint(1) NOT NULL default '1',
	`type` tinyint(1) NOT NULL default '1',
	`chart_percentage` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_status` (
	`id` int(11) NOT NULL auto_increment,
	`description` varchar(100) NOT NULL default '',
	`show` tinyint(1) NOT NULL default '0',
	`status_group` enum('O','C') NOT NULL default 'O',
	`isdefault` tinyint(1) NOT NULL default '0',
	`user_access` tinyint(1) NOT NULL default '1',
	`status_workflow` varchar(100) DEFAULT '',
	`allow_old_status_back` TINYINT(1) NOT NULL DEFAULT 1,
	`ticket_side` TINYINT( 1 ) NOT NULL COMMENT '0=Not used, 1=Support, 2=Customer',
	`isdefault_manager` tinyint(1) NOT NULL default '0',
	`auto_status_agents` TINYINT(1) NOT NULL DEFAULT '0',
	`auto_status_users` TINYINT(1) NOT NULL DEFAULT '0',
	`ordering` int(11) NOT NULL DEFAULT '0',
	`color` varchar(7) DEFAULT '',
	INDEX `idx_status_user_access` ( `id`, `status_group`, `user_access` ),
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_sysmsgs` (
	`id` int(11) NOT NULL auto_increment,
	`location` enum('f','a') NOT NULL default 'f',
	`userid` varchar(11) NOT NULL default '0',
	`username` varchar(50) NOT NULL default '',
	`ipaddress` varchar(20) NOT NULL default '0.0.0.0',
	`msgtype` enum('i','w','e') NOT NULL default 'i',
	`message` longtext NOT NULL,
	`occurred` varchar(14) NOT NULL default '0',
	`displayed` varchar(14) NOT NULL default '0',
	`cleaned` enum('0','1') NOT NULL default '0',
	`debugmsg` longtext NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_task` (
	`id` int(11) NOT NULL auto_increment,
	`id_ticket` int(11) NOT NULL default '0',
	`id_creator` int(11) NOT NULL default '0',
	`id_user` TEXT NOT NULL default '',
	`date_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`task` varchar(250) NOT NULL default '',
	`status` enum('O','C') NOT NULL default 'O',
	`timeused` double(14,2) NOT NULL default '0.00',
	`travel` tinyint(1) NOT NULL default '1',
	`traveltime` double(14,2) NOT NULL default '0.00',
	`rate` double(2,1) NOT NULL default '0.0',
	`id_activity_rate` int(11) NOT NULL default '0',
	`id_activity_type` int(11) NOT NULL default '0',
	`start_time` varchar(5) NOT NULL default '',
	`end_time` varchar(5) NOT NULL default '',
	`break_time` varchar(5) NOT NULL default '',
	`end_date` datetime default NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_ticket` (
	`id` int(11) NOT NULL auto_increment,
	`id_workgroup` int(11) NOT NULL default '0',
	`id_status` int(11) NOT NULL default '1',
	`id_user` int(11) NOT NULL default '0',
	`id_category` INT( 11 ) NOT NULL DEFAULT '0',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	`subject` varchar(75) NOT NULL default '',
	`message` longtext NOT NULL,
	`last_update` datetime NOT NULL default '0000-00-00 00:00:00',
	`assign_to` int(11) NOT NULL default '0',
	`id_priority` int(11) NOT NULL default '0',
	`id_kb` int(11) NOT NULL default '0',
	`source` enum('F','P','M','W','O','A','T') NOT NULL default 'W',
	`ticketmask` VARCHAR(50) NULL DEFAULT NULL,
	`an_name` varchar(100) default NULL,
	`an_mail` varchar(100) default NULL,
	`duedate` datetime default NULL,
	`id_export` int(11) NOT NULL default '0',
	`date_support` DATETIME NULL DEFAULT NULL,
	`day_week` TINYINT( 1 ) NOT NULL,
	`id_client` INT( 11 ) NOT NULL,
	`ipaddress` varchar(25) NOT NULL DEFAULT '0',
	`id_ticket_parent` INT( 11 ) NOT NULL DEFAULT '0',
	`sticky` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`id_directory` INT(11) NOT NULL DEFAULT '0',
	`master_ticket` INT( 11 ) NOT NULL DEFAULT '0',
	`queue` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`approved` INT( 11 ) NOT NULL DEFAULT '1',
	`autoclosed` date DEFAULT NULL,
	`github_repository` varchar(50) NOT NULL DEFAULT '',
	`github_issue` varchar(50) NOT NULL DEFAULT '',
	`internal` TINYINT( 1 ) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`id`),
	INDEX `idx_ticket_manager` (`id_workgroup`, `id_status`, `id_priority`, `id_user`, `assign_to`, `id_category`),
	INDEX `idx_ticket_report` ( `id`, `id_status` )
);
CREATE TABLE IF NOT EXISTS `#__support_ticket_resp` (
	`id` int(11) NOT NULL auto_increment,
	`id_ticket` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	`message` longtext NOT NULL,
	`timeused` double(14,2) NOT NULL default '0.00',
	`travel_time` tinyint(1) NOT NULL default '0',
	`tickettravel` double(14,2) NOT NULL default '0.00',
	`id_activity_type` int(11) NOT NULL default '0',
	`id_activity_rate` int(11) NOT NULL default '0',
	`user_rate` double(14,2) NOT NULL default '0.00',
	`start_time` varchar(5) default NULL,
	`end_time` varchar(5) default NULL,
	`break_time` varchar(5) default NULL,
	`reply_summary` VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_troubleshooter` (
	`id` int(11) NOT NULL auto_increment,
	`parent` int(11) NOT NULL default '0',
	`title` varchar(200) NOT NULL default '',
	`description` longtext NOT NULL,
	`show` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_updates` (
	`id` int(11) NOT NULL auto_increment,
	`date_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`type` varchar(50) NOT NULL default '',
	`id_user` int(11) NOT NULL default '0',
	`previous_version` varchar(15) NOT NULL default '',
	`installed_version` varchar(15) NOT NULL default '',
	`description` text NOT NULL,
	`files` text NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_users` (
	`id_user` INT( 11 ) NOT NULL ,
	`phone` VARCHAR( 50 ) NOT NULL ,
	`fax` VARCHAR( 50 ) NOT NULL ,
	`mobile` VARCHAR( 50 ) NOT NULL ,
	`address1` VARCHAR( 100 ) NOT NULL ,
	`address2` VARCHAR( 100 ) NOT NULL ,
	`zipcode` VARCHAR( 15 ) NOT NULL ,
	`location` VARCHAR( 100 ) NOT NULL ,
	`city` VARCHAR( 100 ) NOT NULL ,
	`country` VARCHAR( 100 ) NOT NULL,
	`avatar` VARCHAR( 200 ) NOT NULL DEFAULT 'anonymous.png',
	`vacances` text NOT NULL default '',
	`id_schedule` int(11) NOT NULL default '0',
	PRIMARY KEY ( `id_user` )
);
CREATE TABLE IF NOT EXISTS `#__support_user_fields` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
	`id_field` INT( 11 ) NOT NULL ,
	`ordering` INT( 11 ) NOT NULL ,
	`required` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
	`support_only` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
	PRIMARY KEY ( `id` )
);
CREATE TABLE IF NOT EXISTS `#__support_user_values` (
	`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
	`id_user` int( 11 ) NOT NULL default '0',
	`id_field` int( 11 ) NOT NULL default '0',
	`value` varchar( 200 ) NOT NULL default '',
	PRIMARY KEY ( `id` )
);
CREATE TABLE IF NOT EXISTS `#__support_wk_fields` (
	`id` int(11) NOT NULL auto_increment,
	`id_workgroup` int(11) NOT NULL default '0',
	`id_category` TEXT NULL default NULL,
	`id_field` int(11) NOT NULL default '0',
	`required` tinyint(1) NOT NULL default '1',
	`support_only` tinyint(1) NOT NULL default '0',
	`ordering` int(3) NOT NULL default '0',
	`new_only` int(11) not null default '0',
	`section` varchar( 100 ) NOT NULL default '',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_workgroup` (
	`id` int(11) NOT NULL auto_increment,
	`wkdesc` varchar(100) NOT NULL default '',
	`logo` varchar(100) default NULL,
	`wkabout` longtext NOT NULL,
	`wkkb` tinyint(1) NOT NULL default '0',
	`wkemail` tinyint(1) NOT NULL default '0',
	`wkticket` tinyint(1) NOT NULL default '0',
	`show` tinyint(1) NOT NULL default '0',
	`wkmail_address` varchar(254) default NULL,
	`wkmail_address_name` varchar(254) default NULL,
	`wkadmin_email` varchar(254) NOT NULL default '',
	`auto_assign` int(4) NOT NULL default '0',
	`trouble` tinyint(1) NOT NULL default '0',
	`contract` tinyint(1) NOT NULL default '0',
	`ordering` int(11) NOT NULL default '0',
	`use_activity` tinyint(1) NOT NULL default '0',
	`anonymous_access` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`hyper_links` ENUM( "0", "1" ) DEFAULT '1' NOT NULL,
	`lim_actwords` ENUM( "0", "1" ) DEFAULT '1' NOT NULL,
	`lim_actwords_chars` INT DEFAULT '75' NOT NULL,
	`lim_actmsgs` ENUM( "0", "1" ) DEFAULT '0' NOT NULL,
	`lim_actmsgs_chars` INT DEFAULT '300' NOT NULL,
	`lim_actmsgs_lines` INT DEFAULT '10' NOT NULL,
	`theme` VARCHAR( 255 ) DEFAULT 'default' NOT NULL,
	`tkt_crt_nfy_mgr` TINYINT(1) NOT NULL default '1',
	`tkt_crt_nfy_admin` TINYINT(1) NOT NULL default '1',
	`tkt_asgn_old_asgn` TINYINT(1) NOT NULL default '1',
	`tkt_asgn_new_asgn` TINYINT(1) NOT NULL default '1',
	`tkt_asgn_nfy_usr_one` TINYINT(1) NOT NULL default '1',
	`wkfaq` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`wkdownloads` tinyint(1) NOT NULL default '1',
	`wkannounces` tinyint(1) NOT NULL default '1',
	`wkglossary` tinyint(1) NOT NULL default '0',
	`enable_discussions` tinyint(1) NOT NULL default '0',
	`contract_total_disable` tinyint(1) NOT NULL default '0',
	`id_priority` INT( 11 ) NOT NULL,
	`use_account` TINYINT(1) NOT NULL DEFAULT '1',
	`use_bookmarks` TINYINT(1) NOT NULL DEFAULT '1',
	`add_mail_tag` TINYINT(1) NOT NULL DEFAULT '0',
	`tkt_nfy_agent` TINYINT(1) NOT NULL default '1',
	`digistore` TINYINT(1) NOT NULL default '0',
	`shortdesc` VARCHAR( 255 ) DEFAULT '' NOT NULL,
	`slug` VARCHAR( 100 ) NULL,
	`bugtracker` TINYINT(1) NOT NULL default '0',
	`id_group` INT(11) NOT NULL default '0',
	`support_only` TINYINT(1) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_escalation_config` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_workgroup` INT( 11 ) NOT NULL ,
	`id_assign` INT( 11 ) NOT NULL ,
	`id_priority` INT( 11 ) NOT NULL ,
	`id_category` INT( 11 ) NOT NULL ,
	`id_status` INT( 11 ) NOT NULL ,
	`id_client` INT( 11 ) NOT NULL ,
	`id_user` INT( 11 ) NOT NULL ,
	`days_reply` INT( 11 ) NOT NULL DEFAULT '10' ,
	`days_open` INT( 11 ) NOT NULL DEFAULT '10' ,
	`ordering` INT( 11 ) NOT NULL ,
	`id_status_trigger` int(11) not null default 0,
	`id_assign_trigger` int(11) not null default 0
);
CREATE TABLE IF NOT EXISTS `#__support_links` (
	`id` int(11) NOT NULL auto_increment,
	`id_workgroup` int(11) NOT NULL,
	`name` varchar(100) NOT NULL,
	`description` text NOT NULL,
	`image` varchar(200) NOT NULL,
	`link` varchar(200) NOT NULL,
	`section` enum('A','F') NOT NULL default 'A',
	`published` tinyint(1) NOT NULL default '1',
	`ordering` int(11) NOT NULL,
	`public` tinyint( 1 ) NOT NULL DEFAULT '1',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_holidays` (
	`id` int(11) NOT NULL auto_increment,
	`holiday_date` DATE NOT NULL default '0000-00-00',
	`name` varchar(60) NOT NULL default '',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_schedule` (
	`id` int(11) UNSIGNED NOT NULL auto_increment,
	`profile` varchar(30) NOT NULL default '',
	`description` varchar(255) NOT NULL default '',
	`work_on_holidays`	TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
	INDEX (`id`),
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_schedule_weekday` (
	`id` int(11) NOT NULL auto_increment,
	`id_schedule` int(11) NOT NULL REFERENCES `#__support_schedule`.`id`,
	`weekday` enum ('1','2','3','4','5','6','7','8') NOT NULL,										# seg, ter, qua, qui, sex, sab, dom
	`work_start` TIME NOT NULL DEFAULT '00:00:00',
	`work_end` TIME NOT NULL DEFAULT '00:00:00',
	`break_start` TIME NOT NULL DEFAULT '00:00:00',
	`break_end` TIME NOT NULL DEFAULT '00:00:00',
	INDEX (`id`),
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_workgroup_category_assign` (
	`id_workgroup` INT( 11 ) NOT NULL ,
	`id_category` INT( 11 ) NOT NULL ,
	`city` VARCHAR( 100 ) NULL ,
	`id_user` INT( 11 ) NOT NULL ,
	PRIMARY KEY (  `id_workgroup` ,  `id_category` ,  `city`(100) ,  `id_user` )
);
CREATE TABLE IF NOT EXISTS `#__support_views` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_user` INT( 11 ) NOT NULL ,
	`name` VARCHAR( 100 ) NOT NULL ,
	`viewtype` VARCHAR( 10 ) NOT NULL ,
	`ordering` VARCHAR( 20 ) NOT NULL ,
	`operator` TEXT NOT NULL ,
	`field` TEXT NOT NULL ,
	`arithmetic` TEXT NOT NULL ,
	`value` TEXT NOT NULL ,
	`orderby` VARCHAR( 4 ) NOT NULL DEFAULT 'ASC' ,
	`default` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `#__support_bugtracker` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_workgroup` INT( 11 ) NOT NULL ,
	`id_user` INT( 11 ) NOT NULL ,
	`id_category` INT( 11 ) NOT NULL ,
	`id_assign` INT( 11 ) NOT NULL ,
	`id_product` INT( 11 ) NOT NULL DEFAULT '0' ,
	`id_version` INT( 11 ) NOT NULL DEFAULT '0' ,
	`id_version_fixed` INT( 11 ) NOT NULL DEFAULT '0' ,
	`priority` INT( 11 ) NOT NULL DEFAULT '0' COMMENT '1=High, 2=Medium High, 3=Medium, 4=Low, 5=Very Low',
	`date_created` DATETIME NOT NULL ,
	`date_updated` DATETIME NOT NULL ,
	`slug` VARCHAR( 200 ) NOT NULL ,
	`title` VARCHAR( 200 ) NOT NULL ,
	`content` TEXT NOT NULL ,
	`status` VARCHAR( 1 ) NOT NULL DEFAULT 'P' COMMENT 'P=Pending, O=Open, I=In progress, R=Resolved, C=Closed, D=Reopened',
	`type` VARCHAR( 1 ) NULL DEFAULT NULL COMMENT 'B=Bug, I=Improvement, N=New feature, R=Request'
);
CREATE TABLE IF NOT EXISTS `#__support_bugtracker_messages` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_bugtracker` INT( 11 ) NOT NULL ,
	`id_user` INT( 11 ) NOT NULL ,
	`date_created` DATETIME NOT NULL ,
	`content` TEXT NOT NULL ,
	`published` TINYINT( 1 ) NOT NULL DEFAULT  '0'
);
CREATE TABLE IF NOT EXISTS `#__support_bbb` (
	`id` int(11) NOT NULL auto_increment,
	`id_user` int(11) NOT NULL,
	`date_created` datetime NOT NULL,
	`meeting_date` date NOT NULL,
	`meeting_hours` time NOT NULL,
	`id_ticket` int(11) NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_bbb_invites` (
	`id` int(11) NOT NULL auto_increment,
	`id_meeting` int(11) NOT NULL,
	`invite` varchar(250) NOT NULL default '',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_bbb_links` (
	`id` int(11) NOT NULL auto_increment,
	`id_meeting` int(11) NOT NULL,
	`id_user` int(11) NOT NULL,
	`link` varchar(250) NOT NULL default '',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_mail_fetch_ignore` (
	`id` int(11) NOT NULL auto_increment,
	`field` varchar(15) NOT NULL default 'subject',
	`operator` varchar(15) NOT NULL default '=',
	`value` varchar(250) NOT NULL default '',
	`published` tinyint( 1 ) NOT NULL DEFAULT  '0',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_department_group` (
	`id` int(11) NOT NULL auto_increment,
	`title` varchar(100) NOT NULL default '',
	`description` longtext NOT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_file_notify` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_file` int(11) NOT NULL,
	`id_user` int(11) NOT NULL,
	PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_ticket_screenr` (
	`id` int(11) NOT NULL auto_increment,
	`id_user` int(11) NOT NULL,
	`id_ticket` int(11) NOT NULL,
	`id_reply` int(11) NOT NULL,
	`id_screen` varchar(250) NOT NULL default '',
	`url` varchar(250) NOT NULL default '',
	`embedurl` varchar(250) NOT NULL default '',
	`thumbnailurl` varchar(250) NOT NULL default '',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_twitter` (
	`id` int(11) NOT NULL auto_increment,
	`id_workgroup` int(11) NOT NULL,
	`consumer_key` varchar(255) NOT NULL default '',
	`consumer_secret` varchar(255) NOT NULL default '',
	`account` varchar(50) NOT NULL default '',
	`last_check` DATETIME NOT NULL,
	`last_id` varchar(255) NOT NULL default '',
	`ignore_rt` tinyint(1) NOT NULL default '1',
	`published` tinyint(1) NOT NULL default '1',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_twitter_log` (
	`id` int(11) NOT NULL auto_increment,
	`id_twitter` int(11) NOT NULL default '0',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	`id_user` varchar(255) NOT NULL default '',
	`log` varchar(250) NOT NULL default '',
	`tweet_id` varchar(250) NOT NULL default '',
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__support_timesheet` (
	`id` int(11) NOT NULL auto_increment,
	`id_client` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`year` varchar(4) NOT NULL default '',
	`month` varchar(2) NOT NULL default '',
	`day` varchar(2) NOT NULL default '',
	`time` TIME NOT NULL,
	PRIMARY KEY  (`id`)
);

INSERT INTO `#__support_config` (`id`, `update_version`, `support_version`, `docspath`, `date_short`, `date_long`, `extensions`, `maxAllowed`, `public_attach`, `attachs_num`, `receive_mail`, `ac_active`, `ac_days`, `rating`, `kb_popinfo`, `kb_recent`, `notify_rate`, `less_rate`, `unregister`, `links`, `week_start`, `default_source`, `rss`, `minutes`, `users_login`, `use_uncategorized`, `client_change_status`, `support_change_status`, `register_user_change_status`, `support_only_show_assign`, `duedate_algoritm`, `duedate_holidays`,`duedate_vacations`, `duedate_firstday`, `duedate_schedule`, `duedate_firstday_minimum`, `duedate_hoursday`, `ticket_ignore_letter` )
VALUES (1, '20101215', '4.0.0', '', '%d/%m/%Y %H:%M', '%e %B %Y, %H:%M', 'pdf,tar,gz,bmp,gif,jpg,png,zip,txt', '8000000', 0, 1, 0, 0, 30, 'star', 1, 0, 0, 2, 1, 1, 1, '0', 0, 15, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 8, 'a e o u i and or for is at that');

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('readmail', 'Create Tickets from E-Mail\'s', 'The <b>Create Tickets from E-Mail\'s</b> add-on reads e-mail accounts and creates new tickets and/or add new messages to existing tickets.', 1, '1.0', 1, '', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('autoclose', 'Auto Close Utility', 'The <b>Auto Close Utility</b> automatically closes tickets that are open without any message for more than the number of days that are set.', 1, '1.0', 1, '', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('tasks', 'Tasks Reminder', 'The <b>Tasks Reminder</b> sends e-mail\'s to users informing them about tasks that are already overdue or near the due date.', 1, '1.0', 1, '', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('dbcleanup', 'Database Clean-Up', 'The <b>Database Clean-Up Add-On</b> automatically runs the Database Clean-Up Tool and removes the necessity of running the tool manually.', 1, '1.0', 1, '', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('escalation', 'SLA Escalation', 'The <b>SLA Escalation Add-On</b> allows you to set escalation of tickets depending on the configurations.', 1, '1.0', 1, 'config', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('fetchlog', 'Fetch Log Notification', 'The <b>Fetch Log Notification Add-On</b> allows you to set a daily cron to send the log of the e-mail fetching of the previous day.', 1, '1.0', 1, '', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('contracts', 'Contracts Notification', 'The <b>Contracts Notification Add-On</b> allows you to set a percentage that when achieved will send e-mail notifications to the support maintainer of the client and all client managers.', 1, '1.0', 1, 'config', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('tickets', 'Tickets Notifier', 'The <b>Tickets Notifier Add-On</b> sends e-mail\'s to users informing them about the open tickets they have assigned to them.', 1, '1.0', 1, '', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `publish`)
VALUES('mailqueue', 'Process Enqueued E-Mails\'s', 'The <b>Process Enqueued E-Mails\'s</b> add-on reads enqueue e-mail and process them.', 1, '1.0', 1, '', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `date`, `publish`)
VALUES('alert', 'TicketMonitor', 'The <b>Ticket Monitor</b> add-on communicates with the Windows application to provide warning messages to the support users.', 1, '1.0', 1, '', '2009-07-13', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `date`, `publish`)
VALUES('activities', 'Daily Activities', 'The <b>Daily Activities</b> add-on allows you to receive a daily email with all the activities that happen in the component for the previous day.', 1, '1.0', 1, '', '2011-07-05', 1);

INSERT INTO `#__support_addon` (`sname`, `lname`, `description`, `iscore`, `version`, `execution`, `menu`, `date`, `publish`)
VALUES('rating', 'Rating Request', 'The <b>Rating Request</b> add-on allows you to an e-mail to customers in which the tickets were closed in the previous day without rating.', 1, '1.0', 1, '', '2011-12-02', 1);

INSERT INTO `#__support_addon_contract` ( `percentage` , `notify` )
VALUES ('10', '1');

INSERT INTO `#__support_activity_rate` (`id`, `description`, `multiplier`, `isdefault`, `published`)
VALUES (1, '1.0', 1.0, 1, 1);

INSERT INTO `#__support_activity_rate` (`id`, `description`, `multiplier`, `isdefault`, `published`)
VALUES (2, '1.5', 1.5, 0, 1);

INSERT INTO `#__support_activity_rate` (`id`, `description`, `multiplier`, `isdefault`, `published`)
VALUES (3, '2.0', 2.0, 0, 1);

INSERT INTO `#__support_activity_type` (`id`, `description`, `isdefault`, `published`)
VALUES (1, 'Pre-quoted', 0, 1);

INSERT INTO `#__support_activity_type` (`id`, `description`, `isdefault`, `published`)
VALUES (2, 'Ad-hoc', 1, 1);

INSERT INTO `#__support_category` (`id`, `name`, `show`, `id_workgroup`, `parent`, `level`, `tickets`, `kb`)
VALUES (1, 'Software', 1, 1, 0, 1, 1, 1);

INSERT INTO `#__support_category` (`id`, `name`, `show`, `id_workgroup`, `parent`, `level`, `tickets`, `kb`)
VALUES (2, 'Hardware', 1, 1, 0, 1, 1, 1);

INSERT INTO `#__support_category` (`id`, `name`, `show`, `id_workgroup`, `parent`, `level`, `tickets`, `kb`)
VALUES (3, 'Training', 1, 1, 0, 1, 1, 1);

INSERT INTO `#__support_priority` (`id`, `description`, `show`, `timevalue`, `timeunit`, `isdefault`)
VALUES (2, 'Low', 1, 30, 'D', 1);

INSERT INTO `#__support_priority` (`id`, `description`, `show`, `timevalue`, `timeunit`, `isdefault`)
VALUES (3, 'Medium', 1, 14, 'D', 0);

INSERT INTO `#__support_priority` (`id`, `description`, `show`, `timevalue`, `timeunit`, `isdefault`)
VALUES (4, 'High', 1, 7, 'D', 0);

INSERT INTO `#__support_priority` (`id`, `description`, `show`, `timevalue`, `timeunit`, `isdefault`)
VALUES (5, 'Urgent', 1, 2, 'D', 0);

INSERT INTO `#__support_reply` (`id`, `subject`, `answer`)
VALUES (1, 'Please confirm completed', 'Your request has been completed, please confirm.\r\n');

INSERT INTO `#__support_reply` (`id`, `subject`, `answer`)
VALUES (2, 'Duplicate ticket', 'This issue has already been logged as a ticket #');

INSERT INTO `#__support_status` (`id`, `description`, `show`, `status_group`, `isdefault`, `user_access`, `allow_old_status_back`, `status_workflow`)
VALUES (1, 'Open', 1, 'O', 1, 1, 1, '1#2#3#4');

INSERT INTO `#__support_status` (`id`, `description`, `show`, `status_group`, `isdefault`, `user_access`, `allow_old_status_back`, `status_workflow`)
VALUES (2, 'Pending 3rd Party', 1, 'O', 0, 1, 1, '1#2#3#4');

INSERT INTO `#__support_status` (`id`, `description`, `show`, `status_group`, `isdefault`, `user_access`, `allow_old_status_back`, `status_workflow`)
VALUES (3, 'Pending Client', 1, 'O', 0, 1, 1, '1#2#3#4');

INSERT INTO `#__support_status` (`id`, `description`, `show`, `status_group`, `isdefault`, `user_access`, `allow_old_status_back`, `status_workflow`)
VALUES (4, 'Closed', 1, 'C', 0, 1, 1, '1#2#3#4');

INSERT INTO `#__support_workgroup` (`id`, `wkdesc`, `logo`, `wkabout`, `wkkb`, `wkemail`, `wkticket`, `show`, `wkmail_address`, `auto_assign`, `trouble`, `contract`, `ordering`, `use_activity`, `theme`, `wkannounces`, `wkglossary`)
VALUES (1, 'Support Services', NULL, 'Workgroup for provision of support services', 1, 0, 1, 1, '', 0, 0, 0, 1, 0, 'default', 1, 0);

INSERT INTO `#__support_export_profile`
VALUES (1, 'Default Export', 'Example of an export profile', 1, 0, '<tag:act_author_name{S}2{/S} />,<tag:act_author_name{S}1{/S} />,<tag:act_date{D}d/m/Y{/D} />,<tag:act_type_name />,<tag:client_name />,<tag:act_labour_time />,<tag:act_rate_desc />,<tag:ticket_num />,<tag:act_summary_msg />', 'A', 1, 0, 0, 0, 0, 0);

INSERT INTO `#__support_links` (`id`, `id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`)
VALUES (1, 0, 'Configuration', '', '../media/com_maqmahelpdesk/images/themes/default/48px/config.png', 'index.php?option=com_maqmahelpdesk&task=config', 'A', 1, 1);

INSERT INTO `#__support_links` (`id`, `id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`)
VALUES (2, 0, 'Support Staff', '', '../media/com_maqmahelpdesk/images/themes/default/48px/support_staff.png', 'index.php?option=com_maqmahelpdesk&task=staff', 'A', 1, 2);

INSERT INTO `#__support_links` (`id`, `id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`)
VALUES (3, 0, 'Workgroups', '', '../media/com_maqmahelpdesk/images/themes/default/48px/workgroup.png', 'index.php?option=com_maqmahelpdesk&task=workgroup', 'A', 1, 3);

INSERT INTO `#__support_links` (`id`, `id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`)
VALUES (4, 0, 'Clients', '', '../media/com_maqmahelpdesk/images/themes/default/48px/clients.png', 'index.php?option=com_maqmahelpdesk&task=client', 'A', 1, 4);

INSERT INTO `#__support_links` (`id`, `id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`)
VALUES (6, 0, 'Knowledge Base', '', '../media/com_maqmahelpdesk/images/themes/default/48px/kb.png', 'index.php?option=com_maqmahelpdesk&task=kb_search', 'A', 1, 6);

INSERT INTO `#__support_links` (`id`, `id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`)
VALUES (7, 0, 'Announcements', '', '../media/com_maqmahelpdesk/images/themes/default/48px/announcements.png', 'index.php?option=com_maqmahelpdesk&task=announce', 'A', 1, 7);

INSERT INTO `#__support_links` (`id`, `id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`)
VALUES (8, 0, 'Troubleshooter', '', '../media/com_maqmahelpdesk/images/themes/default/48px/troubleshooter.png', 'index.php?option=com_maqmahelpdesk&task=troubleshooter', 'A', 1, 8);

INSERT INTO `#__support_links` (`id`, `id_workgroup`, `name`, `description`, `image`, `link`, `section`, `published`, `ordering`)
VALUES (9, 0, 'Mail Fetching', '', '../media/com_maqmahelpdesk/images/themes/default/48px/inbox.png', 'index.php?option=com_maqmahelpdesk&task=mail', 'A', 1, 9);

INSERT INTO `#__support_links` (`id` ,`id_workgroup` ,`name` ,`description` ,`image` ,`link` ,`section` ,`published` ,`ordering`)
VALUES (NULL , '0', 'Users', '', '../media/com_maqmahelpdesk/images/themes/default/48px/users.png', 'index.php?option=com_maqmahelpdesk&task=users', 'A', 1, 10);

ALTER TABLE `#__support_status` ADD INDEX `idx_status_group` (  `status_group` );
ALTER TABLE `#__support_status` ADD INDEX `idx_status` (  `id` ,  `status_group` );
ALTER TABLE `#__support_ticket` ADD INDEX `idx_last_update` (  `last_update` );
ALTER TABLE `#__support_ticket_resp` ADD INDEX `idx_date` (  `date` );
ALTER TABLE `#__support_ticket` ADD INDEX `idx_update_status` (  `id_status` ,  `last_update` );
ALTER TABLE `#__support_ticket_resp` ADD INDEX `idx_message` (  `message` ( 255 ) );

CREATE TABLE IF NOT EXISTS `#__support_download_field_value` (
	`id_field` int(11) NOT NULL default '0',
	`id_download` int(11) NOT NULL default '0',
	`value` text default NULL,
	PRIMARY KEY  (`id_field`,`id_download`),
	INDEX `idx_id_download` (`id_download`)
);

CREATE TABLE IF NOT EXISTS `#__support_client_field_value` (
	`id_field` int(11) NOT NULL default '0',
	`id_client` int(11) NOT NULL default '0',
	`value` text default NULL,
	PRIMARY KEY  (`id_field`,`id_client`),
	INDEX `idx_id_client` (`id_client`)
);