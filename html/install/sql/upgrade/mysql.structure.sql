ALTER TABLE banner CHANGE bid bid smallint(5) unsigned not null auto_increment, change cid cid tinyint(3) unsigned not null, CHANGE imptotal imptotal mediumint(8) unsigned NOT NULL, CHANGE impmade impmade mediumint(8) unsigned NOT NULL, CHANGE clicks clicks mediumint(8) unsigned NOT NULL, ADD htmlbanner tinyint(1) NOT NULL, ADD htmlcode text NOT NULL;

ALTER TABLE bannerclient CHANGE cid cid smallint(5) unsigned NOT NULL auto_increment;

ALTER TABLE bannerfinish CHANGE bid bid smallint(5) unsigned NOT NULL auto_increment, CHANGE cid cid smallint(5) unsigned NOT NULL, CHANGE impressions impressions mediumint(8) unsigned NOT NULL, CHANGE clicks clicks mediumint(8) unsigned NOT NULL, CHANGE datestart datestart int(10) unsigned NOT NULL, CHANGE dateend dateend int(10) unsigned NOT NULL;

ALTER TABLE groups CHANGE groupid groupid smallint(5) unsigned NOT NULL auto_increment, CHANGE description description text NOT NULL, CHANGE type group_type varchar(10) NOT NULL;

CREATE TABLE group_permission (
  gperm_id int(10) unsigned NOT NULL auto_increment,
  gperm_groupid smallint(5) unsigned NOT NULL default '0',
  gperm_itemid mediumint(8) unsigned NOT NULL default '0',
  gperm_modid mediumint(5) unsigned NOT NULL default '0',
  gperm_name varchar(50) NOT NULL default '',
  PRIMARY KEY  (gperm_id),
  KEY groupid (gperm_groupid),
  KEY itemid (gperm_itemid),
  KEY gperm_modid (gperm_modid,gperm_name(10))
) ENGINE=MyISAM;


ALTER TABLE groups_users_link CHANGE groupid groupid smallint(5) unsigned NOT NULL, CHANGE uid uid mediumint(8) unsigned NOT NULL, ADD linkid mediumint(8) unsigned NOT NULL auto_increment PRIMARY KEY FIRST;

DROP TABLE metafooter ;

ALTER TABLE modules CHANGE mid mid smallint(5) unsigned NOT NULL auto_increment, CHANGE version version smallint(5) unsigned NOT NULL default '100', CHANGE last_update last_update int(10) unsigned NOT NULL, CHANGE weight weight smallint(3) unsigned NOT NULL, CHANGE isactive isactive tinyint(1) unsigned NOT NULL, CHANGE hasmain hasmain tinyint(1) unsigned NOT NULL, CHANGE hasadmin hasadmin tinyint(1) unsigned NOT NULL, CHANGE hassearch hassearch tinyint(1) unsigned NOT NULL, ADD trust_dirname varchar(25) NOT NULL default '', ADD role varchar(15) NOT NULL default '', ADD hasconfig tinyint(1) unsigned NOT NULL, ADD hascomments tinyint(1) unsigned NOT NULL, ADD hasnotification tinyint(1) unsigned NOT NULL default '0';

ALTER TABLE newblocks CHANGE bid bid mediumint(8) unsigned NOT NULL auto_increment, CHANGE mid mid smallint(5) unsigned NOT NULL, CHANGE func_num func_num tinyint(3) unsigned NOT NULL, DROP position, CHANGE title title VARCHAR(255) NOT NULL, CHANGE side side tinyint(1) unsigned NOT NULL, CHANGE weight weight smallint(5) unsigned NOT NULL, CHANGE visible visible tinyint(1) unsigned NOT NULL, CHANGE isactive isactive tinyint(1) unsigned NOT NULL, ADD template varchar(50) NOT NULL default '', ADD bcachetime int(10) unsigned NOT NULL, ADD last_modified int(10) unsigned NOT NULL, CHANGE type block_type char(1) NOT NULL;

ALTER TABLE priv_msgs CHANGE msg_id msg_id mediumint(8) unsigned NOT NULL auto_increment, CHANGE from_userid from_userid mediumint(8) unsigned NOT NULL, CHANGE subject subject varchar(255) NOT NULL, CHANGE to_userid to_userid mediumint(8) unsigned NOT NULL, CHANGE msg_time msg_time int(10) unsigned NOT NULL, CHANGE msg_text msg_text	text NOT NULL, CHANGE read_msg read_msg tinyint(1) unsigned NOT NULL;

