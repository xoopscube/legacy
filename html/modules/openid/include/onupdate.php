<?php
/**
 * On module update function
 * @version $Rev$
 * @link $URL$
 */

function xoops_module_update_openid ( $module ) {
	$db =& Database::getInstance();
	
	$table = $db->prefix('openid_localid');
    $newTable = $db->prefix('openid_identifier');
    if($db->query('SELECT `id` FROM ' . $table)) {
        // Update from original by Nat.
        $db->query(
'ALTER TABLE `' . $table . '`
 RENAME TO `' . $newTable . "`,
 ADD `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' AFTER `openid`,
 ADD `omode` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `uid`"
        );
        if ($result = $db->query('SELECT `id`, `localid` FROM ' . $newTable)) {
            $userTable = $db->prefix('users');
            while ($row = $db->fetchArray($result)) {
                $sql = 'SELECT `uid` FROM `' . $userTable
                     . '` WHERE `uname`=' . $db->quoteString($row['localid']);
                if ($result_user = $db->query($sql)
                  && $user_row = $db->fetchArray($result_user)) {
                    $sql = 'UPDATE `' . $newTable . '` SET `uid`='
                         . $user_row['uid'] . ', `omode`=3 WHERE `id`=' . $row['id'];
                    $db->query($sql);
                }
            }
        }
        $db->query(
'ALTER TABLE `' . $newTable . "`
 DROP `localid`,
 CHANGE `openid` `claimed_id` VARCHAR(255) NOT NULL DEFAULT '',
 ADD `local_id` VARCHAR(255) NOT NULL DEFAULT '' AFTER `omode`,
 ADD UNIQUE (`claimed_id`),
 ADD KEY (`uid`),
 ADD KEY (`omode`),
 ADD KEY (`local_id`)"
        );
    } else if ($db->query('SELECT `openid` FROM ' . $table)) {
        //ver 0.22 rev 168 - ver 0.24 rev 238
        $db->query(
'ALTER TABLE `' . $table . '`
 RENAME TO `' . $newTable . "`,
 DROP PRIMARY KEY,
 ADD `id` mediumint(8) unsigned NOT NULL auto_increment FIRST,
 ADD PRIMARY KEY (`id`),
 CHANGE `openid` `claimed_id` VARCHAR(255) NOT NULL DEFAULT '',
 CHANGE `localid` `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' AFTER `claimed_id`,
 ADD `omode` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `uid`,
 ADD `local_id` VARCHAR(255) NOT NULL DEFAULT '' AFTER `omode`,
 ADD UNIQUE (`claimed_id`),
 ADD KEY (`uid`),
 ADD KEY (`omode`),
 ADD KEY (`local_id`)"
         );
        $db->query('UPDATE `' . $newTable . '` SET `omode`=3');
    } else if ($db->query('SELECT `id` FROM ' . $newTable)) {
        // ver 0.28 or later
    } else if ($db->query('SELECT `isactive` FROM ' . $newTable)) {
        //ver 0.24 rev 246 - rev 254
        $db->query(
'ALTER TABLE `' . $newTable . '`
 DROP PRIMARY KEY,
 ADD `id` mediumint(8) unsigned NOT NULL auto_increment FIRST,
 ADD PRIMARY KEY (`id`),
 ADD UNIQUE (`claimed_id`),
 CHANGE `isactive` `omode` TINYINT(1) UNSIGNED NOT NULL default \'0\''
        );
        $db->query('UPDATE `' . $newTable . '` SET `omode`=3, `modified`=NOW() WHERE `omode`=1');
    } else if ($db->query('SELECT `omode` FROM ' . $newTable)) {
        // ver 0.25 - 0.27
        $db->query(
'ALTER TABLE `' . $newTable . '`
 DROP PRIMARY KEY,
 ADD `id` mediumint(8) unsigned NOT NULL auto_increment FIRST,
 ADD PRIMARY KEY (`id`),
 ADD UNIQUE (`claimed_id`)'
        );
    } else {
        $flag = false;
        if ($result = $db->query('SHOW INDEX FROM ' . $newTable)) {
            while ($row = $db->fetchArray($result)) {
                if (@$row['Key_name'] == 'uid') {
                    //ver 0.24 rev 245
                    $flag = true;
                    break;
                }
            }
        }
        if ($flag) {
            //ver 0.24 rev 245
            $db->query(
'ALTER TABLE `' . $newTable . "`
 DROP PRIMARY KEY,
 ADD `id` mediumint(8) unsigned NOT NULL auto_increment FIRST,
 ADD PRIMARY KEY (`id`),
 ADD UNIQUE (`claimed_id`)',
 ADD `omode` TINYINT(1) UNSIGNED NOT NULL default '0',
 ADD KEY (`omode`)"
            );
            $db->query('UPDATE `' . $newTable . '` SET `omode`=3');
        } else {
            //ver 0.24 rev 241
            $db->query(
'ALTER TABLE `' . $newTable . "`
 DROP INDEX local_id,
 DROP PRIMARY KEY,
 ADD `id` mediumint(8) unsigned NOT NULL auto_increment FIRST,
 ADD PRIMARY KEY (`id`),
 ADD UNIQUE (`claimed_id`)',
 ADD `omode` TINYINT(1) UNSIGNED NOT NULL default '0',
 ADD KEY (`uid`),
 ADD KEY (`omode`),
 ADD KEY (`local_id`)"
            );
            $db->query('UPDATE `' . $newTable . '` SET `omode`=3');
        }
    }

	$table = $db->prefix('openid_nonce');
	$query = 'SELECT count(*) FROM ' . $table;
	if(! $db->query($query)) {
		$db->query(
"CREATE TABLE `{$table}` (
  `server_url` varchar(255) NOT NULL default '',
  `timestamp` int(10) unsigned NOT NULL default '0',
  `salt` varchar(40) NOT NULL default '',
  UNIQUE KEY `server_url` (`server_url`,`timestamp`,`salt`)
) TYPE=MyISAM /*!40100 DEFAULT CHARACTER SET=binary*/;"
		);
	}
	
	$table = $db->prefix('openid_assoc');
	$query = 'SELECT count(*) FROM ' . $table;
	if(! $db->query($query)) {
		$db->query(
"CREATE TABLE `{$table}` (
  `server_url` varchar(255) NOT NULL default '',
  `handle` varchar(255) NOT NULL default '',
  `secret` blob NOT NULL,
  `issued` int(10) unsigned NOT NULL default '0',
  `lifetime` int(10) unsigned NOT NULL default '0',
  `assoc_type` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`server_url`(245),`handle`)
) TYPE=MyISAM; /*!40100 DEFAULT CHARACTER SET=binary*/"
		);
	}

	// Drop Old Style Filter Table
    $table = $db->prefix('openid_filter');
    if ($db->query('SELECT `owner` FROM ' . $table)) {
        $db->query('DROP TABLE ' . $table);
    }

	// Add Filter Table
	$query = 'SELECT count(*) FROM ' . $table;
	if(! $db->query($query)) {
		$db->query(
"CREATE TABLE `{$table}` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `pattern` varchar(255) NOT NULL default '',
  `auth` tinyint(1) unsigned NOT NULL default '0',
  `groupid` varchar(32) NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `pattern` (`pattern`)
) TYPE=MyISAM;"
		);
	} else {
        // Enable Multi Group ID
        $query = "SHOW COLUMNS FROM `{$table}` LIKE 'groupid'";
        if (($result = $db->query($query)) && ($row = $db->fetchArray($result)) && (@$row['Type'] != 'varchar(32)')) {
            $query = "ALTER TABLE `{$table}` CHANGE `groupid` `groupid` varchar(32) NOT NULL default ''";
            $db->query($query);
        }

        if ($db->query('SELECT `checkpoint` FROM ' . $table)) {
            $db->query("ALTER TABLE `{$table}` DROP `checkpoint`");
        }
	}

    // Drop Old Style Extension Table
    $table = $db->prefix('openid_extension');
    if ($db->query('SELECT `owner` FROM ' . $table)) {
        $db->query('DROP TABLE ' . $table);
    }

    // Add Extension Table
    if (!$db->query('SELECT count(*) FROM ' . $table)) {
        $db->query(
"CREATE TABLE `{$table}` (
  `dirname` varchar(25) NOT NULL default '',
  `options` varchar(255) NOT NULL default '',
  PRIMARY KEY (`dirname`)
) TYPE=MyISAM;");
    }

    // Add Button Table
    $table = $db->prefix('openid_buttons');
    if (!$db->query('SELECT count(*) FROM ' . $table)) {
        $db->query(
"CREATE TABLE `{$table}` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `range` varchar(5) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY (`id`)
) TYPE=MyISAM;");
    }

    return TRUE;
}