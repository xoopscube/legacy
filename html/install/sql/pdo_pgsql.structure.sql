# phpMyAdmin MySQL-Dump
# version 2.3.2
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Jan 09, 2003 at 12:35 AM
# Server version: 3.23.54
# PHP Version: 4.2.2
# Database : `xoops2`
# --------------------------------------------------------

#
# Table structure for table `avatar`
#

CREATE TABLE avatar (
  avatar_id serial,
  avatar_file varchar(30) NOT NULL default '',
  avatar_name varchar(100) NOT NULL default '',
  avatar_mimetype varchar(30) NOT NULL default '',
  avatar_created int NOT NULL default '0',
  avatar_display smallint  NOT NULL default '0',
  avatar_weight smallint  NOT NULL default '0',
  avatar_type char(1) NOT NULL default '',
  PRIMARY KEY  (avatar_id)
);
CREATE INDEX avatar_type ON avatar (avatar_type,avatar_display);
# --------------------------------------------------------

#
# Table structure for table `avatar_user_link`
#

CREATE TABLE avatar_user_link (
  avatar_id int  NOT NULL default '0',
  user_id int  NOT NULL default '0'
);
CREATE INDEX avatar_user_id ON avatar_user_link (avatar_id,user_id);
# --------------------------------------------------------

#
# Table structure for table `banner`
#

CREATE TABLE banner (
  bid serial,
  cid smallint  NOT NULL default '0',
  imptotal int  NOT NULL default '0',
  impmade int  NOT NULL default '0',
  clicks int  NOT NULL default '0',
  imageurl varchar(255) NOT NULL default '',
  clickurl varchar(255) NOT NULL default '',
  date int NOT NULL default '0',
  htmlbanner smallint NOT NULL default '0',
  htmlcode text NOT NULL,
  PRIMARY KEY  (bid)
);
CREATE INDEX idxbannercid ON banner (cid);
CREATE INDEX idxbannerbidcid ON banner (bid,cid);
# --------------------------------------------------------

#
# Table structure for table `bannerclient`
#

CREATE TABLE bannerclient (
  cid serial,
  name varchar(60) NOT NULL default '',
  contact varchar(60) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  login varchar(10) NOT NULL default '',
  passwd varchar(10) NOT NULL default '',
  extrainfo text NOT NULL,
  PRIMARY KEY  (cid)
);
CREATE INDEX login ON bannerclient (login);
# --------------------------------------------------------

#
# Table structure for table `bannerfinish`
#

CREATE TABLE bannerfinish (
  bid serial,
  cid smallint  NOT NULL default '0',
  impressions int  NOT NULL default '0',
  clicks int  NOT NULL default '0',
  datestart int  NOT NULL default '0',
  dateend int  NOT NULL default '0',
  PRIMARY KEY  (bid)
);
CREATE INDEX cid ON bannerfinish (cid);
# --------------------------------------------------------

#
# Table structure for table `block_module_link`
#

CREATE TABLE block_module_link (
  block_id int  NOT NULL default '0',
  module_id smallint NOT NULL default '0'
);
CREATE INDEX module_id ON block_module_link (module_id);
CREATE INDEX block_id ON block_module_link (block_id);
# --------------------------------------------------------

#
# Table structure for table `comments`
#

