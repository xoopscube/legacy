CREATE TABLE `{prefix}_{dirname}_store`
(
    `sid`          int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name`         varchar(191)     NOT NULL DEFAULT '',
    `contents`     varchar(191)     NOT NULL DEFAULT '',
    `addon_url`    varchar(191)     NOT NULL DEFAULT '',
    `setting_type` int(11) unsigned NOT NULL DEFAULT '0',
    `reg_unixtime` int(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`sid`)
) ENGINE=InnoDB;

CREATE TABLE `{prefix}_{dirname}_modulestore`
(
    `id`            int(11) unsigned    NOT NULL AUTO_INCREMENT,
    `sid`           int(11) unsigned    NOT NULL DEFAULT '0',
    `dirname`       varchar(191)        NOT NULL DEFAULT '',
    `trust_dirname` varchar(191)                 DEFAULT '',
    `version`       smallint(5) unsigned         DEFAULT '100',
    `license`       varchar(191)        NOT NULL DEFAULT '',
    `required`      varchar(191)        NOT NULL DEFAULT '',
    `last_update`   int(10) unsigned             DEFAULT '0',
    `target_key`    varchar(191)        NOT NULL DEFAULT '',
    `target_type`   varchar(191)        NOT NULL DEFAULT '',
    `replicatable`  tinyint(1) unsigned NOT NULL DEFAULT '0',
    `description`   varchar(191)        NOT NULL DEFAULT '',
    `unzipdirlevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `addon_url`     varchar(191)        NOT NULL DEFAULT '',
    `detail_url`    varchar(191)        NOT NULL DEFAULT '',
    `options`       text,
    `isactive`      int(11)             NOT NULL DEFAULT '-1',
    `hasupdate`     tinyint(1)          NOT NULL DEFAULT '0',
    `contents`      varchar(191)        NOT NULL DEFAULT '',
    `category_id`   int(11)             NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY sid (sid),
    KEY dirname (dirname)
) ENGINE=InnoDB;

