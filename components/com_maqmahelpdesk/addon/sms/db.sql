
INSERT INTO `#__support_addon` ( `id` , `sname` , `lname` , `description` , `iscore` , `version` , `execution` , `menu` , `date` , `publish` ) 
VALUES ( NULL , 'sms', 'SMS Notifications', 'The <b>SMS Notifications</b> add-on allows you to send notifications to mobile phones by SMS messages.', '1', '1.0', '2', 'config', '2010-04-14', '1' );

CREATE TABLE `#__support_sms_log` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_user` INT( 11 ) NOT NULL ,
	`id_user_message` INT( 11 ) NOT NULL ,
	`id_ticket` INT( 11 ) NOT NULL ,
	`phone_number` VARCHAR( 25 ) NOT NULL ,
	`date_message` DATETIME NOT NULL ,
	`action` TINYINT( 1 ) NOT NULL
);

CREATE TABLE `#__support_sms_config` (
	`id` TINYINT( 1 ) NOT NULL ,
	`gateway` VARCHAR( 15 ) NOT NULL ,
	`assigned` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`creation` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`customer_activ` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`support_activ` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`username` VARCHAR( 200 ) NOT NULL ,
	`password` VARCHAR( 200 ) NOT NULL ,
	`host` VARCHAR( 200 ) NOT NULL ,
	`port` INT( 3 ) NOT NULL ,
	`from` VARCHAR( 200 ) NOT NULL ,
	`fromname` VARCHAR( 200 ) NOT NULL ,
	PRIMARY KEY ( `id` )
);

INSERT INTO `#__support_sms_config` (`id`, `gateway`, `assigned`, `creation`, `customer_activ`, `support_activ`, `username`, `password`, `host`, `port`, `from`, `fromname`) VALUES
(1, '', 1, 1, 1, 1, '', '', '', 0, '', '');
