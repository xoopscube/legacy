<?php

// Xupdate_ftp excutr function
if(!class_exists('ZipArchive') ){
	$mod_zip=false;
	if (!extension_loaded('zip')) {
		if (function_exists('dl')){
			$prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
			if(@dl($prefix . 'zip.' . PHP_SHLIB_SUFFIX)){
				$mod_zip=true;
			}
		}
	}
	if(!class_exists('ZipArchive') ){
		require_once XUPDATE_TRUST_PATH .'/include/FtpCommonFileArchive.class.php';
	}else{
		require_once XUPDATE_TRUST_PATH .'/include/FtpCommonZipArchive.class.php';
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
	 * @param string $caller
	 *
	 * @return	bool
	 **/
	public function execute( $caller )
	{

		$result = true;
		if( $this->Xupdate->params['is_writable']['result'] === true ) {

			$downloadUrl = $this->Func->_getDownloadUrl( $this->target_key, $this->downloadUrlFormat );
			$tempFilename = $this->target_key . '.tgz';
			if ($this->Func->_downloadFile( $this->target_key, $downloadUrl, $tempFilename, $this->downloadedFilePath )){
				$downloadDirPath = realpath($this->Xupdate->params['temp_path']);
				$this->exploredDirPath = realpath($downloadDirPath.'/'.$this->target_key);
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
						$this->Ftp->app_logout();

					}else{
						$this->_set_error_log('Ftp->app_login false');
						$result = false;
					}

					//一つディレクトリ階層を戻す
					if ($downdir_result){
						$this->_exploredDirPath_UpDir();
					}

				}else{
					$this->_set_error_log('unzipFile false ');
					$result = false;
				}
			}else{
				$this->_set_error_log('downloadFile false');
				$result = false;
			}

			$this->content.= 'cleaning up... <br />';
			$this->_cleanup($this->exploredDirPath);
			//

//TODO unlink ok?
			@unlink( $this->downloadedFilePath );

			$this->content.= 'completed <br /><br />';
		}else{
			$result = false;
		}

		if ($result){
			$this->content.= $this->_get_nextlink($this->dirname, $caller);
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
					$this->Ftp->appendMes( 'fail upload xoops_trust_path uploadNakami<br />');
					return false;
				}
				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/modules/' ;
				$unzipPath =  $this->exploredDirPath .'/html/modules';
				$result = $this->Ftp->uploadNakami_To_module($unzipPath, $uploadPath ,$this->trust_dirname,$this->dirname);
				if (!$result){
					$this->Ftp->appendMes( 'fail upload html/modules uploadNakami_To_module<br />');
					return false;
				}

				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath .'/html';
				$result = $this->Ftp->uploadNakami_OtherThan_module($unzipPath, $uploadPath ,$this->trust_dirname,$this->dirname);
				if (!$result){
					$this->Ftp->appendMes( 'fail upload html uploadNakami_OtherThan_module<br />');
					return false;
				}

			}else{

				// copy xoops_trust_path
				$uploadPath = XOOPS_TRUST_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
				$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
				if (!$result){
					$this->Ftp->appendMes( 'fail upload xoops_trust_path uploadNakami<br />');
					return false;
				}

				// copy html
				$uploadPath = XOOPS_ROOT_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath .'/html';
				$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
				if (!$result){
					$this->Ftp->appendMes( 'fail upload html uploadNakami<br />');
					return false;
				}
			}
		}else{

			// copy html
			$uploadPath = XOOPS_ROOT_PATH . '/' ;
			$unzipPath =  $this->exploredDirPath .'/html';
			$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
			if (!$result){
				$this->Ftp->appendMes( 'fail upload html uploadNakami<br />');
				return false;
			}
		}

		$this->Ftp->appendMes( 'end uploaded success<br />');
		return true;
	}

	/**
	 * _get_nextlink
	 *
	 * @param   string  $dirname
	 *
	 * @return	string
	 **/
	private function _get_nextlink($dirname, $caller)
	{
		$ret ='';
		if ($caller == 'module'){
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

		} elseif ($caller == 'theme'){
			$ret ='<a href="'.XOOPS_MODULE_URL.'/legacy/admin/index.php?action=ThemeList">'._MI_XUPDATE_LANG_UPDATE.'</a>';
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