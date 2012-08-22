<?php

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

if( ! class_exists( 'Xupdate_Func' ) ) {

class Xupdate_Func {

	/* xupdate module variables */
	public $Xupdate = null ;	// xupdate module object
	public $Ftp  ;	// FTP instance
	public $mod_config ;

	public function __construct($XupdateObj)
	{
		$this->Xupdate = $XupdateObj ;
		$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->mod_config = $this->Xupdate->mod_config ;
		//$this->_makeTmpDir();
	}

	public function & getInstance($mydirname)
	{
		static $instance ;
		if( ! isset( $instance[$mydirname] ) ) {
			$instance[$mydirname] = new Xupdate_Func($mydirname) ;
		}
		return $instance[$mydirname] ;
	}

	/**
	 * _downloadFile
	 *
	 * @param string $target_key
	 * @param string $downloadUrl
	 * @param string $tempFilename
	 * @byref string $downloadedFilePath
	 * @param int   $cacheTTL
	 * 
	 * @return	bool
	 **/
	public function _downloadFile( $target_key, $downloadUrl, $tempFilename, &$downloadedFilePath, $cacheTTL = 0 )
	{
		$multiData = array(array(
			'target_key' => $target_key,
			'downloadUrl' => $downloadUrl,
			'tempFilename' => $tempFilename,
			'downloadedFilePath' => ''));
		
		if ($this->_multiDownloadFile( $multiData, $cacheTTL )) {
			$downloadedFilePath = $multiData[0]['downloadedFilePath'];
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * @byref array $multiData
	 * @param int   $cacheTTL
	 * @throws Exception
	 * 
	 * @return boolean
	 */
	function _multiDownloadFile( &$multiData, $cacheTTL )
	{
		$chs = array();
		foreach($multiData as $key => $data) {
			$downloadDirPath = $this->Xupdate->params['temp_path'];
			$realDirPath = realpath($downloadDirPath);
			
			$target_key = $data['target_key'];
			
			$this->Ftp->appendMes('downladed in: '.$downloadDirPath.'<br />');
			$this->content.= 'downladed in: '.$downloadDirPath.'<br />';
			
			// TODO ファイルNotFound対策
			//$url = $this->_getDownloadUrl( $target_key, $downloadUrlFormat );
			if (empty($data['downloadUrl'])){
				$this->_set_error_log('_getDownloadUrl false');
				continue;
			}
			
			$downloadedFilePath = $multiData[$key]['downloadedFilePath'] = $this->_getDownloadFilePath( $realDirPath, $data['tempFilename'] );
			
			// cache check
			if ($cacheTTL && is_file($downloadedFilePath) && filemtime($downloadedFilePath) + $cacheTTL > $_SERVER['REQUEST_TIME']) {
				continue;
			}
			
			try {
				try {
					if(!function_exists('curl_init') ){
						throw new Exception('curl_init function no found fail',1);
					}
				} catch (Exception $e) {
					$this->_set_error_log($e->getMessage());
					return false;
				}
			
				$ch = curl_init($data['downloadUrl']);
				if($ch === false ){
					throw new Exception('curl_init fail',2);
				}
				$this->Ftp->appendMes('curl_init OK<br />');
			} catch (Exception $e) {
				$this->_set_error_log($e->getMessage());
				return false;
			}
			
			$fp = fopen($downloadedFilePath, 'wb');
			
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
			if (ini_get('safe_mode') != '1' && ini_get('open_basedir') == '') {
				try {
					//redirect suport
					$setopt4 = curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					$setopt5 = curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			
					if(!$setopt4 || !$setopt5 ){
						throw new Exception('curl_setopt CURLOPT_FOLLOWLOCATION fail skip',4);
					}
				} catch (Exception $e) {
					$this->_set_error_log($e->getMessage());
				}
			} else if (empty($data['noRedirect'])) {
				curl_setopt($ch, CURLOPT_URL, Xupdate_Utils::getRedirectUrl($data['downloadUrl']));
			}
			
			//SSL NO VERIFY setting
			try {
				$setopt6 = curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$setopt7 = curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				if(!$setopt6 || !$setopt7 ){
					throw new Exception('curl_setopt SSL fail',5);
				}
			} catch (Exception $e) {
				$this->_set_error_log($e->getMessage());
		
				fclose($fp);
				continue;
			}
			
			// Proxy setting
			if (!empty($_SERVER['HTTP_PROXY']) || !empty($_SERVER['http_proxy'])) {
				$proxy = parse_url(!empty($_SERVER['http_proxy']) ? $_SERVER['http_proxy'] : $_SERVER['HTTP_PROXY']);
				if (!empty($proxy) && isset($proxy['host'])) {
					// url
					$proxyURL = (isset($proxy['scheme']) ? $proxy['scheme'] : 'http') . '://';
					$proxyURL .= $proxy['host'];
					
					if (isset($proxy['port'])) {
						$proxyURL .= ":" . $proxy['port'];
					} elseif ('http://' == substr($proxyURL, 0, 7)) {
						$proxyURL .= ":80";
					} elseif ('https://' == substr($proxyURL, 0, 8)) {
						$proxyURL .= ":443";
					}
					try {
						if(! curl_setopt($ch, CURLOPT_PROXY, $proxyURL)) {
							throw new Exception('curl_setopt PROXY fail', 6);
						}
					} catch (Exception $e) {
						$this->_set_error_log($e->getMessage());
					}
					// user:password
					if (isset($proxy['user'])) {
						$proxyAuth = $proxy['user'];
						if (isset($proxy['pass'])) {
							$proxyAuth .= ':' . $proxy['pass'];
						}
						try {
							if(! curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyAuth)) {
								throw new Exception('curl_setopt PROXYUSERPWD fail', 7);
							}
						} catch (Exception $e) {
							$this->_set_error_log($e->getMessage());
						}
					}
				}
			}
			
			$chs[$key] = $ch;
			$fps[$key] = $fp;
			$ch = null;
			$fp = null;
		}
		
		if (! $chs) {
			return true;
		}
		
		// make multi handle
		$mh = curl_multi_init();
		
		foreach($chs as $ch) {
			curl_multi_add_handle($mh,$ch);
		}
		
		$active = null;
		// multi exec
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		
		while ($active && $mrc == CURLM_OK) {
			if (curl_multi_select($mh) != -1) {
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		
		foreach($chs as $key => $ch) {
			$this->_set_error_log(curl_error($ch));
			curl_multi_remove_handle($mh, $ch);
			fclose($fps[$key]);
		}
		curl_multi_close($mh);
		
		return true;
	}

	/**
	 * makeDirectory
	 *
	 * @param string $realDirPath
	 * @param string $directoryName
	 *
	 * @return	string
	 **/
	public function makeDirectory( $realDirPath, $directoryName )
	{
		if (empty($realDirPath) ) {
			$this->_set_error_log('directory path not found error in: '.$realDirPath);
			return null;
		}
		if (! chdir($realDirPath) ) {
			$this->_set_error_log('chdir error in: '.$realDirPath);
			return null;//chdir error
		}
		
		if (! is_dir($directoryName)) {
			@mkdir($directoryName);
		}
		$newDirPath = realpath($realDirPath.'/'.$directoryName);

		if (strpos($newDirPath, $realDirPath) === false){
			$this->_set_error_log('directory traversal error in: '.$newDirPath);
			return null;
		}
		if (!is_dir($newDirPath)){
			$this->_set_error_log('not is_dir error in: '.$newDirPath);
			return null;//chdir error
		}
		return $newDirPath;
	}

	/**
	 * _getDownloadUrl
	 *
	 * @param string $downloadUrlFormat
	 * @param string $target_key
	 *
	 * @return	string
	 **/
	public function _getDownloadUrl( $target_key, $downloadUrlFormat )
	{
		// TODO ファイルNotFound対策
		//$url = sprintf( $downloadUrlFormat, $target_key );
		$url = str_replace(array('%s', '%u'), $target_key, $downloadUrlFormat);
		return $url;
	}

	/**
	 * _getDownloadFilePath
	 *
	 * @param string $downloadDirPath
	 * @param string $target_key
	 * @param string $extension
	 *
	 * @return	string
	 **/
	public function _getDownloadFilePath( $downloadDirPath, $tempFilename )
	{
		$downloadPath = $downloadDirPath .'/'. $tempFilename;
		return $downloadPath;
	}
	
	/**
	 * Set tag cloud size
	 * 
	 * @param array $cloud
	 * @param int $smallest
	 * @param int $duration
	 * @param int $step
	 */
	public function setTagCloudSize(& $cloud, $smallest = 100, $duration = 24, $step = 10) {
		$min = sqrt(min($cloud));
		$max = sqrt(max($cloud));
		$factor = 0;
		// specal case all tags having the same count
		if (($max - $min) == 0) {
			$min -= $duration;
			$factor = 1;
		} else {
			$factor = $duration / ($max - $min);
		}
		foreach($cloud as $key => $count) {
			$level = (int)((sqrt($count) - $min) * $factor);
			$cloud[$key] = $level * $step + $smallest;
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
} // end if

?>