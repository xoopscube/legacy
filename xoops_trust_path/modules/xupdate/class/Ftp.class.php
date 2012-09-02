<?php

/* class Xupdate_Ftp  ## Base Class ##
*/
if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

if (!defined('_XUPDATE_FTP_CUSTOM')){
	define ('_XUPDATE_FTP_CUSTOM',	'0' ) ;
	define ('_XUPDATE_FTP_PHP_MODULE',	'1' ) ;
	define ('_XUPDATE_FTP_CUSTOM_SFTP',	'2' ) ;
	define ('_XUPDATE_FTP_CUSTOM_SSH2',	'3' ) ;
	define ('_XUPDATE_FTP_DIRECT',	'4' ) ;
}

// module config
$mod_config = XCube_Root::getSingleton()->mContext->mModuleConfig ;
//	adump($this->mod_config);

// FTP class
switch ( $mod_config['ftp_method'] ) {
	case _XUPDATE_FTP_CUSTOM :
		require_once dirname(__FILE__) . '/ftp/Custom.class.php';
		break;
	case _XUPDATE_FTP_PHP_MODULE :
		require_once dirname(__FILE__) . '/ftp/Phpfunc.class.php';
		break;
	case _XUPDATE_FTP_CUSTOM_SFTP :
		// To Do
		if (!defined('PATH_SEPARATOR')) {
			if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
				define('PATH_SEPARATOR', ':');
			} else {
				define('PATH_SEPARATOR', ';');
			}
		}
		set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/ftp/phpseclib');
		require_once dirname(__FILE__) . '/ftp/Sftp.class.php';
		break;
	case _XUPDATE_FTP_CUSTOM_SSH2 :
		// To Do
		if (!defined('PATH_SEPARATOR')) {
			if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
				define('PATH_SEPARATOR', ':');
			} else {
				define('PATH_SEPARATOR', ';');
			}
		}
		set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/ftp/phpseclib');
		require_once dirname(__FILE__) . '/ftp/Ssh2.class.php';
		break;
	case _XUPDATE_FTP_DIRECT :
		require_once dirname(__FILE__) . '/ftp/Direct.class.php';
		define('_XUPDATE_FTP_ROOT', '');
		break;
	default:
} // end switch


/* FTP class
*/

class Xupdate_Ftp extends Xupdate_Ftp_ {
	
	private $loginCheckFile;
	private $phpPerm;
	private $uploaded_files = array();
	
	/* Constructor */
	public function __construct($XupdateObj, $port_mode=FALSE, $verb=FALSE, $le=FALSE) {
		parent::__construct($XupdateObj);
		
		$this->loginCheckFile = XOOPS_TRUST_PATH.'/'.trim($this->mod_config['temp_path'], '/').'/'.rawurlencode(substr(XOOPS_URL, 7)).'_logincheck.ini.php';
		if (! empty($this->mod_config['php_perm'])) {
			$this->phpPerm = intval($this->mod_config['php_perm'], 8);
		}
		
		// for upload retry mode
		$retry_cache_file = XOOPS_TRUST_PATH.'/'.trim($this->mod_config['temp_path'], '/').'/retry_cache.ser';
		if (isset($_POST['upload_retry']) && is_file($retry_cache_file)) {
			if ($retry_cache = @unserialize(file_get_contents($retry_cache_file))) {
				$this->uploaded_files = $retry_cache['uploaded_files'];
				$GLOBALS['xupdate_retry_cache'] = $retry_cache;
			}
		}
		if (! isset($GLOBALS['xupdate_retry_cache'])) {
			$GLOBALS['xupdate_retry_cache'] = array();
		}
		$GLOBALS['xupdate_retry_cache']['uploaded_files'] = array();
	}

// <!-- --------------------------------------------------------------------------------------- -->
// <!--	   public functions																  -->
// <!-- --------------------------------------------------------------------------------------- -->

	public function getMes(){
		if($this->mod_config['Show_debug']) {
			return $this->mes;
		}
	}

	public function appendMes($message){
		$this->mes.= $message;
	}


	public function app_login($server = null){
		if (is_null($server)) {
			$server = (empty($this->mod_config['FTP_server']))? '127.0.0.1' : $this->mod_config['FTP_server'];
		}
		if (! $ret = parent::app_login($server)) {
			@ unlink($this->loginCheckFile);
		}
		return $ret;
	}

