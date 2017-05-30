CREATE TABLE IF NOT EXISTS "#__jfbconnect_user_map" (
	"id" SERIAL,
	"j_user_id" INT NOT NULL,
	"provider_user_id" VARCHAR(40) NOT NULL,
	"authorized" BOOLEAN NOT NULL,
	"params" TEXT,
	"provider" VARCHAR(20) NOT NULL,
	"access_token" TEXT NOT NULL,
  "created_at" TIMESTAMP NOT NULL,
	"updated_at" TIMESTAMP NOT NULL,
	PRIMARY KEY ("id")
);

# 6.0 ;
CREATE TABLE IF NOT EXISTS "#__jfbconnect_channel" (
  "id" SERIAL,
  "provider" varchar(20) NOT NULL,
  "type" varchar(20) DEFAULT NULL,
  "title" varchar(40) NOT NULL DEFAULT '',
  "description" TEXT,
  "attribs" TEXT,
  "published" BOOLEAN DEFAULT NULL,
  "created" TIMESTAMP DEFAULT NULL,
  "modified" TIMESTAMP DEFAULT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__opengraph_action" (
  "id" SERIAL,
  "plugin" varchar(20) DEFAULT NULL,
  "system_name" varchar(20) DEFAULT NULL,
  "display_name" varchar(50) DEFAULT NULL,
  "action" varchar(20) DEFAULT NULL,
  "fb_built_in" BOOLEAN DEFAULT NULL,
  "can_disable" BOOLEAN DEFAULT NULL,
  "params" text,
  "published" BOOLEAN DEFAULT NULL,
  "created" TIMESTAMP DEFAULT NULL,
  "modified" TIMESTAMP DEFAULT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__opengraph_object" (
  "id" SERIAL,
  "plugin" varchar(15) DEFAULT NULL,
  "system_name" varchar(20) DEFAULT NULL,
  "display_name" varchar(50) DEFAULT NULL,
  "type" varchar(20) DEFAULT NULL,
  "fb_built_in" BOOLEAN DEFAULT NULL,
  "published" BOOLEAN DEFAULT NULL,
  "params" text,
  "created" TIMESTAMP DEFAULT NULL,
  "modified" TIMESTAMP DEFAULT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__opengraph_activity" (
  "id" SERIAL,
  "action_id" int DEFAULT NULL,
  "object_id" int DEFAULT NULL,
  "user_id" int DEFAULT NULL,
  "url" text,
  "status" smallint DEFAULT NULL,
  "unique_key" varchar(32) DEFAULT NULL,
  "response" text,
  "created" TIMESTAMP DEFAULT NULL,
  "modified" TIMESTAMP DEFAULT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__opengraph_action_object" (
  "id" SERIAL,
  "object_id" int DEFAULT NULL,
  "action_id" int DEFAULT NULL,
  PRIMARY KEY ("id")
);

# 4.2 Not adding the unique to "setting" because we add it later with the install scripts, which can cause a duplicate key
# Not a big deal having 2, but cleaner to only have one.
# In a future version, add it by default ;
CREATE TABLE IF NOT EXISTS "#__jfbconnect_config" (
	"id" SERIAL,
	"setting" VARCHAR(50) UNIQUE NOT NULL,
	"value" TEXT,
	"created_at" TIMESTAMP NOT NULL,
	"updated_at" TIMESTAMP NOT NULL,
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__jfbconnect_request" (
	"id" SERIAL,
	"published" SMALLINT NOT NULL,
	"title" VARCHAR(50) NOT NULL,
	"message" VARCHAR(250) NOT NULL,
	"destination_url" VARCHAR(200) NOT NULL,
	"thanks_url" VARCHAR(200) NOT NULL,
	"breakout_canvas" SMALLINT NOT NULL,
    "created" TIMESTAMP NOT NULL,
	"modified" TIMESTAMP NOT NULL,
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__jfbconnect_notification" (
	"id" SERIAL,
	"fb_request_id" BIGINT NOT NULL,
	"fb_user_to" BIGINT NOT NULL,
	"fb_user_from" BIGINT NOT NULL,
	"jfbc_request_id" INT NOT NULL,
	"status" SMALLINT NOT NULL,
	"created" TIMESTAMP NOT NULL,
	"modified" TIMESTAMP NOT NULL,
	PRIMARY KEY ("id")
);