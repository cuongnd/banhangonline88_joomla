ALTER TABLE `#__cmgroupbuying_configuration` ADD `mail_cash_admin` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `mail_pending_coupon_admin`;

INSERT INTO `#__cmgroupbuying_mail_templates` (`id`, `name`, `subject`, `body`) VALUES (NULL, 'cash_admin', 'A new cash order has just been made', '<p>Dear Administrator,</p>
<p>A new cash order has just been made by. Order ID: {order_id}.</p>
<p><strong>(This is a sample template for demonstration)</strong></p>');