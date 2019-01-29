<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class User_AbstractFilterForm
{
    public $mSort = 0;
    public $mSortKeys = array();
    public $_mCriteria = null;
    public $mNavi = null;

    public function User_AbstractFilterForm(&$navi, &$handler)
    {
        self::__construct($navi, $handler);
    }

    public function __construct(&$navi, &$handler)
    {
        $this->mNavi =& $navi;
        $this->_mHandler =& $handler;
        
        $this->_mCriteria =new CriteriaCompo();
        
        $this->mNavi->mGetTotalItems->add(array(&$this, 'getTotalItems'));
    }

    public function getDefaultSortKey()
    {
    }
    
    public function getTotalItems(&$total)
    {
        $total = $this->_mHandler->getCount($this->getCriteria());
    }
    
    public function fetchSort()
    {
        $root =& XCube_Root::getSingleton();
        $this->mSort = intval($root->mContext->mRequest->getRequest('sort'));
        
        if (!isset($this->mSortKeys[abs($this->mSort)])) {
            $this->mSort = $this->getDefaultSortKey();
        }
        
        $this->mNavi->mSort['sort'] = $this->mSort;
    }

    public function fetch()
    {
        $this->mNavi->fetch();
        $this->fetchSort();
    }
    
    public function getSort()
    {
        $sortkey = abs($this->mSort);
        return isset($this->mSortKeys[$sortkey]) ? $this->mSortKeys[$sortkey] : 0;
    }

    public function getOrder()
    {
        return ($this->mSort < 0) ? "DESC" : "ASC";
    }

    public function getCriteria($start = null, $limit = null)
    {
        $t_start = ($start === null) ? $this->mNavi->getStart() : intval($start);
        $t_limit = ($limit === null) ? $this->mNavi->getPerpage() : intval($limit);
        
        $criteria = $this->_mCriteria;
        
        $criteria->setStart($t_start);
        $criteria->setLimit($t_limit);
        
        return $criteria;
    }
}
