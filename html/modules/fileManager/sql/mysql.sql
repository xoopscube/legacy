# --------------------------------------------------------
# Table structure for table {prefix}_{dirname}_token
# --------------------------------------------------------
CREATE TABLE `{prefix}_{dirname}_token` (
  `expire` INT(10) NOT NULL ,
  `token` VARCHAR(32) NOT NULL ,
  `uid` MEDIUMINT(8) NOT NULL default '0' ,
  `ipaddress` VARCHAR(15) NOT NULL ,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM ;
