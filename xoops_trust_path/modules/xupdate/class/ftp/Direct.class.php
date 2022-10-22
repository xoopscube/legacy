<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2022 XOOPS Cube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

include_once __DIR__ . '/Abstract.class.php';

class Xupdate_Ftp_ extends Xupdate_Ftp_Abstract {

	/**
	 * app_login
	 *
	 * @param string $server
	 *
	 * @return    bool
	 **/
	public function app_login( $server ) {
		$test = XOOPS_TRUST_PATH . '/xupdate_test';

		return ( @ mkdir( $test ) && @ chmod( $test, 0777 ) && @ rmdir( $test ) );
	}

	/**
	 * quit
	 *
	 * @param bool $force
	 *
	 * @return bool
	 */
	public function quit( $force = false ) {
		return true;
	}

	public function chdir( $pathname ) {
		return @ chdir( $pathname );
	}

	public function rmdir( $pathname ) {
		return @ rmdir( $pathname );
	}

	public function mkdir( $pathname ) {
		return @ mkdir( $pathname );
	}

	public function rename( $from, $to ) {
		return @ rename( $from, $to );
	}

	public function filesize( $pathname ) {
		return filesize( $pathname );
	}

	public function mdtm( $pathname ) {
		return filemtime( $pathname );
	}

	public function lstat( $pathname ) {
		return lstat( $pathname );
	}

	public function stat( $pathname ) {
		return fstat( $pathname );
	}

	public function systype() {
		return false;
	}

	public function delete( $pathname ) {
		return unlink( $pathname );
	}

	public function chmod( $pathname, $mode ) {
		return chmod( $pathname, $mode );
	}

	public function nlist( $pathname = '' ) {
		return glob( $pathname . '/*' );
	}

	public function get( $remotefile, $localfile = null, $rest = 0 ) {
		return @ copy( $remotefile, $localfile );
	}

	public function put( $localfile, $remotefile = null, $rest = 0 ) {
		return @ copy( $localfile, $remotefile );
	}
}

?>

