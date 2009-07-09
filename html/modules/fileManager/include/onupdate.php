<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

if( ! defined( "XOOPS_ROOT_PATH" ) ) exit ;

function xoops_module_update_fileManager( $module, $prev_version ){
	global $xoopsDB;

	$update = true;
	if ( $prev_version < 98 ){
		// version 0.98
		// original token
		// SWF uploader does not support basic authentication.
		// change to cookie & DB token
		$sql = sprintf( "CREATE TABLE %s (`token` VARCHAR(32) NOT NULL ,`expire` INT(10) NOT NULL default '0', `uid` MEDIUMINT(8) NOT NULL default '0' , `ipaddress` VARCHAR(15) NOT NULL , PRIMARY KEY (`token`)) ENGINE=MyISAM ;" , $xoopsDB->prefix( "filemanager_token" ) );
		$result = $xoopsDB->query( $sql );
	}
	print mysql_error();

	return $update;
}


?>
