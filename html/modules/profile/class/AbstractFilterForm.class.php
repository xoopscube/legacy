<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_AbstractFilterForm
{
    public $mSort = 0;
    public $mSortKeys = array();
    public $mNavi = null;
    public $_mHandler = null;
    public $_mCriteria = null;
    public $mObjectHandler = null;

    /**
     * @protected
     */
    public function _getId()
    {
    }

    /**
     * @protected
     */
    public function &_getHandler()
    {
    }

    /**
     * @protected
     */
    // !Fix deprecated constructor for PHP 7.x
    public function __construct()
    // public function Profile_AbstractFilterForm()
    {
        $this->_mCriteria =new CriteriaCompo();
    }

    /**
     * @protected
     */
    public function prepare(&$navi, &$handler)
    {
        $this->mNavi =& $navi;
        $this->_mHandler =& $handler;
    
        $this->mNavi->mGetTotalItems->add(array(&$this, 'getTotalItems'));
    }

    /**
     * @protected
     */
    public function getTotalItems(&$total)
    {
        $total = $this->_mHandler->getCount($this->getCriteria());
    }

    /**
     * @protected
     */
    public function fetchSort()
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
    public function fetch()
    {
        $this->mNavi->fetch();
        $this->fetchSort();
    }

    /**
     * @public
     */
    public function getSort()
    {
        $sortkey = abs($this->mSort);
        return $this->mSortKeys[$sortkey];
    }

    /**
     * @public
     */
    public function getOrder()
    {
        return ($this->mSort < 0) ? "DESC" : "ASC";
    }

    /**
     * @public
     */
    public function &getCriteria($start = null, $limit = null)
    {
        $t_start = ($start === null) ? $this->mNavi->getStart() : intval($start);
        $t_limit = ($limit === null) ? $this->mNavi->getPerpage() : intval($limit);
    
        $criteria = $this->_mCriteria;
    
        $criteria->setStart($t_start);
        $criteria->setLimit($t_limit);
        return $criteria;
    }
}
