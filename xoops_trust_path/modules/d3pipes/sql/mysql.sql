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


CREATE TABLE categories (
  cat_id smallint(5) unsigned NOT NULL auto_increment,
  pid smallint(5) unsigned NOT NULL default 0,
  cat_title varchar(255) NOT NULL default '',
  cat_desc text,
  cat_depth_in_tree smallint(5) NOT NULL default 0,
  cat_order_in_tree smallint(5) NOT NULL default 0,
  cat_path_in_tree text,
  cat_unique_path text,
  cat_weight smallint(5) NOT NULL default 0,
  cat_options text,
  cat_created_time int(10) NOT NULL default 0,
  cat_modified_time int(10) NOT NULL default 0,
  KEY (cat_weight),
  KEY (pid),
  PRIMARY KEY (cat_id)
) ENGINE=MyISAM;


CREATE TABLE pipes (
  pipe_id int(10) unsigned NOT NULL auto_increment,
  cat_id smallint(5) unsigned NOT NULL default 0,
  name varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  image varchar(255) NOT NULL default '',
  main_disp tinyint NOT NULL default 1,
  main_list tinyint NOT NULL default 1,
  main_aggr tinyint NOT NULL default 1,
  main_rss  tinyint NOT NULL default 1,
  block_disp tinyint NOT NULL default 1,
  in_submenu tinyint NOT NULL default 1,
  weight smallint NOT NULL default 0,
  joints text,
  description text,
  options text,
  created_time int(10) NOT NULL default 0,
  modified_time int(10) NOT NULL default 0,
  lastfetch_time int(10) NOT NULL default 0,
  KEY (cat_id),
  KEY (main_disp),
  KEY (main_list),
  KEY (main_aggr),
  KEY (main_rss),
  KEY (block_disp),
  KEY (in_submenu),
  KEY (weight),
  PRIMARY KEY (pipe_id)
) ENGINE=MyISAM;



CREATE TABLE clippings (
  clipping_id int(10) unsigned NOT NULL auto_increment,
  pipe_id int(10) unsigned NOT NULL default 0,
  fingerprint varchar(255) NOT NULL default '',
  pubtime int(10) NOT NULL default 0,
  link varchar(255) NOT NULL default '',
  headline varchar(255) NOT NULL default '',
  can_search tinyint NOT NULL default 1,
  highlight tinyint NOT NULL default 0,
  weight smallint NOT NULL default 0,
  comments_count smallint NOT NULL default 0,
  fetched_time int(10) NOT NULL default 0,
  data mediumtext,
  KEY (fingerprint),
  KEY (can_search),
  KEY (highlight),
  KEY (weight),
  KEY (pipe_id),
  KEY (pubtime),
  KEY (fetched_time),
  UNIQUE KEY (pipe_id,fingerprint),
  PRIMARY KEY (clipping_id)
) ENGINE=MyISAM;



CREATE TABLE joints (
  joint_type varchar(32) NOT NULL default '',
  default_class varchar(32) NOT NULL default '',
  parameters text,
  PRIMARY KEY (joint_type)
) ENGINE=MyISAM;

INSERT INTO joints (joint_type,default_class) VALUES ('fetch','snoopy');
INSERT INTO joints (joint_type,default_class) VALUES ('filter','pcre');
INSERT INTO joints (joint_type,default_class) VALUES ('parse','keithxml');
INSERT INTO joints (joint_type,default_class) VALUES ('utf8from','mbstring');
INSERT INTO joints (joint_type,default_class) VALUES ('utf8to','mbstring');
INSERT INTO joints (joint_type,default_class) VALUES ('union','mergesort');
INSERT INTO joints (joint_type,default_class) VALUES ('clip','moduledb');
INSERT INTO joints (joint_type,default_class) VALUES ('cache','trustpath');
INSERT INTO joints (joint_type,default_class) VALUES ('ping','xmlrpc2');
INSERT INTO joints (joint_type,default_class) VALUES ('reassign','allowhtml');
INSERT INTO joints (joint_type,default_class) VALUES ('block','d3forumtopics');
INSERT INTO joints (joint_type,default_class) VALUES ('replace','mbregex');
INSERT INTO joints (joint_type,default_class) VALUES ('sort','pubtimedsc');


CREATE TABLE joint_classes (
  class_name varchar(32) NOT NULL default '',
  parameters text,
  PRIMARY KEY (class_name)
) ENGINE=MyISAM;


