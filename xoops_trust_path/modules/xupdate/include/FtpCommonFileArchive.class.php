<?php

// Xupdate_ftp excutr function
require_once XUPDATE_TRUST_PATH .'/include/FtpCommonFunc.class.php';

class Xupdate_FtpCommonZipArchive extends Xupdate_FtpCommonFunc {

	public function __construct() {
		parent::__construct();
	}

	public function _unzipFile() {
		// local file name
		$downloadDirPath = realpath($this->Xupdate->params['temp_path']);
		$downloadFilePath = $this->Xupdate->params['temp_path'].'/'.$this->download_file;
		$exploredDirPath = realpath($downloadDirPath.'/'.$this->target_key);
		if (empty($downloadFilePath) ) {
			$this->_set_error_log('getDownloadFilePath not found error in: '.$this->_getDownloadFilePath());
			return false;
		}
		
		// preload
		if (substr($this->download_file, -10) === '.class.php') {
			if (@ copy($this->Xupdate->params['temp_path'].'/'.$this->download_file, $exploredDirPath.'/'.$this->download_file)) {
				$this->exploredPreloadPath = $exploredDirPath;
				return true;
			} else {
				$this->_set_error_log('copy error in: '.$exploredDirPath);
				return false;
			}
		}
		
		if ($this->retry_phase === 2) {
			$extractor = '_unzipFile_FileArchiveCareful';
		} else {
			$extractor = ($this->Ftp->isSafeMode)? '_unzipFile_FileArchiveCareful' : '_unzipFile_FileArchive';
			
			if (! $this->Ftp->isSafeMode) {
				// check shell cmd
				if (substr($this->download_file, -4) === '.zip') {
					// check shell cmd
					$this->procExec('unzip --help', $o, $c);
					if ($c === 0) {
						$extractor = '_unzipFile_Unzip';
					} else {
						// check ZipArchive
						if (! $this->Ftp->isSafeMode) {
							$mod_zip = false;
							if(! class_exists('ZipArchive')){
								if (! extension_loaded('zip')) {
									if (function_exists('dl')){
										$prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
										@ dl($prefix . 'zip.' . PHP_SHLIB_SUFFIX);
									}
								}
								if (class_exists('ZipArchive')) {
									$mod_zip = true;
								}
							} else {
								$mod_zip = true;
							}
							if ($mod_zip) {
								$extractor = '_unzipFile_ZipArchive';
							}
						}
					}
				} else if (substr($this->download_file, -7) === '.tar.gz') {
					// check shell cmd
					$this->procExec('tar --version', $o, $c);
					if ($c === 0) {
						unset($o);
						$this->procExec('gzip --version', $o, $c);
						if ($c === 0) {
							$extractor = '_unzipFile_Tar';
						}
					}
				}
			}
		}
		
		$this->Ftp->appendMes('set extractor: '.$extractor.'<br />');
		$this->Ftp->appendMes('Srart extract into '.$exploredDirPath.'.<br />');
		if ($this->$extractor($downloadFilePath, $exploredDirPath)) {
			$this->Ftp->appendMes('Extracting all done.<br />');
			$ret = true;
		} else {
			$this->_set_error_log('extract error.');
			$ret = false;
		}
		return $ret;
	}
	
