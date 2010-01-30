=================================================
Title:  Check Block Table
Date:   2007-10-10
Author: Kenichi OHWADA
URL:    http://linux2.ohwada.net/
Email:  webmaster@ohwada.net
=================================================

To the modules of XOOPS,
this program compares defined in xoops_version.php and stored in the block table.


* install
copy "check_mysql.php" in XOOPS_ROOT_PATH directory

* usage
login by the administrator.
accesse in check_blocks.php.

if this program shows unmatch messagse,
firstly execute the module update.

if cannot correct,
execute "Remove Block" which delete the records in the block table,
and then execute the module update.

* coverage version
this progarm in tested in the following version.

- XOOPS 2.0.16aJP
- XOOPS Cube 2.1.2
- XOOPS 2.0.17

* enclosed files
- check_blocks.php
