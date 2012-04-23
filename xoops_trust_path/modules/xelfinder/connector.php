<?php

@ set_time_limit(120); // just in case it too long, not recommended for production
ini_set('max_file_uploads', 50);   // allow uploading up to 50 files at once

// needed for case insensitive search to work, due to broken UTF-8 support in PHP
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('mbstring.func_overload', 2);

//if (function_exists('date_default_timezone_set')) {
//	date_default_timezone_set('Europe/Moscow');
//}

//error_reporting(E_ALL | E_STRICT); // Set E_ALL for debuging

define('_MD_ELFINDER_LIB_PATH', XOOPS_TRUST_PATH . '/libs/elfinder');

require _MD_ELFINDER_LIB_PATH . '/php/elFinderConnector.class.php';
require _MD_ELFINDER_LIB_PATH . '/php/elFinder.class.php';
require _MD_ELFINDER_LIB_PATH . '/php/elFinderVolumeDriver.class.php';
require _MD_ELFINDER_LIB_PATH . '/php/elFinderVolumeLocalFileSystem.class.php';

//////////////////////////////////////////////////////
// for XOOPS
if (! defined('XOOPS_MODULE_PATH')) define('XOOPS_MODULE_PATH', XOOPS_ROOT_PATH . '/modules');
if (! defined('XOOPS_MODULE_URL')) define('XOOPS_MODULE_URL', XOOPS_URL . '/modules');

define('_MD_ELFINDER_MYDIRNAME', $mydirname);
if (empty($_REQUEST['xoopsUrl'])) {
	define('_MD_XELFINDER_SITEURL', XOOPS_URL);
	define('_MD_XELFINDER_MODULE_URL', XOOPS_MODULE_URL);
} else {
	define('_MD_XELFINDER_SITEURL', $_REQUEST['xoopsUrl']);
	define('_MD_XELFINDER_MODULE_URL', str_replace(XOOPS_URL, _MD_XELFINDER_SITEURL, XOOPS_MODULE_URL));
	header('Access-Control-Allow-Origin: ' . _MD_XELFINDER_SITEURL);
}

require dirname(__FILE__) . '/class/xelFinder.class.php';
require dirname(__FILE__) . '/class/xelFinderVolumeFTP.class.php';

$isAdmin = false;
$memberUid = 0;
$memberGroups = array(XOOPS_GROUP_ANONYMOUS);
if (is_object($xoopsUser)) {
	if ($xoopsUser->isAdmin()) {
		$isAdmin = true;
	}
	$memberUid = $xoopsUser->getVar('uid');
	$memberGroups = $xoopsUser->getGroups();
}

$extras = array();
$config = $xoopsModuleConfig;
if (strtoupper(_CHARSET) !== 'UTF-8') {
	mb_convert_variables('UTF-8', _CHARSET, $config);
}
// set umask
foreach(array('default', 'users_dir', 'guest_dir', 'group_dir') as $_key) {
	$config[$_key.'_umask'] = strval(dechex(0xfff - intval(strval($config[$_key.'_item_perm']), 16)));
}

$inSpecialGroup = (array_intersect($memberGroups, ( isset($config['special_groups'])? $config['special_groups'] : array() )));

// set uploadAllow
if ($isAdmin) {
	$config['uploadAllow'] = @$config['upload_allow_admin'];
	$config['autoResize'] = @$config['auto_resize_admin'];
} elseif ($inSpecialGroup) {
	$config['uploadAllow'] = @$config['upload_allow_spgroups'];
	$config['auto_resize'] = @$config['auto_resize_spgroups'];
} elseif ($memberUid) {
	$config['uploadAllow'] = @$config['upload_allow_user'];
	$config['autoResize'] = @$config['auto_resize_user'];
} else {
	$config['uploadAllow'] = @$config['upload_allow_guest'];
	$config['autoResize'] = @$config['auto_resize_guest'];
}

$config['uploadAllow'] = trim($config['uploadAllow']);
if (! $config['uploadAllow'] || $config['uploadAllow'] === 'none') {
	$config['uploadAllow'] = array();
} else {
	$config['uploadAllow'] = explode(' ', $config['uploadAllow']);
	$config['uploadAllow'] = array_map('trim', $config['uploadAllow']);
}
$config['autoResize'] = (int)$config['autoResize'];

if (! empty($xoopsConfig['cool_uri'])) {
	$config['URL'] = _MD_XELFINDER_SITEURL . '/' . $mydirname . '/view/';
} else if (empty($config['disable_pathinfo'])) {
	$config['URL'] = _MD_XELFINDER_MODULE_URL . '/' . $mydirname . '/index.php/view/';
} else {
	$config['URL'] = _MD_XELFINDER_MODULE_URL . '/' . $mydirname . '/index.php?page=view&file=';
}

