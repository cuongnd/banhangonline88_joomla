CREATE TABLE `#__banners` (
  `id` INTEGER NOT NULL auto_increment,
  `cid` INTEGER NOT NULL DEFAULT '0',
  `type` INTEGER NOT NULL DEFAULT '0',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
  `imptotal` INTEGER NOT NULL DEFAULT '0',
  `impmade` INTEGER NOT NULL DEFAULT '0',
  `clicks` INTEGER NOT NULL DEFAULT '0',
  `clickurl` VARCHAR(200) NOT NULL DEFAULT '',
  `state` TINYINT(3) NOT NULL DEFAULT '0',
  `catid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `description` TEXT NOT NULL,
  `custombannercode` VARCHAR(2048) NOT NULL,
  `sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `metakey` TEXT NOT NULL,
  `params` TEXT NOT NULL,
  `own_prefix` TINYINT(1) NOT NULL DEFAULT '0',
  `metakey_prefix` VARCHAR(255) NOT NULL DEFAULT '',
  `purchase_type` TINYINT NOT NULL DEFAULT '-1',
  `track_clicks` TINYINT NOT NULL DEFAULT '-1',
  `track_impressions` TINYINT NOT NULL DEFAULT '-1',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reset` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language` char(7) NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  INDEX `idx_state` (`state`),
  INDEX `idx_own_prefix` (`own_prefix`),
  INDEX `idx_metakey_prefix` (`metakey_prefix`),
  INDEX `idx_banner_catid`(`catid`),
  INDEX `idx_language` (`language`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `#__banner_clients` (
  `id` INTEGER NOT NULL auto_increment,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `contact` VARCHAR(255) NOT NULL DEFAULT '',
  `email` VARCHAR(255) NOT NULL DEFAULT '',
  `extrainfo` TEXT NOT NULL,
  `state` TINYINT(3) NOT NULL DEFAULT '0',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `metakey` TEXT NOT NULL,
  `own_prefix` TINYINT NOT NULL DEFAULT '0',
  `metakey_prefix` VARCHAR(255) NOT NULL default '',
  `purchase_type` TINYINT NOT NULL DEFAULT '-1',
  `track_clicks` TINYINT NOT NULL DEFAULT '-1',
  `track_impressions` TINYINT NOT NULL DEFAULT '-1',
  PRIMARY KEY  (`id`),
  INDEX `idx_own_prefix` (`own_prefix`),
  INDEX `idx_metakey_prefix` (`metakey_prefix`)
)  DEFAULT CHARSET=utf8;

CREATE TABLE  `#__banner_tracks` (
  `track_date` DATETIME NOT NULL,
  `track_type` INTEGER UNSIGNED NOT NULL,
  `banner_id` INTEGER UNSIGNED NOT NULL,
  `count` INTEGER UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`track_date`, `track_type`, `banner_id`),
  INDEX `idx_track_date` (`track_date`),
  INDEX `idx_track_type` (`track_type`),
  INDEX `idx_banner_id` (`banner_id`)
)  DEFAULT CHARSET=utf8;