CREATE TABLE xoopscomments (
  com_id serial,
  com_pid int  NOT NULL default '0',
  com_rootid int  NOT NULL default '0',
  com_modid smallint  NOT NULL default '0',
  com_itemid int  NOT NULL default '0',
  com_icon varchar(25) NOT NULL default '',
  com_created int  NOT NULL default '0',
  com_modified int  NOT NULL default '0',
  com_uid int  NOT NULL default '0',
  com_ip varchar(15) NOT NULL default '',
  com_title varchar(255) NOT NULL default '',
  com_text text NOT NULL,
  com_sig smallint  NOT NULL default '0',
  com_status smallint  NOT NULL default '0',
  com_exparams varchar(255) NOT NULL default '',
  dohtml smallint  NOT NULL default '0',
  dosmiley smallint  NOT NULL default '0',
  doxcode smallint  NOT NULL default '0',
  doimage smallint  NOT NULL default '0',
  dobr smallint  NOT NULL default '0',
  PRIMARY KEY  (com_id)
);
CREATE INDEX  com_pid ON xoopscomments (com_pid);
CREATE INDEX  com_itemid ON xoopscomments (com_itemid);
CREATE INDEX  com_uid ON xoopscomments (com_uid);
CREATE INDEX  com_title ON xoopscomments (com_title(40));
# --------------------------------------------------------

# RMV-NOTIFY
# Table structure for table `notifications`
#

CREATE TABLE xoopsnotifications (
  not_id serial,
  not_modid smallint  NOT NULL default '0',
  not_itemid int  NOT NULL default '0',
  not_category varchar(30) NOT NULL default '',
  not_event varchar(30) NOT NULL default '',
  not_uid int  NOT NULL default '0',
  not_mode smallint NOT NULL default 0,
  PRIMARY KEY (not_id)
);
CREATE INDEX not_modid ON xoopsnotifications (not_modid);
CREATE INDEX not_itemid ON xoopsnotifications (not_itemid);
CREATE INDEX not_class ON xoopsnotifications (not_category);
CREATE INDEX not_uid ON xoopsnotifications (not_uid);
CREATE INDEX not_event ON xoopsnotifications (not_event);
# --------------------------------------------------------

#
# Table structure for table `config`
#

CREATE TABLE config (
  conf_id serial,
  conf_modid smallint  NOT NULL default '0',
  conf_catid smallint  NOT NULL default '0',
  conf_name varchar(25) NOT NULL default '',
  conf_title varchar(255) NOT NULL default '',
  conf_value text NOT NULL,
  conf_desc varchar(255) NOT NULL default '',
  conf_formtype varchar(15) NOT NULL default '',
  conf_valuetype varchar(10) NOT NULL default '',
  conf_order smallint  NOT NULL default '0',
  PRIMARY KEY  (conf_id)
);
CREATE INDEX conf_mod_cat_id ON config (conf_modid,conf_catid);
# --------------------------------------------------------

#
# Table structure for table `configcategory`
#

CREATE TABLE configcategory (
  confcat_id serial,
  confcat_name varchar(25) NOT NULL default '',
  confcat_order smallint  NOT NULL default '0',
  PRIMARY KEY  (confcat_id)
);
# --------------------------------------------------------

#
# Table structure for table `configoption`
#

CREATE TABLE configoption (
  confop_id serial,
  confop_name varchar(255) NOT NULL default '',
  confop_value varchar(255) NOT NULL default '',
  conf_id smallint  NOT NULL default '0',
  PRIMARY KEY  (confop_id)
);
CREATE INDEX conf_id ON configoption (conf_id);
# --------------------------------------------------------

#
# Table structure for table `groups`
#

CREATE TABLE groups (
  groupid serial,
  name varchar(50) NOT NULL default '',
  description text NOT NULL,
  group_type varchar(10) NOT NULL default '',
  PRIMARY KEY  (groupid)
);
CREATE INDEX group_type ON groups (group_type);
# --------------------------------------------------------

#
# Table structure for table `group_permission`
#

CREATE TABLE group_permission (
  gperm_id serial,
  gperm_groupid smallint  NOT NULL default '0',
  gperm_itemid int  NOT NULL default '0',
  gperm_modid int  NOT NULL default '0',
  gperm_name varchar(50) NOT NULL default '',
  PRIMARY KEY  (gperm_id)
);
CREATE INDEX groupid ON group_permission (gperm_groupid);
CREATE INDEX itemid ON group_permission (gperm_itemid);
CREATE INDEX gperm_modid ON group_permission (gperm_modid,gperm_name(10));
# --------------------------------------------------------


