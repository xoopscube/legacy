<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

if (is_dir(XOOPS_ROOT_PATH . $path)) {

	$volumeOptions = array(
		'driverSrc' => dirname(__FILE__) . '/driver.class.php',
		'driver'    => 'XoopsD3diary',
		'mydirname' => $mydirname,
		'path'      => '_',
		'filePath'  => XOOPS_ROOT_PATH . $path,
		'URL'       => _MD_XELFINDER_SITEURL . $path,
		'alias'     => $title,
		'readonly'  => true,
		'icon'       => is_file(XOOPS_MODULE_PATH.'/'.$mydirname.'/images/elfinder_volume_icon.png')? _MD_XELFINDER_MODULE_URL.'/'.$mydirname.'/images/elfinder_volume_icon.png' : '',
		'smallImg'  => '/uploads/thumb'
	);

}
