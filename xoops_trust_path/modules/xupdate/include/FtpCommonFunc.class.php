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

	public $nextlink = "";

	public $target_key;
	public $target_type;

	public $options = array();

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
		return $this->Func->downloadDirPath($this->target_key);
	}

	/**
	 * _cleanup
	 *
	 * @return	void
	 **/
	public function _cleanup($dir)
	{
		if ($handle = opendir("$dir")) {
			$safemode = (ini_get('safe_mode') == "1");
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
			if ($safemode) {
				$this->Ftp->localRmdir($dir);
			} else {
				rmdir($dir);
			}
			
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
	
	/**
	 * Check exists & writable ExploredDirPath
	 * @param  string  $target_key
	 * @return boolean
	 */
	public function checkExploredDirPath($target_key) {
		if ($this->Ftp->app_login('127.0.0.1')) {
			
			$downloadDirPath = realpath($this->Xupdate->params['temp_path']);
			$exploredDirPath = $downloadDirPath.'/'.$target_key;
			$ret = ((file_exists($exploredDirPath) || $this->Ftp->localMkdir($exploredDirPath)) && (is_writable($exploredDirPath) || $this->Ftp->localChmod($exploredDirPath, 0707)));
			
			return $ret;
		}
		return false;
	}

} // end class

?>