<?php

// Xupdate_ftp excutr function
// if(!class_exists('ZipArchive') ){
// 	$mod_zip=false;
// 	if (!extension_loaded('zip')) {
// 		if (function_exists('dl')){
// 			$prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
// 			if(@dl($prefix . 'zip.' . PHP_SHLIB_SUFFIX)){
// 				$mod_zip=true;
// 			}
// 		}
// 	}
// 	if(!class_exists('ZipArchive') ){
// 		require_once XUPDATE_TRUST_PATH .'/include/FtpCommonFileArchive.class.php';
// 	}else{
// 		require_once XUPDATE_TRUST_PATH .'/include/FtpCommonZipArchive.class.php';
// 	}
// }else{
// 	require_once XUPDATE_TRUST_PATH .'/include/FtpCommonZipArchive.class.php';
// }
require_once XUPDATE_TRUST_PATH .'/include/FtpCommonFileArchive.class.php';

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

	public $nextlink ;

	public $target_key;
	public $target_type;
*/

	public $trust_dirname;
	public $dirname;
	public $html_only;
	
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
			$is_upload_retry = isset($_POST['upload_retry']);
			
			$GLOBALS['xupdate_stage'] = 1;
			
			// clean up download dirctory
			if (! $is_upload_retry) {
				$this->_cleanUp_downloadDir();
			}
			
			if(! $this->checkExploredDirPath($this->target_key)) {
				$this->_set_error_log(_MI_XUPDATE_ERR_MAKE_EXPLOREDDIR . ': ' .$this->target_key);
				return false;
			}
			if (! $this->is_xupdate_excutable()) {
				$this->content.= '<div class="error">' . _MI_XUPDATE_ANOTHER_PROCESS_RUNNING . '</div>';
				return false;
			}
			
			
			$downloadUrl = $this->Func->_getDownloadUrl( $this->target_key, $this->downloadUrlFormat );
			
			if ($caller === 'preload' && preg_match('/\.php$/i', $downloadUrl)) {
				$this->download_file = $this->target_key . '.class.php';
			} else {
				$this->download_file = $this->target_key . (preg_match('/\btar\b/i', $downloadUrl)? '.tar.gz' : '.zip');
			}
			
			register_shutdown_function('xupdate_on_shutdown', $this->Xupdate->params['temp_path'], $downloadUrl);
			
			$this->content.= _MI_XUPDATE_PROG_FILE_GETTING . '<br />';
			if ($is_upload_retry || $this->Func->_downloadFile( $this->target_key, $downloadUrl, $this->download_file, $this->downloadedFilePath )){
				$GLOBALS['xupdate_stage'] = 2;
				$downloadDirPath = realpath($this->Xupdate->params['temp_path']);
				$exploredRoot = $this->exploredDirPath = realpath($downloadDirPath.'/'.$this->target_key);
				if ($is_upload_retry) {
					$this->downloadedFilePath = $this->Func->_getDownloadFilePath( $downloadDirPath, $this->download_file );
				}
				if($this->_unzipFile($caller)) {
					$GLOBALS['xupdate_stage'] = 3;
					if ($caller === 'preload') {
						$set_member = 'exploredPreloadPath';
						$serach_file = $this->target_key . '.class.php';
					} else {
						$set_member = $serach_file = '';
					}
					// ディレクトリを掘り下げて探索
					if ($this->exploredPreloadPath || $this->_exploredDirPath_DownDir($set_member, $serach_file)) {
						// TODO port , timeout
						if ($this->Ftp->isConnected() || $this->Ftp->app_login()==true) {
							$GLOBALS['xupdate_stage'] = 4;
							// overwrite control
							if(! isset($this->options['no_overwrite'])){
								$this->options['no_overwrite'] = array();
							}
							if(! isset($this->options['install_only'])){
								$this->options['install_only'] = array();
							}
							$this->Ftp->set_no_overwrite(array($this->options['no_overwrite'], $this->options['install_only']));
							$GLOBALS['xupdate_stage'] = 5;
							if (!$this->uploadFiles()){
								$this->_set_error_log(_MI_XUPDATE_ERR_FTP_UPLOADFILES);
								$result = false;
							}
							$GLOBALS['xupdate_stage'] = 6;
							
							$this->_set_item_perm();
							
							$GLOBALS['xupdate_stage'] = 7;
						} else {
							$this->_set_error_log(_MI_XUPDATE_ERR_FTP_LOGIN);
							$result = false;
						}
					} else {
						$this->_set_error_log(_MI_XUPDATE_ERR_FTP_NOTFOUND);
						$result = false;
					}

				}else{
					$this->_set_error_log(_MI_XUPDATE_ERR_UNZIP_FILE);
					$result = false;
				}
			}else{
				$this->_set_error_log(_MI_XUPDATE_ERR_DOWNLOAD_FILE);
				$result = false;
			}

			$this->content.= _MI_XUPDATE_PROG_CLEANING_UP . '<br />';
			$this->_cleanup($exploredRoot);

			if ($this->Ftp->isConnected()) {
				$this->Ftp->app_logout();
			}
			//

