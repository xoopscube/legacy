<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2022 XOOPSCube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

/* *
 * PHP version 7 (Nuno Luciano aka gigamaster)
 * Xupdate_ftp excute function 
 * */

require_once XUPDATE_TRUST_PATH . '/include/FtpCommonFunc.class.php';

class Xupdate_FtpCommonZipArchive extends Xupdate_FtpCommonFunc {

	public function __construct() {
		parent::__construct();
	}

	public function _unzipFile() {
		// local file name
		$downloadDirPath  = str_replace( DIRECTORY_SEPARATOR, '/', realpath( $this->Xupdate->params['temp_path'] ) );
		$downloadFilePath = $this->Xupdate->params['temp_path'] . '/' . $this->download_file;
		$exploredDirPath  = str_replace( DIRECTORY_SEPARATOR, '/', realpath( $downloadDirPath . '/' . $this->target_key ) );
		if ( empty( $downloadFilePath ) ) {
			$this->_set_error_log( 'getDownloadFilePath not found error in: ' . $this->_getDownloadFilePath() );

			return false;
		}

		// preload
		if ( '.class.php' === substr( $this->download_file, - 10 ) ) {
			if ( @ copy( $this->Xupdate->params['temp_path'] . '/' . $this->download_file, $exploredDirPath . '/' . $this->download_file ) ) {
				$this->exploredPreloadPath = $exploredDirPath;

				return true;
			}

			$this->_set_error_log( 'copy error in: ' . $exploredDirPath );

			return false;
		}

		if ( 2 === $this->retry_phase ) {
			$extractor = '_unzipFile_FileArchiveCareful';
		} else {
			$extractor = ( $this->Ftp->isSafeMode ) ? '_unzipFile_FileArchiveCareful' : '_unzipFile_FileArchive';

			if ( ! $this->Ftp->isSafeMode ) {
				// check shell cmd
				if ( '.zip' === substr( $this->download_file, - 4 ) ) {
					// check shell cmd
					$this->procExec( 'unzip --help', $o, $c );
					if ( 0 === $c ) {
						$extractor = '_unzipFile_Unzip';
					} else {
						// check ZipArchive
						if ( ! $this->Ftp->isSafeMode ) {
							$mod_zip = false;
							if ( ! class_exists( 'ZipArchive' ) ) {
								if ( ! extension_loaded( 'zip' ) ) {
									if ( function_exists( 'dl' ) ) {
										$prefix = ( PHP_SHLIB_SUFFIX === 'dll' ) ? 'php_' : '';
										@ dl( $prefix . 'zip.' . PHP_SHLIB_SUFFIX );
									}
								}
								if ( class_exists( 'ZipArchive' ) ) {
									$mod_zip = true;
								}
							} else {
								$mod_zip = true;
							}
							if ( $mod_zip ) {
								$extractor = '_unzipFile_ZipArchive';
							}
						}
					}
				} elseif ( '.tar.gz' === substr( $this->download_file, - 7 ) ) {
					// check shell cmd
					$this->procExec( 'tar --version', $o, $c );
					if ( 0 === $c ) {
						unset( $o );
						$this->procExec( 'gzip --version', $o, $c );
						if ( 0 === $c ) {
							$extractor = '_unzipFile_Tar';
						}
					}
				}
			}
		}

		$this->Ftp->appendMes( 'set extractor: ' . $extractor . '<br>' );
		$this->Ftp->appendMes( 'Srart extract into ' . $exploredDirPath . '.<br>' );
		if ( $this->$extractor( $downloadFilePath, $exploredDirPath ) ) {
			$this->Ftp->appendMes( 'Extracting all done.<br>' );
			$ret = true;
		} else {
			$this->_set_error_log( 'extract error.' );
			$ret = false;
		}

		return $ret;
	}

	/**
	 * _unzipFile use unzip cmd
	 *
	 * @param $downloadFilePath
	 * @param $exploredDirPath
	 *
	 * @return    bool
	 */
	private function _unzipFile_Unzip( $downloadFilePath, $exploredDirPath ) {
		$this->_cleanup( $exploredDirPath );
		$this->procExec( 'unzip ' . $downloadFilePath . ' -d ' . $exploredDirPath, $o, $c, $e );
		if ( 0 !== $c ) {
			$this->_set_error_log( 'unzip: ' . $o );
			$this->_set_error_log( 'unzip error: ' . $e );

			return false;
		}

		return true;
	}

	/**
	 * _unzipFile use tar cmd
	 *
	 * @param $downloadFilePath
	 * @param $exploredDirPath
	 *
	 * @return    bool
	 */
	private function _unzipFile_Tar( $downloadFilePath, $exploredDirPath ) {
		$this->_cleanup( $exploredDirPath );
		$this->procExec( 'tar -xzf ' . $downloadFilePath . ' -C ' . $exploredDirPath, $o, $c, $e );
		if ( 0 !== $c ) {
			$this->_set_error_log( 'tar: ' . $o );
			$this->_set_error_log( 'tar error: ' . $e );

			return false;
		}

		return true;
	}

