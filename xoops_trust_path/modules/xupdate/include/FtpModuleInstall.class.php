<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2022 XOOPSCube Project
 * @license GPL v2.0
 */

// Xupdate_ftp excutr function

require_once XUPDATE_TRUST_PATH . '/include/FtpCommonFileArchive.class.php';

class Xupdate_FtpModuleInstall extends Xupdate_FtpCommonZipArchive {

	public $trust_dirname;
	public $dirname;
	public $html_only;

	private $systemModules = [];
	private $systemDirs = [];

	public function __construct() {
		parent::__construct();

		// @todo
		$this->systemModules = [ 'legacy' ];

		// @todo
		$this->systemDirs = [
			'html',
			'html/class',
			'html/class/database/*',
			'html/class/mail/*',
//			'html/class/xml/*',
			'html/class/xoopsform/*',
			'html/core/*',
			'html/include/*',
			'html/install/*',
			'html/kernel/*',
			'html/modules/legacy/*',
			'html/modules/legacyRender/*',
			'html/modules/message/*',
			'html/modules/profile/*',
			'html/modules/stdCache/*',
			'html/modules/user/*',
			'xoops_trust_path/settings/definition.inc.php',
			'xoops_trust_path/settings/site_default.dist.ini',
			'xoops_trust_path/settings/site_default.ini'
		];
	}