	/**
	 * _unzipFile use unzip cmd
	 *
	 * @return	bool
	 **/
	private function _unzipFile_Unzip($downloadFilePath, $exploredDirPath) {
		$this->_cleanup($exploredDirPath);
		$this->procExec('unzip ' . $downloadFilePath . ' -d ' . $exploredDirPath, $o, $c, $e);
		if  ($c !== 0) {
			$this->_set_error_log('unzip: '.$o);
			$this->_set_error_log('unzip error: '.$e);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * _unzipFile use tar cmd
	 *
	 * @return	bool
	 **/
	private function _unzipFile_Tar($downloadFilePath, $exploredDirPath) {
		$this->_cleanup($exploredDirPath);
		$this->procExec('tar -xzf ' . $downloadFilePath . ' -C ' . $exploredDirPath, $o, $c, $e);
		if  ($c !== 0) {
			$this->_set_error_log('tar: '.$o);
			$this->_set_error_log('tar error: '.$e);
			return false;
		} else {
			return true;
		}
	}

	/**
	 * _unzipFile use ZipArchive
	 *
	 * @return	bool
	 **/
	private function _unzipFile_ZipArchive($downloadFilePath, $exploredDirPath)
	{
		try {
			if(!class_exists('ZipArchive') ){
				throw new Exception('ZipArchive class not found fail',1);
			}
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());
			return false;
		}
		$zip = new ZipArchive;
	
		try {
			$result = $zip->open($downloadFilePath);
			if($result !==true ){
				throw new Exception('ZipArchive open fail '.$downloadFilePath ,2);
			}
		} catch (Exception $e) {
			$zip_open_error_arr = array(
					ZIPARCHIVE::ER_EXISTS => 'ER_EXISTS',
					ZIPARCHIVE::ER_INCONS => 'ER_INCONS',
					ZIPARCHIVE::ER_INVAL => 'ER_INVAL',
					ZIPARCHIVE::ER_MEMORY => 'ER_MEMORY',
					ZIPARCHIVE::ER_NOENT => 'ER_NOENT',
					ZIPARCHIVE::ER_NOZIP => 'ER_NOZIP',
					ZIPARCHIVE::ER_OPEN => 'ER_OPEN',
					ZIPARCHIVE::ER_READ => 'ER_READ',
					ZIPARCHIVE::ER_SEEK => 'ER_SEEK'
			);
			$this->_set_error_log($e->getMessage().(in_array($result,$zip_open_error_arr) ? f : 'undefine' ));
			return false;
		}
	
		try {
			$result = $zip->extractTo($exploredDirPath);
			if($result !==true ){
				throw new Exception('extractTo fail ',3);
			}
		} catch (Exception $e) {
			$this->_set_error_log($e->getMessage());
	
			$zip->close();
			return false;
		}
	
		$zip->close();
	
		return true;
	}
	
	/**
	 * _unzipFile use File_Archive
	 *
	 * @return	bool
	 **/
	private function _unzipFile_FileArchive($downloadFilePath, $exploredDirPath)
	{
		require_once 'File/Archive.php';
		
		if ($source = File_Archive::read($downloadFilePath.'/')) {
			if (is_object($source) && get_class($source) !== 'PEAR_Error') {
				File_Archive::extract(
					$source,
					File_Archive::appender($exploredDirPath)
				);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * _unzipFile use ZipArchive for recovery
	 *
	 * @return	bool
	 **/
	private function _unzipFile_FileArchiveCareful($downloadFilePath, $exploredDirPath)
	{
		require_once 'File/Archive.php';
		
		$source = File_Archive::read($downloadFilePath . '/');
		$className = 'File_Archive_Reader';
		$ret = true;
		if ($source instanceof $className) {
			$writer = File_Archive::appender($exploredDirPath);
			if (PEAR::isError($writer)) {
				$source->close();
				//$this->message = $writer->getMessage()
				return false;
			}
				
			$dirs = array();
			while ($source->next() === true) {
				Xupdate_Utils::check_http_timeout();
				
				$inner = $source->getFilename();
				$file = $exploredDirPath . '/' . $inner;
				$stat = $source->getStat();
	
				// skip extract if file already exists.
				if ( is_dir($file)
						|| (is_file($file) && filesize($file) == $stat['size'])) {
					continue;
				}
	
				// make dirctory at first for safe_mode
				if ($this->Ftp->isSafeMode) {
					$dir = (substr($file, -1) == '/') ? substr($file, 0, -1) : dirname($file);
					if (!isset($dirs[$dir]) && $dir != $exploredDirPath) {
						$this->Ftp->localMkdir($dir);
						while (!isset($dirs[$dir]) && $dir != $exploredDirPath) {
							$dirs[$dir] = true;
							$this->Ftp->localChmod($dir, _MD_XUPDATE_WRITABLE_DIR_PERM);
							$dir = dirname($dir);
						}
					}
				}
	
				$error = $writer->newFile($inner, $stat);
				if (PEAR::isError($error)) {
					//$this->message = $error->getMessage();
					$ret = false;
					break;
				}
	
				$error = $source->sendData($writer);
				if (PEAR::isError($error)) {
					//$this->message = $error->getMessage();
					$ret = false;
					break;
				}
			}//end loop
		} else {
			if (PEAR::isError($source)) {
				//$this->message = $source->getMessage();
			}
			return false;
		}
		$writer->close();
		$source->close();
		return $ret;
	}
	
	/**
	 * Execute shell command
	 *
	 * @param  string  $command       command line
	 * @param  array   $output        stdout strings
	 * @param  array   $return_var    process exit code
	 * @param  array   $error_output  stderr strings
	 * @return int     exit code
	 * @author Alexey Sukhotin
	 **/
	private function procExec($command , array &$output = null, &$return_var = -1, array &$error_output = null) {
	
		$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin
				1 => array("pipe", "w"),  // stdout
				2 => array("pipe", "w")   // stderr
		);
	
		$command = escapeshellcmd($command);
		$process = proc_open($command, $descriptorspec, $pipes, null, null);
	
		if (is_resource($process)) {
	
			fclose($pipes[0]);
	
			$tmpout = '';
			$tmperr = '';
	
			$output = stream_get_contents($pipes[1]);
			$error_output = stream_get_contents($pipes[2]);
	
			fclose($pipes[1]);
			fclose($pipes[2]);
			$return_var = proc_close($process);
	
	
		}
	
		return $return_var;
	
	}
} // end class

?>
