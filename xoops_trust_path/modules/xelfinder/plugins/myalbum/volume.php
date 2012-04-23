<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

if (is_dir(XOOPS_ROOT_PATH . $path)) {

	require_once dirname(__FILE__) . '/driver.class.php';

	$volumeOptions = array(
		'driver'    => 'XoopsMyalbum',
		'mydirname' => $mydirname,
		'path'      => '_',
		'filePath'  => XOOPS_ROOT_PATH . $path,
		'URL'       => _MD_XELFINDER_SITEURL . $path,
		'alias'     => $title,
		'smallImg'  => '/uploads/thumb'
	);

}