	/**
	 * execute
	 *
	 * @param string $caller
	 *
	 * @return    bool
	 **/
	public function execute( $caller ) {
		$result        = true;
		$siteCloseConf = null;
		if ( true === $this->Xupdate->params['is_writable']['result'] ) {
			$this->retry_phase = isset( $_POST['upload_retry'] ) ? (int) $_POST['upload_retry'] : 0;

			$downloadDirPath = realpath( $this->Xupdate->params['temp_path'] );
			// check excutable & retry_phase
			if ( ! $this->is_xupdate_excutable() ) {
				if ( 0 === $this->retry_phase || ! file_exists( $downloadDirPath . '/' . $this->target_key ) ) {
					$this->content .= '<div class="error">' . _MI_XUPDATE_ANOTHER_PROCESS_RUNNING . '</div>';

					return false;
				}
			}

			// clean up download dirctory
			if ( ! $this->retry_phase ) {
				$this->_cleanUp_downloadDir();
			} else {
				$this->Ftp->appendMes( 'Retry phase: ' . $this->retry_phase . '<br>' );
			}

			$this->_set_stage( 1 );

			if ( ! $exploredDirPath = $this->checkExploredDirPath( $this->target_key ) ) {
				$this->_set_error_log( _MI_XUPDATE_ERR_MAKE_EXPLOREDDIR . ': ' . $this->target_key );

				return false;
			}

			$downloadUrl = $this->Func->_getDownloadUrl( $this->target_key, $this->downloadUrlFormat );

			if ( 'preload' === $caller && preg_match( '/\.php$/i', $downloadUrl ) ) {
				$this->download_file = $this->target_key . '.class.php';
			} else {
				$this->download_file = $this->target_key . ( preg_match( '/\btar\b/i', $downloadUrl ) ? '.tar.gz' : '.zip' );
			}

			register_shutdown_function( 'xupdate_on_shutdown', $this->Xupdate->params['temp_path'], $downloadUrl );
			// set stat time
			Xupdate_Utils::check_http_timeout();

			$this->content .= _MI_XUPDATE_PROG_FILE_GETTING . '<br>';
			if ( $this->retry_phase || $this->Func->_downloadFile( $this->target_key, $downloadUrl, $this->download_file, $this->downloadedFilePath ) ) {
				$this->_set_stage( 2 );
				$this->exploredDirPath = $exploredDirPath;

				if ( $this->retry_phase ) {
					$this->downloadedFilePath = $this->Func->_getDownloadFilePath( $downloadDirPath, $this->download_file );
				}
				if ( $this->retry_phase > 2 || $this->_unzipFile() ) {
					$this->_set_stage( 3 );

					// delete downloaded archive
					@ unlink( $this->downloadedFilePath );

					if ( 'preload' === $caller ) {
						$set_member  = 'exploredPreloadPath';
						$serach_file = $this->target_key . '.class.php';
					} else {
						$set_member = $serach_file = '';
					}
					// ディレクトリを掘り下げて探索
					if ( $this->exploredPreloadPath || $this->_exploredDirPath_DownDir( $set_member, $serach_file ) ) {
						// TODO port , timeout
						if ( $this->Ftp->isConnected() || true == $this->Ftp->app_login() ) {
							$this->_set_stage( 4 );
							// overwrite control
							if ( ! isset( $this->options['no_overwrite'] ) ) {
								$this->options['no_overwrite'] = [];
							}
							if ( ! isset( $this->options['no_update'] ) ) {
								$this->options['no_update'] = [];
							}
							$this->Ftp->set_item_options( $this->options );

							// is system module?
							$_isSystemModule = ( in_array( $this->dirname, $this->systemModules ) || ( ! empty( $this->trust_dirname ) && in_array( $this->trust_dirname, $this->systemModules ) ) );
							if ( ! $_isSystemModule ) {
								$this->_removeSystemFile();
							}

							// do close site
							if ( isset( $_POST['do_closesite'] ) || ! $this->mRoot->mContext->getXoopsConfig( 'closesite' ) ) {
								$cHandler  =& xoops_gethandler( 'config' );
								$_criteria = new CriteriaCompo();
								$_criteria->add( new Criteria( 'conf_modid', 0 ) );
								$_criteria->add( new Criteria( 'conf_catid', 1 ) );
								$_criteria->add( new Criteria( 'conf_name', 'closesite' ) );
								$_confObjects =& $cHandler->getConfigs( $_criteria );
								if ( $_confObjects && is_object( $_confObjects[0] ) ) {
									$siteCloseConf = $_confObjects[0];
									if ( isset( $_POST['do_closesite'] ) ) {
										$GLOBALS['xupdate_do_closesite'] = true;
									} else {
										$siteCloseConf->set( 'conf_value', 1 );
										if ( ! $cHandler->insertConfig( $siteCloseConf ) ) {
											$siteCloseConf = null;
										} else {
											$GLOBALS['xupdate_do_closesite'] = true;
										}
									}
								}
							}

							$this->_set_stage( 5 );
							if ( $this->retry_phase < 6 ) {
								if ( $this->uploadFiles( $caller ) ) {
									@ unlink( _MD_XUPDATE_SYS_RETRYSER_FILE );
								} else {
									$this->_set_error_log( _MI_XUPDATE_ERR_FTP_UPLOADFILES );
									$result = false;
								}
							}
							$this->_set_stage( 6 );

							$this->retry_phase > 6 || $this->_set_item_perm();

							$this->_set_stage( 7 );
						} else {
							$this->_set_error_log( _MI_XUPDATE_ERR_FTP_LOGIN );
							$result = false;
						}
					} else {
						$this->_set_error_log( _MI_XUPDATE_ERR_FTP_NOTFOUND );
						$result = false;
					}
				} else {
					$this->_set_error_log( _MI_XUPDATE_ERR_UNZIP_FILE );
					$result = false;
				}
			} else {
				$this->_set_error_log( _MI_XUPDATE_ERR_DOWNLOAD_FILE );
				$result = false;
			}

			$this->content .= _MI_XUPDATE_PROG_CLEANING_UP . '<br>';
			$this->_cleanup( $exploredDirPath );

			if ( $this->Ftp->isConnected() ) {
				$this->Ftp->app_logout();
			}
			//

			@ unlink( $this->downloadedFilePath );

			if ( $result ) {
				$this->content .= _MI_XUPDATE_PROG_COMPLETED . '<br><br>';
			}

			@ unlink( _MD_XUPDATE_SYS_LOCK_FILE );
		} else {
			$result = false;
		}

		if ( $result ) {
			$this->nextlink = $this->_get_nextlink( $this->dirname, $caller );
		} else {
			$this->content .= _ERRORS;
		}

		// do open site
		if ( is_object( $siteCloseConf ) ) {
			$siteCloseConf->set( 'conf_value', 0 );
			$cHandler->insertConfig( $siteCloseConf );
		}

		return $result;
	}

