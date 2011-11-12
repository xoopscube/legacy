<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_AbstractFilterForm
{
	var $mSort = 0;
	var $mSortKeys = array();
	var $mNavi = null;
	var $_mHandler = null;
	var $_mCriteria = null;
	var $mObjectHandler = null;

	/**
	 * @protected
	 */
	function _getId()
	{
	}

	/**
	 * @protected
	 */
	function &_getHandler()
	{
	}

	/**
	 * @protected
	 */
	function Profile_AbstractFilterForm()
	{
		$this->_mCriteria =new CriteriaCompo();
	
	}

	/**
	 * @protected
	 */
	function prepare(&$navi, &$handler)
	{
		$this->mNavi =& $navi;
		$this->_mHandler =& $handler;
	
		$this->mNavi->mGetTotalItems->add(array(&$this, 'getTotalItems'));
	}

	/**
	 * @protected
	 */
	function getTotalItems(&$total)
	{
		$total = $this->_mHandler->getCount($this->getCriteria());
	}

	/**
	 * @protected
	 */
	function fetchSort()
	{
		$root =& XCube_Root::getSingleton();
		$this->mSort = intval($root->mContext->mRequest->getRequest($this->mNavi->mPrefix . 'sort'));
	
		if (!isset($this->mSortKeys[abs($this->mSort)])) {
			$this->mSort = $this->getDefaultSortKey();
		}
	
		$this->mNavi->mSort[$this->mNavi->mPrefix . 'sort'] = $this->mSort;
	}

	/**
	 * @public
	 */
	function fetch()
	{
		$this->mNavi->fetch();
		$this->fetchSort();
	}

	/**
	 * @public
	 */
	function getSort()
	{
		$sortkey = abs($this->mSort);
		return $this->mSortKeys[$sortkey];
	}

	/**
	 * @public
	 */
	function getOrder()
	{
		return ($this->mSort < 0) ? "DESC" : "ASC";
	}

	/**
	 * @public
	 */
	function &getCriteria($start = null, $limit = null)
	{
		$t_start = ($start === null) ? $this->mNavi->getStart() : intval($start);
		$t_limit = ($limit === null) ? $this->mNavi->getPerpage() : intval($limit);
	
		$criteria = $this->_mCriteria;
	
		$criteria->setStart($t_start);
		$criteria->setLimit($t_limit);
		return $criteria;
	}
}

?>
