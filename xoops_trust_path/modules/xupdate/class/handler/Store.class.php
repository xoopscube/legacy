<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2023 The XOOPSCube Project
 * @license GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

/**
 * Xupdate_StoreObject
 **/
class Xupdate_StoreObject extends XoopsSimpleObject {
	/**
	 * __construct
	 *
	 * @param void
	 *
	 * @return    void
	 **/
	public function __construct() {
		$this->initVar( 'sid', XOBJ_DTYPE_INT, '0', false );
		$this->initVar( 'name', XOBJ_DTYPE_STRING, '', false, 191 );
		$this->initVar( 'contents', XOBJ_DTYPE_STRING, '', false, 191 );
		$this->initVar( 'addon_url', XOBJ_DTYPE_STRING, '', false, 191 );
		$this->initVar( 'setting_type', XOBJ_DTYPE_INT, '0', false );
		$this->initVar( 'reg_unixtime', XOBJ_DTYPE_INT, '0', false );
	}
}

/**
 * Xupdate_StoreHandler
 **/
class Xupdate_StoreHandler extends XoopsObjectGenericHandler {
	/*** string ***/
	public $mTable = '{dirname}_store';

	/*** string ***/
	public $mPrimary = 'sid';

	/*** string ***/
	public $mClass = 'Xupdate_StoreObject';

	private string $cacheCheckFile;

	/**
	 * __construct
	 *
	 * @param XoopsDatabase  &$db
	 * @param string $dirname
	 *
	 * @return    void
	 **/
	public function __construct( /*** XoopsDatabase ***/ &$db, /*** string ***/ $dirname ) {
		$this->mTable = strtr( $this->mTable, [ '{dirname}' => $dirname ] );
		parent::__construct( $db );
		$configHandler        = &xoops_gethandler( 'config' );
		$module_config        = $configHandler->getConfigsByDirname( $dirname );
		$this->cacheCheckFile = XOOPS_TRUST_PATH . '/' . trim( $module_config['temp_path'], '/' ) . '/' . rawurlencode( substr( XOOPS_URL, 7 ) ) . '_cacheCheck.ini.php';
	}

	public function setNeedCacheRemake( $remove = false ) {
		if ( $remove ) {
			is_file( $this->cacheCheckFile ) && unlink( $this->cacheCheckFile );
		} else {
			touch( $this->cacheCheckFile, 0 );
		}
	}

	public function getCacheCheckFile() {
		return $this->cacheCheckFile;
	}
}
