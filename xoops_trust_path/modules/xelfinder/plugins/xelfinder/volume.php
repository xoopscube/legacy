<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

$_path = '';
if (strpos($path, '/[trust]') === 0) {
	$path = str_replace('/[trust]', XOOPS_TRUST_PATH, $path);
} else {
	$_path = $path;
	$path = XOOPS_ROOT_PATH . $path;
}
if (is_dir($path)) {

	$volumeOptions = array(
		'driverSrc'  => dirname(__FILE__) . '/driver.class.php',
		'driver'     => 'XoopsXelfinder',
		'mydirname'  => $mydirname,
		'path'       => $path,
		'URL'        => $_path? _MD_XELFINDER_SITEURL . $_path : '',
		'alias'      => $title,
		'tmbURL'     => _MD_XELFINDER_MODULE_URL . '/'.$mydirname.'/cache/tmb/',
		'tmbPath'    => XOOPS_MODULE_PATH . '/'.$mydirname.'/cache/tmb',
		'quarantine' => XOOPS_TRUST_PATH.'/cache',
		//'tmbSize'    => 140,
		//'tmbCrop'    => false,
		// 'startPath'  => '../files/test',
		// 'deep' => 3,
		// 'separator' => ':',
		'uploadAllow'     => ($isAdmin? array('all') : array('image')),
		// mimetypes not allowed to upload
		'uploadDeny'      => ($isAdmin? array('') : array('all')),
		// order to proccess uploadAllow and uploadDeny options
		'uploadOrder'     => array('deny', 'allow'),
		// regexp or function name to validate new file name
		'acceptedName'    => ($isAdmin? '/^[^\/\\?*:|"<>]*[^.\/\\?*:|"<>]$/' : '/^(?:\w+|\w[\w\s\.\%\-\(\)\[\]]*\.(?:txt|gif|jpeg|jpg|png))$/ui'),
		'defaults' => array('read' => true, 'write' => true)
	);

}