#
# Table structure for table `groups_users_link`
#

CREATE TABLE groups_users_link (
  linkid serial,
  groupid smallint  NOT NULL default '0',
  uid int  NOT NULL default '0',
  PRIMARY KEY  (linkid)
);
CREATE UNIQUE INDEX uid_groupid ON groups_users_link (uid,groupid);
# --------------------------------------------------------

#
# Table structure for table `image`
#

CREATE TABLE image (
  image_id serial,
  image_name varchar(30) NOT NULL default '',
  image_nicename varchar(255) NOT NULL default '',
  image_mimetype varchar(30) NOT NULL default '',
  image_created int  NOT NULL default '0',
  image_display smallint  NOT NULL default '0',
  image_weight smallint  NOT NULL default '0',
  imgcat_id smallint  NOT NULL default '0',
  PRIMARY KEY  (image_id)
);
CREATE INDEX imgcat_id ON image (imgcat_id);
CREATE INDEX image_display ON image (image_display);
# --------------------------------------------------------

#
# Table structure for table `imagebody`
#

CREATE TABLE imagebody (
  image_id int  NOT NULL default '0',
  image_body bytea
);
CREATE INDEX image_id ON imagebody (image_id);
# --------------------------------------------------------

#
# Table structure for table `imagecategory`
#

CREATE TABLE imagecategory (
  imgcat_id serial,
  imgcat_name varchar(100) NOT NULL default '',
  imgcat_maxsize int  NOT NULL default '0',
  imgcat_maxwidth smallint  NOT NULL default '0',
  imgcat_maxheight smallint  NOT NULL default '0',
  imgcat_display smallint  NOT NULL default '0',
  imgcat_weight smallint  NOT NULL default '0',
  imgcat_type char(1) NOT NULL default '',
  imgcat_storetype varchar(5) NOT NULL default '',
  PRIMARY KEY  (imgcat_id)
);
CREATE INDEX imgcat_display ON imagecategory (imgcat_display);
# --------------------------------------------------------


#
# Table structure for table `imgset`
#

CREATE TABLE imgset (
  imgset_id serial,
  imgset_name varchar(50) NOT NULL default '',
  imgset_refid int  NOT NULL default '0',
  PRIMARY KEY  (imgset_id)
);
CREATE INDEX imgset_refid ON imgset (imgset_refid);
# --------------------------------------------------------

#
# Table structure for table `imgset_tplset_link`
#

CREATE TABLE imgset_tplset_link (
  imgset_id smallint  NOT NULL default '0',
  tplset_name varchar(50) NOT NULL default ''
);
CREATE INDEX tplset_name ON imgset_tplset_link (tplset_name(10));
# --------------------------------------------------------

#
# Table structure for table `imgsetimg`
#

CREATE TABLE imgsetimg (
  imgsetimg_id serial,
  imgsetimg_file varchar(50) NOT NULL default '',
  imgsetimg_body bytea NOT NULL,
  imgsetimg_imgset smallint  NOT NULL default '0',
  PRIMARY KEY  (imgsetimg_id)
);
CREATE INDEX imgsetimg_imgset ON imgsetimg (imgsetimg_imgset);
# --------------------------------------------------------

#
# Table structure for table `modules`
#

CREATE TABLE modules (
  mid serial,
  name varchar(150) NOT NULL default '',
  version smallint  NOT NULL default '100',
  last_update int  NOT NULL default '0',
  weight smallint  NOT NULL default '0',
  isactive smallint  NOT NULL default '0',
  dirname varchar(25) NOT NULL default '',
  trust_dirname varchar(25) NOT NULL default '',
  role varchar(15) NOT NULL default '',
  hasmain smallint  NOT NULL default '0',
  hasadmin smallint  NOT NULL default '0',
  hassearch smallint  NOT NULL default '0',
  hasconfig smallint  NOT NULL default '0',
  hascomments smallint  NOT NULL default '0',
  hasnotification smallint  NOT NULL default '0',
  PRIMARY KEY  (mid)
);
ALTER SEQUENCE modules_mid_seq restart with 2;
CREATE INDEX hasmain ON modules (hasmain);
CREATE INDEX hasadmin ON modules (hasadmin);
CREATE INDEX hassearch ON modules (hassearch);
CREATE INDEX hasnotification ON modules (hasnotification);
CREATE INDEX dirname ON modules (dirname);
CREATE INDEX trust_dirname ON modules (trust_dirname);
CREATE INDEX name ON modules (name(15));

