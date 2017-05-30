# Move to generic provider columns instead of Facebook specific ;
ALTER TABLE `#__jfbconnect_user_map` ADD COLUMN `provider` VARCHAR(20);
ALTER TABLE `#__jfbconnect_user_map` CHANGE `fb_user_id` `provider_user_id` VARCHAR(40);
UPDATE `#__jfbconnect_user_map`
SET `provider`='facebook', `provider_user_id`=`provider_user_id`
WHERE `provider` IS NULL;

# Update access_token size as we are now supporing more authentication mechanisms. Yay! ;
ALTER TABLE `#__jfbconnect_user_map` MODIFY `access_token` TEXT;