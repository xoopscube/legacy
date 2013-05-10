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
	public $content ; // Instance of content of Xupdate_FtpModuleInstall class object

	public function __construct($XupdateObj)
	{
		$this->Xupdate = $XupdateObj ;
		$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->mod_config = $this->Xupdate->mod_config ;
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
	public function _multiDownloadFile( &$multiData, $cacheTTL )
	{
		$timeout = 300;
		$this->put_debug_log(str_repeat('-', 10) . date("H:i:s"));
		$this->put_debug_log('Start _multiDownloadFile().');

		$downloadDirPath = $this->Xupdate->params['temp_path'];
		$realDirPath = realpath($downloadDirPath);

		$this->appendMes('downloaded in: '.$downloadDirPath);
		
		$max = (!empty($this->mod_config['parallel_fetch_max']))? intval($this->mod_config['parallel_fetch_max']) : 50;
		$start = 0;
		$count = count($multiData);
		$ret = true;
		$this->recent_error = '';
		while($fetchs = array_slice($multiData, $start, $max, true)) {
			$this->appendMes('multi download start: '.($start + 1).' to '.(min($start + $max, $count)));
			$fps = $chs = array();
			$start += $max;
			foreach($fetchs as $key => $data) {
			
				$target_key = $data['target_key'];
				
				// TODO ファイルNotFound対策
				//$url = $this->_getDownloadUrl( $target_key, $downloadUrlFormat );
				if (empty($data['downloadUrl'])){
					$this->_set_error_log('_multiDownloadFile false. empty downloadUrl');
					continue;
				}
				
				$downloadedFilePath = $multiData[$key]['downloadedFilePath'] = $this->_getDownloadFilePath( $realDirPath, $data['tempFilename'] );
				
				// cache check
				if ($cacheTTL && is_file($downloadedFilePath) && filemtime($downloadedFilePath) + $cacheTTL > $_SERVER['REQUEST_TIME']) {
					$this->put_debug_log('"' . $data['tempFilename'] . '" cache found');
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
					$this->appendMes('curl_init OK ('.$data['downloadUrl'].')');
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
						throw new Exception('curl_setopt CURLOPT_FILE, CURLOPT_HEADER or CURLOPT_FAILONERROR fail',3);
					}
				} catch (Exception $e) {
					$this->_set_error_log($e->getMessage());
					
					fclose($fp);
					return false;
				}
				
				//safe_mode  CURLOPT_FOLLOWLOCATION cannot be activated when in safe_mode
				if (! $this->Ftp->isSafeMode && ini_get('open_basedir') == '') {
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
					curl_setopt($ch, CURLOPT_URL, Xupdate_Utils::getRedirectUrl($data['downloadUrl'], $this->mod_config['curl_ssl_no_verify']));
				}
				
				//SSL NO VERIFY setting
				try {
					if(! Xupdate_Utils::setupCurlSsl($ch, $this->mod_config['curl_ssl_no_verify']) ){
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
								throw new Exception('curl_setopt PROXY fail skip', 6);
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
									throw new Exception('curl_setopt PROXYUSERPWD fail skip', 7);
								}
							} catch (Exception $e) {
								$this->_set_error_log($e->getMessage());
							}
						}
					}
				}
				// set timeout
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
				
				$chs[$key] = $ch;
				$fps[$key] = $fp;
				$ch = null;
				$fp = null;
			}
		
			if (! $chs) {
				$this->put_debug_log('No fetch on this time. Uses cache all.');
				continue;
			}
			
			$error_touch_time = $_SERVER['REQUEST_TIME'] - $cacheTTL + 10;
			if (count($chs) > 1) {
				$this->put_debug_log('Start curl_multi.');
				// multi exec
				// make multi handle
				$mh = curl_multi_init();
				foreach($chs as $ch) {
					curl_multi_add_handle($mh,$ch);
				}
				$this->put_debug_log('Done curl_multi_add_handle().');
				
				$timeover = time() + $timeout;
				$active = null;
				
				// multi exec
				if (empty($this->mod_config['curl_multi_select_not_use'])) {
					do {
						$mrc = curl_multi_exec($mh, $active);
					} while ($mrc === CURLM_CALL_MULTI_PERFORM);
					
					$this->put_debug_log('1st curl_multi_exec() $mrc: ' . $mrc);
					$this->put_debug_log('1st curl_multi_exec() $active: ' . $active);
					
					if ($active && $mrc == CURLM_OK) {
						do switch ($_res = curl_multi_select($mh)) {
							case 0: // 正常な場合でも 0 が返ることがある(例： XAMPP 1.7.7 Win [PHP: 5.3.8])
							case -1: // 正常な場合でも -1 が返ることがある(例： XAMPP 1.8.1 win [PHP: 5.4.7])
								if ($timeover < time()) {
									$active = false;
									$this->_set_error_log('curl_multi_select() timeout');
									break;
								} else {
									$this->_set_error_log('curl_multi_select() wait a little');
									// ref. https://bugs.php.net/bug.php?id=61141
									usleep(100); // wait a little
								}
							default:
								$this->put_debug_log('curl_multi_select(): '.$_res);
								do {
									$this->put_debug_log('Do curl_multi_exec()');
									$mrc = curl_multi_exec($mh, $active);
									$this->put_debug_log(str_repeat('-', 5) . date("H:i:s"));
									$this->put_debug_log('2nd+ curl_multi_exec() $mrc: ' . $mrc);
									$this->put_debug_log('2nd+ curl_multi_exec() $active: ' . $active);
								} while ($mrc === CURLM_CALL_MULTI_PERFORM);
						} while ($active);
						$this->put_debug_log('curl_multi_exec() Finished');
					}
				} else {
					$this->put_debug_log('Not uses curl_multi_select()');
					do {
						$mrc = curl_multi_exec($mh, $active);
						$this->put_debug_log('curl_multi_exec() $mrc: ' . $mrc);
						$this->put_debug_log('curl_multi_exec() $active: ' . $active);
						if ($timeover < time()) {
							$this->_set_error_log('curl_multi_select() timeout');
							break;
						}
						usleep(500000); // wait 500ms
					} while ($mrc === CURLM_CALL_MULTI_PERFORM || $active);
				}
				
				foreach($chs as $key => $ch) {
					fclose($fps[$key]);
					if ($_err = curl_error($ch)) {
						$_info = curl_getinfo($ch);
						$this->_set_error_log($_err . "\n" . '<div><pre>'.print_r($_info, true).'</pre></div>');
					}
					curl_multi_remove_handle($mh, $ch);
					if ($_err && $_info['http_code'] != 404 /* NotFound */ && is_file($multiData[$key]['downloadedFilePath'])) {
						// retry 10sec later if has error
						touch($multiData[$key]['downloadedFilePath'], $error_touch_time);
						$multiData[$key]['cacheMtime'] = $error_touch_time;
					}
				}
				curl_multi_close($mh);
			} else {
				// single exec
				reset($chs);
				$ch = current($chs);
				$key = key($chs);
				curl_exec($ch);
				fclose($fps[$key]);
				if ($_err = curl_error($ch)) {
					$_info = curl_getinfo($ch);
					$this->_set_error_log($_err . "\n" . '<div><pre>'.print_r($_info, true).'</pre></div>');
					$this->appendContent($_err, true);
					$ret = false;
				}
				if ($_err && $_info['http_code'] != 404 /* NotFound */ && is_file($multiData[$key]['downloadedFilePath'])) {
					// retry 10sec later if has error
					touch($multiData[$key]['downloadedFilePath'], $error_touch_time);
					$multiData[$key]['cacheMtime'] = $error_touch_time;
				}
				curl_close($ch);
			}
		}
		return $ret;
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
			return null;
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
		if ($msg) {
			$this->Ftp->appendMes('<span style="color:red;">'.$msg.'</span><br />');
			$this->put_debug_log('[Error] ' . strip_tags($msg));
		}
	}
	
	/**
	 * appendMes
	 * 
	 * @param string $msg
	 * @return void
	 */
	private function appendMes($msg) {
		if ($msg) {
			$this->Ftp->appendMes($msg.'<br />');
			$this->put_debug_log(strip_tags($msg));
		}
	}
	
	/**
	 * append content of Xupdate_FtpModuleInstall class instance
	 * 
	 * @param string $msg
	 * @param bool   $isError
	 * @return void
	 */
	private function appendContent($msg, $isError = false) {
		if ($msg) {
			$style = $isError? 'color:red;' : '';
			$this->content.= '<span style="'.$style.'">'.$msg.'</span><br />';
		}
	}
	
	/**
	 * _make_debug_log
	 * 
	 * @param string $msg
	 * @return void
	 */
	private function put_debug_log($msg) {
		static $debuglog;
		if ($msg && $this->mod_config['Show_debug']) {
			if (! $debuglog) {
				$debuglog = realpath($this->Xupdate->params['temp_path']) . '/'.rawurlencode(substr(XOOPS_URL, 7)).'_Func_debug.log';
				if (!is_file($debuglog) || filemtime($debuglog) + 600 < time()) {
					file_put_contents($debuglog, '');
				}
			}
			file_put_contents($debuglog, $msg . "\n", FILE_APPEND | LOCK_EX);
		}
	}
	
	/**
	 * enable protector of mainfile.php
	 * 
	 * @param boolean $do_chmod
	 * @return boolean
	 */
	public function write_mainfile_protector($do_chmod = false) {
		$mailfile = XOOPS_ROOT_PATH . '/mainfile.php';
		$src = file_get_contents($mailfile);
		if (! preg_match('#(?:include|require)\s*\(?\s*XOOPS_TRUST_PATH\s*.\s*[\'|"]/modules/protector/include/precheck.inc.php\'#i', $src)) {
			$src = str_replace('if (!defined(\'_LEGACY_PREVENT_LOAD_CORE_\') && XOOPS_ROOT_PATH != \'\') {', 'if (!defined(\'_LEGACY_PREVENT_LOAD_CORE_\') && XOOPS_ROOT_PATH != \'\') {
        include XOOPS_TRUST_PATH.\'/modules/protector/include/precheck.inc.php\' ;', $src);
			$src = preg_replace('#include XOOPS_ROOT_PATH.\'/include/common.php\';\s+}#', 'include XOOPS_ROOT_PATH.\'/include/common.php\';
        }
        include XOOPS_TRUST_PATH.\'/modules/protector/include/postcheck.inc.php\' ;', $src);
			if ($do_chmod) {
				$mod = @ fileperms($mailfile);
				$this->Ftp->localChmod($mailfile, 0606);
			}
			file_put_contents($mailfile, $src, LOCK_EX);
			if ($do_chmod) {
				$this->Ftp->localChmod($mailfile, $mod? $mod : 0404);
			}
		}
		return true;
	}
} // end class
} // end if

?>