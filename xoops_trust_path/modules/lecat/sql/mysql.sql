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


INSERT INTO `{prefix}_{dirname}_cat` (`cat_id`, `title`, `gr_id`, `p_id`, `modules`, `description`, `depth`, `weight`, `options`) VALUES (1, 'common', 1, 0, '', 'common category', 0, 10, '');

CREATE TABLE IF NOT EXISTS `{prefix}_{dirname}_gr` (
  `gr_id` smallint(5) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `level` tinyint(3) unsigned NOT NULL default '0',
  `actions` text NOT NULL,
  PRIMARY KEY  (`gr_id`)
) ENGINE=MyISAM;

INSERT INTO `{prefix}_{dirname}_gr` (`gr_id`, `title`, `level`, `actions`) VALUES
(1, 'common', 1, 'a:2:{s:5:"title";a:3:{s:6:"viewer";s:6:"Viewer";s:6:"poster";s:6:"Poster";s:7:"manager";s:7:"Manager";}s:7:"default";a:3:{s:6:"viewer";s:1:"1";s:6:"poster";s:1:"1";s:7:"manager";N;}}');

CREATE TABLE IF NOT EXISTS `{prefix}_{dirname}_permit` (
  `permit_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat_id` smallint(5) unsigned NOT NULL default '0',
  `groupid` smallint(5) unsigned NOT NULL default '0',
  `permissions` text NOT NULL,
  PRIMARY KEY  (`permit_id`),
  KEY `groupid` (`groupid`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM;
