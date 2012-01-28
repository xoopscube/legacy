CREATE TABLE `{prefix}_{dirname}_store` (
  `sid` int(11) unsigned NOT NULL  auto_increment,
  `uid` int(11) unsigned NOT NULL,
  `valid` int(3) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`addon_url` varchar(255) NOT NULL,
	`theme_url` varchar(255) NOT NULL,
  `reg_unixtime` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`sid`)) ENGINE=MyISAM;

