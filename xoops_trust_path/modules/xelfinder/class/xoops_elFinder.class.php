<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 * $Id: xoops_elFinder.class.php,v 1.1 2012/01/20 13:32:02 nao-pon Exp $
 */

class xoops_elFinder {

	protected $defaultVolumeOptions = array(
		'dateFormat' => 'y/m/d H:i',
		'mimeDetect' => 'auto',
		'tmbSize'	 => 48,
		'tmbCrop'	 => true,
		'defaults' => array('read' => true, 'write' => false)
	);

	public function __construct($opt = array()) {
		$this->defaultVolumeOptions = array_merge($this->defaultVolumeOptions, $opt);
	}

	public function getRootVolumes($config, $extras = array()) {
		$pluginPath = dirname(dirname(__FILE__)) . '/plugins/';
		$configs = explode("\n", $config);
		$roots = array();
		foreach($configs as $_conf) {
			$_conf = trim($_conf);
			if (! $_conf || $_conf[0] === '#') continue;
			$_confs = explode(':', $_conf);
			$_confs = array_map('trim', $_confs);
			list($mydirname, $plugin, $path, $title, $options) = array_pad($_confs, 6, '');
			if ($title === '') $title = $mydirname;
			$path = '/' . trim($path, '/') . '/';
			$volume = $pluginPath . $plugin . '/volume.php';
			if (is_file($volume)) {
				$extra = isset($extras[$mydirname.':'.$plugin])? $extras[$mydirname.':'.$plugin] : array();
				$volumeOptions = array();
				require $volume;
				if ($volumeOptions) {
					$volumeOptions = array_merge($this->defaultVolumeOptions, $volumeOptions, $extra);
					$roots[] = $volumeOptions;
				}
			}
		}
		return $roots;
	}

}
