<?php
require_once _MD_ELFINDER_LIB_PATH . '/php/elFinderVolumeDropbox2.class.php';

class elFinderVolumeDropboxX extends elFinderVolumeDropbox2 {
	protected function init() {
		$this->options['tmpPath'] = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache';
		$this->options['tmbPath'] = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';
		$this->options['tmbURL']  = _MD_XELFINDER_MODULE_URL.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';
		return parent::init();
	}
}
