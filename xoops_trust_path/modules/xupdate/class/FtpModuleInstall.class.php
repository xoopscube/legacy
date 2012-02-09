<?php

// Xupdate_ftp excutr function
require_once XUPDATE_TRUST_PATH .'/class/FtpCommonFunc.class.php';

class Xupdate_FtpModuleInstall extends Xupdate_FtpCommonFunc {

	public $trust_dirname;
	public $dirname;

	public function __construct() {

		parent::__construct();

	}
	/**
	 * execute
	 *
	 * @return	void
	 **/

	public function execute()
	{

		$result = true;
		if( $this->Xupdate->params['is_writable']['result'] === true ) {

			if ($this->_downloadFile()){
				if($this->_unzipFile()==true) {
					// ToDo port , timeout
					if($this->Ftp->app_login("127.0.0.1")==true) {
						if (!$this->uploadFiles()){
							$this->_set_error_log('Ftp uploadFiles false');
							$result = false;
						}
					}else{
						$this->_set_error_log('Ftp->app_login false');
						$result = false;
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
			unlink( $downloadPath );

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

} // end class

?>