CREATE TABLE IF NOT EXISTS `#__rsform_config` (
  `SettingName` varchar(64) NOT NULL default '',
  `SettingValue` text NOT NULL,
  PRIMARY KEY (`SettingName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;