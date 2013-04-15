<?php

// Xupdate_ftp excutr function
require_once XUPDATE_TRUST_PATH .'/include/FtpCommonFunc.class.php';

class Xupdate_FtpCommonZipArchive extends Xupdate_FtpCommonFunc {

	private $is_safemode;
	
	public function __construct() {
		parent::__construct();
		$this->is_safemode = (ini_get('safe_mode') == "1");
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
		
		// current work directory
		$cwd = getcwd();
		if (! chdir($exploredDirPath) ) {
			$this->_set_error_log('chdir error in: '.$exploredDirPath);
			return false;//chdir error
		}
		
		$extractor = '_unzipFile_FileArchive';
		
		// check shell cmd
		if (substr($this->download_file, -4) === '.zip') {
			// check shell cmd
			$this->procExec('unzip --help', $o, $c);
			if ($c === 0) {
				$extractor = '_unzipFile_Unzip';
			} else {
				// check ZipArchive
				if (! $this->is_safemode) {
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
		
		$this->Ftp->appendMes('set extractor: '.$extractor.'<br />');
		$this->Ftp->appendMes('Srart extract into '.$exploredDirPath.'.<br />');
		if ($this->$extractor($downloadFilePath, $exploredDirPath)) {
			$this->Ftp->appendMes('Extracting all done.<br />');
			$ret = true;
		} else {
			$this->_set_error_log('extract error.');
			$ret = false;
		}
		if ($cwd) @ chdir($cwd);
		return $ret;
	}
	
	private function _unzipFile_Unzip($downloadFilePath, $exploredDirPath) {
		
		$this->procExec('unzip ' . $downloadFilePath, $o, $c);
		if  ($c !== 0) {
			$this->_set_error_log('unzip error: '.$o);
			return false;
		} else {
			return true;
		}
	}
	
	private function _unzipFile_Tar($downloadFilePath, $exploredDirPath) {
	
		$this->procExec('tar -xzf ' . $downloadFilePath, $o, $c);
		if  ($c !== 0) {
			$this->_set_error_log('tar error: '.$o);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * _unzipFile use File_Archive
	 *
	 * @return	bool
	 **/
	private function _unzipFile_FileArchive($downloadFilePath, $exploredDirPath)
	{
		require_once 'File/Archive.php';
		
		if ($this->is_safemode) {
			// make dirctory at first for safe_mode
			$dirs = array();
			if ($source = File_Archive::read($downloadFilePath.'/', $exploredDirPath)) {
				if (is_object($source) && get_class($source) !== 'PEAR_Error') {
					while ($source->next()) {
						$file = $source->getFilename();
						$dir = dirname($file);
						if (!isset($dirs[$dir])) {
							$dirs[$dir] = true;
							$this->Ftp->localMkdir($dir);
							$this->Ftp->localChmod($dir, 0707);
						}
					}
					$source->close();
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
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
	
		return true;
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