# mid=1 is reserved for old XOOPS system module
# --------------------------------------------------------

#
# Table structure for table `newblocks`
#

CREATE TABLE newblocks (
  bid serial,
  mid smallint  NOT NULL default '0',
  func_num smallint  NOT NULL default '0',
  options varchar(255) NOT NULL default '',
  name varchar(150) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  content text NOT NULL,
  side smallint  NOT NULL default '0',
  weight smallint  NOT NULL default '0',
  visible smallint  NOT NULL default '0',
  block_type char(1) NOT NULL default '',
  c_type char(1) NOT NULL default '',
  isactive smallint  NOT NULL default '0',
  dirname varchar(50) NOT NULL default '',
  func_file varchar(50) NOT NULL default '',
  show_func varchar(50) NOT NULL default '',
  edit_func varchar(50) NOT NULL default '',
  template varchar(50) NOT NULL default '',
  bcachetime int  NOT NULL default '0',
  last_modified int  NOT NULL default '0',
  PRIMARY KEY  (bid)
);
CREATE INDEX mid ON newblocks (mid);
CREATE INDEX visible ON newblocks (visible);
CREATE INDEX isactive_visible_mid ON newblocks (isactive,visible,mid);
CREATE INDEX mid_funcnum ON newblocks (mid,func_num);
# --------------------------------------------------------

#
# Table structure for table `online`
#

CREATE TABLE online (
  online_uid int  NOT NULL default '0',
  online_uname varchar(25) NOT NULL default '',
  online_updated int  NOT NULL default '0',
  online_module smallint  NOT NULL default '0',
  online_ip varchar(15) NOT NULL default ''
);
CREATE INDEX online_module ON online (online_module);
# --------------------------------------------------------

#
# Table structure for table `priv_msgs`
#

CREATE TABLE priv_msgs (
  msg_id serial,
  msg_image varchar(100) default NULL,
  subject varchar(255) NOT NULL default '',
  from_userid int  NOT NULL default '0',
  to_userid int  NOT NULL default '0',
  msg_time int  NOT NULL default '0',
  msg_text text NOT NULL,
  read_msg smallint  NOT NULL default '0',
  PRIMARY KEY  (msg_id)
);
CREATE INDEX to_userid ON priv_msgs (to_userid);
CREATE INDEX touseridreadmsg ON priv_msgs (to_userid,read_msg);
CREATE INDEX msgidfromuserid ON priv_msgs (msg_id,from_userid);
# --------------------------------------------------------

#
# Table structure for table `ranks`
#

CREATE TABLE ranks (
  rank_id serial,
  rank_title varchar(50) NOT NULL default '',
  rank_min int  NOT NULL default '0',
  rank_max int  NOT NULL default '0',
  rank_special smallint  NOT NULL default '0',
  rank_image varchar(255) default NULL,
  PRIMARY KEY  (rank_id)
);
CREATE INDEX rank_min ON ranks (rank_min);
CREATE INDEX rank_max ON ranks (rank_max);
CREATE INDEX rankminrankmaxranspecial ON ranks (rank_min,rank_max,rank_special);
CREATE INDEX rankspecial ON ranks (rank_special);
# --------------------------------------------------------

#
# Table structure for table `session`
#

