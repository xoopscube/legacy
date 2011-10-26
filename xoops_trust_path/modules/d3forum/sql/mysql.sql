CREATE TABLE category_access (
  cat_id smallint(5) unsigned NOT NULL default 0,
  uid mediumint(8) default NULL,
  groupid smallint(5) default NULL,
  can_post tinyint(1) NOT NULL default 0,
  can_edit tinyint(1) NOT NULL default 0,
  can_delete tinyint(1) NOT NULL default 0,
  post_auto_approved tinyint(1) NOT NULL default 0,
  can_makeforum tinyint(1) NOT NULL default 0,
  is_moderator tinyint(1) NOT NULL default 0,
  UNIQUE KEY (cat_id,uid),
  UNIQUE KEY (cat_id,groupid),
  KEY (cat_id),
  KEY (uid),
  KEY (groupid),
  KEY (can_post)
) TYPE=MyISAM;


CREATE TABLE forum_access (
  forum_id int(6) unsigned NOT NULL default 0,
  uid mediumint(8) default NULL,
  groupid smallint(5) default NULL,
  can_post tinyint(1) NOT NULL default 0,
  can_edit tinyint(1) NOT NULL default 0,
  can_delete tinyint(1) NOT NULL default 0,
  post_auto_approved tinyint(1) NOT NULL default 0,
  is_moderator tinyint(1) NOT NULL default 0,
  UNIQUE KEY (forum_id,uid),
  UNIQUE KEY (forum_id,groupid),
  KEY (forum_id),
  KEY (uid),
  KEY (groupid),
  KEY (can_post)
) TYPE=MyISAM;


CREATE TABLE categories (
  cat_id smallint(5) unsigned NOT NULL auto_increment,
  pid smallint(5) unsigned NOT NULL default 0,
  cat_title varchar(255) NOT NULL default '',
  cat_desc text,
  cat_topics_count int(8) NOT NULL default 0,
  cat_posts_count int(10) NOT NULL default 0,
  cat_last_post_id int(10) NOT NULL default 0,
  cat_last_post_time int(10) NOT NULL default 0,
  cat_topics_count_in_tree int(8) NOT NULL default 0,
  cat_posts_count_in_tree int(10) NOT NULL default 0,
  cat_last_post_id_in_tree int(10) NOT NULL default 0,
  cat_last_post_time_in_tree int(10) NOT NULL default 0,
  cat_depth_in_tree smallint(5) NOT NULL default 0,
  cat_order_in_tree smallint(5) NOT NULL default 0,
  cat_path_in_tree text,
  cat_unique_path text,
  cat_weight smallint(5) NOT NULL default 0,
  cat_options text,
  PRIMARY KEY (cat_id),
  KEY (cat_weight),
  KEY (pid)
) TYPE=MyISAM;


CREATE TABLE forums (
  forum_id int(6) unsigned NOT NULL auto_increment,
  cat_id smallint(5) unsigned NOT NULL default 0,
  forum_external_link_format varchar(255) NOT NULL default '',
  forum_title varchar(255) NOT NULL default '',
  forum_desc text,
  forum_topics_count int(8) NOT NULL default 0,
  forum_posts_count int(10) NOT NULL default 0,
  forum_last_post_id int(10) unsigned NOT NULL default 0,
  forum_last_post_time int(10) NOT NULL default 0,
  forum_weight int(8) NOT NULL default 0,
  forum_options text,
  PRIMARY KEY (forum_id),
  KEY (forum_last_post_id),
  KEY (forum_last_post_time),
  KEY (forum_weight),
  KEY (cat_id)
) TYPE=MyISAM;


