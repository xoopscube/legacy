<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Haruki Setoyama
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0

 */

class mainfile_manager {

	public $path = '../mainfile.php';
	public $distfile = '../mainfile.dist.php';
	public $rewrite = [];

	public $report = [];
	public $error = false;

	public function setRewrite( $def, $val ) {
		$this->rewrite[ $def ] = $val;
	}

	public function copyDistFile() {
		if ( ! copy( $this->distfile, $this->path ) ) {
			$this->report[] = _NGIMG . sprintf( _INSTALL_L126, '<b>' . $this->path . '</b>' );
			$this->error    = true;

			return false;
		}
		$this->report[] = _OKIMG . sprintf( _INSTALL_L125, '<b>' . $this->path . '</b>', '<b>' . $this->distfile . '</b>' );

		return true;
	}

	public function doRewrite() {
		if ( ! $file = fopen( $this->path, 'r' ) ) {
			$this->error = true;

			return false;
		}
		clearstatcache();
		$content = fread( $file, filesize( $this->path ) );
		fclose( $file );

		foreach ( $this->rewrite as $key => $val ) {
			if ( is_int( $val ) &&
			     preg_match( "/(define\()([\"'])(" . $key . ")\\2,\s*([0-9]+)\s*\)/", $content ) ) {
				$content        = preg_replace( "/(define\()([\"'])(" . $key . ")\\2,\s*([0-9]+)\s*\)/", "define('" . $key . "', " . $val . ')', $content );
				$this->report[] = _OKIMG . sprintf( _INSTALL_L121, "<b>$key</b>", $val );
			} elseif ( preg_match( "/(define\()([\"'])(" . $key . ")\\2,\s*([\"'])(.*?)\\4\s*\)/", $content ) ) {
				if ( 'XOOPS_DB_TYPE' === $key && 'mysql' === $val ) {
					$content        = preg_replace( "/(define\()([\"'])(" . $key . ")\\2,\s*([\"'])(.*?)\\4\s*\)/", "extension_loaded('mysql')? define('XOOPS_DB_TYPE', 'mysql') : define('XOOPS_DB_TYPE', 'mysqli')", $content );
					$this->report[] = _OKIMG . sprintf( _INSTALL_L121, '<b>' . $key . '</b>', $val );
				} else {
					$content        = preg_replace( "/(define\()([\"'])(" . $key . ")\\2,\s*([\"'])(.*?)\\4\s*\)/", "define('" . $key . "', '" . $this->sanitize( $val ) . "')", $content );
					$this->report[] = _OKIMG . sprintf( _INSTALL_L121, '<b>' . $key . '</b>', $val );
				}
			} else {
				$this->error    = true;
				$this->report[] = _NGIMG . sprintf( _INSTALL_L122, '<b>' . $val . '</b>' );
			}
		}

		if ( ! $file = fopen( $this->path, 'wb' ) ) {
			$this->error = true;

			return false;
		}

		if ( fwrite( $file, $content ) === - 1 ) {
			fclose( $file );
			$this->error = true;

			return false;
		}

		fclose( $file );

		return true;
	}

	public function report() {
		return $this->report;
	}

	public function error() {
		return $this->error;
	}

	public function sanitize( $val ) {
		$val = addslashes( $val );

		return str_replace( '$', '\$', $val );
	}
}
