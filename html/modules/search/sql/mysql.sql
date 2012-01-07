#
# `xoops_search`
#

CREATE TABLE search (
  `mid` int(8) NOT NULL default '0',
  `notshow` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `mid_2` (`mid`)
) ENGINE = MYISAM;
