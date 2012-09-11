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
	public function _unzipFile($caller)
	{
		// local file name
		$downloadDirPath = realpath($this->Xupdate->params['temp_path']);
		$downloadFilePath = $this->Xupdate->params['temp_path'].'/'.$this->download_file;
		$exploredDirPath = realpath($downloadDirPath.'/'.$this->target_key);
		if (empty($downloadFilePath) ) {
			$this->_set_error_log('getDownloadFilePath not found error in: '.$this->_getDownloadFilePath());
			return false;
		}
		if (! chdir($exploredDirPath) ) {
			$this->_set_error_log('chdir error in: '.$exploredDirPath);
			return false;//chdir error
		}
		
		if (substr($this->download_file, -10) === '.class.php') {
			if (@ copy($this->Xupdate->params['temp_path'].'/'.$this->download_file, $exploredDirPath.'/'.$this->download_file)) {
				$this->exploredPreloadPath = $exploredDirPath;
				return true;
			}
		}
		
		if (ini_get('safe_mode') == "1") {
			// make dirctory at first for safe_mode
			$dirs = array();
			if ($source = File_Archive::read($downloadFilePath.'/', $exploredDirPath)) {
				if (is_object($source) && get_class($source) !== 'PEAR_Error') {
					while ($source->next()) {
						$file = $source->getFilename();
						$dir = dirname($file);
						if (!isset($dirs[$dir])) {
							$dirs[$dir] = true;
							$this->Ftp->localMkdir($dir);
							$this->Ftp->localChmod($dir, 0707);
						}
					}
					$source->close();
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		if ($source = File_Archive::read($downloadFilePath.'/')) {
			if (is_object($source) && get_class($source) !== 'PEAR_Error') {
				File_Archive::extract(
					$source,
					File_Archive::appender($exploredDirPath)
				);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

} // end class

?>
