<?php
/*
 * Created on 2014/03/12 by nao-pon http://xoops.hypweb.net/
 */

if (version_compare(PHP_VERSION, '5.5.0', '>=') && defined('ELFINDER_DROPBOX_CONSUMERKEY') && $mConfig['dropbox_acc_token'] && class_exists('\Kunnu\Dropbox\DropboxApp')) {
	$token = trim($mConfig['dropbox_acc_token']);
	if (!empty($mConfig['dropbox_acc_seckey'])) {
		$token = elFinderVolumeDropbox2::getTokenFromOauth1(ELFINDER_DROPBOX_APPKEY, ELFINDER_DROPBOX_APPSECRET, $token, trim($mConfig['dropbox_acc_seckey']));
	}
	
	if ($token) {
		$volumeOptions = array(
			'driver'       => 'Dropbox2',
			'alias'        => $title,
			'path'         => '/'.trim($path, ' /'),
			'defaults'     => array('read' => true, 'write' => true, 'hidden' => false, 'locked' => false),
			'app_key'      => ELFINDER_DROPBOX_APPKEY,
			'app_secret'   => ELFINDER_DROPBOX_APPSECRET,
			'access_token' => $token,
			'tmpPath'      => XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache',
			'tmbPath'      => XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb',
			'tmbURL'       => _MD_XELFINDER_MODULE_URL.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb',
		);
	}

}