ALTER TABLE ranks CHANGE rank_id rank_id smallint(5) unsigned NOT NULL auto_increment, CHANGE rank_min rank_min mediumint(8) unsigned NOT NULL, CHANGE rank_max rank_max mediumint(8) unsigned NOT NULL, CHANGE rank_special rank_special tinyint(1) unsigned NOT NULL;

DROP TABLE session ;

CREATE TABLE session (
  sess_id varchar(32) NOT NULL default '',
  sess_updated int(10) unsigned NOT NULL default '0',
  sess_ip varchar(15) NOT NULL default '',
  sess_data text NOT NULL,
  PRIMARY KEY  (sess_id),
  KEY updated (sess_updated)
) ENGINE=MyISAM;

ALTER TABLE smiles CHANGE id id smallint(5) unsigned NOT NULL auto_increment, CHANGE code code VARCHAR(50) NOT NULL, CHANGE smile_url smile_url VARCHAR(100) NOT NULL, CHANGE emotion emotion VARCHAR(75) NOT NULL, ADD display tinyint(1) NOT NULL;

ALTER TABLE stories CHANGE uid uid mediumint(8) unsigned NOT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE created created int(10) unsigned NOT NULL, CHANGE published published int(10) unsigned NOT NULL, CHANGE topicid topicid smallint(5) unsigned NOT NULL, CHANGE type story_type VARCHAR(5) NOT NULL, ADD expired int(10) unsigned NOT NULL, ADD comments smallint(5) unsigned NOT NULL;

ALTER TABLE users CHANGE uid uid mediumint(8) unsigned NOT NULL auto_increment, CHANGE user_avatar user_avatar VARCHAR(30) NOT NULL default 'blank.gif', CHANGE user_regdate user_regdate int(10) unsigned NOT NULL, CHANGE user_icq user_icq VARCHAR(15) NOT NULL, CHANGE user_from user_from VARCHAR(100) NOT NULL, CHANGE user_sig user_sig tinytext NOT NULL, CHANGE user_viewemail user_viewemail tinyint(1) unsigned NOT NULL, CHANGE actkey actkey VARCHAR(8) NOT NULL, CHANGE user_aim user_aim VARCHAR(18) NOT NULL, CHANGE user_yim user_yim VARCHAR(25) NOT NULL, CHANGE user_msnm user_msnm VARCHAR(100) NOT NULL, CHANGE posts posts mediumint(8) unsigned NOT NULL, CHANGE attachsig attachsig tinyint(1) unsigned NOT NULL, CHANGE rank rank smallint(5) unsigned NOT NULL, CHANGE level level tinyint(3) unsigned NOT NULL default '1', CHANGE last_login last_login int(10) unsigned NOT NULL, CHANGE uorder uorder tinyint(1) unsigned NOT NULL, CHANGE user_occ user_occ VARCHAR(100) NOT NULL, CHANGE user_intrest user_intrest VARCHAR(150) NOT NULL, ADD user_mailok tinyint(1) unsigned NOT NULL default '1', ADD notify_mode tinyint(1) NOT NULL default '0' after uorder, ADD notify_method tinyint(1) NOT NULL default '1' after uorder;

CREATE TABLE avatar (
  avatar_id mediumint(8) unsigned NOT NULL auto_increment,
  avatar_file varchar(30) NOT NULL default '',
  avatar_name varchar(100) NOT NULL default '',
  avatar_mimetype varchar(30) NOT NULL default '',
  avatar_created int(10) NOT NULL default '0',
  avatar_display tinyint(1) unsigned NOT NULL default '0',
  avatar_weight smallint(5) unsigned NOT NULL default '0',
  avatar_type char(1) NOT NULL default '',
  PRIMARY KEY  (avatar_id),
  KEY avatar_type (avatar_type,avatar_display)
) ENGINE=MyISAM;


CREATE TABLE avatar_user_link (
  avatar_id mediumint(8) unsigned NOT NULL default '0',
  user_id mediumint(8) unsigned NOT NULL default '0',
  KEY avatar_user_id (avatar_id,user_id)
) ENGINE=MyISAM;

