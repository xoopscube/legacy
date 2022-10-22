CREATE TABLE `{prefix}_{dirname}_mailjob`
(
    `mailjob_id`      int(10)      NOT NULL auto_increment,
    `title`           varchar(191) NOT NULL default '',
    `body`            text         NOT NULL,
    `from_name`       varchar(191)          default NULL,
    `from_email`      varchar(191)          default NULL,
    `is_pm`           tinyint(1)   NOT NULL default '0',
    `is_mail`         tinyint(1)   NOT NULL default '0',
    `create_unixtime` int(10)      NOT NULL default '0',
    PRIMARY KEY (`mailjob_id`)
) ENGINE=InnoDB;

CREATE TABLE `{prefix}_{dirname}_mailjob_link`
(
    `mailjob_id` int(10)      NOT NULL default '0',
    `uid`        mediumint(8) NOT NULL default '0',
    `retry`      tinyint(3)   NOT NULL default '0',
    `message`    varchar(191)          default NULL,
    PRIMARY KEY (`mailjob_id`, `uid`)
) ENGINE=InnoDB;
