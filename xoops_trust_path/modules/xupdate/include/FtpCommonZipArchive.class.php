<?php

// Xupdate_ftp excutr function
require_once XUPDATE_TRUST_PATH .'/include/FtpCommonFunc.class.php';

class Xupdate_FtpCommonZipArchive extends Xupdate_FtpCommonFunc {

	public function __construct() {

		parent::__construct();

	}


	/**
	 * _unzipFile
	 *
	 * @return	bool
	 **/
	public function _unzipFile( )
	{
		// local file name
		//$downloadFilePath = $this->_getDownloadFilePath();
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

		try {
			if(!class_exists('ZipArchive') ){
				throw new Exception('ZipArchive class not found fail',1);
			}
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());
			return false;
		}
		$zip = new ZipArchive;

		try {
			 $result = $zip->open($downloadFilePath);
			if($result !==true ){
				throw new Exception('ZipArchive open fail '.$downloadFilePath ,2);
			}
		} catch (Exception $e) {
			$zip_open_error_arr = array(
				ZIPARCHIVE::ER_EXISTS => 'ER_EXISTS',
				ZIPARCHIVE::ER_INCONS => 'ER_INCONS',
				ZIPARCHIVE::ER_INVAL => 'ER_INVAL',
				ZIPARCHIVE::ER_MEMORY => 'ER_MEMORY',
				ZIPARCHIVE::ER_NOENT => 'ER_NOENT',
				ZIPARCHIVE::ER_NOZIP => 'ER_NOZIP',
				ZIPARCHIVE::ER_OPEN => 'ER_OPEN',
				ZIPARCHIVE::ER_READ => 'ER_READ',
				ZIPARCHIVE::ER_SEEK => 'ER_SEEK'
			);
			$this->_set_error_log($e->getMessage().(in_array($result,$zip_open_error_arr) ? f : 'undefine' ));
			return false;
		}

		//$zip->extractTo('./');
		try {
			 $result = $zip->extractTo('./');
			if($result !==true ){
				throw new Exception('extractTo fail ',3);
			}
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());

			$zip->close();
			return false;
		}

		$zip->close();
		$this->Ftp->appendMes('explored in: '.$exploredDirPath.'<br />');
		$this->content.= 'explored in: '.$exploredDirPath.'<br />';

		return true;
	}



} // end class

?>