	/**
	 * _getDownloadUrl
	 *
	 * @return    string
	 **/
	public function _getDownloadUrl() {
		//$url = sprintf($this->downloadUrlFormat, $this->target_key);
		$url = str_replace( $this->downloadUrlFormat, '%s', $this->target_key );

		return $url;
	}

	/**
	 * uploadFiles
	 *
	 * @param $caller
	 *
	 * @return    bool
	 */
	private function uploadFiles( $caller ) {
		//$this->Ftp->connect();

		$this->Ftp->appendMes( 'start uploading..<br>' );
		$this->content .= _MI_XUPDATE_PROG_UPLOADING . '<br>';

		if ( 'module' === $caller && 'TrustModule' === $this->target_type ) {
			if ( ! empty( $this->trust_dirname ) && ! empty( $this->dirname ) && $this->trust_dirname != $this->dirname ) {
				if ( ! $this->html_only ) {
					// copy xoops_trust_path
					$uploadPath = XOOPS_TRUST_PATH . '/';
					$unzipPath  = $this->exploredDirPath . '/xoops_trust_path';
					$result     = $this->Ftp->uploadNakami( $unzipPath, $uploadPath );
					if ( ! $this->_check_file_upload_result( $result, 'xoops_trust_path' ) ) {
						return false;
					}
				}

				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/modules/';
				$unzipPath  = $this->exploredDirPath . '/html/modules';
				$result     = $this->Ftp->uploadNakami_To_module( $unzipPath, $uploadPath, $this->trust_dirname, $this->dirname );
				if ( ! $this->_check_file_upload_result( $result, 'html/modules' ) ) {
					return false;
				}

				// rename copy html module
				$uploadPath = XOOPS_ROOT_PATH . '/';
				$unzipPath  = $this->exploredDirPath . '/html';
				$result     = $this->Ftp->uploadNakami_OtherThan_module( $unzipPath, $uploadPath, $this->trust_dirname );
				if ( ! $this->_check_file_upload_result( $result, 'html', true ) ) {
					return false;
				}
			} else {
				if ( ! $this->html_only ) {
					// copy xoops_trust_path
					$uploadPath = XOOPS_TRUST_PATH . '/';
					$unzipPath  = $this->exploredDirPath . '/xoops_trust_path';
					$result     = $this->Ftp->uploadNakami( $unzipPath, $uploadPath );
					if ( ! $this->_check_file_upload_result( $result, 'xoops_trust_path' ) ) {
						return false;
					}
				}

				// copy html
				$uploadPath = XOOPS_ROOT_PATH . '/';
				$unzipPath  = $this->exploredDirPath . '/html';
				$result     = $this->Ftp->uploadNakami( $unzipPath, $uploadPath );
				if ( ! $this->_check_file_upload_result( $result, 'html' ) ) {
					return false;
				}
			}

			// check extra languages
			if ( ! $this->html_only ) {
				$this->_copy_extra_langs( $this->dirname, $this->trust_dirname, 'trust' );
			}
			$this->_copy_extra_langs( $this->dirname, $this->trust_dirname, 'html' );

			if ( 'protector' === $this->trust_dirname ) {
				// for protector 'manip_value' update
				if ( ! XC_CLASS_EXISTS( 'Protector' ) ) {
					// check and enable protector in mainfile.php
					if ( file_exists( XOOPS_TRUST_PATH . '/modules/protector/include/precheck.inc.php' ) ) {
						$this->Func->write_mainfile_protector( true );
					}
				}
			}
		} elseif ( $this->exploredPreloadPath ) {

			// copy html/preload
			$uploadPath = XOOPS_ROOT_PATH . '/preload/';
			$unzipPath  = $this->exploredPreloadPath;
			$result     = $this->Ftp->uploadNakami( $unzipPath, $uploadPath );
			if ( ! $this->_check_file_upload_result( $result, 'html/preload' ) ) {
				return false;
			}
		} else {

			// copy xoops_trust_path if exists
			$uploadPath = XOOPS_TRUST_PATH . '/';
			$unzipPath  = $this->exploredDirPath . '/xoops_trust_path';
			if ( file_exists( $unzipPath ) ) {
				$result = $this->Ftp->uploadNakami( $unzipPath, $uploadPath );
				if ( ! $this->_check_file_upload_result( $result, 'xoops_trust_path' ) ) {
					return false;
				}
			}

			// copy html
			$uploadPath = XOOPS_ROOT_PATH . '/';
			$unzipPath  = $this->exploredDirPath . '/html';
			$result     = $this->Ftp->uploadNakami( $unzipPath, $uploadPath );
			if ( ! $this->_check_file_upload_result( $result, 'html' ) ) {
				return false;
			}

			// check extra languages
			( 'module' === $caller ) && $this->_copy_extra_langs( $this->dirname );

			// for legacy only
			if ( ( 'module' === $caller && 'legacy' === $this->dirname ) || $this->Ftp->isRootDirChange() ) {
				// for protector 'manip_value' update
				if ( XC_CLASS_EXISTS( 'Protector' ) ) {
					$db        =& Database::getInstance();
					$protector =& Protector::getInstance();
					$protector->setConn( $db->conn );
					$protector->updateConfIntoDb( 'manip_value', '' );
				} else {
					// check and enable protector in mainfile.php
					if ( file_exists( XOOPS_TRUST_PATH . '/modules/protector/include/precheck.inc.php' ) ) {
						$this->Func->write_mainfile_protector( true );
					}
				}
			}
		}

		// maifile write protect
		$this->Func->mainfile_to_readonly();

		$this->Ftp->appendMes( 'end uploaded success<br>' );

		return true;
	}

