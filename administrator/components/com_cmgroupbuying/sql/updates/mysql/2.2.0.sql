ALTER TABLE `#__cmgroupbuying_deals` ADD `commission_rate` DECIMAL( 10, 2 ) NOT NULL AFTER `shipping_cost`;

CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_management` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `partner_welcome` text NOT NULL,
  `partner_footer` text NOT NULL,
  `partner_view_deal_list` tinyint(1) NOT NULL,
  `partner_submit_new_deal` tinyint(1) NOT NULL,
  `partner_check_coupon_status` tinyint(1) NOT NULL,
  `partner_change_coupon_status` tinyint(1) NOT NULL,
  `partner_view_coupon_list` tinyint(1) NOT NULL,
  `partner_view_buyer_info` tinyint(1) NOT NULL,
  `partner_view_commission_report` tinyint(1) NOT NULL,
  `staff_access_level` int(11) NOT NULL,
  `staff_welcome` text NOT NULL,
  `staff_footer` text NOT NULL,
  `staff_change_order_paid` tinyint(1) NOT NULL,
  `staff_change_order_unpaid` tinyint(1) NOT NULL,
  `staff_change_user_info` tinyint(1) NOT NULL,
  `staff_view_coupon` tinyint(1) NOT NULL,
  `staff_send_coupon` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;