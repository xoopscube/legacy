CREATE TABLE "{prefix}_{dirname}_store" (
	"sid" serial,
	"name" varchar(255) NOT NULL default '',
	"contents" varchar(255) NOT NULL default '',
	"addon_url" varchar(255) NOT NULL default '',
	"setting_type" int NOT NULL default 0,
	"reg_unixtime" int NOT NULL default 0,
PRIMARY KEY  ("sid"));

CREATE TABLE "{prefix}_{dirname}_modulestore" (
	"id" serial,
	"sid" int NOT NULL default 0,
	"dirname" varchar(255) NOT NULL default '',
	"trust_dirname" varchar(255) default '',
	"version" smallint default '100',
	"license" varchar(255) NOT NULL default '',
	"required" varchar(255) NOT NULL default '',
	"last_update" int default '0',
	"target_key" varchar(255) NOT NULL default '',
	"target_type" varchar(255) NOT NULL default '',
	"replicatable" smallint NOT NULL default '0',
	"description" varchar(255) NOT NULL default '',
	"unzipdirlevel" smallint NOT NULL default '0',
	"addon_url" varchar(255) NOT NULL default '',
	"detail_url" varchar(255) NOT NULL default '',
	"options" text,
	"isactive" int NOT NULL DEFAULT '-1',
	"hasupdate" smallint NOT NULL DEFAULT '0',
	"contents" varchar(255) NOT NULL default '',
PRIMARY KEY  ("id")
 );
CREATE INDEX {prefix}_{dirname}_modulestore_sid_idx ON {prefix}_{dirname}_modulestore (sid);
CREATE INDEX {prefix}_{dirname}_modulestore_dirname_idx ON {prefix}_{dirname}_modulestore (dirname);