	public function uploadNakami($sourcePath, $targetPath)
	{
		$this->mes .= " start FTP put (normal mode) ".htmlspecialchars($targetPath ,ENT_QUOTES ,_CHARSET)." ..<br>\n";
		$result = $this->_ftpPutNakami($sourcePath, $targetPath);
		return $result;
	}
	//for module rename uploading
	public function uploadNakami_To_module($sourcePath, $targetPath , $trust_dirname , $dirname)
	{
		$this->mes .= " start FTP put (replicate module mode) ".htmlspecialchars($targetPath."->".$dirname ,ENT_QUOTES ,_CHARSET)." ..<br>\n";
		$result = $this->_ftpPutNakami($sourcePath, $targetPath , $trust_dirname , $dirname );
		return $result;
	}
	//for other than module uploading
	public function uploadNakami_OtherThan_module($sourcePath, $targetPath , $trust_dirname)
	{
		$this->mes .= " start FTP put (replicate misc mode) ".htmlspecialchars($targetPath ,ENT_QUOTES ,_CHARSET)." ..<br>\n";
		$result = $this->_ftpPutNakami($sourcePath, $targetPath , $trust_dirname );
		return $result;
	}

	public function app_logout(){
		$this->quit() ;
		return true;
	}

	public function chmod($pathname, $mode) {
		return parent::chmod($pathname, $mode);
	}

	/**
	 *  set_no_overwrite
	 *  
	 * @param array $no_overwrite
	 * @return void
	 */
	public function set_no_overwrite($no_overwrite) {
		$this->no_overwrite = $no_overwrite;
	}
	
	/**
	 * Make dirctory by server path
	 * 
	 * @param string $dir server path
	 * @return Ambigous <void, boolean>
	 */
	public function localMkdir($dir) {
		return $this->ftp_mkdir($dir);
	}
	
	/**
	 * Remove directory by server path
	 * 
	 * @param string $dir server path
	 * @return boolean
	 */
	public function localRmdir($dir) {
		return parent::rmdir($this->getLocalPath($dir));
	}
	
	/**
	 * chmod by server path
	 * 
	 * @param string $item server path
	 * @param integer $mode
	 * @return Ambigous <boolean, number, Mixed, unknown, string>
	 */
	public function localChmod($item, $mode) {
		return $this->chmod($this->getLocalPath($item), $mode);
	}
	
	/**
	 * delete file by server path
	 * 
	 * @param string $file server path
	 * @return boolean
	 */
	public function localDelete($file) {
		return parent::delete($this->getLocalPath($file));
	}
	
	/**
	 * remove directory recursive by server path
	 * 
	 * @param string $dir server path
	 * @return boolean
	 */
	public function localRmdirRecursive($dir) {
		$localDir = $this->getLocalPath($dir);
		if ($list = parent::nlist($localDir)) {
			$ftproot = $this->seekFTPRoot();
			foreach($list as $path) {
				$name = basename($path);
				if ($name !== '.' && $name !=='..') {
					$serverPath = $ftproot.$path;
					if (is_dir($serverPath)) {
						$this->localRmdirRecursive($serverPath);
					} else {
						$this->localDelete($serverPath);
					}
				}
			}
		}
		return parent::rmdir($localDir);
	}
	
	/**
	 * @return boolean
	 */
	public function checkLogin() {
		$checkKey = md5(serialize($this->mod_config));
		if (! ($ret = @ unserialize(@ file_get_contents($this->loginCheckFile))) || !is_array($ret) || !isset($ret[$checkKey])) {
			$ret = array();
			if ($this->app_login()) {
				$this->app_logout();
				$ret[$checkKey] = true;
			} else {
				$ret[$checkKey] = false;
			}
			file_put_contents($this->loginCheckFile, serialize($ret));
		}
		return $ret[$checkKey];
	}
	
	/**
	 * @return boolean
	 */
	public function isConnected() {
		return $this->_connected;
	}
	
// <!-- --------------------------------------------------------------------------------------- -->
// <!--	   protected functions																  -->
// <!-- --------------------------------------------------------------------------------------- -->


