<?php

// Xupdate_ftp excutr function
if(!class_exists('ZipArchive') ){
	$mod_zip=false;
	if (extension_loaded('zip')) {
		if (function_exists('dl')){
			$prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
			if(@dl($prefix . 'zip.' . PHP_SHLIB_SUFFIX)){
				$mod_zip=true;
			}
		}
	}
	if ($mod_zip){
		require_once XUPDATE_TRUST_PATH .'/include/FtpCommonZipArchive.class.php';
	}else{
		require_once XUPDATE_TRUST_PATH .'/include/FtpCommonFileArchive.class.php';
	}
}else{
	require_once XUPDATE_TRUST_PATH .'/include/FtpCommonZipArchive.class.php';
}

class Xupdate_FtpModuleInstall extends Xupdate_FtpCommonZipArchive {

/*parent public
	public $mRoot ;
	public $mModule ;
	public $mAsset ;

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
*/

	public $trust_dirname;
	public $dirname;
	public $unzipdirlevel;

	public function __construct() {

		parent::__construct();

	}

	/**
	 * execute
	 *
	 * @return	bool
	 **/
	public function execute()
	{

		$result = true;
		if( $this->Xupdate->params['is_writable']['result'] === true ) {

			if ($this->_downloadFile()){
				if($this->_unzipFile()==true) {
					//一つディレクトリ階層を下げる
					$downdir_result = false;
					if (!empty($this->unzipdirlevel)){
						$downdir_result = $this->_exploredDirPath_DownDir();
					}
					// TODO port , timeout
					if($this->Ftp->app_login("127.0.0.1")==true) {
						if (!$this->uploadFiles()){
							$this->_set_error_log('Ftp uploadFiles false');
							$result = false;
						}
					}else{
						$this->_set_error_log('Ftp->app_login false');
						$result = false;
					}

					//一つディレクトリ階層を戻す
					if ($downdir_result){
						$this->_exploredDirPath_UpDir();
					}

				}else{
					$this->_set_error_log('unzipFile false false');
					$result = false;
				}
			}else{
				$this->_set_error_log('downloadFile fale');
				$result = false;
			}

			$this->Ftp->app_logout();
			$this->content.= 'cleaning up... <br />';
			$this->_cleanup($this->exploredDirPath);
			//
			$downloadPath= $this->_getDownloadFilePath() ;
//TODO unlink ok?
			@unlink( $downloadPath );

			$this->content.= 'completed <br /><br />';
		}else{
			$result = false;
		}

		if ($result){
			$this->content.= $this->_get_nextlink($this->dirname);
		}else{
			$this->content.= _ERRORS;
		}
		return $result;
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
	 * uploadFiles
	 *
	 * @return	bool
	 **/
	private function uploadFiles()
	{
		//$this->Ftp->connect();

		$this->Ftp->appendMes( 'start uploading..<br />');
		$this->content.=  'uploading..<br />';

		if ($this->target_type == 'TrustModule'){
			if (!empty($this->trust_dirname) && !empty($this->dirname) && $this->trust_dirname != $this->dirname){

				// copy xoops_trust_path
				$uploadPath = XOOPS_TRUST_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
				$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
				if (!$result){
					return false;
				}
				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/modules/' ;
				$unzipPath =  $this->exploredDirPath .'/html/modules';
				$result = $this->Ftp->uploadNakami_To_module($unzipPath, $uploadPath ,$this->trust_dirname,$this->dirname);
				if (!$result){
					return false;
				}

				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath .'/html';
				$result = $this->Ftp->uploadNakami_OtherThan_module($unzipPath, $uploadPath ,$this->trust_dirname,$this->dirname);
				if (!$result){
					return false;
				}

			}else{

				// copy xoops_trust_path
				$uploadPath = XOOPS_TRUST_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
				$this->Ftp->uploadNakami($unzipPath, $uploadPath);

				// copy html
				$uploadPath = XOOPS_ROOT_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath .'/html';
				$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
				if (!$result){
					return false;
				}
			}
		}else{

			// copy html
			$uploadPath = XOOPS_ROOT_PATH . '/' ;
			$unzipPath =  $this->exploredDirPath .'/html';
			$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
			if (!$result){
				return false;
			}
		}

		return true;
	}

	/**
	 * _get_nextlink
	 *
	 * @param   string  $dirname
	 *
	 * @return	string
	 **/
	private function _get_nextlink($dirname)
	{
		$ret ='';
		$hModule = Xupdate_Utils::getXoopsHandler('module');
		$module =& $hModule->getByDirname($dirname) ;
		if (is_object($module)){
			if ($module->getVar('isactive') ){
				$ret ='<a href="'.XOOPS_URL.'/modules/legacy/admin/index.php?action=ModuleUpdate&dirname='.$dirname.'">'._MI_XUPDATE_LANG_UPDATE.'</a>';
			}else{
				$ret =_AD_LEGACY_LANG_BLOCK_INACTIVETOTAL;
			}
		}else{
			$ret ='<a href="'.XOOPS_URL.'/modules/legacy/admin/index.php?action=ModuleInstall&dirname='.$dirname.'">'._MI_XUPDATE_LANG_UPDATE.'</a>';
		}
		return $ret;
	}

	/**
	 * _exploredDirPath_DownDir
	 *
	 * @param   void
	 *
	 * @return	void
	 **/
	private function _exploredDirPath_DownDir()
	{
		$ret = false;
		$dir = $this->exploredDirPath;
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..'){
				continue;
			}
			if ($item =='html' || $item =='xoops_trust_path') {
				$ret = false;
				break;
			}
			if (is_dir($dir.'/'.$item)) {
				$this->exploredDirPath = realpath($dir.'/'.$item);
				$this->Ftp->appendMes('down dir exploredDirPath: '.$this->exploredDirPath.'<br />');
				$ret = true;
				break;
			}
		}
		return $ret;
	}

	/**
	 * _exploredDirPath_UpDir
	 *
	 * @param   void
	 *
	 * @return	void
	 **/
	private function _exploredDirPath_UpDir()
	{
		$this->exploredDirPath = dirname($this->exploredDirPath);
		$this->Ftp->appendMes('up dir exploredDirPath: '.$this->exploredDirPath.'<br />');
	}

} // end class

?>