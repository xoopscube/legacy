<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

if (is_dir(XOOPS_ROOT_PATH . $path)) {

	$volumeOptions = array(
		'driverSrc' => dirname(dirname(dirname(__FILE__))) . '/class/xelFinderVolumeFTP.class.php',
		'driver'  => 'FTPx',
		'alias'   => $title,
		'host'    => $mConfig['ftp_host'],
		'port'    => $mConfig['ftp_port'],
		'path'    => XOOPS_ROOT_PATH . $path,
		'user'    => $mConfig['ftp_user'],
		'pass'    => $mConfig['ftp_pass'],
		'disabled' => !empty($mConfig['ftp_search'])? array() : array('search'),
		'is_local'=> true,
		'tmpPath' => XOOPS_MODULE_PATH . '/'.$mDirname.'/cache',
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
		),
		'mimeDetect' => 'internal'
	);

}
