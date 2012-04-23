<?php

/**
 * elFinder driver for local filesystem.
 *
 * @author Dmitry (dio) Levashov
 * @author Troex Nevelin
 **/
class elFinderVolumeXoopsMailbbs extends elFinderVolumeLocalFileSystem {

	protected $mydirname = '';

	protected $enabledFiles = array();

	protected function set_mailbbs_enabledFiles() {

		include(XOOPS_MODULE_PATH.'/'.$this->mydirname.'/config.php');

		$log = preg_replace('#^\./#', '', $log);
		$logfile = XOOPS_MODULE_PATH.'/'.$this->mydirname.'/'.$log;
		$logs = file($logfile);

		$ret = array();
		foreach ($logs as $log) {
			$data = array_pad(explode('<>', $log), 8, '');
			if ($data[7]) continue; //未承認
			$ret[] = $data[5];
		}

		$this->enabledFiles = $ret;
	}

	/**
	 * Constructor
	 * Extend options with required fields
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function __construct() {
		$this->options['alias']    = '';              // alias to replace root dir name
		$this->options['dirMode']  = 0755;            // new dirs mode
		$this->options['fileMode'] = 0644;            // new files mode
		$this->options['quarantine'] = XOOPS_MODULE_PATH . '/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb/.quarantine';  // quarantine folder name - required to check archive (must be hidden)
		$this->options['maxArcFilesSize'] = 0;        // max allowed archive files size (0 - no limit)

		$this->options['path'] = '';
		$this->options['separator'] = '/';
		$this->options['mydirname'] = 'mailbbs';
		$this->options['mimeDetect'] = 'internal';
		$this->options['tmbPath'] = XOOPS_MODULE_PATH . '/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb/';
		$this->options['tmbURL'] = _MD_XELFINDER_MODULE_URL . '/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb/';

	}

	/*********************************************************************/
	/*                        INIT AND CONFIGURE                         */
	/*********************************************************************/

	/**
	 * Prepare driver before mount volume.
	 * Connect to db, check required tables and fetch root path
	 *
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function init() {

		parent::init();

		$this->mydirname = $this->options['mydirname'];

		$this->set_mailbbs_enabledFiles();

		return true;
	}


	/******************** file/dir content *********************/
	/**
	 * Return files list in directory.
	 *
	 * @param  string  $path  dir path
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _scandir($path) {
		$files = array();
		if ($path === $this->root) {
			foreach ($this->enabledFiles as $name) {
				$files[] = $path.'/'.$name;
			}
		}
		return $files;
	}

	/***************** file stat ********************/
	/**
	 * Return true if path is dir and has at least one childs directory
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _subdirs($path) {
		return false;
	}

} // END class
