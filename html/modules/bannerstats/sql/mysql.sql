CREATE TABLE `{prefix}_bannerclient` (
  `cid` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `contact` varchar(60) default NULL,
  `email` varchar(191) NOT NULL default '',
  `tel` varchar(50) default NULL,
  `address1` varchar(191) default NULL,
  `address2` varchar(191) default NULL,
  `city` varchar(100) default NULL,
  `region` varchar(100) default NULL,
  `postal_code` varchar(20) default NULL,
  `country_code` varchar(2) default NULL,
  `login` varchar(25) NOT NULL default '',
  `passwd` varchar(255) NOT NULL default '',
  `extrainfo` text,
  `status` tinyint(1) NOT NULL default '1',
  `date_created` int(10) unsigned NOT NULL default '0',
  `last_updated` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB;

CREATE TABLE `{prefix}_banner` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `campaign_id` int(11) default NULL,
  `name` varchar(255) NOT NULL default '',
  `banner_type` enum('image','html','ad_tag','video') NOT NULL default 'image',
  `imageurl` varchar(255) default NULL,
  `clickurl` varchar(255) default NULL,
  `htmlcode` text,
  `width` smallint(5) unsigned default NULL,
  `height` smallint(5) unsigned default NULL,
  `imptotal` int(11) unsigned NOT NULL default '0',
  `impmade` int(11) unsigned NOT NULL default '0',
  `clicks` int(11) unsigned NOT NULL default '0',
  `start_date` int(10) unsigned NOT NULL default '0',
  `end_date` int(10) unsigned NOT NULL default '0',
  `last_impression_time` int(10) unsigned NOT NULL default '0',
  `last_click_time` int(10) unsigned NOT NULL default '0',
  `timezone` varchar(50) default NULL,
  `date_created` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  `weight` tinyint(3) unsigned NOT NULL default '10',
  `low_impression_alert_sent` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`bid`),
  KEY `cid` (`cid`),
  KEY `idx_status_dates_impressions` (`status`,`start_date`,`end_date`,`imptotal`,`impmade`)
) ENGINE=InnoDB;



CREATE TABLE `{prefix}_bannerfinish` (
  `bid` int(11) NOT NULL,
  `cid` int(11) NOT NULL default '0',
  `campaign_id` int(11) default NULL,
  `name` varchar(255) NOT NULL default '',
  `banner_type` enum('image','html','ad_tag','video') NOT NULL default 'image',
  `imageurl` varchar(255) default NULL,
  `clickurl` varchar(255) default NULL,
  `htmlcode` text,
  `width` smallint(5) unsigned default NULL,
  `height` smallint(5) unsigned default NULL,
  `imptotal_allocated` int(11) unsigned NOT NULL default '0',
  `impressions_made` int(11) unsigned NOT NULL default '0',
  `clicks_made` int(11) unsigned NOT NULL default '0',
  `datestart_original` int(10) unsigned NOT NULL default '0',
  `dateend_original` int(10) unsigned NOT NULL default '0',
  `timezone_original` varchar(50) default NULL,
  `date_created_original` int(10) unsigned NOT NULL default '0',
  `date_finished` int(10) unsigned NOT NULL default '0',
  `finish_reason` varchar(255) NULL default NULL,
  `finished_by_uid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`bid`),
  KEY `cid` (`cid`),
  KEY `idx_finished_dates` (`date_finished`,`datestart_original`,`dateend_original`)
) ENGINE=InnoDB;
