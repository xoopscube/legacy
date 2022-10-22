<?php
/*
 * Created on 2017/04/01 by nao-pon http://xoops.hypweb.net/
 */

if (version_compare(PHP_VERSION, '5.4.0', '>=') && class_exists('\Google_Client')) {
	$_token = array();
	$_service_key_file = '';
	if (! empty($extOptions['ext_token'])) {
		$_token = json_decode($extOptions['ext_token'], true);
	} else if (! empty($extOptions['ext_service_key_file'])) {
		$_service_key_file = XOOPS_TRUST_PATH . '/uploads/xelfinder/' . trim($extOptions['ext_service_key_file']);
		if (! is_file($_service_key_file)) {
			$_service_key_file = '';
		}
	}
	if (! empty($_token['refresh_token']) || $_service_key_file) {
		$path = trim($path, ' /');
		if ($path === '') {
			$path = 'root';
		}
		$volumeOptions = array(
			'driver'        => 'GoogleDrive',
			'alias'         => $title,
			'path'          => $path,
			'defaults'      => array('read' => true, 'write' => true, 'hidden' => false, 'locked' => false),
			'tmpPath'       => XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache'
		);
		if (! empty($_token['refresh_token'])) {
			$volumeOptions += array(
				'client_id'     => isset($_token['client_id'])? $_token['client_id'] : '',
				'client_secret' => isset($_token['client_secret'])? $_token['client_secret'] : '',
				'refresh_token' => $_token['refresh_token']
			);
		} else {
			$volumeOptions += array(
				'serviceAccountConfigFile' => $_service_key_file
			);
		}
	}

}
