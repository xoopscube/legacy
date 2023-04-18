<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

if (is_dir(XOOPS_ROOT_PATH . $path)) {

	$module_handler = xoops_getHandler('module');
	$gnaviModule = $module_handler->getByDirname($mydirname);
	$config_handler = xoops_getHandler('config');
	$myConfig = $config_handler->getConfigsByCat(0, $gnaviModule->mid());
	
	$path = '/' . trim($myConfig['gnavi_photospath'], '/') . '/';

	$volumeOptions = ['driverSrc' => __DIR__ . '/driver.class.php', 'driver'    => 'XoopsGnavi', 'mydirname' => $mydirname, 'path'      => '_', 'filePath'  => XOOPS_ROOT_PATH . $path, 'URL'       => _MD_XELFINDER_SITEURL . $path, 'alias'     => $title, 'readonly'  => true, 'icon'      => is_file(XOOPS_MODULE_PATH . '/'.$mydirname.'/images/elfinder_volume_icon.png')? _MD_XELFINDER_MODULE_URL . '/'.$mydirname.'/images/elfinder_volume_icon.png' : '', 'smallImg'  => $myConfig['gnavi_thumbspath']];

}
