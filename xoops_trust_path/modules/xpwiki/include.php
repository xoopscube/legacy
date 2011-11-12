<?php
//
// Created on 2006/10/03 by nao-pon http://hypweb.net/
// $Id: include.php,v 1.14 2010/05/19 11:23:55 nao-pon Exp $
//

if (! defined('_XPWIKI_FILES_LOADED')) {
	$mytrustdirpath = dirname( __FILE__ );

	define('_XPWIKI_FILES_LOADED', TRUE);

	// For PHP >= 5.3
	if (error_reporting() > 6143) {
		error_reporting(E_ALL & ~E_DEPRECATED);
	}

	if (! function_exists('XC_CLASS_EXISTS')) {
		include dirname(dirname($mytrustdirpath)) . '/class/hyp_common/XC_CLASS_EXISTS.inc.php';
	}

	// Load & check a class HypCommonFunc
	if(! XC_CLASS_EXISTS('HypCommonFunc')) {
		include dirname(dirname($mytrustdirpath)) . '/class/hyp_common/hyp_common_func.php';
	}

	// Set 'memory_limit'
	// 64M = 64 * 1024 * 1024 = 67108864 bytes
	if (HypCommonFunc::return_bytes(ini_get('memory_limit')) < 67108864) ini_set('memory_limit', '64M');

	include($mytrustdirpath.'/class/xpwiki.php');

	include($mytrustdirpath.'/class/root.php');

	include($mytrustdirpath.'/class/func/base_func.php');
	include($mytrustdirpath.'/class/func/pukiwiki_func.php');
	if (extension_loaded('zlib')) {
		include($mytrustdirpath.'/class/func/backup_gzip.php');
	} else {
		include($mytrustdirpath.'/class/func/backup_text.php');
	}
	include($mytrustdirpath.'/class/func/xoops_wrapper.php');
	include($mytrustdirpath.'/class/func/xpwiki_func.php');

	include($mytrustdirpath.'/class/extension.php');

	include($mytrustdirpath.'/class/plugin.php');

	include($mytrustdirpath.'/class/convert_html.php');

	include($mytrustdirpath.'/class/make_link.php');

	include($mytrustdirpath.'/class/diff.php');

	include($mytrustdirpath.'/class/config.php');

	include($mytrustdirpath.'/plugin/attach.inc.php');
	include($mytrustdirpath.'/class/attach.php');

	// add compat functions
	include($mytrustdirpath.'/include/compat.php');
}
?>