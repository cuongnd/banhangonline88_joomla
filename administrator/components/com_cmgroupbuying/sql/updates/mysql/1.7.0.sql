ALTER TABLE  `#__cmgroupbuying_deal_option` ADD  `advance_price` DECIMAL( 10, 2 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_deals` ADD  `advance_payment` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `shipping_cost`;
ALTER TABLE  `#__cmgroupbuying_orders` CHANGE  `payment_id`  `payment_id` VARCHAR( 100 ) NOT NULL;
DELETE FROM `#__menu` WHERE link = 'index.php?option=com_cmgroupbuying&view=payments';