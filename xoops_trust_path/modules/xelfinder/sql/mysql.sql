CREATE TABLE `file` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(191) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0',
  `mtime` int(10) unsigned NOT NULL DEFAULT '0',
  `perm` varchar(3) NOT NULL DEFAULT '',
  `umask` varchar(3) NOT NULL DEFAULT '022',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `gid` int(10) unsigned NOT NULL DEFAULT '0',
  `home_of` int(10) DEFAULT NULL,
  `mime` varchar(255) NOT NULL DEFAULT 'unknown',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `gids` varchar(255) NOT NULL DEFAULT '',
  `mime_filter` varchar(255) NOT NULL DEFAULT '',
  `local_path` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `parent_name` (`parent_id`,`name`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB;

CREATE TABLE `link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL DEFAULT '0',
  `mid` int(10) unsigned NOT NULL DEFAULT '0',
  `param` varchar(25) NOT NULL DEFAULT '',
  `val` varchar(25) NOT NULL DEFAULT '',
  `uri` text NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mid_key_val` (`mid`,`param`,`val`),
  KEY `uri` (`uri`(255)),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB;

CREATE TABLE `userdat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `key` varchar(255) character SET ascii NOT NULL DEFAULT '',
  `data` blob NOT NULL,
  `mtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid_key` (`uid`,`key`)
) ENGINE=InnoDB;
