CREATE TABLE IF NOT EXISTS `{prefix}_{dirname}_cat` (
  `cat_id` smallint(5) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `p_id` smallint(5) unsigned NOT NULL default '0',
  `modules` text NOT NULL,
  `description` text NOT NULL,
  `weight` smallint(5) unsigned NOT NULL default '0',
  `options` text NOT NULL,
  PRIMARY KEY  (`cat_id`),
  KEY `p_id` (`p_id`),
  KEY `weight` (`weight`)
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

INSERT INTO `{prefix}_{dirname}_permit` (`permit_id`, `cat_id`, `groupid`, `permissions`) VALUES 
(1, 0, 1, 'a:3:{s:6:"viewer";s:1:"1";s:6:"poster";s:1:"1";s:7:"manager";s:1:"1";}'),
(2, 0, 2, 'a:2:{s:6:"viewer";s:1:"1";s:6:"poster";s:1:"1";}'),
(3, 0, 3, 'a:1:{s:6:"viewer";s:1:"1";}');

