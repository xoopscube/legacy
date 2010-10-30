<?php

/**
 *
 * XOOPS Cube Legacy 2.2 Upgrade Checker
 * @copyright Copyright 2005-2010 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @usage Put this file on XOOPS_ROOT_PATH and access by your browser.
 */


if(file_exists('preload/SetupAltsysLangMgr.class.php')){
	$contents = file_get_contents('preload/SetupAltsysLangMgr.class.php');
	if(! preg_match('/setting/', $contents)){
		die('Add "$this->_loadLanguage("legacy", "setting");" to (html)/preload/SetupAltsysLangMgr.class.php');
	}
}

if(!file_exists('preload/upgrade22.class.php')){
	echo '<p style="color:red;">Maybe, you should move upgrade22.class.php from extras/preload to (html)/preload</p>';
}

if(! file_exists('mainfile.php')){
	echo 'legacy22_check.php should set the same directory of mainfile.php.';
	die();
}

require_once 'mainfile.php';

$messages = array();

switch(checkMainfile()){
	case 0:
		$messages[] = 'You must add XOOPS_TRUST_PATH setting in (html)/mainfile.php';
		break;
	case 1:
		$messages[] = 'Directory XOOPS_TRUST_PATH('.XOOPS_TRUST_PATH.') is NOT found.';
		break;
}

if(! checkTable()){
	$messages[] = 'You must put extras/extra_preload/upgrade22.class.php in (html)/preload';
}

$modules = checkVersion();
foreach($modules as $mod){
	$messages[] = 'You must upgrade module "'.$mod.'" in module administration page.';
}


$directories = checkDirectory();
foreach($directories as $dir){
	$messages[] = $dir;
}

$files = checkFile();
foreach($files as $file){
	$messages[] = 'You must change file name from "'.$file['from_path'].'" to '. $file['to_path'];
}

$permissions = checkPermission();
foreach($permissions as $perm){
	$messages[] = $perm;
}

if(count($messages)===0){
	$messages[] = 'Congraturation! You are ready for upgrade XCL2.2.<br />Remove (html)/legacy22_check.php file.';
	if(! checkPreload()){
		$messages[] = 'You should remove upgrade22.class.php from (html)/preload';
	}
}

echo '<html><body><ul>';
foreach($messages as $message){
	echo '<li>'.$message.'</li>';
}
echo '</ul></body></html>';


function checkTable()
{
	$db = _check_getDb();

	$checkSql = 'DESC `'.$db->prefix('modules').'`';

	$resultC = $db->queryF($checkSql);
	$cols = $db->fetchArray($resultC);
	while($row = $db->fetchArray($resultC)) {
		if($row['Field']=='trust_dirname'){
			return true;
		}
	}
	return false;
}

function checkPreload()
{
	return file_exists(XOOPS_ROOT_PATH. '/preload/upgrade22.class.php') ? false : true;
}

function checkDirectory()
{
	$ret = array();
	if(is_dir(XOOPS_ROOT_PATH.'/settings')){
		$ret[] = 'Delete '.XOOPS_ROOT_PATH.'/settings directory';
	}
	if(is_dir(! XOOPS_TRUST_PATH.'/settings')){
		$ret[] = 'Copy '.XOOPS_TRUST_PATH.'/settings from XCL22';
	}
	if(is_dir(XOOPS_ROOT_PATH.'/cache')){
		$ret[] = 'Delete '.XOOPS_ROOT_PATH.'/cache directory';
	}
	if(is_dir(! XOOPS_TRUST_PATH.'/cache')){
		$ret[] = 'Copy '.XOOPS_TRUST_PATH.'/cache from XCL22 and change permission 777';
	}
	if(is_dir(XOOPS_ROOT_PATH.'/class/smarty/core')){
		$ret[] = 'Reove '.XOOPS_ROOT_PATH.'/class/smarty/core directory.';
	}
	if(count(glob(XOOPS_ROOT_PATH.'/class/smarty/*.*'))>0){
		$ret[] = 'Reove all files(<span style="color:red;">NOT DIRECTORY</span>) in '.XOOPS_ROOT_PATH.'/class/smarty/*.*';
	}
	if(count(glob(XOOPS_ROOT_PATH.'/class/smarty/plugins/*.php'))>0){
		$ret[] = 'Move all php files in '.XOOPS_ROOT_PATH.'/class/smarty/plugins/ to '.XOOPS_TRUST_PATH.'/libs/smarty/plugins. <br /><span style="color:red;">BE CAREFUL NOT TO OVERWRITE EXISTING FILES !!!</span>';
	}
	if(! is_dir(XOOPS_TRUST_PATH.'/libs/smarty')){
		$ret[] = 'Copy '.XOOPS_TRUST_PATH.'/libs/smarty from XCL22';
	}
	return $ret;
}

function checkFile()
{
	if(file_exists(XOOPS_TRUST_PATH.'/settings/site_custom.ini.php')){
		$ret[] = array('from_path'=>'(xoops_trust_path)/settings/site_custom.ini.php', 'to_path'=>'(xoops_trust_path)/settings/site_custom.ini');
	}
	if(file_exists(XOOPS_TRUST_PATH.'/settings/site_default.ini.php')){
		$ret[] = array('from_path'=>'(xoops_trust_path)/settings/site_default.ini.php', 'to_path'=>'(xoops_trust_path)/settings/site_default.ini');
	}
	return $ret;
}

function checkPermission()
{
	if(! is_writable(XOOPS_TRUST_PATH.'/cache')){
		$ret[] = 'Change directory '.XOOPS_TRUST_PATH.'/cache WRITABLE';
	}
	return $ret;
}

function checkVersion()
{
	$db = _check_getDb();

	$checkSql = 'SELECT * FROM `'.$db->prefix('modules').'`';

	$resultC = $db->queryF($checkSql);
	while($row = $db->fetchArray($resultC)) {
		if($row['dirname']=='legacy' && $row['version']<200){
			$ret[] = 'legacy';
		}
		if($row['dirname']=='legacyRender' && $row['version']<200){
			$ret[] = 'legacyRender';
		}
		if($row['dirname']=='user' && $row['version']<200){
			$ret[] = 'user';
		}
		if($row['dirname']=='stdCache' && $row['version']<200){
			$ret[] = 'stdCache';
		}
	}
	return $ret;
}

function checkMainfile()
{
	if(! defined('XOOPS_TRUST_PATH')){
		return 0;
	}
	if(! is_dir(XOOPS_TRUST_PATH)){
		return 1;
	}
	return 9;
}


/**
 * Create the instance of DataBase class, and set it to member property.
 * @access protected
 */
function _check_getDB()
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
