CREATE TABLE `{prefix}_profile_data` (
  `uid` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM;

CREATE TABLE `{prefix}_profile_definitions` (
  `field_id` smallint(5) unsigned NOT NULL auto_increment,
  `field_name` varchar(32) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` varchar(16) NOT NULL,
  `validation` varchar(255) NOT NULL,
  `required` tinyint(1) unsigned NOT NULL,
  `show_form` tinyint(1) unsigned NOT NULL,
  `weight` tinyint(3) unsigned NOT NULL,
  `description` text NOT NULL,
  `access` text NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY  (`field_id`)
) ENGINE=MyISAM;
