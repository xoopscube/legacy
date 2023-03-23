<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.3
 * @author     Other authors Minahito, 2007/05/15
 * @author     Haruki Setoyama
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


class cache_manager {

	public $s_files = [];
	public $f_files = [];

	public function write( $file, $source ) {
		if ( false !== $fp = fopen( XOOPS_CACHE_PATH . '/' . $file, 'wb' ) ) {
			fwrite( $fp, $source );
			fclose( $fp );
			$this->s_files[] = $file;
		} else {
			$this->f_files[] = $file;
		}
	}

	public function report() {
		$reports = [];
		foreach ( $this->s_files as $val ) {
			$reports[] = _OKIMG . sprintf( _INSTALL_L123, "<b>$val</b>" );
		}
		foreach ( $this->f_files as $val ) {
			$reports[] = _NGIMG . sprintf( _INSTALL_L124, "<b>$val</b>" );
		}

		return $reports;
	}
}