//TODO unlink ok?
			@unlink( $this->downloadedFilePath );

			$this->content.= _MI_XUPDATE_PROG_COMPLETED . '<br /><br />';
			
			@ unlink($this->lockfile);
		}else{
			$result = false;
		}

		if ($result){
			$this->nextlink = $this->_get_nextlink($this->dirname, $caller);
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
		//$url = sprintf($this->downloadUrlFormat, $this->target_key);
		$url = str_replace($this->downloadUrlFormat, '%s', $this->target_key);
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
		$this->content.= _MI_XUPDATE_PROG_UPLOADING . '<br />';

		if ($this->target_type == 'TrustModule'){
			if (!empty($this->trust_dirname) && !empty($this->dirname) && $this->trust_dirname != $this->dirname){

				if (! $this->html_only) {
					// copy xoops_trust_path
					$uploadPath = XOOPS_TRUST_PATH . '/' ;
					$unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
					$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
					if (! $this->_check_file_upload_result($result, 'xoops_trust_path')){
						return false;
					}
				}

				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/modules/' ;
				$unzipPath =  $this->exploredDirPath .'/html/modules';
				$result = $this->Ftp->uploadNakami_To_module($unzipPath, $uploadPath ,$this->trust_dirname,$this->dirname);
				if (! $this->_check_file_upload_result($result, 'html/modules')){
					return false;
				}

				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath .'/html';
				$result = $this->Ftp->uploadNakami_OtherThan_module($unzipPath, $uploadPath ,$this->trust_dirname);
				if (! $this->_check_file_upload_result($result, 'html', true)){
					return false;
				}

			}else{

				if (! $this->html_only) {
					// copy xoops_trust_path
					$uploadPath = XOOPS_TRUST_PATH . '/' ;
					$unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
					$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
					if (! $this->_check_file_upload_result($result, 'xoops_trust_path')){
						return false;
					}
				}

				// copy html
				$uploadPath = XOOPS_ROOT_PATH . '/' ;
				$unzipPath =  $this->exploredDirPath .'/html';
				$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
				if (! $this->_check_file_upload_result($result, 'html')){
					return false;
				}
			}
			
		} else if ($this->exploredPreloadPath) {
			
			// copy html/preload
			$uploadPath = XOOPS_ROOT_PATH . '/preload/' ;
			$unzipPath =  $this->exploredPreloadPath;
			$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
			if (! $this->_check_file_upload_result($result, 'html/preload')){
				return false;
			}
			
		} else {
			
			// copy xoops_trust_path if exists
			$uploadPath = XOOPS_TRUST_PATH . '/' ;
			$unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
			if (file_exists($unzipPath)) {
				$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
				if (! $this->_check_file_upload_result($result, 'xoops_trust_path')){
					return false;
				}
			}
			
			// copy html
			$uploadPath = XOOPS_ROOT_PATH . '/' ;
			$unzipPath =  $this->exploredDirPath .'/html';
			$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
			if (! $this->_check_file_upload_result($result, 'html')){
				return false;
			}
			
			// for legacy core extra languages
			if ($this->dirname === 'legacy') {
				// copy extras languages
				$langs = array();
				if ($handle = opendir(XOOPS_ROOT_PATH . '/language')) {
					while (false !== ($name = readdir($handle))) {
						if ($name[0] !== '.' && is_dir(XOOPS_ROOT_PATH . '/language/' . $name)) {
							$langs[] = $name;
						}
					}
					closedir($handle);
				}
				//adump($langs);
				foreach ($langs as $lang) {
					$uploadPath = XOOPS_ROOT_PATH . '/' ;
					$unzipPath =  $this->exploredDirPath . '/extras/extra_languages/' . $lang;
					if (file_exists($unzipPath)) {
						$result = $this->Ftp->uploadNakami($unzipPath, $uploadPath);
						if (! $this->_check_file_upload_result($result, 'html')){
							return false;
						}
					}
				}
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
		if ($caller === 'module') {
			$hModule = Xupdate_Utils::getXoopsHandler('module');
			$module =& $hModule->getByDirname($dirname) ;
			if (is_object($module)){
				if ($module->getVar('isactive') ) {
					$ret ='<a href="'.XOOPS_MODULE_URL.'/legacy/admin/index.php?action=ModuleUpdate&dirname='.$dirname.'">'._MI_XUPDATE_ADMENU_MODULE._MI_XUPDATE_UPDATE.'</a>';
				} else {
					$ret =_AD_LEGACY_LANG_BLOCK_INACTIVETOTAL;
				}
			} else if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $dirname)) {
				$ret ='<a href="'.XOOPS_MODULE_URL.'/legacy/admin/index.php?action=ModuleInstall&dirname='.$dirname.'">'._MI_XUPDATE_ADMENU_MODULE._INSTALL.'</a>';
			} else {
				$ret ='<a href="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleStore">'._AD_XUPDATE_LANG_MESSAGE_GETTING_FILES._AD_XUPDATE_LANG_MESSAGE_SUCCESS.'</a>';
			}

		} elseif ($caller === 'theme') {
			$ret ='<a href="'.XOOPS_MODULE_URL.'/legacy/admin/index.php?action=ThemeList">'._MI_XUPDATE_ADMENU_THEME._MI_XUPDATE_MANAGE.'</a>';
		} elseif ($caller === 'preload') {
			$ret ='<a href="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=PreloadStore">'._AD_XUPDATE_LANG_MESSAGE_GETTING_FILES._AD_XUPDATE_LANG_MESSAGE_SUCCESS.'</a>';
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
	private function _exploredDirPath_DownDir($member = '', $checkfile = '')
	{
		$dir = $this->exploredDirPath;
		$this->Ftp->appendMes('check exploredDirPath: '.$this->exploredDirPath.'<br />');
		$items = scandir($dir);
		$checker = array();
		foreach($items as $item) {
			if ($item === '.' || $item === '..' || $item === '__MACOSX'){
				continue;
			}
			if (is_dir($dir.'/'.$item)) {
				$checker[$item] = true;
			}
			if ($member && $checkfile) {
				if (is_file($dir.'/'.$checkfile)) {
					$this->Ftp->appendMes('found '.$checkfile.' in exploredDirPath: '.$this->exploredDirPath.'<br />');
					$this->$member = $this->exploredDirPath;
					return true;
				}
			}
		}
		if (isset($checker['html']) || isset($checker['xoops_trust_path'])) {
			$this->Ftp->appendMes('found files exploredDirPath: '.$this->exploredDirPath.'<br />');
			return true;
		}
		foreach (array_keys($checker) as $item) {
			$this->exploredDirPath = realpath($dir.'/'.$item);
			if ($this->_exploredDirPath_DownDir($member, $checkfile)) {
				return true;
			}
		}
		return false;
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

	/**
	 * _chmod_dir
	 *
	 * @param   string $directory
	 *
	 * @return	void
	 **/
	private function _chmod_dir( &$directory)
	{
		if (! file_exists($directory)) {
			$this->Ftp->localMkdir($directory);
		}
		if(file_exists($directory) && is_dir($directory)){
			$this->Ftp->localChmod($directory, 0707);
		}
	}

	/**
	 * _chmod_file
	 *
	 * @param   string $directory
	 *
	 * @return	void
	 **/
	private function _chmod_file( &$directory)
	{
		if(file_exists($directory) && !is_dir($directory)){
			$this->Ftp->localChmod($directory, 0606);
		}
	}
	
	private function _delete($path) {
		if (is_file($path)) {
			$this->Ftp->localDelete($path);
		}
	}
	
	private function _rmdir_recursive($path) {
		if (is_dir($path)) {
			$this->Ftp->localRmdirRecursive($path);
		}
	}
	
	public function _cleanUp_downloadDir() {
		$path = realpath($this->Xupdate->params['temp_path']);
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry !== '.' && $entry !== '..' && is_dir($path . DIRECTORY_SEPARATOR . $entry)) {
					$this->_cleanup($path . DIRECTORY_SEPARATOR . $entry);
				}
			}
			closedir($handle);
		}
	}
	
	private function _set_item_perm() {
		// change directories to writable
		if(isset($this->options['writable_dir'])){
			array_map(array($this, '_chmod_dir'),$this->options['writable_dir']);
		}
		// change files to writable
		if(isset($this->options['writable_file'])){
			array_map(array($this, '_chmod_file'),$this->options['writable_file']);
		}
		// delete dirs recursive
		if(isset($this->options['delete_dir'])){
			array_map(array($this, '_rmdir_recursive'),$this->options['delete_dir']);
		}
		// delete files
		if(isset($this->options['delete_file'])){
			array_map(array($this, '_delete'),$this->options['delete_file']);
		}
	}
} // end class

