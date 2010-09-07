<?php

if(! defined('XOOPS_ROOT_PATH')) exit();

if(alterModulesTable()==false){
	echo 'unable to alter modules table for XCL2.2 upgrade';die();
}
if(insertXoopsConfig()==false){
	echo 'unable to insert cool_uri config for XCL2.2 upgrade';die();
}

function alterModulesTable()
{
	$db = _getDb();

	$checkSql = 'DESC `'.$db->prefix('modules').'`';

	$alterSql = 'ALTER TABLE `'.$db->prefix('modules').'` ADD `role` VARCHAR( 15 ) NOT NULL default "" AFTER `dirname`, ADD `trust_dirname` VARCHAR( 25 ) NOT NULL default "" AFTER `dirname`';

	$resultC = $db->queryF($checkSql);
	$cols = $db->fetchArray($resultC);
	while($row = $db->fetchArray($resultC)) {
		if($row['Field']=='trust_dirname'){
			return true;
		}
	}
	return $db->queryF($alterSql);
}

function insertXoopsConfig()
{
	$db = _getDB();

	$checkSql = 'SELECT COUNT(*) FROM `'.$db->prefix('config').'` WHERE `conf_name`="cool_uri"';
	$insertSql = 'INSERT INTO `'.$db->prefix('config').'` VALUES (48,0,1,"cool_uri","_MD_AM_COOLURI","0","_MD_AM_COOLURIDSC","yesno","int",17)';

	$resultC = $db->queryF($checkSql);
	if($row = $db->fetchArray($resultC)){
		if($row['COUNT(*)']>0){
			return true;
		}
	}
	return $db->queryF($insertSql);
}

/**
 * Create the instance of DataBase class, and set it to member property.
 * @access protected
 */
function _getDB()
{
	require_once XOOPS_ROOT_PATH . '/class/logger.php';
	$root = XCube_Root::getSingleton();
	if(!defined('XOOPS_DB_CHKREF'))
		define('XOOPS_DB_CHKREF', 1);
	else
		define('XOOPS_DB_CHKREF', 0);

	require_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';

	if ($root->getSiteConfig('Legacy', 'AllowDBProxy') == true) {
		if (xoops_getenv('REQUEST_METHOD') != 'POST' || !xoops_refcheck(XOOPS_DB_CHKREF)) {
			define('XOOPS_DB_PROXY', 1);
		}
	}
	elseif (xoops_getenv('REQUEST_METHOD') != 'POST') {
		define('XOOPS_DB_PROXY', 1);
	}

	return XoopsDatabaseFactory::getDatabaseConnection();
}

?>
