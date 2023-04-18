<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright (c) 2005-2023 The XOOPSCube Project
 * @license GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractAction.class.php';

/**
 * Xupdate_Admin_IndexAction
 **/
class Xupdate_Admin_UserPassColumnLenFixAction extends Xupdate_AbstractAction {
	/**
	 * getDefaultView
	 *
	 * @return void
	 */
	public function getDefaultView() {
		$redirect = xoops_getrequest( 'xoops_redirect' );

		if ( '/' !== $redirect[0] ) {
			$redirect = XOOPS_URL . '/index.php';
		}
		if ( ! defined( 'XCUBE_CORE_USER_PASS_LEN_FIXED' ) && $this->mod_config['_FtpLoginCheck'] && is_callable( 'User_Utils::checkUsersPassColumnLength' ) ) {
			$this->_userPassColumnLenFix();
		}
		header( 'Location: ' . $redirect );
		exit();
	}

	private function _userPassColumnLenFix() {
		if ( User_Utils::checkUsersPassColumnLength() ) {
			// update mainfile.php that adds `define('XCUBE_CORE_USER_PASS_LEN_FIXED', true);`
			if ( $this->Ftp->app_login() ) {
				$mainfile = XOOPS_ROOT_PATH . '/mainfile.php';
				$src      = file_get_contents( $mainfile );
				$sens     = '/define\s*\(\s*(["\'])XOOPS_MAINFILE_INCLUDED\\1\s*,\s*1\s*\)\s*;\s*(?:\r\n|\n|\r)/';
				if ( preg_match( $sens, $src ) && ! preg_match( '/define\s*\(\s*(["\'])XCUBE_CORE_USER_PASS_LEN_FIXED\\1/', $src ) ) {
					$add   = '    // Already fixed length of users table pass column of this DB (Auto inserts by X-update)' . "\n";
					$add   .= '    define(\'XCUBE_CORE_USER_PASS_LEN_FIXED\', true);' . "\n\n";
					$src   = preg_replace( $sens, '$0' . $add, $src );
					$local = XOOPS_TRUST_PATH . '/' . $this->mod_config['temp_path'] . '/mainfile.custom.php';
					file_put_contents( $local, $src );
					$mod = @ fileperms( $mainfile );
					$this->Ftp->localChmod( $mainfile, 0606 );
					$this->Ftp->localPut( $local, $mainfile );
					$this->Ftp->localChmod( $mainfile, $mod ?: 0404 );
					@unlink( $local );
				}
			}
		}
	}
}