	/**
	 * _copy_extra_langs: copy extras languages
	 *
	 * @param string $dirname
	 * @param string $trust_dirname
	 * @param string $side
	 */
	private function _copy_extra_langs( $dirname, $trust_dirname = '', $side = 'html' ) {
		static $langs = null;

		if ( null === $langs ) {
			$langs = [];
			if ( $handle = opendir( XOOPS_ROOT_PATH . '/language' ) ) {
				while ( false !== ( $name = readdir( $handle ) ) ) {
					if ( '.' !== $name[0] && is_dir( XOOPS_ROOT_PATH . '/language/' . $name ) ) {
						$langs[] = $name;
					}
				}
				closedir( $handle );
			}
		}

		$uploadDir = $checkDir = [];
		$isLegacy  = ( 'legacy' === $dirname );
		if ( $isLegacy ) {
			$checkDir[]  = $this->exploredDirPath . '/extras/extra_languages/<LANG>/html';
			$uploadDir[] = XOOPS_ROOT_PATH . '/';

			$checkDir[]  = $this->exploredDirPath . '/extras/extra_languages/<LANG>';
			$uploadDir[] = XOOPS_ROOT_PATH . '/';
		} else {
			if ( 'trust' === $side ) {
				$side        = 'xoops_trust_path';
				$base        = XOOPS_TRUST_PATH;
				$arc_dirname = $trust_dirname;
			} else {
				$side        = 'html';
				$base        = XOOPS_ROOT_PATH;
				$arc_dirname = $trust_dirname ?: $dirname;
			}
			$checkDir[]  = $this->exploredDirPath . '/extras/' . $side . '/modules/' . $arc_dirname . '/language/<LANG>';
			$uploadDir[] = $base . '/modules/' . $dirname . '/language/<LANG>/';
		}
		foreach ( $langs as $lang ) {
			$unzipPath = '';
			foreach ( $checkDir as $i => $dir ) {
				$dir = str_replace( '<LANG>', $lang, $dir );
				if ( is_dir( $dir ) && ( ! $isLegacy || is_dir( $dir . '/language' ) ) ) {
					$unzipPath  = $dir;
					$uploadPath = str_replace( '<LANG>', $lang, $uploadDir[ $i ] );
					break;
				}
			}
			if ( $unzipPath ) {
				$result = $this->Ftp->uploadNakami( $unzipPath, $uploadPath );
				$this->_check_file_upload_result( $result, $side );
			}
		}
	}

