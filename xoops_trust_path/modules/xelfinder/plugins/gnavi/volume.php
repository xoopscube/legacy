<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

if (is_dir(XOOPS_ROOT_PATH . $path)) {

	require_once dirname(dirname(__FILE__)) . '/myalbum/driver.class.php';
	
	$module_handler = xoops_gethandler('module');
	$gnaviModule = $module_handler->getByDirname($mydirname);
	$config_handler = xoops_gethandler('config');
	$myConfig = $config_handler->getConfigsByCat(0, $gnaviModule->mid());
	
	$path = '/' . trim($myConfig['gnavi_photospath'], '/') . '/';

	$volumeOptions = array(
		'driver'    => 'XoopsMyalbum',
		'mydirname' => $mydirname,
		'path'      => '_',
		'filePath'  => XOOPS_ROOT_PATH . $path,
		'URL'       => XOOPS_URL . $path,
		'alias'     => $title,
		'smallImg'  => $myConfig['gnavi_thumbspath']
	);

}
