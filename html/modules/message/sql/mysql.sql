CREATE TABLE {prefix}_{dirname}_inbox (
  `inbox_id` int(8) unsigned NOT NULL auto_increment,
  `uid` int(8) unsigned NOT NULL default '0',
  `from_uid` int(8) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `utime` int(11) unsigned NOT NULL default '0',
  `is_read` int(1) unsigned NOT NULL default '0',
  `uname` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`inbox_id`),
  KEY uid (`uid`)
) ENGINE = MYISAM ;

CREATE TABLE {prefix}_{dirname}_outbox (
  `outbox_id` int(8) unsigned NOT NULL auto_increment,
  `uid` int(8) unsigned NOT NULL default '0',
  `to_uid` int(8) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `utime` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`outbox_id`),
  KEY uid (`uid`)
) ENGINE = MYISAM ;

CREATE TABLE {prefix}_{dirname}_users (
  `uid` int(8) unsigned NOT NULL default '0',
  `usepm` int(1) unsigned NOT NULL default '0',
  `tomail` int(1) unsigned NOT NULL default '0',
  `viewmsm` int(1) unsigned NOT NULL default '0',
  `pagenum` int(2) unsigned NOT NULL default '0',
  `blacklist` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uid`)
) ENGINE = MYISAM ;
