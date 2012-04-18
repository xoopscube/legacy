CREATE TABLE `file` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0',
  `ctime` int(10) unsigned NOT NULL,
  `mtime` int(10) unsigned NOT NULL,
  `perm` varchar(3) NOT NULL,
  `umask` varchar(3) NOT NULL DEFAULT '022',
  `uid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL,
  `home_of` int(10) DEFAULT NULL,
  `mime` varchar(60) NOT NULL DEFAULT 'unknown',
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `gids` varchar(255) NOT NULL,
  `mime_filter` varchar(255) NOT NULL,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `parent_name` (`parent_id`,`name`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM;

CREATE TABLE `link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `mid` int(10) unsigned NOT NULL,
  `param` varchar(25) NOT NULL,
  `val` varchar(25) NOT NULL,
  `uri` text NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mid_key_val` (`mid`,`param`,`val`),
  KEY `uri` (`uri`(255)),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM;
