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
	exit();
}

/**
 * Xupdate_NotifyBlock
 **/
class Xupdate_NotifyBlock extends Legacy_BlockProcedure {
	/**
	 * @var Xupdate_ModuleHandler
	 *
	 * @private
	 **/
	protected $_mHandler = null;

	/**
	 * @protected Legacy_AbstractCategoryObject
	 *
	 * @private
	 **/
	protected $_mOject = null;

	/**
	 * @protected int
	 *
	 * @private
	 **/
	protected $_mCount = [];

	/**
	 * @protected string[]
	 *
	 * @private
	 **/
	protected $_mOptions = [];

	/**
	 * prepare
	 *
	 * @param void
	 *
	 * @return  bool
	 *
	 * @public
	 **/
	public function prepare() {
		return parent::prepare() && $this->_setupObject( $this->_mBlock->get( 'dirname' ) );
	}

	/**
	 * _setupObject
	 *
	 * @param void
	 *
	 * @return  bool
	 *
	 * @private
	 **/
	protected function _setupObject( $dirname ) {
		$root        =& XCube_Root::getSingleton();
		$roleManager = new Legacy_RoleManager();
		$roleManager->loadRolesByDirname( $dirname );
		if ( $root->mContext->mUser->isInRole( 'Module.' . $dirname . '.Admin' ) ) {
			$this->_mHandler = Legacy_Utils::getModuleHandler( 'ModuleStore', $dirname );

			return true;
		} else {
			return false;
		}
	}

	/**
	 * execute
	 *
	 * @param void
	 *
	 * @return  void
	 *
	 * @public
	 **/
	public function execute() {
		$result = '';

		// load data refrash image by JS
		$root         =& XCube_Root::getSingleton();
		$headerScript = $root->mContext->getAttribute( 'headerScript' );
		$headerScript->addScript( 'var xupdateCheckImg=new Image();xupdateCheckImg.src="' . XOOPS_MODULE_URL . '/xupdate/admin/index.php?action=ModuleView&checkonly=1";' );

		$this->_mHandler->getNotifyHTML();
	}

	public function isDisplay() {
		return false;
	}
}