	/**
	 * _get_nextlink
	 *
	 * @param string $dirname
	 *
	 * @param        $caller
	 *
	 * @return    string
	 */
	private function _get_nextlink( $dirname, $caller ) {
		$ret = '';
		if ( 'module' === $caller ) {
			$hModule = Xupdate_Utils::getXoopsHandler( 'module' );
			$module  =& $hModule->getByDirname( $dirname );
			if ( is_object( $module ) ) {
				if ( $module->getVar( 'isactive' ) ) {
					$ret = '<a href="' . XOOPS_MODULE_URL . '/legacy/admin/index.php?action=ModuleUpdate&dirname=' . $dirname . '">' . _MI_XUPDATE_ADMENU_MODULE . _MI_XUPDATE_UPDATE . '</a>';
				} else {
					$ret = _AD_LEGACY_LANG_BLOCK_INACTIVETOTAL;
				}
			} elseif ( file_exists( XOOPS_ROOT_PATH . '/modules/' . $dirname ) ) {
				$ret = '<a href="' . XOOPS_MODULE_URL . '/legacy/admin/index.php?action=ModuleInstall&dirname=' . $dirname . '">' . _MI_XUPDATE_ADMENU_MODULE . _INSTALL . '</a>';
			} else {
				$ret = '<a href="' . XOOPS_MODULE_URL . '/xupdate/admin/index.php?action=ModuleStore">' . _AD_XUPDATE_LANG_MESSAGE_GETTING_FILES . _AD_XUPDATE_LANG_MESSAGE_SUCCESS . '</a>';
			}
		} elseif ( 'theme' === $caller ) {
			$ret = '<a href="' . XOOPS_MODULE_URL . '/legacy/admin/index.php?action=ThemeList">' . _MI_XUPDATE_ADMENU_THEME . _MI_XUPDATE_MANAGE . '</a>';
		} elseif ( 'preload' === $caller ) {
			$ret = '<a href="' . XOOPS_MODULE_URL . '/xupdate/admin/index.php?action=PreloadStore">' . _AD_XUPDATE_LANG_MESSAGE_GETTING_FILES . _AD_XUPDATE_LANG_MESSAGE_SUCCESS . '</a>';
		}

		return $ret;
	}

