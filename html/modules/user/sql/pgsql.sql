CREATE TABLE {prefix}_{dirname}_mailjob (
  mailjob_id serial,
  title varchar(255) NOT NULL default '',
  body text NOT NULL,
  from_name varchar(255) default NULL,
  from_email varchar(255) default NULL,
  is_pm smallint NOT NULL default '0',
  is_mail smallint NOT NULL default '0',
  create_unixtime int NOT NULL default '0',
  PRIMARY KEY (mailjob_id)
);

CREATE TABLE {prefix}_{dirname}_mailjob_link (
  mailjob_id int NOT NULL default '0',
  uid int NOT NULL default '0',
  retry smallint NOT NULL default '0',
  message varchar(255) default NULL,
  PRIMARY KEY (mailjob_id, uid)
);
