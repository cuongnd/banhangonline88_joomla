-- Basic release schema 1.0

CREATE TABLE IF NOT EXISTS `#__jchat` (
	 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	 `from` VARCHAR( 255 ) NOT NULL,
	 `to` VARCHAR( 255 ) NOT NULL,
	 `fromuser` int(11) DEFAULT NULL,
	 `touser` int(11) DEFAULT NULL,
	 `message` text NOT NULL,
	 `sent` int(11) NOT NULL,
	 `read` tinyint(4) NOT NULL,
	 `type` varchar(255) NOT NULL DEFAULT 'message',
	 `status` tinyint(4) NOT NULL DEFAULT 0,
	 `clientdeleted` tinyint(4) NOT NULL DEFAULT 0,
	 `actualfrom` VARCHAR( 255 ) NOT NULL,
	 `actualto` VARCHAR( 255 ) NOT NULL,
	 `sentroomid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `to` (`to`),
  INDEX `from` (`from`),
  INDEX `fromuser` (`fromuser`),
  INDEX `touser` (`touser`),
  INDEX `actualfrom` (`actualfrom`),
  INDEX `actualto` (`actualto`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_public_sessionrelations` (
	`ownerid` VARCHAR( 255 ) NOT NULL, 
	`contactid` VARCHAR( 255 ) NOT NULL,
  PRIMARY KEY (`ownerid`, `contactid`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_public_readmessages` (
	`messageid` int(11) NOT NULL, 
	`sessionid` varchar(255) NOT NULL,
  PRIMARY KEY (`messageid`, `sessionid`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_sessionstatus` (
	 `sessionid` varchar(255) NOT NULL,
	 `status` varchar(11) DEFAULT NULL,
	 `override_name` varchar(255) DEFAULT NULL,
	 `email` varchar(255) DEFAULT NULL,
	 `description` text DEFAULT NULL,
	 `skypeid` varchar(255) DEFAULT NULL,
	 `roomid` int(11) DEFAULT NULL,
	 `typing` TINYINT NULL,
	 `typing_to` VARCHAR( 100 ) NULL,
	PRIMARY KEY (`sessionid`),
	INDEX `overridenameidx` (`override_name`),
	INDEX `roomidx` (`roomid`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_userstatus` (
	`userid` INT NOT NULL ,
	`skypeid` VARCHAR( 255 ) NOT NULL ,
	`roomid` int(11) DEFAULT NULL,
	PRIMARY KEY ( `userid` ),
	INDEX `roomidx` (`roomid`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `#__jchat_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  `name` varchar(255) NOT NULL, 
  `description` text NULL , 
  `checked_out` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL default '0',
  `access` int(11) NOT NULL default '1',
  PRIMARY KEY (`id`),
  INDEX `idxname` (`name`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_banned_users` (
	`banning` VARCHAR( 255 ) NOT NULL, 
	`banned` VARCHAR( 255 ) NOT NULL,
  PRIMARY KEY (`banning`, `banned`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_webrtc` (
	`peer1` VARCHAR( 255 ) NOT NULL,
	`peer2` VARCHAR( 255 ) NOT NULL,
	`sdp` TEXT NULL, 
	`icecandidate` TEXT NULL,
	`videocam` tinyint(4) NOT NULL default '1',
  PRIMARY KEY (`peer1`, `peer2`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_webrtc_conference` (
	`peer1` VARCHAR( 255 ) NOT NULL,
	`peer2` VARCHAR( 255 ) NOT NULL,
	`sdp` TEXT NULL, 
	`icecandidate` TEXT NULL,
	`videocam` tinyint(4) NOT NULL default '1',
	`other_peers` TEXT NULL, 
  PRIMARY KEY (`peer1`, `peer2`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_lamessages` (
  `id`       int(11) NOT NULL AUTO_INCREMENT, 
  `name`     varchar(255), 
  `email`    varchar(255), 
  `message`  varchar(255), 
  `sentdate` date, 
  `userid`   int(11), 
  `worked`   tinyint, 
  `responses` text NULL,
  `checked_out` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `closed_ticket` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `customer_name` (`name`),
  INDEX `customer_email` (`email`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_messaging_deletedmessages` (
	`messageid` int(11) NOT NULL, 
	`userid` int(11) NOT NULL, 
  PRIMARY KEY (`messageid`, `userid`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__jchat_login` (
	 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	 `j_uid` int(11) unsigned NOT NULL,
	 `fb_uid` VARCHAR( 255 ) NOT NULL,
	 `email` VARCHAR( 255 ) NOT NULL,
	 `picture` VARCHAR( 255 ) NOT NULL,
	 `first_name` VARCHAR( 255 ) NULL,
	 `last_name` VARCHAR( 255 ) NULL,
	 `name` VARCHAR( 255 ) NULL,
  PRIMARY KEY (`id`),
  INDEX `first_name` (`first_name`),
  INDEX `last_name` (`last_name`),
  INDEX `name` (`name`),
  INDEX `fb_uid` (`fb_uid`),
  INDEX `email` (`email`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

-- Updates on version 2.7
CREATE TABLE IF NOT EXISTS `#__jchat_emoticons` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`linkurl` varchar(255) NOT NULL,
	`keycode` varchar(255) NULL,
	`ordering` int(11) NOT NULL default '0',
  	`published` tinyint(1) NOT NULL default '0',
  	PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

-- Updates on version 2.9
CREATE TABLE IF NOT EXISTS `#__jchat_recordings` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`title` varchar(255) NOT NULL,
	`size` varchar(255) NOT NULL,
	`timerecord` datetime NOT NULL,
	`peer1` varchar(255) NOT NULL,
	`peer2` varchar(255) NOT NULL,
  	PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET `utf8`;

INSERT IGNORE INTO `#__jchat_emoticons` (`id`, `linkurl`, `keycode`, `ordering`, `published`) VALUES
(1, 'components/com_jchat/emoticons/smile.png', ':)', 1, 1),
(2, 'components/com_jchat/emoticons/laugh.png', ':A', 2, 1),
(3, 'components/com_jchat/emoticons/muhaha.png', ':D', 3, 1),
(4, 'components/com_jchat/emoticons/angry_002.png', ';A', 4, 1),
(5, 'components/com_jchat/emoticons/surprised.png', ':O', 5, 1),
(6, 'components/com_jchat/emoticons/sad.png', ':(', 6, 1),
(7, 'components/com_jchat/emoticons/annoyed.png', ':-X', 7, 1),
(8, 'components/com_jchat/emoticons/cool.png', ':B', 8, 1),
(9, 'components/com_jchat/emoticons/tired.png', '(:', 9, 1),
(10, 'components/com_jchat/emoticons/wave.png', ':|', 10, 1),
(11, 'components/com_jchat/emoticons/sick.png', ':-\\', 11, 1),
(12, 'components/com_jchat/emoticons/sleeping.png', ':Z', 12, 1),
(13, 'components/com_jchat/emoticons/startled.png', ':-I', 13, 1),
(14, 'components/com_jchat/emoticons/tears.png', ':-(', 14, 1),
(15, 'components/com_jchat/emoticons/thinking.png', ':-M', 15, 1),
(16, 'components/com_jchat/emoticons/tongue_002.png', ':--', 16, 1),
(17, 'components/com_jchat/emoticons/unsure.png', '-@-', 17, 1),
(18, 'components/com_jchat/emoticons/unsure_002.png', '-@+', 18, 1),
(19, 'components/com_jchat/emoticons/w00t.png', ':F', 19, 1),
(20, 'components/com_jchat/emoticons/grin.png', ':U', 20, 1),
(21, 'components/com_jchat/emoticons/eyeroll.png', ':M', 21, 1),
(22, 'components/com_jchat/emoticons/haha.png', ':S', 22, 1),
(23, 'components/com_jchat/emoticons/wink.png', ':W', 23, 1),
(24, 'components/com_jchat/emoticons/angry.png', ':X', 24, 1),
(25, 'components/com_jchat/emoticons/question.png', ':?', 25, 1),
(26, 'components/com_jchat/emoticons/nerd.png', ':N', 26, 1),
(27, 'components/com_jchat/emoticons/ninja.png', ':J', 27, 1),
(28, 'components/com_jchat/emoticons/not_talking.png', '-sh', 28, 1),
(29, 'components/com_jchat/emoticons/party.png', '-pt', 29, 1),
(30, 'components/com_jchat/emoticons/scenic.png', '-blah', 30, 1),
(31, 'components/com_jchat/emoticons/fever.png', ';N', 31, 1),
(32, 'components/com_jchat/emoticons/girl_kiss.png', ';O', 32, 1),
(33, 'components/com_jchat/emoticons/gril_tongue.png', ';P', 33, 1),
(34, 'components/com_jchat/emoticons/gym.png', ';R', 34, 1),
(35, 'components/com_jchat/emoticons/like_food.png', ';W', 35, 1),
(36, 'components/com_jchat/emoticons/evil_grin.png', ';H', 36, 1),
(37, 'components/com_jchat/emoticons/joyful.png', ';U', 37, 1),
(38, 'components/com_jchat/emoticons/kiss.png', ';V', 38, 1),
(39, 'components/com_jchat/emoticons/evilsmirk.png', ';L', 39, 1),
(40, 'components/com_jchat/emoticons/like.png', '+1', 40, 1),
(41, 'components/com_jchat/emoticons/tongue.png', ':P', 41, 1),
(42, 'components/com_jchat/emoticons/big_eyed.png', ';B', 42, 1),
(43, 'components/com_jchat/emoticons/blush.png', ';C', 43, 1),
(44, 'components/com_jchat/emoticons/heart_beat.png', ';T', 44, 1),
(45, 'components/com_jchat/emoticons/broken_heart.png', ';E', 45, 1),
(46, 'components/com_jchat/emoticons/coffee.png', ';F', 46, 1),
(47, 'components/com_jchat/emoticons/cry.png', ';G', 47, 1),
(48, 'components/com_jchat/emoticons/bring_it_on.png', ';D', 48, 1),
(49, 'components/com_jchat/emoticons/mrenges.png', '-birth', 49, 1),
(50, 'components/com_jchat/emoticons/money.png', '-money', 50, 1);

-- Exceptions queries in reverse versioning order 10.0 -> 1.0
-- Version 2.10
ALTER TABLE  `#__jchat` CHANGE  `message`  `message` MEDIUMTEXT;

-- Version 2.8
ALTER TABLE  `#__jchat_sessionstatus` ADD `banstatus` TINYINT NOT NULL DEFAULT  '0';

-- Version 2.4
ALTER TABLE  `#__jchat_userstatus` ADD  `status` VARCHAR( 11 ) NULL;

-- Version 2.2
ALTER TABLE  `#__jchat_sessionstatus` ADD `geoip` VARCHAR( 255 ) NULL;

-- Version 2.1
ALTER TABLE  `#__jchat_userstatus` ADD `banstatus` TINYINT NOT NULL DEFAULT  '0';

-- Version 2.0
ALTER TABLE  `#__jchat` ADD `ipaddress` VARCHAR( 255 ) NULL;