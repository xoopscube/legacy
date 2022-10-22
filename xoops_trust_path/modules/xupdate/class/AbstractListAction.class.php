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

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

require_once XOOPS_ROOT_PATH . '/core/XCube_PageNavigator.class.php';

/**
 * Xupdate_AbstractListAction
 **/
abstract class Xupdate_AbstractListAction extends Xupdate_AbstractAction {
	/*** XoopsSimpleObject[] ***/
	public $mObjects = null;

	/*** Xupdate_AbstractFilterForm ***/
	public $mFilter = null;

	/**
	 * &_getHandler
	 *
	 * @return void &XoopsObjectGenericHandler
	 */
	protected function &_getHandler() {
	}

	/**
	 * _getActionName
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	protected function _getActionName() {
		return _LIST;
	}

	/**
	 * &_getFilterForm
	 *
	 * @return void &XupdateAbstractFilterForm
	 */
	protected function &_getFilterForm() {
	}

	/**
	 * _getBaseUrl
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	protected function _getBaseUrl() {
	}

	/**
	 * &_getPageNavi
	 *
	 * @return \XCube_PageNavigator &XCube_PageNavigator
	 */
	protected function &_getPageNavi() {
		$navi = new XCube_PageNavigator( $this->_getBaseUrl(), XCUBE_PAGENAVI_START );

		return $navi;
	}

	/**
	 * getDefaultView
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function getDefaultView() {
		$this->mFilter =& $this->_getFilterForm();
		$this->mFilter->fetch();

		$handler        =& $this->_getHandler();
		$this->mObjects =& $handler->getObjects( $this->mFilter->getCriteria() );

		return XUPDATE_FRAME_VIEW_INDEX;
	}

	/**
	 * execute
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function execute() {
		return $this->getDefaultView();
	}
}
