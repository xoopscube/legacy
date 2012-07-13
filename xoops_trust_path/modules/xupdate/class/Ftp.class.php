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
	default:
} // end switch


/* FTP class
*/

class Xupdate_Ftp extends Xupdate_Ftp_ {
	
	private $loginCheckFile;
	
	/* Constructor */
	public function __construct($XupdateObj, $port_mode=FALSE, $verb=FALSE, $le=FALSE) {
		parent::__construct($XupdateObj);
		$this->loginCheckFile = XOOPS_TRUST_PATH.'/'.trim($this->mod_config['temp_path'], '/').'/logincheck.ini.php';
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


	public function app_login($server){
		if (! $ret = parent::app_login($server)) {
			@ unlink($this->loginCheckFile);
		}
		return $ret;
	}

	public function uploadNakami($sourcePath, $targetPath)
	{
		$this->mes .= " start uploadNakami ".htmlspecialchars($targetPath ,ENT_QUOTES ,_CHARSET)." ..<br>\n";
		$result = $this->_ftpPutNakami($sourcePath, $targetPath);
		return $result;
	}
	//for module rename uploading
	public function uploadNakami_To_module($sourcePath, $targetPath , $trust_dirname , $dirname)
	{
		$this->mes .= " start uploadNakami_To_module ".htmlspecialchars($targetPath."->".$dirname ,ENT_QUOTES ,_CHARSET)." ..<br>\n";
		$result = $this->_ftpPutNakami_To_module($sourcePath, $targetPath , $trust_dirname , $dirname );
		return $result;
	}
	//for other than module uploading
	public function uploadNakami_OtherThan_module($sourcePath, $targetPath , $trust_dirname , $dirname)
	{
		$this->mes .= " start uploadNakami_OtherThan_module ".htmlspecialchars($targetPath ,ENT_QUOTES ,_CHARSET)." ..<br>\n";
		$result = $this->_ftpPutNakami_OtherThan_module($sourcePath, $targetPath , $trust_dirname , $dirname );
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
	 * @param array $no_overwrite
	 * @return void
	 */
	public function set_no_overwrite($no_overwrite) {
		$this->no_overwrite = $no_overwrite;
	}
	
	public function localMkdir($dir) {
		return $this->ftp_mkdir($dir);
	}
	
	public function localRmdir($dir) {
		$ftpRoot = $this->seekFTPRoot();
		$localDir = substr($dir, strlen($ftpRoot));
		return parent::rmdir($localDir);
	}
	
	public function localChmod($dir, $mode) {
		$ftpRoot = $this->seekFTPRoot();
		$localDir = substr($dir, strlen($ftpRoot));
		return $this->chmod($localDir, $mode);
	}
	
	public function checkLogin() {
		$ret = true;
		$this->loginCheckFile = XOOPS_TRUST_PATH.'/'.trim($this->mod_config['temp_path'], '/').'/logincheck.ini.php';
		if (! @ unserialize(@ file_get_contents($this->loginCheckFile))) {
			if ($this->app_login('127.0.0.1')) {
				$this->app_logout();
				$ret = true;
			} else {
				$ret = false;
			}
			file_put_contents($this->loginCheckFile, serialize($ret));
		}
		return $ret;
	}
	
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

	protected function _ftpPutNakami($local_path, $remote_path)
	{
		$remote_pos = strlen($local_path) + 1;
		$this->mes .= $remote_pos. "<br>\n";
		////adump($local_path);
		////adump($remote_path);
		$result = $this->_ftpPutSub($local_path, $remote_path, $remote_pos);
		return $result;
	}
	protected function _ftpPutNakami_To_module($local_path, $remote_path , $trust_dirname , $dirname )
	{
		$remote_pos = strlen($local_path) + 1;
		$this->mes .= $remote_pos. "<br>\n";
		////adump($local_path);
		////adump($remote_path);
		$result = $this->_ftpPutSub_To_module($local_path, $remote_path, $remote_pos , $trust_dirname , $dirname );
		return $result;
	}
	protected function _ftpPutNakami_OtherThan_module($local_path, $remote_path , $trust_dirname , $dirname )
	{
		$remote_pos = strlen($local_path) + 1;
		$this->mes .= $remote_pos. "<br>\n";
		////adump($local_path);
		////adump($remote_path);
		$result = $this->_ftpPutSub_OtherThan_module($local_path, $remote_path, $remote_pos , $trust_dirname , $dirname );
		return $result;
	}

	protected function _ftpPutSub($local_path, $remote_path, $remote_pos)
	{
		$ftp_root = $this->seekFTPRoot();
		if ($ftp_root===false){
			return false;
		}
		$file_list = $this->_getFileList($local_path);
		if (!isset($file_list['dir']) ){
			return true;
		}
		if (!is_array($file_list['dir']) ){
			return true;
		}
		$dir = $file_list['dir'];
		krsort($dir);
		foreach ($dir as $directory){
			$remote_directory = $remote_path.substr($directory, $remote_pos);
			if (!is_dir($remote_directory)){
				$this->ftp_mkdir($remote_directory);
			}
		}

		/// put files
		if (! $this->chdir('/') ){
			return false;
		}
		$res = array('ok' => false, 'ng' => array());
		foreach ($file_list['file'] as $l_file){
			$r_file = $remote_path.substr($l_file, $remote_pos ); // +1 is remove first flash
			$ftp_remote_file = substr($r_file, strlen($ftp_root));
			//$l_file = str_replace( '/','\\',$l_file );
			//$ftp_remote_file = str_replace( '/','\\',$ftp_remote_file );
			//$this->put($l_file, $ftp_remote_file, FTP_BINARY);
			$dont_overwrite = $this->_dont_overwrite($r_file, $this->no_overwrite);
			if ( $dont_overwrite === false &&  !$this->put($l_file, $ftp_remote_file) ){
				$res['ng'][] = $ftp_remote_file;
				//adump($ftp_remote_file);
			} else if ($res['ok'] === false) {
				$res['ok'] = true;
			}
		}
		return $res;
	}

	protected function _ftpPutSub_To_module($local_path, $remote_path, $remote_pos , $trust_dirname , $dirname )
	{
		$ftp_root = $this->seekFTPRoot();
		if ($ftp_root===false){
			return false;
		}
		$file_list = $this->_getFileList($local_path);
		if (!isset($file_list['dir']) ){
			return true;
		}
		if (!is_array($file_list['dir']) ){
			return true;
		}
		$dir = $file_list['dir'];
		krsort($dir);
		foreach ($dir as $directory){
			$directory = str_replace('/modules/'.$trust_dirname,'/modules/'.$dirname ,$directory);
			$remote_directory = $remote_path.substr($directory, $remote_pos);
			//adump( '/modules/'.$trust_dirname,'/modules/'.$dirname ,$directory, $remote_path, $remote_directory);
			if (!is_dir($remote_directory)){
				$mkdir_result = $this->ftp_mkdir($remote_directory);
			}
		}

		/// put files
		if (! $this->chdir('/') ){
			return false;
		}
		$res = array('ok' => false, 'ng' => array());
		foreach ($file_list['file'] as $l_file){
			//rename dirname
			$r_file = $remote_path.substr(str_replace('/modules/'.$trust_dirname.'/','/modules/'.$dirname.'/' ,$l_file), $remote_pos ); // +1 is remove first flash
			$ftp_remote_file = substr($r_file, strlen($ftp_root));
			$dont_overwrite = $this->_dont_overwrite($r_file, $this->no_overwrite);
			if ( $dont_overwrite === false &&  !$this->put($l_file, $ftp_remote_file) ){
				$res['ng'][] = $ftp_remote_file;
				//adump($ftp_remote_file);
			} else if ($res['ok'] === false) {
				$res['ok'] = true;
			}
		}
		return $res;
	}
	protected function _ftpPutSub_OtherThan_module($local_path, $remote_path, $remote_pos , $trust_dirname , $dirname )
	{
		$ftp_root = $this->seekFTPRoot();
		if ($ftp_root===false){
			return false;
		}
		$file_list = $this->_getFileList($local_path);
		if (!isset($file_list['dir']) ){
			return true;
		}
		if (!is_array($file_list['dir']) ){
			return true;
		}
		$dir = $file_list['dir'];
		krsort($dir);

		foreach ($dir as $directory){
			if (strstr($directory ,'/modules/'.$trust_dirname)){
				continue;
			}
			$remote_directory = $remote_path.substr($directory, $remote_pos);
			if (!is_dir($remote_directory)){
				$this->ftp_mkdir($remote_directory);
			}
		}

		/// put files
		if (! $this->chdir('/') ){
			return false;
		}
		$res = array('ok' => false, 'ng' => array());
		foreach ($file_list['file'] as $l_file){
			if (strstr($l_file ,'/modules/'.$trust_dirname.'/')){
				continue;
			}
			$r_file = $remote_path.substr($l_file, $remote_pos ); // +1 is remove first flash
			$ftp_remote_file = substr($r_file, strlen($ftp_root));
			//$l_file = str_replace( '/','\\',$l_file );
			//$ftp_remote_file = str_replace( '/','\\',$ftp_remote_file );
			//$this->put($l_file, $ftp_remote_file, FTP_BINARY);
			$dont_overwrite = $this->_dont_overwrite($r_file, $this->no_overwrite);
			if ( $dont_overwrite === false &&  !$this->put($l_file, $ftp_remote_file) ){
				$res['ng'][] = $ftp_remote_file;
				//adump($ftp_remote_file);
			} else if ($res['ok'] === false) {
				$res['ok'] = true;
			}
		}
		return $res;
	}

	private function _getFileList($dir, $list=array('dir'=> array(), 'file' => array()))
	{
		if (is_dir($dir) == false) {
			return;
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

	private function _dont_overwrite($file, $chk_array)
	{
		if (empty($chk_array)) {
			return false;
		}
		foreach ($chk_array as $item) {
			if( strpos($file, $item) === 0 && file_exists($file)){
				//adump($file, $item);
				return true;
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
		$ftpRoot = $this->seekFTPRoot();
		$localDir = substr($dir, strlen($ftpRoot));
		return $this->ftpMkdirByFtpPath($localDir);
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

}// end class


?>