	/**
	 * _exploredDirPath_DownDir
	 *
	 * @param string $member
	 * @param string $checkfile
	 *
	 * @return bool
	 */
	private function _exploredDirPath_DownDir( $member = '', $checkfile = '' ) {
		$dir = $this->exploredDirPath;
		$this->Ftp->appendMes( 'check exploredDirPath: ' . $this->exploredDirPath . '<br>' );
		$items   = scandir( $dir );
		$checker = [];
		foreach ( $items as $item ) {
			if ( '.' === $item || '..' === $item || '__MACOSX' === $item ) {
				continue;
			}
			if ( is_dir( $dir . '/' . $item ) ) {
				$checker[ $item ] = true;
			}
			if ( $member && $checkfile ) {
				if ( is_file( $dir . '/' . $checkfile ) ) {
					$this->Ftp->appendMes( 'found ' . $checkfile . ' in exploredDirPath: ' . $this->exploredDirPath . '<br>' );
					$this->$member = $this->exploredDirPath;

					return true;
				}
			}
		}
		if ( isset( $checker['html'] ) || isset( $checker['xoops_trust_path'] ) ) {
			$this->Ftp->exploredDirPath = $this->exploredDirPath;
			$this->Ftp->appendMes( 'found files exploredDirPath: ' . $this->exploredDirPath . '<br>' );

			return true;
		}
		foreach ( array_keys( $checker ) as $item ) {
			$this->exploredDirPath = realpath( $dir . '/' . $item );
			if ( $this->_exploredDirPath_DownDir( $member, $checkfile ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * _exploredDirPath_UpDir
	 *
	 * @param void
	 *
	 * @return    void
	 **/
	private function _exploredDirPath_UpDir() {
		$this->exploredDirPath = dirname( $this->exploredDirPath );
		$this->Ftp->appendMes( 'up dir exploredDirPath: ' . $this->exploredDirPath . '<br>' );
	}

	/**
	 * _chmod_dir
	 *
	 * @param string $directory
	 *
	 * @return    void
	 **/
	private function _chmod_dir( &$directory ) {
		if ( ! file_exists( $directory ) ) {
			$this->Ftp->localMkdir( $directory );
		}
		if ( file_exists( $directory ) && is_dir( $directory ) ) {
			$this->Ftp->localChmod( $directory, ( 0 === strpos( $directory, XOOPS_TRUST_PATH ) ) ? _MD_XUPDATE_WRITABLE_DIR_PERM_T : _MD_XUPDATE_WRITABLE_DIR_PERM );
		}
	}

	/**
	 * _chmod_file
	 *
	 * @param $file
	 *
	 * @return    void
	 */
	private function _chmod_file( &$file ) {
		if ( file_exists( $file ) ) {
			if ( ! is_dir( $file ) ) {
				$this->Ftp->localChmod( $file, ( 0 === strpos( $file, XOOPS_TRUST_PATH ) ) ? _MD_XUPDATE_WRITABLE_FILE_PERM_T : _MD_XUPDATE_WRITABLE_FILE_PERM );
			}
		} else {
			// make empty file
			$tmp = $this->exploredDirPath . '/_empty.tmp';
			if ( @ touch( $tmp ) ) {
				if ( $this->Ftp->localPut( $tmp, $file ) ) {
					$this->Ftp->localChmod( $file, ( 0 === strpos( $file, XOOPS_TRUST_PATH ) ) ? _MD_XUPDATE_WRITABLE_FILE_PERM_T : _MD_XUPDATE_WRITABLE_FILE_PERM );
				}
			}
		}
	}

	/**
	 * Rename local file
	 *
	 * @param string $pair The pair "from|to"
	 *
	 * @return void
	 */
	private function _rename( $pair ) {
		list( $from, $to ) = explode( '|', $pair );
		if ( $from && $to && ! file_exists( $to ) ) {
			$this->Ftp->localRename( $from, $to );
		}
	}

	/**
	 * Delete local file
	 *
	 * @param string $path local path
	 *
	 * @return void
	 */
	private function _delete( $path ) {
		if ( is_file( $path ) ) {
			$this->Ftp->localDelete( $path );
		}
	}

	/**
	 * Remove local dirctory recursive
	 *
	 * @param string $path local path
	 *
	 * @return void
	 */
	private function _rmdir_recursive( $path ) {
		if ( is_dir( $path ) ) {
			$this->Ftp->localRmdirRecursive( $path );
		}
	}

	/**
	 * Cleanup download dirctory
	 *
	 * @return void
	 */
	public function _cleanUp_downloadDir() {
		if ( $this->Ftp->isSafeMode ) {
			$this->Ftp->isConnected() || $this->Ftp->app_login();
		}
		$path = realpath( $this->Xupdate->params['temp_path'] );
		if ( $handle = opendir( $path ) ) {
			while ( false !== ( $entry = readdir( $handle ) ) ) {
				if ( '.' !== $entry && '..' !== $entry && is_dir( $path . DIRECTORY_SEPARATOR . $entry ) ) {
					$this->_cleanup( $path . DIRECTORY_SEPARATOR . $entry );
				}
			}
			closedir( $handle );
		}
	}

	/**
	 * Set item permission
	 *
	 * @return void
	 */
	private function _set_item_perm() {
		// rename items
		Xupdate_Utils::debug( $this->options['rename_item'] );
		if ( isset( $this->options['rename_item'] ) ) {
			array_map( [ $this, '_rename' ], $this->options['rename_item'] );
		}
		// change directories to writable
		if ( isset( $this->options['writable_dir'] ) ) {
			array_map( [ $this, '_chmod_dir' ], $this->options['writable_dir'] );
		}
		// change files to writable
		if ( isset( $this->options['writable_file'] ) ) {
			array_map( [ $this, '_chmod_file' ], $this->options['writable_file'] );
		}
		// delete dirs recursive
		if ( isset( $this->options['delete_dir'] ) ) {
			array_map( [ $this, '_rmdir_recursive' ], $this->options['delete_dir'] );
		}
		// delete files
		if ( isset( $this->options['delete_file'] ) ) {
			array_map( [ $this, '_delete' ], $this->options['delete_file'] );
		}
	}


	/**
	 * Remove locked system file or dirctory from extracted archive
	 *
	 * @return void
	 */
	private function _removeSystemFile() {
		$exploredDirCnt = strlen( $this->exploredDirPath );
		foreach ( $this->systemDirs as $dir ) {
			$dir  = rtrim( $dir, '/' );
			$path = $this->exploredDirPath . '/' . $dir;
			if ( '*' === substr( $path, - 1 ) ) {
				$path = rtrim( $path, '*/' );
				if ( is_dir( $path ) ) {
					$this->_set_error_log( 'Remove system dir: [archive]' . substr( $path, $exploredDirCnt ) );
					$this->_cleanup( $path );
				}
			} else {
				if ( is_dir( $path ) && $handle = opendir( $path ) ) {
					while ( false !== ( $entry = readdir( $handle ) ) ) {
						if ( '.' !== $entry && '..' !== $entry && ! is_dir( $path . DIRECTORY_SEPARATOR . $entry ) ) {
							$this->_set_error_log( 'Remove system file: [archive]' . substr( $path, $exploredDirCnt ) . DIRECTORY_SEPARATOR . $entry );
							unlink( $path . DIRECTORY_SEPARATOR . $entry );
						}
					}
					closedir( $handle );
				}
			}
		}
	}

	/**
	 * Set stage
	 *
	 * @param int $stage
	 */
	private function _set_stage( $stage ) {
		$GLOBALS['xupdate_stage'] = $stage;
		$this->save_lockfile( $stage );
		Xupdate_Utils::check_http_timeout();
	}
} // end class

/**
 * Called on shutdown of PHP
 *
 * @param string $cache_dir
 * @param string $download_url
 */
function xupdate_on_shutdown( $cache_dir, $download_url ) {
	@ unlink( _MD_XUPDATE_SYS_RETRYSER_FILE );
	if ( connection_status() > 1 || is_file( _MD_XUPDATE_SYS_LOCK_FILE ) ) {
		@ unlink( _MD_XUPDATE_SYS_LOCK_FILE );
		file_put_contents( _MD_XUPDATE_SYS_RETRYSER_FILE, serialize( $GLOBALS['xupdate_retry_cache'] ) );
		$buf = '';
		while ( ob_get_level() ) {
			$buf .= ob_get_contents();
			if ( ! @ ob_end_clean() ) {
				break;
			}
		}
		$msg                   = [];
		$upload_retry          = isset( $_POST['upload_retry'] ) ? (int) $_POST['upload_retry'] : 0;
		$uploaded_count        = count( $GLOBALS['xupdate_retry_cache']['uploaded_files'] );
		$uploaded_count_before = isset( $_POST['uploaded_count'] ) ? $_POST['uploaded_count'] : 0;
		$total_files           = 0;
		if ( isset( $GLOBALS['xupdate_retry_cache']['file_list'] ) ) {
			foreach ( $GLOBALS['xupdate_retry_cache']['file_list'] as $list ) {
				$total_files += count( $list['file'] );
			}
		}
		$msg[] = '<html><head><title>' . _AD_XUPDATE_LANG_TIMEOUT_ERROR . '</title></head><body>';
		$msg[] = '<h1>' . _AD_XUPDATE_LANG_TIMEOUT_ERROR . '</h1>';
		$start = $upload_retry ?: 1;
		for ( $i = $start; $i <= $GLOBALS['xupdate_stage']; $i ++ ) {
			$done_files = '';
			if ( 5 === $i ) {
				$done_files = ' (' . _AD_XUPDATE_LANG_MESSAGE_SUCCESS . ': ' . $uploaded_count . '/' . $total_files . ')';
			}
			$msg[] = constant( '_AD_XUPDATE_LANG_STAGE_' . $i ) . $done_files;
		}
		$msg[] = _AD_XUPDATE_LANG_STAGE_TIMEOUT;
		if ( $GLOBALS['xupdate_stage'] < 2 ) {
			$msg[] = sprintf( _AD_XUPDATE_LANG_STAGE_UPLOAD_NOT_COMPLETE, $download_url );
		} else {
			$post = $_POST;
			unset( $_POST['uploaded_count'], $_POST['upload_retry'] );
			if ( isset( $_SESSION['XCUBE_TOKEN'] ) ) {
				foreach ( $_SESSION['XCUBE_TOKEN'] as $key => $val ) {
					if ( $key ) {
						$key = strtr( $key, '.', '_' );
						unset( $post[ $key ] );
						$post[ $key ] = $val;
					}
				}
				$post['upload_retry']   = $GLOBALS['xupdate_stage'];
				$post['uploaded_count'] = $uploaded_count;
			}
			if ( $upload_retry > 4 && $uploaded_count_before > $uploaded_count ) {
				$msg[] = sprintf( _AD_XUPDATE_LANG_STAGE_UPLOAD_NOT_COMPLETE, $download_url );
			} else {
				if ( $GLOBALS['xupdate_do_closesite'] ) {
					$post['do_closesite'] = 1;
				}
				$form = '<form method="post" action="./?' . $_SERVER['QUERY_STRING'] . '" onsubmit="document.getElementById(\'retry\').disabled=\'disabled\'">';
				foreach ( $post as $key => $val ) {
					if ( is_array( $val ) ) {
						foreach ( $val as $_val ) {
							$form .= '<input type="hidden" name="' . htmlspecialchars( $key ) . '[]" value="' . htmlspecialchars( $_val, ENT_COMPAT, _CHARSET ) . '" />';
						}
					} else {
						$form .= '<input type="hidden" name="' . htmlspecialchars( $key ) . '" value="' . htmlspecialchars( $val, ENT_COMPAT, _CHARSET ) . '" />';
					}
				}
				$form  .= '<input id="retry" type="submit" value="' . ( ( 5 === $GLOBALS['xupdate_stage'] ) ? _AD_XUPDATE_LANG_STAGE_UPLOAD_RETRY : _AD_XUPDATE_LANG_STAGE_TASK_RETRY ) . '" />';
				$form  .= '</form>';
				$msg[] = $form;
			}
		}
		if ( $buf ) {
			$msg[] = str_repeat( '-', 20 );
			$msg[] = 'PHP error message:';
			$msg[] = $buf;
		}
		$msg[] = '</body></html>';
		if ( ! headers_sent() ) {
			header( 'Content-type: text/html; charset=' . _CHARSET );
		}
		echo implode( '<br>' . "\n", $msg );
	}
}
