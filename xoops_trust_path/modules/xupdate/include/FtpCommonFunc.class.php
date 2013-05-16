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

	protected $download_file;
	protected $exploredPreloadPath;
	protected $retry_phase;
	
	public function __construct() {

		$this->mRoot =& XCube_Root::getSingleton();
		$this->mModule =& $this->mRoot->mContext->mModule;
		$this->mAsset =& $this->mModule->mAssetManager;

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->Func =& $this->Xupdate->func ;		// Functions instance
		$this->content =& $this->Func->content;
		$this->mod_config = $this->mRoot->mContext->mModuleConfig ;	// mod_config

		$this->downloadDirPath = $this->Xupdate->params['temp_path'];
		
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
		if ($handle = opendir($dir)) {
			$this->Ftp->appendMes('removing directory: '.$dir.'<br />');
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dir/$item")) {
						$this->_cleanup("$dir/$item");
					} else {
						unlink("$dir/$item");
						Xupdate_Utils::check_http_timeout();
					}
				}
			}
			closedir($handle);
			@rmdir($dir) || (($this->Ftp->isConnected() || $this->Ftp->app_login()) && $this->Ftp->localRmdir($dir));
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
		if ($msg) {
			$this->Ftp->appendMes('<span style="color:red;">'.$msg.'</span><br />');
			$this->content.= '<span style="color:red;">'.$msg.'</span><br />';
		}
	}
	
	/**
	 * Check exists & writable ExploredDirPath
	 * @param  string  $target_key
	 * @return string | boolean
	 */
	public function checkExploredDirPath($target_key) {
		if ($this->Ftp->app_login()) {
			
			$downloadDirPath = realpath($this->Xupdate->params['temp_path']);
			$exploredDirPath = $downloadDirPath.'/'.$target_key;
			if ((file_exists($exploredDirPath) || $this->Ftp->localMkdir($exploredDirPath)) && (is_writable($exploredDirPath) || $this->Ftp->localChmod($exploredDirPath, _MD_XUPDATE_WRITABLE_DIR_PERM))) {
				return $exploredDirPath;
			}
		}
		return false;
	}

	/**
	 * is_xupdate_excutable
	 *
	 * @return boolean
	 */
	protected function is_xupdate_excutable() {
		if (file_exists(_MD_XUPDATE_SYS_LOCK_FILE) && filemtime(_MD_XUPDATE_SYS_LOCK_FILE) + 600 > time()) {
			$this->retry_phase = intval(file_get_contents(_MD_XUPDATE_SYS_LOCK_FILE));
			return false;
		}
		ignore_user_abort(true); // Ignore user aborts and allow the script
		touch(_MD_XUPDATE_SYS_LOCK_FILE);  // make lock file
		return true;
	}

	/**
	 * save_lockfile
	 * 
	 * @param int $stage
	 */
	protected function save_lockfile($stage) {
		file_put_contents(_MD_XUPDATE_SYS_LOCK_FILE, $stage);
	}
	
	/**
	 * _check_file_upload_result
	 *
	 * @param array  $result
	 * @param string $where
	 * @return boolean
	 */
	protected  function _check_file_upload_result($result, $where, $allow_empty = false) {
		if (is_bool($result)) {
			$result = array('ok' => $result, 'ng' => array());
		}
		if ($result['ok'] === false || !$allow_empty && !$result['ok']) {
			$this->Ftp->appendMes( 'fail upload '.$where.'<br />');
			return false;
		} else if (is_numeric($result['ok'])) {
			$this->Ftp->appendMes( 'uploaded '.$result['ok'].' files into '.$where.'<br />');
		}
		if ($result['ng']) {
			$this->_set_error_log(_MI_XUPDATE_ERR_NOT_UPLOADED.': ' . join('<br />'._MI_XUPDATE_ERR_NOT_UPLOADED.': ', $result['ng']));
		}
		return true;
	}

} // end class

?>