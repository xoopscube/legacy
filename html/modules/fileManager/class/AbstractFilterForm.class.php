<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/

if (!defined('XOOPS_ROOT_PATH')) exit();

class FileManager_AbstractFilterForm
{
	var $mSort = 0;
	var $mSortKeys = array();
	var $_mCriteria = null;
	var $mNavi = null;

	function FileManager_AbstractFilterForm(&$navi)
//	function FileManager_AbstractFilterForm(&$navi, &$handler)
	{
		$this->mNavi =& $navi;
//		$this->_mHandler =& $handler;
//		$this->_mCriteria =& new CriteriaCompo();
		$this->mNavi->mGetTotalItems->add(array(&$this, 'getTotalItems'));
	}

	function getDefaultSortKey()
	{
	}

	function getTotalItems(&$total)
	{
		$total = $this->_mHandler->getCount($this->getCriteria());
	}

	function fetchSort()
	{
		$root =& XCube_Root::getSingleton();
		$this->mSort = intval($root->mContext->mRequest->getRequest('sort'));
		if (!isset($this->mSortKeys[abs($this->mSort)])) {
			$this->mSort = $this->getDefaultSortKey();
		}
		$this->mNavi->mSort['sort'] = $this->mSort;

	}

	function fetch()
	{
		$this->mNavi->fetch();
		$this->fetchSort();
	}

	function getSort()
	{
		$sortkey = abs($this->mSort);
		return isset($this->mSortKeys[$sortkey]) ? $this->mSortKeys[$sortkey] : 0;
	}

	function getOrder()
	{
		return ($this->mSort < 0) ? "DESC" : "ASC";
	}

	function getCriteria($start = null, $limit = null)
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
