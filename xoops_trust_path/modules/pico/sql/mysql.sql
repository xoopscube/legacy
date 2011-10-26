# CREATE TABLE `tablename` will be queried as
# CREATE TABLE `prefix_dirname_tablename`

CREATE TABLE category_permissions (
  cat_id smallint(5) unsigned NOT NULL default 0,
  uid mediumint(8) default NULL,
  groupid smallint(5) default NULL,
  permissions text,
  UNIQUE KEY (cat_id,uid),
  UNIQUE KEY (cat_id,groupid),
  KEY (cat_id),
  KEY (uid),
  KEY (groupid)
) ENGINE=MyISAM;

INSERT INTO category_permissions (cat_id,uid,groupid,permissions) VALUES (0,NULL,1,'a:8:{s:8:"can_read";i:1;s:12:"can_readfull";i:1;s:8:"can_post";i:1;s:8:"can_edit";i:1;s:10:"can_delete";i:1;s:18:"post_auto_approved";i:1;s:12:"is_moderator";i:1;s:19:"can_makesubcategory";i:1;}');
INSERT INTO category_permissions (cat_id,uid,groupid,permissions) VALUES (0,NULL,2,'a:8:{s:8:"can_read";i:1;s:12:"can_readfull";i:1;s:8:"can_post";i:0;s:8:"can_edit";i:0;s:10:"can_delete";i:0;s:18:"post_auto_approved";i:0;s:12:"is_moderator";i:0;s:19:"can_makesubcategory";i:0;}');
INSERT INTO category_permissions (cat_id,uid,groupid,permissions) VALUES (0,NULL,3,'a:8:{s:8:"can_read";i:1;s:12:"can_readfull";i:1;s:8:"can_post";i:0;s:8:"can_edit";i:0;s:10:"can_delete";i:0;s:18:"post_auto_approved";i:0;s:12:"is_moderator";i:0;s:19:"can_makesubcategory";i:0;}');

CREATE TABLE categories (
  cat_id smallint(5) unsigned NOT NULL,
  cat_permission_id int(10) unsigned NOT NULL default 0,
  cat_vpath varchar(255),
  pid smallint(5) unsigned NOT NULL default 0,
  cat_title varchar(255) NOT NULL default '',
  cat_desc mediumtext,
  cat_depth_in_tree smallint(5) NOT NULL default 0,
  cat_order_in_tree smallint(5) NOT NULL default 0,
  cat_path_in_tree text,
  cat_unique_path text,
  cat_weight smallint(5) NOT NULL default 0,
  cat_options text,
  cat_created_time int(10) NOT NULL default 0,
  cat_modified_time int(10) NOT NULL default 0,
  cat_vpath_mtime int(10) NOT NULL default 0,
  cat_redundants mediumtext,
  PRIMARY KEY (cat_id),
  UNIQUE KEY (cat_vpath),
  KEY (cat_permission_id),
  KEY (cat_weight),
  KEY (pid)
) ENGINE=MyISAM;

INSERT INTO categories (cat_id,pid,cat_title) VALUES (0,0xffff,'TOP');

CREATE TABLE contents (
  content_id int(10) unsigned NOT NULL auto_increment,
  permission_id int(10) unsigned NOT NULL default 0,
  vpath varchar(255),
  cat_id smallint(5) unsigned NOT NULL default 0,
  weight smallint(5) NOT NULL default 0,
  created_time int(10) NOT NULL default 0,
  modified_time int(10) NOT NULL default 0,
  expiring_time int(10) NOT NULL default 0x7fffffff,
  last_cached_time int(10) NOT NULL default 0,
  poster_uid mediumint(8) unsigned NOT NULL default 0,
  poster_ip varchar(15) NOT NULL default '',
  modifier_uid mediumint(8) unsigned NOT NULL default 0,
  modifier_ip varchar(15) NOT NULL default '',
  subject varchar(255) NOT NULL default '',
  subject_waiting varchar(255) NOT NULL default '',
  locked tinyint(1) NOT NULL default 0,
  visible tinyint(1) NOT NULL default 1,
  approval tinyint(1) NOT NULL default 1,
  use_cache tinyint(1) NOT NULL default 1,
  allow_comment tinyint(1) NOT NULL default 1,
  show_in_navi tinyint(1) NOT NULL default 1,
  show_in_menu tinyint(1) NOT NULL default 1,
  viewed int(10) unsigned NOT NULL default 0,
  votes_sum int(10) unsigned NOT NULL default 0,
  votes_count int(10) unsigned NOT NULL default 0,
  comments_count int(10) unsigned NOT NULL default 0,
  htmlheader mediumtext,
  htmlheader_waiting mediumtext,
  body mediumtext,
  body_waiting mediumtext,
  body_cached mediumtext,
  filters text,
  tags text,
  extra_fields mediumtext,
  redundants mediumtext,
  for_search mediumtext,
  PRIMARY KEY (content_id),
  UNIQUE KEY (vpath),
  KEY (poster_uid),
  KEY (subject),
  KEY (created_time),
  KEY (modified_time),
  KEY (expiring_time),
  KEY (permission_id),
  KEY (cat_id),
  KEY (visible),
  KEY (votes_sum),
  KEY (votes_count)
) ENGINE=MyISAM;

CREATE TABLE content_votes (
  vote_id int(10) unsigned NOT NULL auto_increment,
  content_id int(10) unsigned NOT NULL default 0,
  uid mediumint(8) unsigned NOT NULL default 0,
  vote_point tinyint(3) NOT NULL default 0,
  vote_time int(10) NOT NULL default 0,
  vote_ip char(16) NOT NULL default '',
  PRIMARY KEY (vote_id),
  KEY (content_id),
  KEY (vote_ip)
) ENGINE=MyISAM;

CREATE TABLE content_histories (
  content_history_id int(10) unsigned NOT NULL auto_increment,
  content_id int(10) unsigned NOT NULL default 0,
  vpath varchar(255),
  cat_id smallint(5) unsigned NOT NULL default 0,
  created_time int(10) NOT NULL default 0,
  modified_time int(10) NOT NULL default 0,
  poster_uid mediumint(8) unsigned NOT NULL default 0,
  poster_ip varchar(15) NOT NULL default '',
  modifier_uid mediumint(8) unsigned NOT NULL default 0,
  modifier_ip varchar(15) NOT NULL default '',
  subject varchar(255) NOT NULL default '',
  htmlheader mediumtext,
  body mediumtext,
  filters text,
  tags text,
  extra_fields mediumtext,
  PRIMARY KEY (content_history_id),
  KEY (content_id),
  KEY (created_time),
  KEY (modified_time),
  KEY (modifier_uid)
) ENGINE=MyISAM;

CREATE TABLE content_extras (
  content_extra_id int(10) unsigned NOT NULL auto_increment,
  content_id int(10) unsigned NOT NULL default 0,
  extra_type varchar(255) NOT NULL default '',
  created_time int(10) NOT NULL default 0,
  modified_time int(10) NOT NULL default 0,
  data mediumtext,
  PRIMARY KEY (content_extra_id),
  KEY (content_id),
  KEY (extra_type),
  KEY (created_time)
) ENGINE=MyISAM;

CREATE TABLE tags (
  label varchar(255) NOT NULL default '',
  weight int(10) unsigned NOT NULL default 0,
  count int(10) unsigned NOT NULL default 0,
  content_ids mediumtext,
  created_time int(10) NOT NULL default 0,
  modified_time int(10) NOT NULL default 0,
  PRIMARY KEY (label),
  KEY (count),
  KEY (weight),
  KEY (created_time)
) ENGINE=MyISAM;


