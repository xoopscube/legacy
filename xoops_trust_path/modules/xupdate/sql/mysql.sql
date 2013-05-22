CREATE TABLE `{prefix}_{dirname}_store` (
	`sid` int(11) unsigned  NOT NULL  auto_increment,
	`name` varchar(255) NOT NULL default '',
	`contents` varchar(255) NOT NULL default '',
	`addon_url` varchar(255) NOT NULL default '',
	`setting_type` int(11) unsigned  NOT NULL default 0,
	`reg_unixtime` int(11) unsigned NOT NULL default 0,
PRIMARY KEY  (`sid`)) ENGINE=MyISAM;

CREATE TABLE `{prefix}_{dirname}_modulestore` (
	`id` int(11) unsigned NOT NULL  auto_increment,
	`sid` int(11) unsigned NOT NULL default 0,
	`dirname` varchar(255) NOT NULL default '',
	`trust_dirname` varchar(255) default '',
	`version` smallint(5) unsigned default '100',
	`license` varchar(255) NOT NULL default '',
	`required` varchar(255) NOT NULL default '',
	`last_update` int(10) unsigned default '0',
	`target_key` varchar(255) NOT NULL default '',
	`target_type` varchar(255) NOT NULL default '',
	`replicatable` tinyint(1) unsigned NOT NULL default '0',
	`description` varchar(255) NOT NULL default '',
	`unzipdirlevel` tinyint(1) unsigned NOT NULL default '0',
	`addon_url` varchar(255) NOT NULL default '',
	`detail_url` varchar(255) NOT NULL default '',
	`options` text,
	`isactive` int(11) NOT NULL DEFAULT '-1',
	`hasupdate` tinyint(1) NOT NULL DEFAULT '0',
	`contents` varchar(255) NOT NULL default '',
PRIMARY KEY  (`id`),
KEY sid (sid),
KEY dirname (dirname)
 ) ENGINE=MyISAM;

