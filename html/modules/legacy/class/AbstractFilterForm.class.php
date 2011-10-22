<?php
/**
 *
 * @package Legacy
 * @version $Id: AbstractFilterForm.class.php,v 1.3 2008/09/25 15:11:30 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_AbstractFilterForm
{
	var $mSort = 0;
	var $mSortKeys = array();
	var $_mCriteria = null;
	var $mNavi = null;
	
	var $_mHandler = null;
	
	function Legacy_AbstractFilterForm(&$navi, &$handler)
	{
		$this->mNavi =& $navi;
		$this->_mHandler =& $handler;
		
		$this->_mCriteria =new CriteriaCompo();
		
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
		$sortkey = abs($this->mNavi->mSort['sort']);
		return isset($this->mSortKeys[$sortkey]) ? $this->mSortKeys[$sortkey] : null;
	}

	function getOrder()
	{
		return ($this->mSort < 0) ? "DESC" : "ASC";
	}

	function getCriteria($start = null, $limit = null)
	{
		$t_start = 0;
		$t_limit = 0;
		if ($start === null) {
			$t_start = $this->mNavi->getStart();
		} else {
			$t_start = intval($start);
			$this->mNavi->setStart($t_start);
		}
		if ($limit === null) {
			$t_limit = $this->mNavi->getPerpage();
		} else {
			$t_limit = intval($limit);
			$this->mNavi->setPerpage($t_limit);
		}
		
		$criteria = $this->_mCriteria;
		
		$criteria->setStart($t_start);
		$criteria->setLimit($t_limit);
		
		return $criteria;
	}
}

?>
