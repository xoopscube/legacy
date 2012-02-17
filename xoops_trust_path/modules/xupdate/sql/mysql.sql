CREATE TABLE `{prefix}_{dirname}_store` (
	`sid` int(11) unsigned NOT NULL  auto_increment,
	`name` varchar(255) NOT NULL default '',
	`addon_url` varchar(255) NOT NULL default '',
	`reg_unixtime` int(11) unsigned NOT NULL default 0,
PRIMARY KEY  (`sid`)) ENGINE=MyISAM;


CREATE TABLE `{prefix}_{dirname}_modulestore` (
	`id` int(11) unsigned NOT NULL  auto_increment,
	`sid` int(11) unsigned NOT NULL default 0,
	`dirname` varchar(25) NOT NULL default '',
	`trust_dirname` varchar(25) default '',
	`version` smallint(5) unsigned default '100',
	`last_update` int(10) unsigned default '0',
	`target_key` varchar(25) NOT NULL default '',
	`target_type` varchar(255) NOT NULL default '',
	`replicatable` tinyint(1) unsigned NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY sid (sid),
KEY dirname (dirname)
 ) ENGINE=MyISAM;

