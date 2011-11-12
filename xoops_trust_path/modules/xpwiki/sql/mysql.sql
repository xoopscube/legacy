CREATE TABLE `attach` (
  `id` int(11) NOT NULL auto_increment,
  `pgid` int(11) NOT NULL default '0',
  `name` varchar(255) binary NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `mtime` int(11) NOT NULL default '0',
  `size` int(11) NOT NULL default '0',
  `mode` varchar(20) NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  `age` tinyint(4) NOT NULL default '0',
  `pass` varchar(16) binary NOT NULL default '',
  `freeze` tinyint(1) NOT NULL default '0',
  `copyright` tinyint(1) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`),
  KEY `pgid` (`pgid`),
  KEY `owner` (`owner`),
  KEY `name` (`name`),
  KEY `type` (`type`),
  KEY `mode` (`mode`),
  KEY `age` (`age`)
) TYPE=MyISAM;

CREATE TABLE `cache` (
  `key` varchar(64) NOT NULL default '',
  `plugin` varchar(100) NOT NULL default '',
  `data` mediumblob NOT NULL,
  `mtime` int(11) NOT NULL default '0',
  `ttl` int(11) NOT NULL default '0',
  KEY `key` (`key`),
  KEY `plugin` (`plugin`)
) TYPE=MyISAM;

CREATE TABLE `count` (
  `pgid` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `today` varchar(10) NOT NULL default '',
  `today_count` int(11) NOT NULL default '0',
  `yesterday_count` int(11) NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`pgid`),
  KEY `today` (`today`)
) TYPE=MyISAM;

CREATE TABLE `pginfo` (
  `pgid` int(11) NOT NULL auto_increment,
  `name` varchar(255) binary NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `buildtime` int(11) NOT NULL default '0',
  `editedtime` int(11) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `ucd` varchar(12) NOT NULL default '',
  `uname` varchar(255) NOT NULL default '',
  `freeze` tinyint(1) NOT NULL default '0',
  `einherit` tinyint(1) NOT NULL default '3',
  `eaids` text NOT NULL default '',
  `egids` varchar(255) NOT NULL default '',
  `vinherit` tinyint(1) NOT NULL default '3',
  `vaids` text NOT NULL default '',
  `vgids` varchar(255) NOT NULL default '',
  `lastuid` mediumint(8) NOT NULL default '0',
  `lastucd` varchar(12) NOT NULL default '',
  `lastuname` varchar(255) NOT NULL default '',
  `update` tinyint(1) NOT NULL default '0',
  `reading` varchar(255) binary NOT NULL default '',
  `name_ci` varchar(255) NOT NULL default '',
  `pgorder` float NOT NULL default '1',
  PRIMARY KEY  (`pgid`),
  UNIQUE KEY `name` (`name`),
  KEY `uid` (`uid`),
  KEY `name_ci` (`name_ci`),
  KEY `editedtime` (`editedtime`),
  KEY `freeze` (`freeze`),
  KEY `egids` (`egids`),
  KEY `vgids` (`vgids`),
  KEY `eaids` (`eaids`(255)),
  KEY `vaids` (`vaids`(255)),
  KEY `vids` (`vaids`(200),`vgids`(133))
) TYPE=MyISAM;

CREATE TABLE `plain` (
  `pgid` int(11) NOT NULL default '0',
  `plain` text NOT NULL,
  PRIMARY KEY  (`pgid`)
) TYPE=MyISAM;

CREATE TABLE `rel` (
  `pgid` int(11) NOT NULL default '0',
  `relid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pgid`,`relid`),
  KEY `pgid` (`pgid`),
  KEY `relid` (`relid`)
) TYPE=MyISAM;

CREATE TABLE `tb` (
  `tbid` varchar(32) NOT NULL default '',
  `pgid` int(11) NOT NULL default '0',
  `last_time` int(11) NOT NULL default '0',
  `url` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `excerpt` text NOT NULL,
  `blog_name` varchar(255) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  KEY `tbid` (`tbid`),
  KEY `pgid` (`pgid`)
) TYPE=MyISAM;
