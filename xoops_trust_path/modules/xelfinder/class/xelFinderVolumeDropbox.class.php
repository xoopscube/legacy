<?php
require_once _MD_ELFINDER_LIB_PATH . '/php/elFinderVolumeDropbox.class.php';

elFinder::$netDrivers['dropbox'] = 'DropboxX';

class elFinderVolumeDropboxX extends elFinderVolumeDropbox {
	
	protected function configure() {
		parent::configure();

		$this->tmp = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache';
		$this->tmbURL = _MD_XELFINDER_MODULE_URL.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb/';
		$this->tmbPath = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';

		$this->disabled[] = 'pixlr';
	}
}