	/**
	 * ftp rootの絶対パスを返す ex /home/ryuji/public_htmlにxoopsがあり、ftp rootが /home/ryuji/ だったら 戻り値は /home/ryuji
	 *　さらに $xoops_root_pathで指定されたディレクトリへ移動する
	 *  // @param string $con  // removed
	 * @param string $xoops_root_path
	 * @return void
	 * @author ryuji
	 * DIRECTORY_SEPARETERを使わないで'/'にしている。WinFileZillaでセパレータに\を使うとftp_chdirできないため
	 *
	 */
	protected function seekFTPRoot()
	{
		if (defined('_XUPDATE_FTP_ROOT')) {
			return _XUPDATE_FTP_ROOT;
		}
		
		$xoops_root_path = $this->XupdateObj->xoops_root_path;
		static $ftp_root ;

		if (!is_null($ftp_root)){
			return $ftp_root ;
		}

		$xoops_root_path = str_replace( "\\","/",$xoops_root_path );
		$path = explode('/', $xoops_root_path);
		//$path = preg_split( '///', $xoops_root_path, PREG_SPLIT_NO_EMPTY );

		$current_path = '';
		for ($i=count($path)-1; $i>=0 ;$i--){
			$current_path = '/'.$path[$i].$current_path;
			if ( $this->chdir($current_path) ){
				$ftp_root = substr($xoops_root_path, 0, strrpos($xoops_root_path, $current_path));
				return $ftp_root;
			}
		}
		if ($this->chdir('/')) {
			// May be XOOPS_ROOT_PATH is FTP root
			return $xoops_root_path;
		}

		//throw new Exception(t("seekFTP fail"), 1);
		$this->mes .= " seekFTP fail<br>\n";//TODO WHY fail?
		return false;
	}