CREATE TABLE block_module_link (
  block_id mediumint(8) unsigned NOT NULL default '0',
  module_id smallint(5) NOT NULL default '0',
  KEY module_id (module_id),
  KEY block_id (block_id)
) ENGINE=MyISAM;

CREATE TABLE xoopscomments (
  com_id mediumint(8) unsigned NOT NULL default '0',
  com_pid mediumint(8) unsigned NOT NULL default '0',
  com_rootid mediumint(8) unsigned NOT NULL default '0',
  com_modid smallint(5) unsigned NOT NULL default '0',
  com_itemid mediumint(8) unsigned NOT NULL default '0',
  com_icon varchar(25) NOT NULL default '',
  com_created int(10) unsigned NOT NULL default '0',
  com_modified int(10) unsigned NOT NULL default '0',
  com_uid mediumint(8) unsigned NOT NULL default '0',
  com_ip varchar(15) NOT NULL default '',
  com_title varchar(255) NOT NULL default '',
  com_text text NOT NULL,
  com_sig tinyint(1) unsigned NOT NULL default '0',
  com_status tinyint(1) unsigned NOT NULL default '0',
  com_exparams varchar(255) NOT NULL default '',
  dohtml tinyint(1) unsigned NOT NULL default '0',
  dosmiley tinyint(1) unsigned NOT NULL default '0',
  doxcode tinyint(1) unsigned NOT NULL default '0',
  doimage tinyint(1) unsigned NOT NULL default '0',
  dobr tinyint(1) unsigned NOT NULL default '0',
  KEY com_pid (com_pid),
  KEY com_itemid (com_itemid),
  KEY com_uid (com_uid),
  KEY com_title (com_title(40))
) ENGINE=MyISAM;


CREATE TABLE config (
  conf_id smallint(5) unsigned NOT NULL auto_increment,
  conf_modid smallint(5) unsigned NOT NULL default '0',
  conf_catid smallint(5) unsigned NOT NULL default '0',
  conf_name varchar(25) NOT NULL default '',
  conf_title varchar(30) NOT NULL default '',
  conf_value text NOT NULL,
  conf_desc varchar(30) NOT NULL default '',
  conf_formtype varchar(15) NOT NULL default '',
  conf_valuetype varchar(10) NOT NULL default '',
  conf_order smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (conf_id),
  KEY conf_mod_cat_id (conf_modid,conf_catid)
) ENGINE=MyISAM;


CREATE TABLE configcategory (
  confcat_id smallint(5) unsigned NOT NULL auto_increment,
  confcat_name varchar(25) NOT NULL default '',
  confcat_order smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (confcat_id)
) ENGINE=MyISAM;


CREATE TABLE configoption (
  confop_id mediumint(8) unsigned NOT NULL auto_increment,
  confop_name varchar(255) NOT NULL default '',
  confop_value varchar(255) NOT NULL default '',
  conf_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (confop_id),
  KEY conf_id (conf_id)
) ENGINE=MyISAM;

CREATE TABLE groups_system_link (
  linkid mediumint(8) unsigned NOT NULL auto_increment,
  groupid smallint(5) unsigned NOT NULL default '0',
  itemid smallint(5) unsigned NOT NULL default '0',
  permtype char(1) NOT NULL default '',
  PRIMARY KEY  (linkid)
) ENGINE=MyISAM;

CREATE TABLE image (
  image_id mediumint(8) unsigned NOT NULL auto_increment,
  image_name varchar(30) NOT NULL default '',
  image_nicename varchar(255) NOT NULL default '',
  image_mimetype varchar(30) NOT NULL default '',
  image_created int(10) unsigned NOT NULL default '0',
  image_display tinyint(1) unsigned NOT NULL default '0',
  image_weight smallint(5) unsigned NOT NULL default '0',
  imgcat_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (image_id),
  KEY imgcat_id (imgcat_id),
  KEY image_display (image_display)
) ENGINE=MyISAM;

CREATE TABLE imagebody (
  image_id mediumint(8) unsigned NOT NULL default '0',
  image_body mediumblob,
  KEY image_id (image_id)
) ENGINE=MyISAM;

