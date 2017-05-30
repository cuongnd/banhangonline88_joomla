CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `deal_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `token` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DELETE FROM #__cmgroupbuying_mail_templates WHERE id = 8 AND name = "late_pay_partner";

ALTER TABLE  `#__cmgroupbuying_coupons` ADD  `item_id` INT NOT NULL AFTER  `order_id`;