	protected function _makeTmpDir()
	{
		// 最初の1回だけ作成

		chdir( $this->XupdateObj->params['temp_path'] );
		if(mkdir( $this->XupdateObj->params['temp_dirname'] ) === false){
			exit();
			throw new Exception(t("make temporary directory fail"), 1);
		}
	}
/*
	protected function _doUnzip($file)
	{
		chdir($this->params['temp_path']);

		$zip = new ZipArchive;
		if ($zip->open($file) === TRUE) {
			$zip->extractTo('./');
			$zip->close();
		} else {
			throw new Exception("unzip fail", 1);
		}

		return true;
	}
*/
	// remove directories recursively
	protected function removeDirectory($dir) {
		if ($handle = opendir("$dir")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dir/$item")) {
						$this->removeDirectory("$dir/$item");
					} else {
						unlink("$dir/$item");
						//$this->mes .= " removing $dir/$item<br>\n";
					}
				}
			}
			closedir($handle);
			rmdir($dir);
		}
	}

	private function _ftpPutNakami($local_path, $remote_path, $trust_dirname = null, $dirname = null )
	{
		$remote_pos = strlen($local_path) + 1;
		//$this->mes .= $remote_pos. "<br>\n";
		$result = $this->_ftpPutSub($local_path, $remote_path, $remote_pos, $trust_dirname, $dirname);
		return $result;
	}

	private function _ftpPutSub($local_path, $remote_path, $remote_pos, $trust_dirname = null, $dirname = null)
	{
		$ftp_root = $this->seekFTPRoot();
		if ($ftp_root === false){
			return false;
		}
		$mode = ($trust_dirname && $dirname)? 'repModule' : ($trust_dirname? 'repMisc' : 'normal');
		if (! isset($GLOBALS['xupdate_retry_cache']['file_list'])) {
			$GLOBALS['xupdate_retry_cache']['file_list'] = array();
			$GLOBALS['xupdate_retry_cache']['dir_cnt'] = array();
		}
		if (! isset($GLOBALS['xupdate_retry_cache']['file_list'][$local_path])) {
			$file_list = $this->_getFileList($local_path);
			$GLOBALS['xupdate_retry_cache']['file_list'][$local_path] = $file_list;
			$GLOBALS['xupdate_retry_cache']['dir_cnt'][$local_path] = array();
		} else {
			$file_list = $GLOBALS['xupdate_retry_cache']['file_list'][$local_path];
		}
		if (! isset($GLOBALS['xupdate_retry_cache']['dir_cnt'][$local_path][$mode])) {
			$dir_cnt = 0;
			if (isset($file_list['dir']) && is_array($file_list['dir'])) {
				$dir = $file_list['dir'];
				krsort($dir);
				foreach ($dir as $directory){
					if ($mode === 'repMisc') {
						if (strstr($directory ,'/modules/'.$trust_dirname)){
							continue;
						}
					} else if ($mode === 'repModule') {
						$directory = str_replace('/modules/'.$trust_dirname, '/modules/'.$dirname, $directory);
					}
					$remote_directory = $remote_path.substr($directory, $remote_pos);
					if (!is_dir($remote_directory) && !$this->_dont_overwrite($remote_directory, true)){
						$this->ftp_mkdir($remote_directory);
					}
					$dir_cnt++;
				}
			}
			$GLOBALS['xupdate_retry_cache']['dir_cnt'][$local_path][$mode] = $dir_cnt;
		} else {
			$dir_cnt = $GLOBALS['xupdate_retry_cache']['dir_cnt'][$local_path][$mode];
		}

		// file nothing
		if (empty($file_list['file'])) {
			return true;
		}
		
		/// put files
		if (! $this->chdir('/') ){
			return false;
		}
		$res = array('ok' => $dir_cnt, 'ng' => array());
		$uploaded_files =& $GLOBALS['xupdate_retry_cache']['uploaded_files'];
		foreach ($file_list['file'] as $l_file){
			// check done file on upload retry mode
			if (isset($this->uploaded_files[$l_file])) {
				$uploaded_files[$l_file] = $this->uploaded_files[$l_file];
				if ($this->uploaded_files[$l_file] === true) {
					$res['ok']++;
				} else if ($this->uploaded_files[$l_file]) {
					$res['ng'][] = $this->uploaded_files[$l_file];
				}
				continue;
			}
			
			if ($mode === 'repMisc') {
				if (strstr($l_file, '/modules/'.$trust_dirname.'/')){
					$uploaded_files[$l_file] = false; // for update retry mode
					continue;
				}
				$r_file = $remote_path.substr($l_file, $remote_pos ); // +1 is remove first flash
			} else if ($mode === 'repModule') {
				//rename dirname
				$r_file = $remote_path.substr(str_replace('/modules/'.$trust_dirname.'/', '/modules/'.$dirname.'/', $l_file), $remote_pos ); // +1 is remove first flash
			} else {
				$r_file = $remote_path.substr($l_file, $remote_pos ); // +1 is remove first flash
			}
			$ftp_remote_file = substr($r_file, strlen($ftp_root));
			$dont_overwrite = $this->_dont_overwrite($r_file);
			if ( $dont_overwrite === false && !$this->put($l_file, $ftp_remote_file) ){
				$res['ng'][] = $ftp_remote_file;
				$uploaded_files[$l_file] = $ftp_remote_file;
			} else {
				$res['ok']++;
				$this->setPhpPerm($ftp_remote_file);
				$uploaded_files[$l_file] = true; // for update retry mode
			}
		}
		return $res;
	}

	private function _getFileList($dir, $list=array('dir'=> array(), 'file' => array()))
	{
		if (is_dir($dir) == false) {
			return array();
		}

		$dh = opendir($dir);
		if ($dh) {
			while (($file = readdir($dh)) !== false) {
				if ($file == '.' || $file == '..'){
					continue;
				}
				else if (is_dir("$dir/$file")) {
					$list = $this->_getFileList("$dir/$file", $list);
					$list['dir'][] = "$dir/$file";
				}
				else {
					$list['file'][] = "$dir/$file";
				}
			}
		}
		closedir($dh);
		return $list;
	}

	private function _dont_overwrite($file, $dir_chk = false)
	{
		list($no_overwrite, $install_only) = $this->no_overwrite;
		if ($install_only) {
			foreach ($install_only as $item) {
				if( strpos($file, $item) === 0){
					return true;
				}
			}
		}
		if (!$dir_chk && $no_overwrite) {
			foreach ($no_overwrite as $item) {
				if( strpos($file, $item) === 0 && file_exists($file)){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * undocumented function
	 *
	 *  @param string $dir ローカルの絶対パス
	 * @return void
	 * @author ryuji
	 */
	protected function ftp_mkdir($dir)
	{
		return $this->ftpMkdirByFtpPath($this->getLocalPath($dir));
	}

	/**
	 * ftp root からのパスで$dirは指定する
	 *
	 * @param string $dir
	 * @return void
	 * @author ryuji
	 */
	protected function ftpMkdirByFtpPath($dir)
	{
		$parent = dirname($dir);
		if ($dir === $parent) {
			return true;
		}

		$ftpRoot = $this->seekFTPRoot();
		if (is_dir($ftpRoot.$parent) === false) {
			if ($this->ftpMkdirByFtpPath( $parent) === false) {
				return false;
			}
		}
		return $this->mkdir($dir);
	}
	
	/**
	 * Set permission of .php
	 * 
	 * @param string $file
	 */
	private function setPhpPerm($file) {
		if ($this->phpPerm && strtolower(substr($file, -4)) === '.php') {
			$this->chmod($file, $this->phpPerm);
		}
	}
	
	/**
	 * Get local(on FTP) path
	 * 
	 * @param string $path
	 * @return string
	 */
	private function getLocalPath($path) {
		static $FTP_root_len = null;
		if (is_null($FTP_root_len)) {
			$FTP_root_len = strlen($this->seekFTPRoot());
		}
		$localPath = substr($path, $FTP_root_len);
		return $localPath;
	}

}// end class


?>