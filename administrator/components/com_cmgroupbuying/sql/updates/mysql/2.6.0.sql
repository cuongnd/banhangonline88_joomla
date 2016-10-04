ALTER TABLE `#__cmgroupbuying_configuration` ADD `payment_method_type` ENUM( 'hosted', 'direct' ) NOT NULL DEFAULT 'hosted',
ADD `direct_payment_method` VARCHAR( 20 ) NOT NULL;
ALTER TABLE `#__cmgroupbuying_configuration` ADD `payment_method_pretext` TEXT NOT NULL ,
ADD `payment_method_posttext` TEXT NOT NULL;