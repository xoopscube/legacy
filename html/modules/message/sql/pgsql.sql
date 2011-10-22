CREATE TABLE {prefix}_{dirname}_inbox (
  inbox_id serial NOT NULL,
  uid integer DEFAULT '0' NOT NULL,
  from_uid integer DEFAULT '0' NOT NULL,
  title varchar(100) NOT NULL,
  message text NOT NULL,
  utime integer DEFAULT '0' NOT NULL,
  is_read smallint DEFAULT '0' NOT NULL,
  uname varchar(255) DEFAULT '' NOT NULL,
  CONSTRAINT PRI_{prefix}_{dirname}_inbox PRIMARY KEY (inbox_id)
);
CREATE INDEX IND_{prefix}_{dirname}_inbox ON {prefix}_{dirname}_inbox (
  uid
);

CREATE TABLE {prefix}_{dirname}_outbox (
  outbox_id serial NOT NULL,
  uid integer DEFAULT '0' NOT NULL,
  to_uid integer DEFAULT '0' NOT NULL,
  title varchar(100) NOT NULL,
  message text NOT NULL,
  utime integer DEFAULT '0' NOT NULL,
  CONSTRAINT PRI_{prefix}_{dirname}_outbox PRIMARY KEY (outbox_id)
);
CREATE INDEX IND_{prefix}_{dirname}_outbox ON {prefix}_{dirname}_outbox (
  uid
);

CREATE TABLE {prefix}_{dirname}_users (
  uid integer DEFAULT '0' NOT NULL,
  usepm smallint DEFAULT '0' NOT NULL,
  tomail smallint DEFAULT '0' NOT NULL,
  viewmsm smallint DEFAULT '0' NOT NULL,
  pagenum smallint DEFAULT '0' NOT NULL,
  blacklist varchar(255) DEFAULT '' NOT NULL,
  CONSTRAINT PRI_{prefix}_{dirname}_users PRIMARY KEY (uid)
);