CREATE TABLE imagecategory (
  imgcat_id smallint(5) unsigned NOT NULL auto_increment,
  imgcat_name varchar(100) NOT NULL default '',
  imgcat_maxsize int(8) unsigned NOT NULL default '0',
  imgcat_maxwidth smallint(3) unsigned NOT NULL default '0',
  imgcat_maxheight smallint(3) unsigned NOT NULL default '0',
  imgcat_display tinyint(1) unsigned NOT NULL default '0',
  imgcat_weight smallint(3) unsigned NOT NULL default '0',
  imgcat_type char(1) NOT NULL default '',
  imgcat_storetype varchar(5) NOT NULL default '',
  PRIMARY KEY  (imgcat_id),
  KEY imgcat_display (imgcat_display)
) ENGINE=MyISAM;


CREATE TABLE imgset (
  imgset_id smallint(5) unsigned NOT NULL auto_increment,
  imgset_name varchar(50) NOT NULL default '',
  imgset_refid mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (imgset_id),
  KEY imgset_refid (imgset_refid)
) ENGINE=MyISAM;

CREATE TABLE imgset_tplset_link (
  imgset_id smallint(5) unsigned NOT NULL default '0',
  tplset_name varchar(50) NOT NULL default '',
  KEY tplset_name (tplset_name(10))
) ENGINE=MyISAM;


CREATE TABLE imgsetimg (
  imgsetimg_id mediumint(8) unsigned NOT NULL auto_increment,
  imgsetimg_file varchar(50) NOT NULL default '',
  imgsetimg_body blob NOT NULL,
  imgsetimg_imgset smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (imgsetimg_id),
  KEY imgsetimg_imgset (imgsetimg_imgset)
) ENGINE=MyISAM;

CREATE TABLE online (
  online_uid mediumint(8) unsigned NOT NULL default '0',
  online_uname varchar(25) NOT NULL default '',
  online_updated int(10) unsigned NOT NULL default '0',
  online_module smallint(5) unsigned NOT NULL default '0',
  online_ip varchar(15) NOT NULL default '',
  KEY online_module (online_module)
) ENGINE=MyISAM;

CREATE TABLE tplset (
  tplset_id int(7) unsigned NOT NULL auto_increment,
  tplset_name varchar(50) NOT NULL default '',
  tplset_desc varchar(255) NOT NULL default '',
  tplset_credits text NOT NULL,
  tplset_created int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (tplset_id)
) ENGINE=MyISAM;


CREATE TABLE tplfile (
  tpl_id mediumint(7) unsigned NOT NULL auto_increment,
  tpl_refid smallint(5) unsigned NOT NULL default '0',
  tpl_module varchar(25) NOT NULL default '',
  tpl_tplset varchar(50) NOT NULL default '',
  tpl_file varchar(50) NOT NULL default '',
  tpl_desc varchar(255) NOT NULL default '',
  tpl_lastmodified int(10) unsigned NOT NULL default '0',
  tpl_lastimported int(10) unsigned NOT NULL default '0',
  tpl_type varchar(20) NOT NULL default '',
  PRIMARY KEY  (tpl_id),
  KEY tpl_refid (tpl_refid,tpl_type),
  KEY tpl_theme (tpl_tplset,tpl_file(10))
) ENGINE=MyISAM;


CREATE TABLE tplsource (
  tpl_id mediumint(7) unsigned NOT NULL default '0',
  tpl_source mediumtext NOT NULL,
  KEY tpl_id (tpl_id)
) ENGINE=MyISAM;

CREATE TABLE xoopsnotifications (
  not_id mediumint(8) unsigned NOT NULL auto_increment,
  not_modid smallint(5) unsigned NOT NULL default '0',
  not_itemid mediumint(8) unsigned NOT NULL default '0',
  not_category varchar(30) NOT NULL default '',
  not_event varchar(30) NOT NULL default '',
  not_uid mediumint(8) unsigned NOT NULL default '0',
  not_mode tinyint(1) NOT NULL default 0,
  PRIMARY KEY (not_id),
  KEY not_modid (not_modid),
  KEY not_itemid (not_itemid),
  KEY not_class (not_category),
  KEY not_uid (not_uid),
  KEY not_event (not_event)
) ENGINE=MyISAM;