CREATE TABLE IF NOT EXISTS "#__jfbconnect_channel" (
  "id" SERIAL,
  "provider" varchar(20) NOT NULL,
  "type" varchar(20) DEFAULT NULL,
  "title" varchar(40) NOT NULL DEFAULT '',
  "description" TEXT,
  "attribs" TEXT,
  "published" SMALLINT DEFAULT NULL,
  "created" TIMESTAMP DEFAULT NULL,
  "modified" TIMESTAMP DEFAULT NULL,
  PRIMARY KEY ("id")
);