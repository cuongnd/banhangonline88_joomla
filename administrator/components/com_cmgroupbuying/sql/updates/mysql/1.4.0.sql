ALTER TABLE  `#__cmgroupbuying_deals` ADD  `approved` TINYINT( 1 ) NOT NULL AFTER  `featured`;
ALTER TABLE  `#__cmgroupbuying_configuration` CHANGE  `mail_cash_buyer`  `mail_cash_buyer` TINYINT( 1 ) NOT NULL DEFAULT  '1';
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `mail_approve_partner` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `mail_cash_buyer`;
INSERT INTO `#__cmgroupbuying_mail_templates` (`id`, `name`, `subject`, `body`) VALUES (NULL, 'approve_partner', 'Your pending deal is approved', '<p>Dear {partner_name},</p>
<p>You deal - {deal_name} - has just been approved by Administrator.</p>
<p style="text-align: left;"><strong>(This is a sample template for demonstration)</strong></p>');
ALTER TABLE  `#__cmgroupbuying_partners` ADD  `location1` TEXT NOT NULL AFTER  `map_zoom_level`;
ALTER TABLE  `#__cmgroupbuying_partners` ADD  `location2` TEXT NOT NULL AFTER  `location1`;
ALTER TABLE  `#__cmgroupbuying_partners` ADD  `location3` TEXT NOT NULL AFTER  `location2`;
ALTER TABLE  `#__cmgroupbuying_partners` ADD  `location4` TEXT NOT NULL AFTER  `location3`;
ALTER TABLE  `#__cmgroupbuying_partners` ADD  `location5` TEXT NOT NULL AFTER  `location4`;
ALTER TABLE  `#__cmgroupbuying_configuration` CHANGE  `cookie_lifetime`  `point_cookie_lifetime` INT( 11 ) NOT NULL DEFAULT  '24';
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `subscription_cookie_lifetime` INT NOT NULL DEFAULT  '24' AFTER  `subscription_redirect`;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `jquery_loading` TEXT NOT NULL;