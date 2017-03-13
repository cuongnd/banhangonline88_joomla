# Increase column width as Amazon has larger provider IDs ;
ALTER TABLE "#__jfbconnect_user_map" ALTER COLUMN "provider_user_id" TYPE VARCHAR(100);