	/**
	 * _unzipFile use ZipArchive
	 *
	 * @param $downloadFilePath
	 * @param $exploredDirPath
	 *
	 * @return    bool
	 */
	private function _unzipFile_ZipArchive( $downloadFilePath, $exploredDirPath ) {
		try {
			if ( ! class_exists( 'ZipArchive' ) ) {
				throw new Exception( 'ZipArchive class not found fail', 1 );
			}
		} catch ( Exception $e ) {
			$this->_set_error_log( $e->getMessage() );

			return false;
		}
		$zip = new ZipArchive();

		try {
			$result = $zip->open( $downloadFilePath );
			if ( true !== $result ) {
				throw new Exception( 'ZipArchive open fail ' . $downloadFilePath, 2 );
			}
		} catch ( Exception $e ) {
			$zip_open_error_arr = [
				ZIPARCHIVE::ER_EXISTS => 'ER_EXISTS',
				ZIPARCHIVE::ER_INCONS => 'ER_INCONS',
				ZIPARCHIVE::ER_INVAL  => 'ER_INVAL',
				ZIPARCHIVE::ER_MEMORY => 'ER_MEMORY',
				ZIPARCHIVE::ER_NOENT  => 'ER_NOENT',
				ZIPARCHIVE::ER_NOZIP  => 'ER_NOZIP',
				ZIPARCHIVE::ER_OPEN   => 'ER_OPEN',
				ZIPARCHIVE::ER_READ   => 'ER_READ',
				ZIPARCHIVE::ER_SEEK   => 'ER_SEEK'
			];
			$this->_set_error_log( $e->getMessage() . ( in_array( $result, $zip_open_error_arr ) ? f : 'undefine' ) );

			return false;
		}

		try {
			$result = $zip->extractTo( $exploredDirPath );
			if ( true !== $result ) {
				throw new Exception( 'extractTo fail ', 3 );
			}
		} catch ( Exception $e ) {
			$this->_set_error_log( $e->getMessage() );

			$zip->close();

			return false;
		}

		$zip->close();

		return true;
	}

	/**
	 * _unzipFile use File_Archive
	 *
	 * @param $downloadFilePath
	 * @param $exploredDirPath
	 *
	 * @return    bool
	 */
	private function _unzipFile_FileArchive( $downloadFilePath, $exploredDirPath ) {
		require_once 'File/Archive.php';

		if ( $source = File_Archive::read( $downloadFilePath . '/' ) ) {
			if ( is_object( $source ) && 'PEAR_Error' !== get_class( $source ) ) {
				File_Archive::extract(
					$source,
					File_Archive::appender( $exploredDirPath )
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
	 * @param $downloadFilePath
	 * @param $exploredDirPath
	 *
	 * @return    bool
	 */
	private function _unzipFile_FileArchiveCareful( $downloadFilePath, $exploredDirPath ) {
		require_once 'File/Archive.php';

		$source    = ( new File_Archive )->read( $downloadFilePath . '/' );
		$className = 'File_Archive_Reader';
		$ret       = true;
		if ( $source instanceof $className ) {
			$writer = ( new File_Archive )->appender( $exploredDirPath );
			if ( ( new PEAR )->isError( $writer ) ) {
				$source->close();

				//$this->message = $writer->getMessage()
				return false;
			}

			$dirs = [];
			while ( true === $source->next() ) {
				Xupdate_Utils::check_http_timeout();

				$inner = $source->getFilename();
				$file  = $exploredDirPath . '/' . $inner;
				$stat  = $source->getStat();

				// skip extract if file already exists.
				if ( is_dir( $file )
				     || ( is_file( $file ) && filesize( $file ) == $stat['size'] ) ) {
					continue;
				}

				// make dirctory at first for safe_mode
				if ( $this->Ftp->isSafeMode ) {
					$dir = ( '/' === substr( $file, - 1 ) ) ? substr( $file, 0, - 1 ) : dirname( $file );
					if ( ! isset( $dirs[ $dir ] ) && $dir != $exploredDirPath ) {
						$this->Ftp->localMkdir( $dir );
						while ( ! isset( $dirs[ $dir ] ) && $dir != $exploredDirPath ) {
							$dirs[ $dir ] = true;
							$this->Ftp->localChmod( $dir, _MD_XUPDATE_WRITABLE_DIR_PERM_T );
							$dir = dirname( $dir );
						}
					}
				}

				$error = $writer->newFile( $inner, $stat );
				if ( ( new PEAR )->isError( $error ) ) {
					//$this->message = $error->getMessage();
					$ret = false;
					break;
				}

				$error = $source->sendData( $writer );
				if ( ( new PEAR )->isError( $error ) ) {
					//$this->message = $error->getMessage();
					$ret = false;
					break;
				}
			}//end loop
		} else {
			if ( ( new PEAR )->isError( $source ) ) {
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
	 * @param string $command command line
	 * @param array $output stdout strings
	 * @param int $return_var process exit code
	 * @param array $error_output stderr strings
	 *
	 * @return int     exit code
	 * @author Alexey Sukhotin
	 */
	private function procExec( $command, array &$output = null, &$return_var = - 1, array &$error_output = null ) {
		$descriptorspec = [
			0 => [ 'pipe', 'r' ],  // stdin
			1 => [ 'pipe', 'w' ],  // stdout
			2 => [ 'pipe', 'w' ]   // stderr
		];

		$command = escapeshellcmd( $command );
		$process = proc_open( $command, $descriptorspec, $pipes, null, null );

		if ( is_resource( $process ) ) {
			fclose( $pipes[0] );

			$tmpout = '';
			$tmperr = '';

			$output       = stream_get_contents( $pipes[1] );
			$error_output = stream_get_contents( $pipes[2] );

			fclose( $pipes[1] );
			fclose( $pipes[2] );
			$return_var = proc_close( $process );
		}

		return $return_var;
	}
} // end class
;
