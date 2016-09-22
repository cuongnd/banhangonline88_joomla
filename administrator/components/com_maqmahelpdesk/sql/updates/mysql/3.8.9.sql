ALTER TABLE `#__support_log` ADD INDEX `idx_log_ticket` (`id_ticket`);

ALTER TABLE `#__support_log` ADD INDEX `idx_log_user` (`id_user`);

CREATE TABLE IF NOT EXISTS `#__support_timesheet` (
	`id` int(11) NOT NULL auto_increment,
	`id_client` int(11) NOT NULL default '0',
	`id_user` int(11) NOT NULL default '0',
	`year` varchar(4) NOT NULL default '',
	`month` varchar(2) NOT NULL default '',
	`day` varchar(2) NOT NULL default '',
	`time` double(14,2) NOT NULL default '0.00',
	PRIMARY KEY  (`id`)
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