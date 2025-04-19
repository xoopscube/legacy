CREATE TABLE csp_violations (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  document_uri varchar(255) NOT NULL,
  violated_directive varchar(255) NOT NULL,
  blocked_uri varchar(255) NOT NULL,
  source_file varchar(255) DEFAULT NULL,
  line_number int(10) unsigned DEFAULT NULL,
  column_number int(10) unsigned DEFAULT NULL,
  referrer varchar(255) DEFAULT NULL,
  user_agent varchar(255) DEFAULT NULL,
  ip varchar(45) NOT NULL,
  created int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY created (created)
) ENGINE=InnoDB;

CREATE TABLE log
(
    lid         mediumint(8) unsigned NOT NULL auto_increment,
    uid         mediumint(8) unsigned NOT NULL default 0,
    ip          varchar(191)          NOT NULL default '0.0.0.0',
    type        varchar(191)          NOT NULL default '',
    agent       varchar(191)          NOT NULL default '',
    description text,
    extra       text,
    timestamp   int(10) unsigned NOT NULL default 0,
    uri         varchar(191)          NOT NULL default '',
    PRIMARY KEY (lid),
    KEY (uid),
    KEY (ip),
    KEY (type),
    KEY (timestamp)
) ENGINE=InnoDB;

CREATE TABLE access
(
    ip                varchar(191) NOT NULL default '0.0.0.0',
    request_uri       varchar(191) NOT NULL default '',
    malicious_actions varchar(191) NOT NULL default '',
    expire            int          NOT NULL default 0,
    KEY (ip),
    KEY (request_uri),
    KEY (malicious_actions),
    KEY (expire)
) ENGINE=InnoDB;
