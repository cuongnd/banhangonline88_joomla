# Increase column width as Amazon has larger provider IDs ;
ALTER TABLE `#__jfbconnect_user_map` MODIFY `provider_user_id` VARCHAR(100);