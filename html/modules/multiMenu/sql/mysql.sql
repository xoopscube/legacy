CREATE TABLE {prefix}_multimenu01 (
  id int(5) unsigned NOT NULL auto_increment,
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;


CREATE TABLE {prefix}_multimenu02 (
  id int(5) unsigned NOT NULL auto_increment,
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;


CREATE TABLE {prefix}_multimenu03 (
  id int(5) unsigned NOT NULL auto_increment,
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;


CREATE TABLE {prefix}_multimenu04 (
  id int(5) unsigned NOT NULL auto_increment,
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;

CREATE TABLE {prefix}_multimenu05 (
  id int(5) unsigned NOT NULL auto_increment,
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;


CREATE TABLE {prefix}_multimenu06 (
  id int(5) unsigned NOT NULL auto_increment,
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;

CREATE TABLE {prefix}_multimenu07 (
  id int(5) unsigned NOT NULL auto_increment,
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;


CREATE TABLE {prefix}_multimenu08 (
  id int(5) unsigned NOT NULL auto_increment,
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;


CREATE TABLE {prefix}_multimenu99 (
  id int(5) unsigned NOT NULL auto_increment,
  block_id int(5) unsigned NOT NULL default '0',
  parent_id int(5) unsigned NOT NULL default '0',
  title varchar(2048) NOT NULL default '',
  hide tinyint(1) unsigned NOT NULL default '0',
  link varchar(255) default NULL,
  weight tinyint(4) unsigned NOT NULL default '0',
  target varchar(10) default NULL,
  groups varchar(255) default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;


CREATE TABLE {prefix}_multimenu_log (
  uid mediumint(8) NOT NULL default '0',
  id int(5) unsigned NOT NULL default '0',
  PRIMARY KEY (uid)
) ENGINE=MyISAM;
