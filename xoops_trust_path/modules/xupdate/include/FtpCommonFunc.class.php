<?php

// Xupdate_ftp class object
require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';
require_once XUPDATE_TRUST_PATH . '/class/Ftp.class.php';

class Xupdate_FtpCommonFunc {

	public /*** XCube_Root ***/ $mRoot = null;
	public /*** Xupdate_Module ***/ $mModule = null;
	public /*** Xupdate_AssetManager ***/ $mAsset = null;

	public $Xupdate  ;	// Xupdate instance
	public $Ftp  ;	// FTP instance
	public $Func ;	// Functions instance
	public $mod_config ;
	public $content ;

	public $downloadDirPath;
	public $exploredDirPath;
	public $downloadUrlFormat;

	public $target_key;
	public $target_type;

	public function __construct() {

		$this->mRoot =& XCube_Root::getSingleton();
		$this->mModule =& $this->mRoot->mContext->mModule;
		$this->mAsset =& $this->mModule->mAssetManager;

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->Func =& $this->Xupdate->func ;		// Functions instance
		$this->mod_config = $this->mRoot->mContext->mModuleConfig ;	// mod_config

		$this->downloadDirPath = $this->Xupdate->params['temp_path'];
//		$this->downloadUrlFormat = $this->mod_config['Mod_download_Url_format'];

	}

	/**
	 * _downloadFile
	 *
	 * @return	bool
	 **/
	public function _downloadFile()
		{
		$realDirPath = $this->downloadDirPath;
		$realDirPath = realpath($realDirPath);
		if (empty($realDirPath) ) {
			$this->_set_error_log('downloadDirPath not found error in: '.$this->downloadDirPath);
			return false;
		}
		if (! chdir($realDirPath) ) {
			$this->_set_error_log('chdir error in: '.$this->downloadDirPath);
			return false;//chdir error
		}
		@mkdir($this->target_key);
		$this->exploredDirPath = realpath($this->downloadDirPath.'/'.$this->target_key);
		//directory traversal check
		if (strpos($this->exploredDirPath, $realDirPath) === false){
			$this->_set_error_log('directory traversal error in: '.$this->downloadDirPath.'/'.$this->target_key);
			return false;
		}
		if (!is_dir($this->exploredDirPath)){
			$this->_set_error_log('not is_dir error in: '.$this->downloadDirPath.'/'.$this->target_key);
			return false;//chdir error
		}

		$this->Ftp->appendMes('downladed in: '.$this->downloadDirPath.'<br />');
		$this->content.= 'downladed in: '.$this->downloadDirPath.'<br />';
		if (! chdir($this->exploredDirPath) ) {
			$this->_set_error_log('chdir error in: '.$this->downloadDirPath);
			return false;//chdir error
		}

		// TODO ファイルNotFound対策
		$url = $this->_getDownloadUrl();
		if (empty($url)){
			$this->_set_error_log('_getDownloadUrl false');
			return false;
		}

		$downloadedFilePath = $this->_getDownloadFilePath();

		try {
			try {
				if(!function_exists('curl_init') ){
					throw new Exception('curl_init function no found fail',1);
				}
			} catch (Exception $e) {
				$this->_set_error_log($e->getMessage());
				return false;
			}

			$ch = curl_init($url);
			if($ch === false ){
				throw new Exception('curl_init fail',2);
			}
			$this->Ftp->appendMes('curl_init OK<br />');
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());
			return false;
		}

		$fp = fopen($downloadedFilePath, "w");

		try {
			$setopt1 = curl_setopt($ch, CURLOPT_FILE, $fp);
			$setopt2 = curl_setopt($ch, CURLOPT_HEADER, 0);
			$setopt3 = curl_setopt($ch, CURLOPT_FAILONERROR, true);

			if(!$setopt1 || !$setopt2 || !$setopt3 ){
				throw new Exception('curl_setopt fail',3);
			}
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());

			fclose($fp);
			return false;
		}

		//safe_mode  CURLOPT_FOLLOWLOCATION cannot be activated when in safe_mode
		if (ini_get('safe_mode')){
			try {
				//redirect suport
				$setopt4 = curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				$setopt5 = curl_setopt($ch, CURLOPT_MAXREDIRS, 1);

				if(!$setopt4 || !$setopt5 ){
					throw new Exception('curl_setopt CURLOPT_FOLLOWLOCATION fail skip',4);
				}
			} catch (Exception $e) {
				$this->_set_error_log($e->getMessage());
			}
		}

		//SSL NO VERIFY setting
		$URI_PARTS = parse_url($url);
		if (strtolower($URI_PARTS["scheme"]) == 'https' ){
			try {
				$setopt6 = curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$setopt7 = curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				if(!$setopt6 || !$setopt7 ){
					throw new Exception('curl_setopt SSL fail',5);
				}
			} catch (Exception $e) {
				$this->_set_error_log($e->getMessage());

				fclose($fp);
				return false;
			}
		}

		try {
			try {
				if(!function_exists('curl_exec') ){
					throw new Exception('curl_exec function not found fail',6);
				}
			} catch (Exception $e) {
				$this->_set_error_log($e->getMessage());
				return false;
			}

			$result = curl_exec($ch);
			if($result === false ){
				throw new Exception('curl_exec fail',7);
			}
			$this->Ftp->appendMes('curl exec OK<br />');
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());

			fclose($fp);
			return false;
		}

		fclose($fp);

		return true;
	}

	/**
	 * _getDownloadUrl
	 *
	 * @return	string
	 **/
	public function _getDownloadUrl()
	{
		// TODO ファイルNotFound対策
		$url = sprintf($this->downloadUrlFormat, $this->target_key);
		return $url;
	}

	/**
	 * _getDownloadFilePath
	 *
	 * @return	string
	 **/
	public function _getDownloadFilePath()
	{
		//$downloadPath = sprintf( $this->downloadUrlFormat, 'd3diary') ;
		$downloadPath = $this->downloadDirPath .'/'. $this->target_key . '.tgz';
		//$downloadPath = TP_ADDON_MANAGER_TMP_PATH .'/'. $this->target_key . '.tgz';
		return $downloadPath;
	}

	/**
	 * _cleanup
	 *
	 * @return	void
	 **/
	public function _cleanup($dir)
	{
		if ($handle = opendir("$dir")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dir/$item")) {
						$this->_cleanup("$dir/$item");
						$this->Ftp->appendMes('removing directory: '.$dir.'/'.$item.'<br />');
					} else {
						unlink("$dir/$item");
					}
				}
			}
			closedir($handle);
			rmdir($dir);
		}
	}

	/**
	 * _set_error_log
	 *
	 * @param   string  $msg
	 *
	 * @return	void
	 **/
	public function _set_error_log($msg)
	{
		$this->Ftp->appendMes('<span style="color:red;">'.$msg.'</span><br />');
		$this->content.= '<span style="color:red;">'.$msg.'</span><br />';
	}

} // end class

?>