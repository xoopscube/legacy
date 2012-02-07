<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractAction.class.php';
require_once XUPDATE_TRUST_PATH . '/class/Root.class.php';

/**
 * Xupdate_Admin_StoreAction
*
 * @property mixed downloadUrlFormat
 */
class Xupdate_Admin_ModuleInstallAction extends Xupdate_AbstractAction
{

	protected $Xupdate  ;	// Xupdate instance
	protected $Ftp  ;	// FTP instance
	protected $Func ;	// Functions instance
	protected $mod_config ;
	protected $content ;

	protected $downloadDirPath;
	protected $exploredDirPath;
	protected $downloadUrlFormat;
	protected $targetKeyName;
	protected $targetType;

	protected $trust_dirname;
	protected $dirname;

	/**
	 * getDefaultView
	 *
	 * @param	void
	 *
	 * @return	Enum
	**/


	public function __construct()
	{
		parent::__construct();

		$this->mRoot =& XCube_Root::getSingleton();
		$this->mModule =& $this->mRoot->mContext->mModule;
		$this->mAsset =& $this->mModule->mAssetManager;

		// Xupdate_ftp class object
		require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->Func =& $this->Xupdate->func ;		// Functions instance
		$this->mod_config = $this->mRoot->mContext->mModuleConfig ;	// mod_config
		//	adump($this->mod_config);
		//	adump($this->Ftp);
		//$this->targetKeyName = $this->mRoot->mContent->mRequest->getRequest('target_key');
		//$this->targetType = $this->mRoot->mContent->mRequest->getRequest('target_key');
		$this->targetKeyName = $this->Xupdate->get('target_key');
		$this->targetKeyName = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->targetKeyName ) ;
		$this->targetType = $this->Xupdate->get('target_type');
		$this->targetType = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->targetType ) ;

		$this->trust_dirname = $this->Xupdate->get('trust_dirname');
		$this->trust_dirname = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->trust_dirname ) ;
		$this->dirname = $this->Xupdate->get('dirname');
		$this->dirname = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->dirname ) ;


		$this->downloadDirPath = $this->Xupdate->params['temp_path'];
		$this->downloadUrlFormat = $this->mod_config['Mod_download_Url_format'];

	}

	public function execute(&$controller, &$xoopsUser)
	{
	}

	public function getDefaultView()
	{
		return XUPDATE_FRAME_VIEW_SUCCESS;
	}

	/**
	 * executeViewSuccess
	 *
	 * @param	XCube_RenderTarget	&$render
	 *
	 * @return	void
	 **/

	public function executeViewSuccess(&$render)
	{

		$result = true;
		if( $this->Xupdate->params['is_writable']['result'] === true ) {

			if ($this->_downloadFile()){
				if($this->_unzipFile()==true) {
					// ToDo port , timeout
					if($this->Ftp->app_login("127.0.0.1")==true) {
						$this->uploadFiles();
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
			if ($result){
				$this->content.= $this->_get_nextlink($this->targetKeyName);
			}else{
				$this->content.= _ERRORS;
			}
		}else{
			$result = false;
		}

		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);
		$render->setAttribute('xupdate_content', $this->content);
		$render->setAttribute('xupdate_message', $this->Ftp->getMes());

		$render->setTemplateName('admin_module_install.html');
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
	}

	/**
	 * @public
	 */
	protected function &_getHandler()
	{
	//	$handler =& $this->mAsset->load('handler', "Module");
	//	return $handler;
	}


	private function _downloadFile()
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
		@mkdir($this->targetKeyName);
		$this->exploredDirPath = realpath($this->downloadDirPath.'/'.$this->targetKeyName);
		//directory traversal check
		if (strpos($this->exploredDirPath, $realDirPath) === false){
			$this->_set_error_log('directory traversal error in: '.$this->downloadDirPath.'/'.$this->targetKeyName);
			return false;
		}
		if (!is_dir($this->exploredDirPath)){
			$this->_set_error_log('not is_dir error in: '.$this->downloadDirPath.'/'.$this->targetKeyName);
			return false;//chdir error
		}

		$this->Ftp->appendMes('downladed in: '.$this->downloadDirPath.'<br />');
		$this->content.= 'downladed in: '.$this->downloadDirPath.'<br />';
		if (! chdir($this->exploredDirPath) ) {
			$this->_set_error_log('chdir error in: '.$this->downloadDirPath);
			return false;//chdir error
		}

		// TODO ファイルNotFound対策
		$url = sprintf($this->downloadUrlFormat, $this->targetKeyName);
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
			if(!$setopt1 || !$setopt2 || !$setopt2 ){
				throw new Exception('curl_setopt fail',3);
			}
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());

			fclose($fp);
			return false;
		}

		try {
			try {
				if(!function_exists('curl_init') ){
					throw new Exception('curl_init function not found fail',4);
				}
			} catch (Exception $e) {
				$this->_set_error_log($e->getMessage());
				return false;
			}

			$result = curl_exec($ch);
			if($result === false ){
				throw new Exception('curl_exec fail',5);
			}
			$this->Ftp->appendMes('curl exec OK<br />');
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());
			return false;
		}

		fclose($fp);

		return true;
	}

	private function _unzipFile()
	{
		// local file name
		$downloadPath = $this->_getDownloadFilePath();

		if (! chdir($this->exploredDirPath) ) {
			$this->_set_error_log('chdir error in: '.$this->exploredDirPath);
			return false;//chdir error
		}

		try {
			if(!class_exists('ZipArchive') ){
				throw new Exception('ZipArchive class fail',1);
			}
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());
			return false;
		}
		$zip = new ZipArchive;

