<?php
require_once _MD_ELFINDER_LIB_PATH . '/php/elFinderFlysystemGoogleDriveNetmount.php';

elFinder::$netDrivers['googledrive'] = 'FlysystemGoogleDriveNetmountX';

class elFinderVolumeFlysystemGoogleDriveNetmountX extends elFinderVolumeFlysystemGoogleDriveNetmount {

	public function __construct() {
		parent::__construct();
		$opts = ['gdCacheDir'     => XOOPS_TRUST_PATH.'/cache', 'gdCachePrefix'  => rawurlencode(substr(XOOPS_URL, 7)).'-'._MD_ELFINDER_MYDIRNAME.'-gd-'];
		$this->options = array_merge($this->options, $opts);
	}
	
	protected function init() {
		$this->options['tmpPath'] = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache';
		$this->options['tmbPath'] = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';
		$this->options['tmbURL']  = _MD_XELFINDER_MODULE_URL.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';
		$this->options['tsPlSleep'] = 15;
		$this->options['syncMinMs'] = 30000;
		return parent::init();
	}
}