/**
 * Called on shutdown of PHP
 *
 * @param string $cache_dir
 * @param string $download_url
 */
function xupdate_on_shutdown($cache_dir, $download_url) {
	$lock_file = realpath($cache_dir) . '/xupdate.lock';
	$retry_cache_file = realpath($cache_dir) . '/retry_cache.ser';
	@ unlink($retry_cache_file);
	if (connection_status() > 1 || is_file($lock_file)) {
		@ unlink($lock_file);
		file_put_contents($retry_cache_file, serialize($GLOBALS['xupdate_retry_cache']));
		$buf = '';
		while (ob_get_level()) {
			$buf .= ob_get_contents();
			if (! @ ob_end_clean()) {
				break;
			}
		}
		$msg = array();
		$is_upload_retry = isset($_POST['upload_retry']);
		$uploaded_count = count($GLOBALS['xupdate_retry_cache']['uploaded_files']);
		$uploaded_count_before = isset($_POST['uploaded_count'])? $_POST['uploaded_count'] : 0;
		$total_files = 0;
		if (isset($GLOBALS['xupdate_retry_cache']['file_list'])) {
			foreach($GLOBALS['xupdate_retry_cache']['file_list'] as $list) {
				$total_files += count($list['file']);
			}
		}
		$msg[] = '<html><head><title>'._AD_XUPDATE_LANG_TIMEOUT_ERROR.'</title></head><body>';
		$msg[] = '<h1>'._AD_XUPDATE_LANG_TIMEOUT_ERROR.'</h1>';
		$start = $is_upload_retry? 3 : 1;
		for ($i = $start; $i <= $GLOBALS['xupdate_stage']; $i++) {
			$done_files = '';
			if ($i === 5) {
				$done_files = ' ('._AD_XUPDATE_LANG_MESSAGE_SUCCESS.': '.$uploaded_count.'/'.$total_files.')';
			}
			$msg[] = constant('_AD_XUPDATE_LANG_STAGE_'.$i) . $done_files;
		}
		$msg[] = _AD_XUPDATE_LANG_STAGE_TIMEOUT;
		if ($GLOBALS['xupdate_stage'] < 5) {
			$msg[] = sprintf(_AD_XUPDATE_LANG_STAGE_UPLOAD_NOT_COMPLETE, $download_url);
		} else if ($GLOBALS['xupdate_stage'] > 4) {
			$post = $_POST;
			unset($_POST['uploaded_count'], $_POST['upload_retry']);
			if (isset($_SESSION['XCUBE_TOKEN'])) {
				foreach($_SESSION['XCUBE_TOKEN'] as $key => $val) {
					if ($key) {
						$key = strtr($key, '.', '_');
						unset($post[$key]);
						$post[$key] = $val;
					}
				}
				$post['upload_retry'] = 1;
				$post['uploaded_count'] = $uploaded_count;
			}
			if ($is_upload_retry && $uploaded_count_before > $uploaded_count) {
				$msg[] = sprintf(_AD_XUPDATE_LANG_STAGE_UPLOAD_NOT_COMPLETE, $download_url);
			} else {
				$form = '<form method="post" action="./?'.$_SERVER['QUERY_STRING'].'" onsubmit="document.getElementById(\'retry\').disabled=\'disabled\'">';
				foreach($post as $key => $val) {
					if (is_array($val)) {
						foreach ($val as $_val) {
							$form .= '<input type="hidden" name="'.htmlspecialchars($key).'[]" value="'.htmlspecialchars($_val).'" />';
						}
					} else {
						$form .= '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($val).'" />';
					}
				}
				$form .= '<input id="retry" type="submit" value="'.(($GLOBALS['xupdate_stage'] === 5)? _AD_XUPDATE_LANG_STAGE_UPLOAD_RETRY : _AD_XUPDATE_LANG_STAGE_TASK_RETRY).'" />';
				$form .= '</form>';
				$msg[] = $form;
			}
		}
		if ($buf) {
			$msg[] = str_repeat('-', 20);
			$msg[] = 'PHP error message:';
			$msg[] = $buf;
		}
		$msg[] = '</body></html>';
		if (! headers_sent()) {
			header('Content-type: text/html; charset='._CHARSET);
		}
		echo join('<br />'."\n", $msg);
	}
}

?>