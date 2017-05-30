ALTER TABLE  `#__cmgroupbuying_partners` ADD  `alias` VARCHAR( 255 ) NOT NULL AFTER  `name`;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `sh404sef_deal_alias` VARCHAR( 100 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `sh404sef_category_alias` VARCHAR( 100 ) NOT NULL;
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `sh404sef_partner_alias` VARCHAR( 100 ) NOT NULL;
INSERT INTO  `#__cmgroupbuying_mail_templates` (
`id` ,
`name` ,
`subject` ,
`body`
)
VALUES (
NULL ,  'cash_buyer',  'You''ve ordered for {deal_name}',  '<p>Dear {buyer_last_name},</p>
<p>You ordered {deal_name} on our website with {order_value}. Your order id is {order_id}.</p>
<p>Please send your cash to our bank account before {order_cash_final_date}: 14423353 ABC bank.</p>
<p>Thank you very much! </p>
<p style="text-align: left;"><strong>(This is a sample template for demonstration)</strong></p>'
);
ALTER TABLE  `#__cmgroupbuying_configuration` ADD  `mail_cash_buyer` TINYINT NOT NULL DEFAULT  '1' AFTER  `mail_void_partner`
