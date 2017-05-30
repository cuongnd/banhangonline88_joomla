ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `partner_folder` VARCHAR( 100 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `geotargeting` VARCHAR( 100 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `maxmind_path` TEXT NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `ipinfodb_key` TEXT NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `geotargeting_cookie_lifetime` INT NOT NULL DEFAULT  '24';
ALTER TABLE  `#__cmgroupbuying_locations` ADD  `geotargeting_name` VARCHAR( 255 ) NOT NULL AFTER  `description`;
ALTER TABLE  `#__cmgroupbuying_orders` ADD  `transaction_id` VARCHAR( 255 ) NOT NULL;
INSERT INTO `#__cmgroupbuying_mail_templates` (`id`, `name`, `subject`, `body`) VALUES (NULL, 'pending_admin', 'A new deal has just submitted', '<p>Dear Administrator,</p>
<p>A new deal - {deal_name} - has just been submitted by {partner_name}.</p>
<p style="text-align: left;"><strong>(This is a sample template for demonstration)</strong></p>');
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `mail_pending_admin` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `mail_approve_partner`;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `admin_email` VARCHAR( 100 ) NOT NULL;