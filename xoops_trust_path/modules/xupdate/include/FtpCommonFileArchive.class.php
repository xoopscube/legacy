<?php

// Xupdate_ftp excutr function
require_once XUPDATE_TRUST_PATH .'/include/FtpCommonFunc.class.php';
// To Do
if (!defined('PATH_SEPARATOR')) {
	if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
		define('PATH_SEPARATOR', ':');
	} else {
		define('PATH_SEPARATOR', ';');
	}
}
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)) . '/PEAR');
require_once 'File/Archive.php';

class Xupdate_FtpCommonZipArchive extends Xupdate_FtpCommonFunc {

	public function __construct() {
		parent::__construct();
	}


	/**
	 * _unzipFile
	 *
	 * @return	bool
	 **/
	public function _unzipFile()
	{
		// local file name
		$downloadDirPath = realpath($this->Xupdate->params['temp_path']);
		$downloadFilePath = $this->Xupdate->params['temp_path'].'/'.$this->target_key .'.zip';
		$exploredDirPath = realpath($downloadDirPath.'/'.$this->target_key);
		if (empty($downloadFilePath) ) {
			$this->_set_error_log('getDownloadFilePath not found error in: '.$this->_getDownloadFilePath());
			return false;
		}
		if (! chdir($exploredDirPath) ) {
			$this->_set_error_log('chdir error in: '.$exploredDirPath);
			return false;//chdir error
		}

		File_Archive::extract(
			File_Archive::read($downloadFilePath.'/'),
			File_Archive::appender($exploredDirPath)
		);
		return true;
	}

} // end class

?>
