# phpMyAdmin MySQL-Dump
# version 2.3.2
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Jan 09, 2003 at 12:37 AM
# Server version: 3.23.54
# PHP Version: 4.2.2
# Database : `xoops2`


#
# Dumping data for table `bannerclient`
#

INSERT INTO bannerclient VALUES (1, 'Sample client', 'XOOPS Cube Project Team', '', '', '', '');

ALTER SEQUENCE bannerclient_cid_seq restart with 2;

#
# Dumping data for table `bannerfinish`
#


#
# Dumping data for table `comments`
#


#
# Dumping data for table `configcategory`
#

INSERT INTO configcategory VALUES (1, '_MD_AM_GENERAL', 0);
INSERT INTO configcategory VALUES (4, '_MD_AM_CENSOR', 0);
INSERT INTO configcategory VALUES (5, '_MD_AM_SEARCH', 0);
INSERT INTO configcategory VALUES (6, '_MD_AM_MAILER', 0);

ALTER SEQUENCE configcategory_confcat_id_seq restart with 7;

#
# Dumping data for table `configoption`
#

INSERT INTO configoption VALUES (1, '_MD_AM_DEBUGMODE1', '1', 13);
INSERT INTO configoption VALUES (2, '_MD_AM_DEBUGMODE2', '2', 13);
INSERT INTO configoption VALUES (3, '_NESTED', 'nest', 32);
INSERT INTO configoption VALUES (4, '_FLAT', 'flat', 32);
INSERT INTO configoption VALUES (5, '_THREADED', 'thread', 32);
INSERT INTO configoption VALUES (6, '_OLDESTFIRST', '0', 33);
INSERT INTO configoption VALUES (7, '_NEWESTFIRST', '1', 33);
INSERT INTO configoption VALUES (14, '_MD_AM_DEBUGMODE3', '3', 13);
INSERT INTO configoption VALUES (23, '_MD_AM_DEBUGMODE0', '0', 13);

INSERT INTO configoption VALUES (24,'PHP mail()','mail',64);
INSERT INTO configoption VALUES (25,'sendmail','sendmail',64);
INSERT INTO configoption VALUES (26,'SMTP','smtp',64);
INSERT INTO configoption VALUES (27,'SMTPAuth','smtpauth',64);

ALTER SEQUENCE configoption_confop_id_seq restart with 28;

#
# Dumping data for table `image`
#


#
# Dumping data for table `imagebody`
#


#
# Dumping data for table `imagecategory`
#


#
# Dumping data for table `imgset`
#

INSERT INTO imgset VALUES (1, 'default', 0);

ALTER SEQUENCE imgset_imgset_id_seq restart with 2;

#
# Dumping data for table `imgset_tplset_link`
#

INSERT INTO imgset_tplset_link VALUES (1, 'default');

#
# Dumping data for table `online`
#


#
# Dumping data for table `priv_msgs`
#


#
# Dumping data for table `session`
#