CREATE TABLE session (
  sess_id varchar(32) NOT NULL default '',
  sess_updated int  NOT NULL default '0',
  sess_ip varchar(15) NOT NULL default '',
  sess_data text NOT NULL,
  PRIMARY KEY  (sess_id)
);
CREATE INDEX updated ON session (sess_updated);
# --------------------------------------------------------

#
# Table structure for table `smiles`
#

CREATE TABLE smiles (
  id serial,
  code varchar(50) NOT NULL default '',
  smile_url varchar(100) NOT NULL default '',
  emotion varchar(75) NOT NULL default '',
  display smallint NOT NULL default '0',
  PRIMARY KEY  (id)
);
# --------------------------------------------------------

#
# Table structure for table `tplset`
#

CREATE TABLE tplset (
  tplset_id serial,
  tplset_name varchar(50) NOT NULL default '',
  tplset_desc varchar(255) NOT NULL default '',
  tplset_credits text NOT NULL,
  tplset_created int  NOT NULL default '0',
  PRIMARY KEY  (tplset_id)
);
# --------------------------------------------------------

#
# Table structure for table `tplfile`
#

CREATE TABLE tplfile (
  tpl_id serial,
  tpl_refid smallint  NOT NULL default '0',
  tpl_module varchar(25) NOT NULL default '',
  tpl_tplset varchar(50) NOT NULL default '',
  tpl_file varchar(50) NOT NULL default '',
  tpl_desc varchar(255) NOT NULL default '',
  tpl_lastmodified int  NOT NULL default '0',
  tpl_lastimported int  NOT NULL default '0',
  tpl_type varchar(20) NOT NULL default '',
  PRIMARY KEY  (tpl_id)
);
CREATE INDEX tpl_refid ON tplfile (tpl_refid,tpl_type);
CREATE INDEX tpl_tplset ON tplfile (tpl_tplset,tpl_file(10));
# --------------------------------------------------------

#
# Table structure for table `tplsource`
#

CREATE TABLE tplsource (
  tpl_id int  NOT NULL default '0',
  tpl_source text NOT NULL
);
CREATE INDEX tpl_id ON tplsource (tpl_id);
# --------------------------------------------------------

# RMV-NOTIFY (added two columns)
# Table structure for table `users`
#

CREATE TABLE users (
  uid serial,
  name varchar(60) NOT NULL default '',
  uname varchar(25) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  url varchar(100) NOT NULL default '',
  user_avatar varchar(30) NOT NULL default 'blank.gif',
  user_regdate int  NOT NULL default '0',
  user_icq varchar(15) NOT NULL default '',
  user_from varchar(100) NOT NULL default '',
  user_sig text NOT NULL,
  user_viewemail smallint  NOT NULL default '0',
  actkey varchar(8) NOT NULL default '',
  user_aim varchar(18) NOT NULL default '',
  user_yim varchar(25) NOT NULL default '',
  user_msnm varchar(100) NOT NULL default '',
  pass varchar(32) NOT NULL default '',
  posts int  NOT NULL default '0',
  attachsig smallint  NOT NULL default '0',
  rank smallint  NOT NULL default '0',
  level smallint  NOT NULL default '1',
  theme varchar(100) NOT NULL default '',
  timezone_offset decimal(3,1) NOT NULL default '0.0',
  last_login int  NOT NULL default '0',
  umode varchar(10) NOT NULL default '',
  uorder smallint  NOT NULL default '0',
  notify_method smallint NOT NULL default '1',
  notify_mode smallint NOT NULL default '0',
  user_occ varchar(100) NOT NULL default '',
  bio text NOT NULL,
  user_intrest varchar(150) NOT NULL default '',
  user_mailok smallint  NOT NULL default '1',
  PRIMARY KEY  (uid)
);
CREATE INDEX uname ON users (uname);
CREATE INDEX email ON users (email);
CREATE INDEX uiduname ON users (uid,uname);
CREATE INDEX unamepass ON users (uname,pass);
