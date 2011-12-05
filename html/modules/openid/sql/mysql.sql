# $Rev: 258 $
# $URL: https://ajax-discuss.svn.sourceforge.net/svnroot/ajax-discuss/openid/trunk/openid/sql/mysql.sql $

CREATE TABLE `openid_identifier` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `claimed_id` varchar(255) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `omode` tinyint(1) unsigned NOT NULL default '0',
  `local_id` varchar(255) NOT NULL default '',
  `displayid` varchar(255) NOT NULL default '',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`claimed_id`),
  KEY (`uid`),
  KEY (`omode`),
  KEY (`local_id`)
) ENGINE=MyISAM;

CREATE TABLE `openid_nonce` (
  `server_url` varchar(255) NOT NULL default '',
  `timestamp` int(10) unsigned NOT NULL default '0',
  `salt` varchar(40) NOT NULL default '',
  UNIQUE KEY `server_url` (`server_url`,`timestamp`,`salt`)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET=binary*/;

CREATE TABLE `openid_assoc` (
  `server_url` varchar(255) NOT NULL default '',
  `handle` varchar(255) NOT NULL default '',
  `secret` blob NOT NULL,
  `issued` int(10) unsigned NOT NULL default '0',
  `lifetime` int(10) unsigned NOT NULL default '0',
  `assoc_type` varchar(64) NOT NULL default '',
  PRIMARY KEY (`server_url`(245),`handle`)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET=binary*/;

CREATE TABLE `openid_filter` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `pattern` varchar(255) NOT NULL default '',
  `auth` tinyint(1) unsigned NOT NULL default '0',
  `groupid` varchar(32) NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `pattern` (`pattern`)
) ENGINE=MyISAM;

CREATE TABLE `openid_extension` (
  `dirname` varchar(25) NOT NULL default '',
  `options` varchar(255) NOT NULL default '',
  PRIMARY KEY (`dirname`)
) ENGINE=MyISAM;

CREATE TABLE `openid_buttons` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `range` varchar(5) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;