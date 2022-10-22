<?php
require_once _MD_ELFINDER_LIB_PATH . '/php/elFinderVolumeFTP.class.php';

elFinder::$netDrivers['ftp'] = 'FTPx';

class elFinderVolumeFTPx extends elFinderVolumeFTP {
	protected function init() {
		//$this->options['tmpPath'] = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache';
		$this->options['tmpPath'] = XOOPS_TRUST_PATH.'/cache';
		$this->options['tmbPath'] = XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';
		$this->options['tmbURL']  = _MD_XELFINDER_MODULE_URL.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb';
		return parent::init();
	}
	
	/**
	 * Connect to ftp server
	 *
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 * @author Naoki Sawada
	 **/
	protected function connect() {
		if (!($this->connect = ftp_connect($this->options['host'], $this->options['port'], $this->options['timeout']))) {
			return $this->setError('Unable to connect to FTP server '.$this->options['host']);
		}
		if (!ftp_login($this->connect, $this->options['user'], $this->options['pass'])) {
			$this->umount();
			return $this->setError('Unable to login into '.$this->options['host']);
		}
	
		// try switch utf8 mode
		if ($this->encoding) {
			@ftp_exec($this->connect, 'OPTS UTF8 OFF');
		} else {
			@ftp_exec($this->connect, 'OPTS UTF8 ON' );
		}
		
		// switch off extended passive mode - may be usefull for some servers
		@ftp_exec($this->connect, 'epsv4 off' );
		// enter passive mode if required
		ftp_pasv($this->connect, $this->options['mode'] == 'passive');
	
		// enter root folder
		if (! @ftp_chdir($this->connect, $this->root)
				|| $this->root != ftp_pwd($this->connect)) {
			if (empty($this->options['is_local']) || ! $this->setLocalRoot()) {
				$this->umount();
				return $this->setError('Unable to open root folder.');
			}
		}
	
		// check for MLST support
		$features = ftp_raw($this->connect, 'FEAT');
		if (!is_array($features)) {
			$this->umount();
			return $this->setError('Server does not support command FEAT.');
		}
	
		foreach ($features as $feat) {
			if (strpos(trim($feat), 'MLST') === 0) {
				$this->MLSTsupprt = true;
				break;
			}
		}
		
		return true;
	}
	
	protected function setLocalRoot() {
		$root = XOOPS_ROOT_PATH . '/class';
		$checkLen = strlen($root);
		do {
			$root = preg_replace('#^/[^/]+#', '', $root);
			if (! $root) {
				return false;
			}
			if (@ ftp_chdir($this->connect, $root)) {
				$this->root = substr($this->root, $checkLen - strlen($root));
				if ($this->root === '') {
					$this->root = '/';
				}
				return true;
			}
		} while($root);
		return false;
	}
	
}
