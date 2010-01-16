CREATE TABLE IF NOT EXISTS `{prefix}_{dirname}_cat` (
  `cat_id` smallint(5) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `gr_id` smallint(5) unsigned NOT NULL default '0',
  `p_id` smallint(5) unsigned NOT NULL default '0',
  `modules` text NOT NULL,
  `description` text NOT NULL,
  `depth` smallint(5) unsigned NOT NULL default '0',
  `weight` smallint(5) unsigned NOT NULL default '0',
  `options` text NOT NULL,
  PRIMARY KEY  (`cat_id`),
  KEY `gr_id` (`gr_id`),
  KEY `p_id` (`p_id`),
  KEY `weight` (`weight`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `{prefix}_{dirname}_gr` (
  `gr_id` smallint(5) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `level` tinyint(3) unsigned NOT NULL default '0',
  `actions` text NOT NULL,
  PRIMARY KEY  (`gr_id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `{prefix}_{dirname}_permit` (
  `permit_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat_id` smallint(5) unsigned NOT NULL default '0',
  `groupid` smallint(5) unsigned NOT NULL default '0',
  `permissions` text NOT NULL,
  PRIMARY KEY  (`permit_id`),
  KEY `groupid` (`groupid`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM;