//		if ($zip->open($downloadPath) === TRUE) {
		try {
			 $result = $zip->open($downloadPath);
			if($result !==true ){
				throw new Exception('ZipArchive open fail ',2);
			}
		} catch (Exception $e) {
			$zip_open_error_arr = array(
				ZIPARCHIVE::ER_EXISTS => 'ER_EXISTS',
				ZIPARCHIVE::ER_INCONS => 'ER_INCONS',
				ZZIPARCHIVE::ER_INVAL => 'ER_INVAL',
				ZZIPARCHIVE::ER_MEMORY => 'ER_MEMORY',
				ZZIPARCHIVE::ER_NOENT => 'ER_NOENT',
				ZIPARCHIVE::ER_NOZIP => 'ER_NOZIP',
				ZIPARCHIVE::ER_OPEN => 'ER_OPEN',
				ZIPARCHIVE::ER_READ => 'ER_READ',
				ZIPARCHIVE::ER_SEEK => 'ER_SEEK'
			);
			$this->_set_error_log($e->getMessage().(in_array($result,$zip_open_error_arr) ? f : 'undfine' ));
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
		$this->Ftp->appendMes('explored in: '.$this->exploredDirPath.'<br />');
		$this->content.= 'explored in: '.$this->exploredDirPath.'<br />';

		return true;
	}

	private function _upload () {

		$this->Ftp->app_login("127.0.0.1") ;
		//$this->uploadFiles();
		//$this->Ftp->app_logout();

	}

	private function uploadFiles()
	{
		//$this->Ftp->connect();

		$this->Ftp->appendMes( 'start uploading..<br />');
		$this->content.=  'uploading..<br />';

		if ($this->targetType == 'TrustModule'){
			if (!empty($this->trust_dirname) && !empty($this->dirname) && $this->trust_dirname != $this->dirname){

				// copy xoops_trust_path
				$uploadPath = XOOPS_TRUST_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
				$this->Ftp->uploadNakami($unzipPath, $uploadPath);

				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/modules/' ;
				$unzipPath =  $this->exploredDirPath .'/html/modules';
				$this->Ftp->uploadNakami_To_module($unzipPath, $uploadPath ,$this->trust_dirname,$this->dirname);

				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath .'/html';
				$this->Ftp->uploadNakami_OtherThan_module($unzipPath, $uploadPath ,$this->trust_dirname,$this->dirname);

			}else{

				// copy xoops_trust_path
				$uploadPath = XOOPS_TRUST_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
				$this->Ftp->uploadNakami($unzipPath, $uploadPath);

				// copy html
				$uploadPath = XOOPS_ROOT_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath .'/html';
				$this->Ftp->uploadNakami($unzipPath, $uploadPath);
			}
		}else{

			// copy html
			$uploadPath = XOOPS_ROOT_PATH . '/' ;
			$unzipPath =  $this->exploredDirPath .'/html';
			$this->Ftp->uploadNakami($unzipPath, $uploadPath);
		}



	}

	private function _getDownloadFilePath()
	{
		//$downloadPath = sprintf( $this->downloadUrlFormat, 'd3diary') ;
		$downloadPath = $this->downloadDirPath .'/'. $this->targetKeyName . '.tgz';
		//$downloadPath = TP_ADDON_MANAGER_TMP_PATH .'/'. $this->targetKeyName . '.tgz';
		return $downloadPath;
	}

	private function _cleanup($dir)
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

	private function _get_nextlink($targetKeyName)
	{
		$ret ='';
		$hModule = Xupdate_Utils::getXoopsHandler('module');
		$module =& $hModule->getByDirname($targetKeyName) ;
		if (is_object($module)){
			if ($module->getVar('isactive') ){
				$ret ='<a href="'.XOOPS_URL.'/modules/legacy/admin/index.php?action=ModuleUpdate&dirname='.$targetKeyName.'">'._MI_XUPDATE_LANG_UPDATE.'</a>';
			}else{
				$ret =_AD_LEGACY_LANG_BLOCK_INACTIVETOTAL;
			}
		}else{
			$ret ='<a href="'.XOOPS_URL.'/modules/legacy/admin/index.php?action=ModuleInstall&dirname='.$targetKeyName.'">'._MI_XUPDATE_LANG_UPDATE.'</a>';
		}
		return $ret;
	}

	private function _set_error_log($msg)
	{
		$this->Ftp->appendMes('<span style="color:red;">'.$msg.'</span><br />');
		$this->content.= '<span style="color:red;">'.$msg.'</span><br />';
	}



}

?>