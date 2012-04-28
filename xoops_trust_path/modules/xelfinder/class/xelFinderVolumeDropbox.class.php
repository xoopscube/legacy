<?php
require_once _MD_ELFINDER_LIB_PATH . '/php/elFinderVolumeDropbox.class.php';

elFinder::$netDrivers['dropbox'] = 'DropboxX';

class elFinderVolumeDropboxX extends elFinderVolumeDropbox {
	protected function init() {
		$this->options['tmpPath'] = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache';
		$this->options['tmbPath'] = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';
		$this->options['tmbURL']  = _MD_XELFINDER_MODULE_URL.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';
		return parent::init();
	}
	protected function configure() {
		parent::configure();
		$this->disabled[] = 'pixlr';
	}
	
}
