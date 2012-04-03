<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

if (is_dir(XOOPS_TRUST_PATH . $path)) {

	require dirname(__FILE__) . '/driver.class.php';

	$volumeOptions = array(
		'driver'    => 'XoopsXelfinder_db',
		'mydirname' => $mydirname,
		'path'      => '1',
		'filePath'  => XOOPS_TRUST_PATH . $path . rawurlencode(substr(XOOPS_URL, strpos(XOOPS_URL, '://') + 3)) . '_' . $mydirname . '_',
		'URL'       => XOOPS_URL . '/modules/' . $mydirname . '/index.php/view/',
		'alias'     => $title,
		'tmbURL'     => XOOPS_URL . '/modules/'.$mydirname.'/cache/tmb/',
		'tmbPath'    => XOOPS_ROOT_PATH . '/modules/'.$mydirname.'/cache/tmb',
		'quarantine' => XOOPS_ROOT_PATH . '/modules/'.$mydirname.'/cache/tmb/.quarantine',
		//'tmbSize'    => 140,
		//'tmbCrop'    => false,
		'uploadAllow'     => array('image'),
		// mimetypes not allowed to upload
		'uploadDeny'      => array('all'),
		// order to proccess uploadAllow and uploadDeny options
		'uploadOrder'     => array('deny', 'allow'),
		// regexp or function name to validate new file name
		'acceptedName'    => '/^[^\/\\?*:|"<>]*[^.\/\\?*:|"<>]$/',
		'defaults' => array('read' => true, 'write' => true, 'hidden' => false, 'locked' => false)
	);

}
