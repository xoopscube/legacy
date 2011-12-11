#
# Table structure for table `stories`
#

CREATE TABLE `stories` (
  `storyid` int(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `created` int(10) unsigned NOT NULL default '0',
  `published` int(10) unsigned NOT NULL default '0',
  `expired` int(10) unsigned NOT NULL default '0',
  `hostname` varchar(20) NOT NULL default '',
  `html` tinyint(1) NOT NULL default '0',
  `smiley` tinyint(1) NOT NULL default '0',
  `br` tinyint(1) NOT NULL default '0',
  `xcode` tinyint(1) NOT NULL default '0',
  `hometext` text NOT NULL,
  `bodytext` text NOT NULL,
  `counter` int(8) unsigned NOT NULL default '0',
  `topicid` smallint(4) unsigned NOT NULL default '1',
  `ihome` tinyint(1) NOT NULL default '0',
  `notifypub` tinyint(1) NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0',
  `topicimg` tinyint(1) NOT NULL default '0',
  `comments` smallint(5) unsigned NOT NULL default '0',
  `block` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`storyid`),
  KEY `idxstoriestopic` (`topicid`),
  KEY `ihome` (`ihome`),
  KEY `uid` (`uid`),
  KEY `published_ihome` (`published`,`ihome`),
  KEY `title` (`title`),
  KEY `created` (`created`)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `topics`
#

CREATE TABLE `topics` (
  `topic_id` smallint(4) unsigned NOT NULL auto_increment,
  `topic_pid` smallint(4) unsigned NOT NULL default '0',
  `topic_imgurl` varchar(255) NOT NULL default '',
  `topic_title` varchar(255) NOT NULL default '',
  `topic_created` int(10) unsigned NOT NULL default 0,
  `topic_modified` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY (`topic_id`),
  KEY `pid` (`topic_pid`)
) ENGINE=MyISAM;

INSERT INTO `topics` VALUES (1,0,'','TOP',UNIX_TIMESTAMP(),0);

#
# Table structure for table `topic_access`
#

CREATE TABLE topic_access (
  topic_id smallint(5) unsigned NOT NULL default 0,
  uid mediumint(8) default NULL,
  groupid smallint(5) default NULL,
  can_post tinyint(1) NOT NULL default 0,
  can_edit tinyint(1) NOT NULL default 0,
  can_delete tinyint(1) NOT NULL default 0,
  post_auto_approved tinyint(1) NOT NULL default 0,
  UNIQUE KEY (topic_id,uid),
  UNIQUE KEY (topic_id,groupid),
  KEY (topic_id),
  KEY (uid),
  KEY (groupid),
  KEY (can_post)
) ENGINE=MyISAM;

INSERT INTO `topic_access` VALUES (1,null,1,1,1,1,1);
INSERT INTO `topic_access` VALUES (1,null,2,1,0,0,1);
INSERT INTO `topic_access` VALUES (1,null,3,0,0,0,0);

#
# Table structure for table `relation`
#

CREATE TABLE `relation` (
  `storyid` int(8) NOT NULL default '0',
  `linkedid` int(8) NOT NULL default '0',
  `dirname` varchar(25) NOT NULL default '',
  KEY (`storyid`),
  PRIMARY KEY (`storyid`,`linkedid`,`dirname`)
) ENGINE=MyISAM;