if (! isset($extras[$mydirname.':xelfinder_db'])) {
	$extras[$mydirname.':xelfinder_db'] = array();
}
foreach (
	array('default_umask', 'use_users_dir', 'users_dir_perm', 'users_dir_umask', 'use_guest_dir', 'guest_dir_perm', 'guest_dir_umask',
	      'use_group_dir', 'group_dir_parent', 'group_dir_perm', 'group_dir_umask', 'uploadAllow', 'autoResize', 'URL', 'unzip_lang_value')
	as $_extra
) {
	$extras[$mydirname.':xelfinder_db'][$_extra] = empty($config[$_extra])? '' : $config[$_extra];
}

// load xoops_elFinder
include_once dirname(__FILE__).'/class/xoops_elFinder.class.php';
$xoops_elFinder = new xoops_elFinder();

// Get volumes
$rootVolumes = $xoops_elFinder->getRootVolumes($config['volume_setting'], $extras);

// Add net(FTP) volume
if ($isAdmin && !empty($config['ftp_host']) && !empty($config['ftp_port']) && !empty($config['ftp_user']) && !empty($config['ftp_pass'])) {
	$ftp = array(
		'driver'  => 'FTPx',
		'alias'   => $config['ftp_name'],
		'host'    => $config['ftp_host'],
		'port'    => $config['ftp_port'],
		'path'    => $config['ftp_path'],
		'user'    => $config['ftp_user'],
		'pass'    => $config['ftp_pass'],
		'enable_search' => !empty($config['ftp_search']),
		'tmpPath' => XOOPS_MODULE_PATH . '/'._MD_ELFINDER_MYDIRNAME.'/cache',
		'utf8fix' => true,
		'defaults' => array('read' => true, 'write' => true, 'hidden' => false, 'locked' => false),
		'attributes' => array(
			array(
				'pattern' => '~/\.~',
				'read' => false,
				'write' => false,
				'hidden' => true,
				'locked' => false
			),
		)
	);
	$rootVolumes[] = $ftp;
}

// End for XOOPS
//////////////////////////////////////////////////////


function debug($o) {
	echo '<pre>';
	print_r($o);
}

/**
 * Simple logger function.
 * Demonstrate how to work with elFinder event api.
 *
 * @package elFinder
 * @author Dmitry (dio) Levashov
 **/
class elFinderSimpleLogger {

	/**
	 * Log file path
	 *
	 * @var string
	 **/
	protected $file = '';

	/**
	 * constructor
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function __construct($path) {
		$this->file = $path;
		$dir = dirname($path);
		if (!is_dir($dir)) {
			mkdir($dir);
		}
	}

	/**
	 * Create log record
	 *
	 * @param  string   $cmd       command name
	 * @param  array    $result    command result
	 * @param  array    $args      command arguments from client
	 * @param  elFinder $elfinder  elFinder instance
	 * @return void|true
	 * @author Dmitry (dio) Levashov
	 **/
	public function log($cmd, $result, $args, $elfinder) {
		$log = $cmd.' ['.date('d.m H:s')."]\n";

		if (!empty($result['error'])) {
			$log .= "\tERROR: ".implode(' ', $result['error'])."\n";
		}

		if (!empty($result['warning'])) {
			$log .= "\tWARNING: ".implode(' ', $result['warning'])."\n";
		}

		if (!empty($result['removed'])) {
			foreach ($result['removed'] as $file) {
				// removed file contain additional field "realpath"
				$log .= "\tREMOVED: ".$file['realpath']."\n";
			}
		}

		if (!empty($result['added'])) {
			foreach ($result['added'] as $file) {
				$log .= "\tADDED: ".$elfinder->realpath($file['hash'])."\n";
			}
		}

		if (!empty($result['changed'])) {
			foreach ($result['changed'] as $file) {
				$log .= "\tCHANGED: ".$elfinder->realpath($file['hash'])."\n";
			}
		}

		$this->write($log);
	}

	/**
	 * Write log into file
	 *
	 * @param  string  $log  log record
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function write($log) {

		if (($fp = @fopen($this->file, 'a'))) {
			fwrite($fp, $log."\n");
			fclose($fp);
		}
	}


} // END class

$logger = new elFinderSimpleLogger(XOOPS_TRUST_PATH . '/cache/elfinder.log.txt');

$debug = (! empty($config['debug']));
$opts = array(
	'locale' => 'ja_JP.UTF-8',
	'bind' => array(
		'mkdir mkfile rename duplicate upload rm paste' => array($logger, 'log'),
	),
	'debug' => $debug,

	'roots' => $rootVolumes,
);

error_reporting(0);

// clear output buffer
while( ob_get_level() ) {
	if (! @ ob_end_clean()) break;
}

//header('Access-Control-Allow-Origin: *');
$connector = new elFinderConnector(new xelFinder($opts), true);
$connector->run();
