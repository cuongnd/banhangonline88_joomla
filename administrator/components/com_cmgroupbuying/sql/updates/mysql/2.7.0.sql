ALTER TABLE `#__cmgroupbuying_configuration` ADD `disqus_comment` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `facebook_admin_user_id`;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `disqus_shortname` VARCHAR( 100 ) NOT NULL AFTER `disqus_comment`;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `disqus_multilanguage` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `disqus_shortname`;