CREATE TABLE topics (
  topic_id int(8) unsigned NOT NULL auto_increment,
  forum_id int(6) unsigned NOT NULL default 0,
  topic_external_link_id varchar(255) NOT NULL default '',
  topic_title varchar(255) default NULL,
  topic_first_uid mediumint(8) NOT NULL default 0,
  topic_first_post_id int(10) unsigned NOT NULL default 0,
  topic_first_post_time int(10) NOT NULL default 0,
  topic_last_uid mediumint(8) NOT NULL default 0,
  topic_last_post_id int(10) unsigned NOT NULL default 0,
  topic_last_post_time int(10) NOT NULL default 0,
  topic_views int(10) NOT NULL default 0,
  topic_posts_count int(10) NOT NULL default 0,
  topic_locked tinyint(1) NOT NULL default 0,
  topic_sticky tinyint(1) NOT NULL default 0,
  topic_solved tinyint(1) NOT NULL default 1,
  topic_invisible tinyint(1) NOT NULL default 0,
  topic_votes_sum int(10) unsigned NOT NULL default 0,
  topic_votes_count int(10) unsigned NOT NULL default 0,
  PRIMARY KEY (topic_id),
  KEY (forum_id),
  KEY (topic_external_link_id),
  KEY (topic_last_post_time),
  KEY (topic_last_post_id),
  KEY (topic_id,forum_id),
  KEY (topic_solved),
  KEY (topic_sticky),
  KEY (topic_invisible),
  KEY (topic_votes_sum),
  KEY (topic_votes_count)
) TYPE=MyISAM;


CREATE TABLE posts (
  post_id int(10) unsigned NOT NULL auto_increment,
  pid int(10) unsigned NOT NULL default 0,
  topic_id int(8) unsigned NOT NULL default 0,
  post_time int(10) NOT NULL default 0,
  modified_time int(10) NOT NULL default 0,
  uid mediumint(8) unsigned NOT NULL default 0,
  uid_hidden mediumint(8) unsigned NOT NULL default 0,
  poster_ip varchar(15) NOT NULL default '',
  modifier_ip varchar(15) NOT NULL default '',
  subject varchar(255) NOT NULL default '',
  subject_waiting varchar(255) NOT NULL default '',
  html tinyint(1) NOT NULL default 0,
  smiley tinyint(1) NOT NULL default 1,
  xcode tinyint(1) NOT NULL default 1,
  br tinyint(1) NOT NULL default 1,
  number_entity tinyint(1) NOT NULL default 0,
  special_entity tinyint(1) NOT NULL default 0,
  icon tinyint(3) NOT NULL default 0,
  attachsig tinyint(1) NOT NULL default 1,
  invisible tinyint(1) NOT NULL default 0,
  approval tinyint(1) NOT NULL default 1,
  votes_sum int(10) unsigned NOT NULL default 0,
  votes_count int(10) unsigned NOT NULL default 0,
  depth_in_tree smallint(5) NOT NULL default 0,
  order_in_tree smallint(5) NOT NULL default 0,
  path_in_tree text,
  unique_path text,
  guest_name varchar(25) NOT NULL default '',
  guest_email varchar(60) NOT NULL default '',
  guest_url varchar(100) NOT NULL default '',
  guest_pass_md5 varchar(40) NOT NULL default '',
  guest_trip varchar(40) NOT NULL default '',
  post_text text,
  post_text_waiting text,
  PRIMARY KEY (post_id),
  KEY (uid),
  KEY (pid),
  KEY (subject),
  KEY (post_time),
  KEY (topic_id),
  KEY (invisible),
  KEY (votes_sum),
  KEY (votes_count)
) TYPE=MyISAM;


CREATE TABLE users2topics (
  uid mediumint(8) unsigned NOT NULL default 0,
  topic_id int(8) unsigned NOT NULL default 0,
  u2t_time int(10) NOT NULL default 0,
  u2t_marked tinyint NOT NULL default 0,
  u2t_rsv tinyint NOT NULL default 0,
  PRIMARY KEY (uid,topic_id),
  KEY (uid),
  KEY (topic_id),
  KEY (u2t_time),
  KEY (u2t_marked),
  KEY (u2t_rsv)
) TYPE=MyISAM;


CREATE TABLE post_votes (
  vote_id int(10) unsigned NOT NULL auto_increment,
  post_id int(10) unsigned NOT NULL default 0,
  uid mediumint(8) unsigned NOT NULL default 0,
  vote_point tinyint(3) NOT NULL default 0,
  vote_time int(10) NOT NULL default 0,
  vote_ip char(16) NOT NULL default '',
  PRIMARY KEY (vote_id),
  KEY (post_id),
  KEY (vote_ip)
) TYPE=MyISAM;


CREATE TABLE post_histories (
  history_id int(10) unsigned NOT NULL auto_increment,
  post_id int(10) unsigned NOT NULL default 0,
  history_time int(10) NOT NULL default 0,
  data text,
  PRIMARY KEY (history_id),
  KEY (post_id)
) TYPE=MyISAM;
