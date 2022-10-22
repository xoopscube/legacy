<?php
/**
 * XCube_RenderCache.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 */

class XCube_RenderCache {
	public $mCacheId;
	public $mResourceName;

	public function __construct() {
	}

	/**
	 * @param null $cachetime
	 *
	 * @return void
	 */
	public function isCache( $cachetime = null ) {
	}

	/**
	 * @return bool
	 */
	public function enableCache(): bool
    {
		return true;
	}

	public function setResourceName( $name ) {
		$this->mResourceName = $name;
	}

	/**
	 * @return string
	 */
	public function getCacheId(): string
    {
	}

	/**
	 * @return string
	 */
	public function _getFileName(): string
    {
	}

	public function save( $renderTarget ) {
		if ( $this->enableCache() ) {
			$filename = $this->_getFileName();
			$fp       = fopen( $filename, 'wb' );
			fwrite( $fp, $renderTarget->getResult() );
			fclose( $fp );
		}
	}

	public function load() {
		if ( $this->isCache() ) {
			return file_get_contents( $this->_getFileName() );
		}
	}

	public function clear() {
	}

	public function reset() {
		$this->mResourceName = null;
	}
}
