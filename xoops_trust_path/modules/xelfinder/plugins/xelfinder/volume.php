<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 * $Id: volume.php,v 1.1 2012/01/20 13:32:02 nao-pon Exp $
 */

if (is_dir(XOOPS_ROOT_PATH . $path)) {

	$volumeOptions = array(
		'driver'     => 'LocalFileSystem',
		'mydirname'  => $mydirname,
		'path'       => XOOPS_ROOT_PATH . $path,
		'URL'        => XOOPS_URL . $path,
		'alias'      => $title,
		'tmbURL'     => XOOPS_URL . '/modules/'.$mydirname.'/cache/tmb/',
		'tmbPath'    => XOOPS_ROOT_PATH . '/modules/'.$mydirname.'/cache/tmb/',
		'quarantine' => XOOPS_ROOT_PATH . '/modules/'.$mydirname.'/cache/tmb/.quarantine',
		'tmbSize'    => 140,
		'tmbCrop'    => false,
		// 'startPath'  => '../files/test',
		// 'deep' => 3,
		// 'separator' => ':',
		'uploadAllow'     => array('image'),
		// mimetypes not allowed to upload
		'uploadDeny'      => array('all'),
		// order to proccess uploadAllow and uploadDeny options
		'uploadOrder'     => array('deny', 'allow'),
		// regexp or function name to validate new file name
		'acceptedName'    => '/^(?:\w+|\w[\w\s\.\%\-\(\)\[\]]*\.(?:txt|gif|jpeg|jpg|png))$/ui',
		'defaults' => array('read' => true, 'write' => true)
	);

}
