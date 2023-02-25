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

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

/**
 * Xupdate_AbstractFilterForm
 **/
abstract class Xupdate_AbstractFilterForm {
	/*** Enum ***/
	public $mSort = 0;

	/*** string[] ***/
	public $mSortKeys = [];

	/*** XCube_PageNavigator ***/
	public $mNavi = null;

	/*** XoopsObjectGenericHandler ***/
	protected $_mHandler = null;

	/*** Criteria ***/
	protected $_mCriteria = null;

	/**
	 * _getId
	 *
	 * @return void
	 */
	protected function _getId() {
	}

	/**
	 * &_getHandler
	 *
	 * @return void
	 */
	protected function &_getHandler() {
	}

	/**
	 * __construct
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	public function __construct() {
		$this->_mCriteria = new CriteriaCompo();
	}

	/**
	 * prepare
	 *
	 * @param XCube_PageNavigator  &$navi
	 * @param XoopsObjectGenericHandler  &$handler
	 *
	 * @return  void
	 **/
	public function prepare( /*** XCube_PageNavigator ***/ &$navi, /*** XoopsObjectGenericHandler ***/ &$handler ) {
		$this->mNavi     =& $navi;
		$this->_mHandler =& $handler;

		$this->mNavi->mGetTotalItems->add( [ &$this, 'getTotalItems' ] );
	}

	/**
	 * getTotalItems
	 *
	 * @param int  &$total
	 *
	 * @return  void
	 **/
	public function getTotalItems( /*** int ***/ &$total ) {
		$total = $this->_mHandler->getCount( $this->getCriteria() );
	}

	/**
	 * fetchSort
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	protected function fetchSort() {
		$root =& XCube_Root::getSingleton();
//fix pagenavi
		$this->mNavi->setStart( (int) $root->mContext->mRequest->getRequest( $this->mNavi->mPrefix . 'start' ) );

		$this->mSort = (int) $root->mContext->mRequest->getRequest( $this->mNavi->mPrefix . 'sort' );

		if ( ! isset( $this->mSortKeys[ abs( $this->mSort ) ] ) ) {
			$this->mSort = $this->getDefaultSortKey();
		} else {
			$this->mNavi->mSort[ $this->mNavi->mPrefix . 'sort' ] = $this->mSort;
		}
	}

	/**
	 * fetch
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	public function fetch() {
		$this->mNavi->fetch();
		$this->fetchSort();
	}

	/**
	 * getSort
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function getSort() {
		$sortkey = abs( $this->mSort );

		return $this->mSortKeys[ $sortkey ];
	}

	/**
	 * getOrder
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function getOrder() {
		return ( $this->mSort < 0 ) ? 'desc' : 'asc';
	}

	/**
	 * &getCriteria
	 *
	 * @param int $start
	 * @param int $limit
	 *
	 * @return \CriteriaCompo|null
	 */
	public function &getCriteria( /*** int ***/ $start = null, /*** int ***/ $limit = null ) {
		$t_start = ( null === $start ) ? $this->mNavi->getStart() : (int) $start;
		$t_limit = ( null === $limit ) ? $this->mNavi->getPerpage() : (int) $limit;

		$criteria = $this->_mCriteria;

		$criteria->setStart( $t_start );
		$criteria->setLimit( $t_limit );

		return $criteria;
	}
}
