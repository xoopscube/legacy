#
# Table structure for table `gnavi_cat`
#

CREATE TABLE cat (
  cid int(5) unsigned NOT NULL auto_increment,
  pid int(5) unsigned NOT NULL default '0',
  title varchar(50) NOT NULL default '',
  imgurl varchar(150) NOT NULL default '',
  kmlurl varchar(150) NOT NULL default '',
  weight int(5) unsigned NOT NULL default 0,
  depth int(5) unsigned NOT NULL default 0,
  description text,
  allowed_ext varchar(255) NOT NULL default 'jpg|jpeg|gif|png',
  lat double(9,6) NOT NULL default '0',
  lng double(9,6) NOT NULL default '0',
  zoom int(2) NOT NULL default '0',
  mtype varchar(30) NOT NULL default '',
  icd int(5) unsigned NOT NULL default '0',
  PRIMARY KEY (cid),
  KEY (weight),
  KEY (depth),
  KEY (pid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gnavi_photos`
#

CREATE TABLE photos (
  lid int(11) unsigned NOT NULL auto_increment,
  cid int(5) unsigned NOT NULL default '0',
  cid1 int(5) unsigned NOT NULL default '0',
  cid2 int(5) unsigned NOT NULL default '0',
  cid3 int(5) unsigned NOT NULL default '0',
  cid4 int(5) unsigned NOT NULL default '0',

  title varchar(255) NOT NULL default '',
  ext varchar(10) NOT NULL default '',
  res_x int(11) NOT NULL default '0',
  res_y int(11) NOT NULL default '0',
  caption varchar(255) NOT NULL default '',
  ext1 varchar(10) NOT NULL default '',
  res_x1 int(11) NOT NULL default '0',
  res_y1 int(11) NOT NULL default '0',
  caption1 varchar(255) NOT NULL default '',
  ext2 varchar(10) NOT NULL default '',
  res_x2 int(11) NOT NULL default '0',
  res_y2 int(11) NOT NULL default '0',
  caption2 varchar(255) NOT NULL default '',
  submitter int(11) unsigned NOT NULL default '0',
  status tinyint(2) NOT NULL default '0',
  date int(10) NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  rating double(6,4) NOT NULL default '0.0000',
  votes int(11) unsigned NOT NULL default '0',
  comments int(11) unsigned NOT NULL default '0',
  poster_name varchar(60) NOT NULL default '',

  url varchar(255) NOT NULL default '',
  rss varchar(255) NOT NULL default '',
  tel varchar(20) NOT NULL default '',
  fax varchar(20) NOT NULL default '',
  zip varchar(20) NOT NULL default '',
  address varchar(255) NOT NULL default '',
  lat double(9,6) NOT NULL default '0',
  lng double(9,6) NOT NULL default '0',
  zoom int(2) NOT NULL default '0',
  mtype varchar(30) NOT NULL default '',
  icd int(5) unsigned NOT NULL default '0',

  PRIMARY KEY (lid),
  KEY (cid),
  KEY (date),
  KEY (status),
  KEY (title),
  KEY (submitter)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gnavi_icons`
#

CREATE TABLE icons (
  icd int(5) unsigned NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  ext varchar(5) NOT NULL default '',
  shadow_ext varchar(5) NOT NULL default '',
  x int(4) NOT NULL default '0',
  y int(4) NOT NULL default '0',
  shadow_x int(4) NOT NULL default '0',
  shadow_y int(4) NOT NULL default '0',
  Anchor_x int(4) NOT NULL default '0',
  Anchor_y int(4) NOT NULL default '0',
  infoWindowAnchor_x int(4) NOT NULL default '0',
  infoWindowAnchor_y int(4) NOT NULL default '0',
  PRIMARY KEY (icd)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gnavi_text`
#

CREATE TABLE text (
  lid int(11) unsigned NOT NULL default '0',
  description text,
  arrowhtml tinyint(1) NOT NULL default '0',
  addinfo text,
  PRIMARY KEY lid (lid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `gnavi_votedata`
#

CREATE TABLE votedata (
  ratingid int(11) unsigned NOT NULL auto_increment,
  lid int(11) unsigned NOT NULL default '0',
  ratinguser int(11) unsigned NOT NULL default '0',
  rating tinyint(3) unsigned NOT NULL default '0',
  ratinghostname varchar(60) NOT NULL default '',
  ratingtimestamp int(10) NOT NULL default '0',
  PRIMARY KEY  (ratingid),
  KEY (lid),
  KEY (ratinguser),
  KEY (ratinghostname)
) ENGINE=MyISAM;

